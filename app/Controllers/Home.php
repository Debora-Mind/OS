<?php

namespace App\Controllers;

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

    public function teste()
    {
        $data = [
            'titulo' => 'Teste'
        ];

        return view('teste', $data);
    }
}
