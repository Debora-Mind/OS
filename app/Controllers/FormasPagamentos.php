<?php

namespace App\Controllers;

use App\Models\FormaPagamentoModel;

class FormasPagamentos extends BaseController
{
    private $formaPagamentoModel;

    public function __construct()
    {
        $this->formaPagamentoModel = new FormaPagamentoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as formas de pagamentos'
        ];

        return view('FormasPagamentos/index', $data);
    }

    public function recuperaFormasPagamentos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $formas = $this->formaPagamentoModel->findAll();

        $data = [];

        foreach ($formas as $forma) {
            $data[] = [
                'nome' => anchor("formaspagamentos/exibir/$forma->id", esc($forma->nome), "title='Exibir a forma de pagamento $forma->nome'"),
                'descricao' => esc($forma->descricao),
                'created_at' => esc($forma->created_at->humanize()),
                'situacao' => $forma->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }
}
