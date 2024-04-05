<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo' => 'Home'
        ];

        return view('Home/index', $data);
    }

    public function login()
    {
        $autenticacao = service('autenticacao');

        $usuario = $autenticacao->pegaUsuarioLogado();

    }
}
