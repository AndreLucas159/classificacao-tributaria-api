<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClassificacaoTributariaController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/analisar-produto', [ClassificacaoTributariaController::class, 'analisar'])->name('api.produto.analisar');
Route::get('/testar-apis', [ClassificacaoTributariaController::class, 'testarApis'])->name('api.testar');
Route::get('/teste-curl-direto', [ClassificacaoTributariaController::class, 'testeGeminiComCurl'])->name('api.teste.curl');

