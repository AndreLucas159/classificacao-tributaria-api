<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesteApiController;
use App\Http\Controllers\Api\ClassificacaoTributariaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/teste-api', [TesteApiController::class, 'index'])->name('teste-api');
Route::post('/analisar-produto', [ClassificacaoTributariaController::class, 'analisar'])->name('api.produto.analisar');
Route::get('/testar-apis', [ClassificacaoTributariaController::class, 'testarApis'])->name('api.testar');


// Route::prefix('tributacao')->group(function () {
//     Route::post('/analisar', [ClassificacaoTributariaController::class, 'analisar']);
//     Route::get('/teste-apis', [ClassificacaoTributariaController::class, 'testarApis']);
//     Route::get('/teste-simples', [ClassificacaoTributariaController::class, 'testeSimples']);
// });
