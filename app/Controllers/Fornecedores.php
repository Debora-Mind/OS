<?php

namespace App\Controllers;

use App\Models\FornecedorModel;

class Fornecedores extends BaseController
{
    private $fornecedorModel;

    public function __construct()
    {
        $this->fornecedorModel = new FornecedorModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Fornecedores'
        ];

        return view('Fornecedores/index', $data);
    }

    public function recuperaFornecedores()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'razao',
            'cnpj',
            'telefone',
            'ativo',
            'deleted_at',
        ];

        $fornecedores = $this->fornecedorModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($fornecedores as $fornecedor) {

            $razaoFornecedor = esc($fornecedor->razao);

            $data[] = [
                'razao' => anchor("fornecedores/exibir/$fornecedor->id", $razaoFornecedor, "title='Exibir fornecedor $razaoFornecedor'"),
                'cnpj' => esc($fornecedor->formatarCNPJ()),
                'telefone' => esc($fornecedor->formatarTelefone()),
                'ativo' => $fornecedor->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

}
