<?php

namespace App\Libraries;

use App\Models\UsuarioModel;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function login(string $email, string $password): bool
    {
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario === null) {
            return false;
        }

        // Verifica se a senha é válida
        if ($usuario->verificaPassword == false) {
            return false;
        }

        if ($usuario->ativo == false) {
            return false;
        }

        $this->logaUsuario($usuario);

        return true;
    }

    private function logaUsuario(object $usuario):void
    {
        $session = session();
        $session->regenerate();

        $session->set('usuario_id', $usuario->id);
    }
}