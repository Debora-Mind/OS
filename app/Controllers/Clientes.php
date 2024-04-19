<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Clientes extends BaseController
{
    private $clienteModel;
    private $usuarioModel;
    private $grupoUsuarioModel;
    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os clientes do sistema',
        ];

        return view('Clientes/index', $data);
    }

    public function recuperaClientes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'cpf',
            'email',
            'telefone',
            'deleted_at',
        ];

        $clientes = $this->clienteModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($clientes as $cliente) {

            $nomeCliente = esc($cliente->nome);

            $data[] = [
                'nome' => anchor("clientes/exibir/$cliente->id", $nomeCliente, "title='Exibir cliente $nomeCliente'"),
                'cpf' => esc($cliente->formatarCPF()),
                'email' => esc($cliente->email),
                'telefone' => esc($cliente->formatarTelefone()),
                'ativo' => $cliente->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

}
