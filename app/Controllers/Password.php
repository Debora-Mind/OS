<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci a minha senha'
        ];

        return view('Password/esqueci', $data);
    }
}
