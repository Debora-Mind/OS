<?php

namespace App\Libraries;

use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;

class Autenticacao
{
    private $usuario;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
    }

    public function login(string $email, string $password): bool
    {
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario === null) {
            return false;
        }

        // Verifica se a senha é válida
        if (!$usuario->verificaPassword($password)) {
            return false;
        }

        if (!$usuario->ativo) {
            return false;
        }

        $this->logaUsuario($usuario);

        return true;
    }

    private function logaUsuario(object $usuario): void
    {
        $session = session();
        $session->regenerate();

        $session->set('usuario_id', $usuario->id);
    }

    public function logout(): void
    {
        session()->destroy();
    }

    public function estaLogado()
    {
        return $this->pegaUsuarioLogado() !== null;
    }

    public function pegaUsuarioLogado()
    {
        if ($this->usuario === null) {
            $this->usuario = $this->pegaUsuarioDaSessao();
        }

        return $this->usuario;
    }

    private function pegaUsuarioDaSessao()
    {
        if (!session()->has('usuario_id')) {
            return null;
        }

        $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

        if ($usuario == null || !$usuario->ativo) {
            return null;
        }

        $usuario = $this->definePermissoesDoUsuarioLogado($usuario);

        return $usuario;
    }

    private function definePermissoesDoUsuarioLogado(object $usuario): object
    {
        $usuario->is_admin = $this->isAdmin();

        if ($usuario->is_admin) {
            $usuario->is_cliente = false;
        } else {
            $usuario->is_cliente = $this->isCliente();
        }

        if (!$usuario->is_admin && !$usuario->is_cliente) {
            $usuario->permissoes = $this->recuperaPermissoesDoUsuarioLogado();
        }

        return $usuario;

    }

    public function isAdmin(): bool
    {
        //ID Grupo Administrador = 1
        $grupoAdmin = 1;
        $usuarioId = session()->get('usuario_id');

        $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, $usuarioId);

        if ($administrador == null) {
            return false;
        }

        return true;
    }

    public function isCliente(): bool
    {
        //ID Grupo Cliente = 2
        $grupoCliente = 2;
        $usuarioId = session()->get('usuario_id');

        $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, $usuarioId);

        if ($cliente == null) {
            return false;
        }

        return true;
    }

    private function recuperaPermissoesDoUsuarioLogado(): array
    {
        $permissoesDoUsuario = $this->usuarioModel->recuperaPermissoesDoUsuarioLogado(session()->get('usuario_id'));

        return array_column($permissoesDoUsuario, 'permissao');
    }
}

