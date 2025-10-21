<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TesteApiController extends Controller
{
    /**
     * Exibe a view de teste da API.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('teste-api');
    }
}