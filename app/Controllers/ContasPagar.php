<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContaPagarModel;
use App\Models\FornecedorModel;
use CodeIgniter\HTTP\ResponseInterface;

class ContasPagar extends BaseController
{
    private $contaPagarModel;
    private $forncedorModel;

    public function __construct()
    {
        $this->contaPagarModel = new ContaPagarModel();
        $this->forncedorModel = new FornecedorModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as contas'
        ];

        return view('ContasPagar/index', $data);
    }

    public function recuperaContas()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $contas = $this->contaPagarModel->recuperaContasPagar();

        $data = [];

        foreach ($contas as $conta) {
            $data[] = [
                'razao' => anchor("contas/exibir/$conta->id", esc($conta->razao) . ' - CNPJ ' . $conta->cnpj, "title='Exibir a conta $conta->razao'"),
                'valor_conta' => 'R$ ' . esc(number_format($conta->valor_conta, 2, ',', '.') ),
                'situacao' => $conta->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

}