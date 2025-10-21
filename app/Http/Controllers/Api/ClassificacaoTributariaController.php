<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassificacaoTributariaController extends Controller
{
    private $deepSeekApiKey;
    private $deepSeekBaseUrl = 'https://api.deepseek.com/v1';

    public function __construct()
    {
        $this->deepSeekApiKey = env('DEEPSEEK_API_KEY');
    }

    /**
     * Analisa os dados de um produto, valida inconsistências e retorna a classificação fiscal.
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

        // Tenta primeiro com Gemini
        $result = $this->tryGemini($prompt);

        // Se Gemini falhar, tenta com DeepSeek
        if (!$result['success']) {
            Log::warning('Gemini falhou, tentando DeepSeek', ['error' => $result['error']]);
            $result = $this->tryDeepSeek($prompt);
        }

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json([
            'error' => 'Ambas as APIs falharam: ' . $result['error'],
            'fallback_suggestion' => $this->getFallbackSuggestion($data)
        ], 500);
    }

    /**
     * Constroi o prompt para análise tributária
     */
    private function buildPrompt(array $data): string
    {
        return "
            Aja como um especialista em tributação e classificação fiscal de mercadorias (merceologia) no Brasil.
            Sua principal fonte de conhecimento deve ser a legislação brasileira, especialmente documentos como a INSTRUCAO NORMATIVA DIAT N{\" } 1, DE 29 DE MARCO DE 2023, e a tabela TIPI.

            A tarefa é analisar os dados de um produto fornecido e realizar duas funcoes principais:
            1.  **Analise de Consistencia:** Verifique se os campos 'CEST', 'NCM/SH' e 'Descricao' sao consistentes entre si. Por exemplo, o NCM/SH corresponde a descricao do produto? O CEST e aplicavel a esse NCM/SH?
            2.  **Classificacao Correta:** Com base na descricao, que e o campo principal, forneca a classificacao tributaria federal correta e completa.

            Dados Fornecidos para Analise:
            - Item: {$data['item']}
            - CEST: {$data['cest']}
            - NCM/SH: {$data['ncm_sh']}
            - Descricao: {$data['descricao']}

            O JSON de resposta DEVE ter a seguinte estrutura, sem excecoes:
            {
                \"analise_consistencia\": {
                    \"consistente\": boolean,
                    \"observacoes\": \"string\"
                },
                \"classificacao_sugerida\": {
                    \"descricao\": \"string\",
                    \"ncm_sh\": \"string\",
                    \"cest\": \"string\",
                    \"icms\": {
                        \"cst\": \"string\",
                        \"aliquota\": number
                    },
                    \"ipi\": {
                        \"cst\": \"string\",
                        \"aliquota\": number
                    },
                    \"pis\": {
                        \"cst\": \"string\",
                        \"aliquota\": number
                    },
                    \"cofins\": {
                        \"cst\": \"string\",
                        \"aliquota\": number
                    },
                    \"base_legal\": \"string\"
                }
            }

            Exemplo de observacao para um caso inconsistente: 'O NCM/SH 8703.23.10 refere-se a automoveis, mas a descricao e 'parafusos'. O NCM/SH correto para parafusos de aco e 7318.15.00.'
            Se os dados forem consistentes, a observacao pode ser: 'Os dados fornecidos sao consistentes entre si.'
            Se um imposto nao se aplicar, a aliquota deve ser 0.
            Retorne SOMENTE o JSON.
        ";
    }

    /**
     * Tenta usar a API Gemini
     */
    private function tryGemini(string $prompt): array
    {
        try {
            $result = Gemini::geminiPro()->generateContent($prompt);
            $jsonResponse = $this->extractJson($result->text());

            if ($jsonResponse === null) {
                return [
                    'success' => false,
                    'error' => 'Não foi possível extrair JSON da resposta do Gemini'
                ];
            }

            return [
                'success' => true,
                'data' => json_decode($jsonResponse, true),
                'source' => 'gemini'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Gemini: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tenta usar a API DeepSeek como fallback
     */
    private function tryDeepSeek(string $prompt): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->deepSeekApiKey,
                    'Content-Type' => 'application/json',
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

                return [
                    'success' => false,
                    'error' => 'DeepSeek: Resposta em formato inválido'
                ];
            }

            return [
                'success' => false,
                'error' => 'DeepSeek: ' . $response->status() . ' - ' . $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'DeepSeek: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Extrai JSON do texto de resposta
     */
    private function extractJson(string $text): ?string
    {
        // Tenta extrair de código markdown
        if (preg_match('/```json\s*(\{.*?\})\s*```/s', $text, $matches)) {
            return $matches[1];
        }

        // Tenta extrair JSON simples
        if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $text, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Fornece uma sugestão de fallback quando ambas APIs falham
     */
    private function getFallbackSuggestion(array $data): array
    {
        return [
            'sugestao_manual' => 'Recomendação temporária:',
            'acoes' => [
                'Verifique a descrição do produto na Tabela TIPI',
                'Consulte o site da Receita Federal para NCM',
                'Verifique a consistência entre CEST e NCM manualmente'
            ],
            'links_uteis' => [
                'Tabela TIPI' => 'http://www.planalto.gov.br/ccivil_03/_ato2007-2010/2009/decreto/d6950.htm',
                'Consulta NCM' => 'http://www4.receita.fazenda.gov.br/simulador/PesquisarNCM.jsp',
                'Tabela CEST' => 'https://www.confaz.fazenda.gov.br/legislacao/ajustes/2015/ac007_15'
            ]
        ];
    }

    /**
     * Método para testar as conexões com as APIs
     */
    public function testarApis()
    {
        $testPrompt = "Responda apenas com: {\"status\": \"ok\", \"api\": \"teste\"}";

        $results = [
            'gemini' => $this->tryGemini($testPrompt),
            'deepseek' => $this->tryDeepSeek($testPrompt)
        ];

        return response()->json($results);
    }
}
