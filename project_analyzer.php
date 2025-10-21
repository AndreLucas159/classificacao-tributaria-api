<?php
// project_analyzer.php

class ProjectAnalyzer {
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function analyzeFile($filepath, $prompt) {
        if (!file_exists($filepath)) {
            return "Arquivo não encontrado: $filepath";
        }

        $content = file_get_contents($filepath);
        $fullPrompt = "Analise este arquivo PHP/Laravel:\n\n$prompt\n\n=== CÓDIGO ===\n$content";

        return $this->callDeepSeek($fullPrompt);
    }

    public function analyzeProjectStructure() {
        $structure = [
            'Controllers' => glob('app/Http/Controllers/*.php'),
            'Models' => glob('app/Models/*.php'),
            'Routes' => glob('routes/*.php'),
            'Config' => glob('config/*.php'),
        ];

        $analysis = "=== ESTRUTURA DO PROJETO ===\n";
        foreach ($structure as $type => $files) {
            $analysis .= "$type: " . count($files) . " arquivos\n";
        }

        $prompt = "Com base nesta estrutura, analise a organização do projeto Laravel e sugira melhorias:\n$analysis";

        return $this->callDeepSeek($prompt);
    }

    private function callDeepSeek($prompt) {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post('https://api.deepseek.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'deepseek-chat',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.1,
                    'max_tokens' => 3000
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'];

        } catch (\Exception $e) {
            return "Erro na API: " . $e->getMessage();
        }
    }
}

// Uso
$analyzer = new ProjectAnalyzer('sk-dfd26cbf71de47e7a20e32917d08c168');

echo "🔍 ANALISADOR DE PROJETO LARAVEL\n";
echo "================================\n\n";

// 1. Análise do Controller Principal
echo "1. ANALISANDO CONTROLLER PRINCIPAL...\n";
$result1 = $analyzer->analyzeFile(
    'app/Http/Controllers/Api/ClassificacaoTributariaController.php',
    "Analise este controller considerando:\n- Arquitetura MVC\n- Segurança\n- Performance\n- Tratamento de erros\n- Integração com APIs externas\n- Boas práticas Laravel"
);
echo $result1 . "\n\n";

// 2. Análise da Estrutura
echo "2. ANALISANDO ESTRUTURA DO PROJETO...\n";
$result2 = $analyzer->analyzeProjectStructure();
echo $result2 . "\n\n";

// 3. Análise do Model (se existir)
if (file_exists('app/Models/Produto.php')) {
    echo "3. ANALISANDO MODEL PRODUTO...\n";
    $result3 = $analyzer->analyzeFile(
        'app/Models/Produto.php',
        "Analise este Eloquent Model considerando:\n- Relacionamentos\n- Fillable/guarded\n- Scopes\n- Boas práticas"
    );
    echo $result3 . "\n\n";
}
