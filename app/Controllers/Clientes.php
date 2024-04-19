<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
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

    public function exibir(int $id = null)
    {
        $cliente = $this->buscaClienteOu404($id);
        $cliente->cpf = $cliente->formatarCPF();
        $cliente->telefone = $cliente->formatarTelefone();

        $data = [
            'titulo' => 'Detalhando o cliente' . esc($cliente->nome),
            'cliente' => $cliente
        ];

        return view('Clientes/exibir', $data);
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

    private function buscaClienteOu404(int $id = null)
    {
        if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o cliente $id");
        }

        return $cliente;
    }

}
