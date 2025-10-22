<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassificacaoTributariaController extends Controller
{
    private $deepSeekApiKey;
    private $deepSeekBaseUrl = 'https://api.deepseek.com/v1';

    public function __construct()
    {
        $this->deepSeekApiKey = env('DEEPSEEK_API_KEY');
    }

    /**
     * Analisa os dados de um produto
     */
    public function analisar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string|max:1000',
            'ncm_sh' => 'nullable|string|max:20',
            'cest' => 'nullable|string|max:10',
            'item' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $prompt = $this->buildPrompt($data);

        Log::info('Solicitação de análise tributária', ['data' => $data]);

        // Tenta primeiro com DeepSeek
        $result = $this->tryDeepSeek($prompt);

        // Se DeepSeek falhar, tenta com Gemini via HTTP direto
        if (!$result['success']) {
            Log::warning('DeepSeek falhou, tentando Gemini', ['error' => $result['error']]);
            $result = $this->tryGeminiDirect($prompt);
        }

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'source' => $result['source']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Não foi possível processar a solicitação no momento',
            'details' => $result['error'],
            'fallback_suggestion' => $this->getFallbackSuggestion($data)
        ], 500);
    }

    /**
     * Constroi o prompt para análise tributária
     */
    private function buildPrompt(array $data): string
    {
        return "
            Você é um especialista em tributação brasileira e classificação fiscal de mercadorias.

            ANALISE OS SEGUINTES DADOS:

            Descrição: {$data['descricao']}
            NCM/SH: {$data['ncm_sh']}
            CEST: {$data['cest']}
            Item: {$data['item']}

            SUAS TAREFAS:
            1. Verificar se NCM, CEST e Descrição são consistentes entre si
            2. Sugerir a classificação tributária correta se houver inconsistências
            3. Fornecer os tributos federais aplicáveis

            RETORNE APENAS JSON COM ESTA ESTRUTURA:

            {
                \"analise_consistencia\": {
                    \"consistente\": true,
                    \"observacoes\": \"Análise de consistência entre os campos\"
                },
                \"classificacao_sugerida\": {
                    \"descricao\": \"Descrição corrigida se necessário\",
                    \"ncm_sh\": \"Código NCM correto de 8 dígitos\",
                    \"cest\": \"Código CEST correto\",
                    \"icms\": {\"cst\": \"00\", \"aliquota\": 12},
                    \"ipi\": {\"cst\": \"50\", \"aliquota\": 0},
                    \"pis\": {\"cst\": \"01\", \"aliquota\": 1.65},
                    \"cofins\": {\"cst\": \"01\", \"aliquota\": 7.6},
                    \"base_legal\": \"Fundamentação legal baseada na legislação tributária brasileira\"
                }
            }

            IMPORTANTE: Retorne SOMENTE o JSON, sem texto adicional.
        ";
    }

    /**
     * Gemini via HTTP direto (mais confiável)
     */
    private function tryGeminiDirect(string $prompt): array
    {
        try {
            $apiKey = env('GEMINI_API_KEY');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'maxOutputTokens' => 2000,
                    ]
                ]);

            Log::info('Resposta Gemini HTTP', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $data['candidates'][0]['content']['parts'][0]['text'];
                    $jsonResponse = $this->extractJson($content);

                    if ($jsonResponse !== null) {
                        return [
                            'success' => true,
                            'data' => json_decode($jsonResponse, true),
                            'source' => 'gemini'
                        ];
                    }
                }
            }

            return [
                'success' => false,
                'error' => 'Gemini: ' . $response->status() . ' - ' . substr($response->body(), 0, 100)
            ];

        } catch (\Exception $e) {
            Log::error('Erro Gemini Direct', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Gemini: ' . $e->getMessage()
            ];
        }
    }

    /**
     * DeepSeek API
     */
    private function tryDeepSeek(string $prompt): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->deepSeekApiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->deepSeekBaseUrl . '/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 2000,
                    'stream' => false
                ]);

            Log::info('Resposta DeepSeek', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['choices'][0]['message']['content'])) {
                    $content = $data['choices'][0]['message']['content'];
                    $jsonResponse = $this->extractJson($content);

                    if ($jsonResponse !== null) {
                        return [
                            'success' => true,
                            'data' => json_decode($jsonResponse, true),
                            'source' => 'deepseek'
                        ];
                    }
                }
            }

            $errorBody = $response->body();
            return [
                'success' => false,
                'error' => 'DeepSeek: ' . $response->status() . ' - ' . (strlen($errorBody) > 100 ? substr($errorBody, 0, 100) . '...' : $errorBody)
            ];

        } catch (\Exception $e) {
            Log::error('Erro DeepSeek', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'DeepSeek: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Extrai JSON do texto
     */
    private function extractJson(string $text): ?string
    {
        $text = trim($text);

        // Remove markdown code blocks
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);

        // Tenta encontrar JSON
        $jsonStart = strpos($text, '{');
        $jsonEnd = strrpos($text, '}');

        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);

            // Valida se é JSON válido
            json_decode($jsonString);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonString;
            }
        }

        return null;
    }

    /**
     * Sugestão de fallback
     */
    private function getFallbackSuggestion(array $data): array
    {
        return [
            'sugestao_manual' => 'Classificação manual necessária',
            'acoes_recomendadas' => [
                'Consultar a Tabela TIPI para NCM correto',
                'Verificar compatibilidade entre CEST e NCM',
                'Validar descrição do produto conforme legislação'
            ],
            'recursos' => [
                'Tabela TIPI' => 'http://www.planalto.gov.br/ccivil_03/_ato2007-2010/2009/decreto/d6950.htm',
                'Consulta NCM' => 'http://www4.receita.fazenda.gov.br/simulador/PesquisarNCM.jsp',
                'Tabela CEST' => 'https://www.confaz.fazenda.gov.br/legislacao/ajustes/2015/ac007_15'
            ]
        ];
    }

    /**
     * Teste das APIs
     */
    public function testarApis()
    {
        $testPrompt = "Responda APENAS com este JSON: {\"status\": \"ok\", \"mensagem\": \"API funcionando\"}";

        $results = [
            'deepseek' => $this->tryDeepSeek($testPrompt),
            'gemini' => $this->tryGeminiDirect($testPrompt),
            'ambiente' => [
                'deepseek_key' => $this->deepSeekApiKey ? '***' . substr($this->deepSeekApiKey, -4) : 'Não configurada',
                'gemini_key' => env('GEMINI_API_KEY') ? '***' . substr(env('GEMINI_API_KEY'), -4) : 'Não configurada',
            ]
        ];

        return response()->json($results);
    }

    /**
     * Teste simples com dados de exemplo
     */
    public function testeSimples()
    {
        $exemploData = [
            'descricao' => 'Parafuso de aço para construção civil',
            'ncm_sh' => '7318.15.00',
            'cest' => '28.038.00',
            'item' => '001'
        ];

        $request = new Request($exemploData);
        return $this->analisar($request);
    }

    /**
     * Teste de diagnóstico com cURL direto via shell
     */
    public function testeGeminiComCurl()
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'GEMINI_API_KEY não está configurada no .env'], 500);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";
        $data = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Responda apenas com a palavra "OK"']
                    ]
                ]
            ]
        ]);

        // Escapa as aspas para o shell
        $escapedData = escapeshellarg($data);

        // Constrói o comando cURL
        $command = "curl -X POST -H \"Content-Type: application/json\" -d {$escapedData} \"{$url}\"";

        // Executa o comando
        $result = shell_exec($command);

        // Retorna o resultado bruto
        return response()->json([
            'command' => $command,
            'result_raw' => $result,
            'result_decoded' => json_decode($result, true)
        ]);
    }
}
