<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TesteApiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/teste-api', [TesteApiController::class, 'index'])->name('teste-api');
