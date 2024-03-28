<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;
use function PHPUnit\Framework\throwException;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem'
        ];

        $usuarios = $this->usuarioModel->select($atributos)
            ->findAll();

        $data = [];

        foreach ($usuarios as $usuario) {
            $nomeUsuario = esc($usuario->nome);

            $data[] = [
                'imagem' => $usuario->imagem,
                'nome' => anchor("usuarios/exibir/$usuario->id", $nomeUsuario, "title='Exibir usuário $nomeUsuario'"),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo' ),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $data= [
            'titulo' => 'Detalhando o usuário' . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/exibir', $data);
    }

    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }
}
