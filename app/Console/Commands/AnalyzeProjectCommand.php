<?php
// app/Console/Commands/AnalyzeProjectCommand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AnalyzeProjectCommand extends Command
{
    protected $signature = 'project:analyze
                            {--controller= : Controller específico para analisar}
                            {--all : Analisar projeto completo}';

    protected $description = 'Analisa o projeto usando DeepSeek API';

    public function handle()
    {
        $apiKey = env('DEEPSEEK_API_KEY');

        if ($this->option('all')) {
            $this->analyzeFullProject($apiKey);
        } elseif ($this->option('controller')) {
            $this->analyzeController($apiKey, $this->option('controller'));
        } else {
            $this->analyzeMainController($apiKey);
        }
    }

    private function analyzeMainController($apiKey)
    {
        $controllerPath = app_path('Http/Controllers/Api/ClassificacaoTributariaController.php');
        $content = file_get_contents($controllerPath);

        $prompt = "Analise este controller Laravel profundamente:\n\n{$content}\n\n" .
                 "Foque em: segurança, performance, arquitetura, tratamento de erros, e sugestões de melhoria.";

        $this->callDeepSeek($apiKey, $prompt, "Análise do Controller Principal");
    }

    private function callDeepSeek($apiKey, $prompt, $title)
    {
        $this->info("🔍 {$title}...");

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.deepseek.com/v1/chat/completions', [
                    'model' => 'deepseek-chat',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.1,
                    'max_tokens' => 4000
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $analysis = $result['choices'][0]['message']['content'];

                $this->line("\n📋 RESULTADO:");
                $this->line(str_repeat('─', 80));
                $this->line($analysis);
                $this->line(str_repeat('─', 80));

                // Salva em arquivo
                $filename = storage_path('logs/analysis_' . date('Y-m-d_H-i-s') . '.txt');
                file_put_contents($filename, $analysis);
                $this->info("\n💾 Análise salva em: " . $filename);

            } else {
                $this->error('Erro na API: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
        }
    }
}
