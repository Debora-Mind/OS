<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrdemModel;
use CodeIgniter\HTTP\ResponseInterface;

class Ordens extends BaseController
{
    private $ordemModel;

    public function __construct()
    {
        $this->ordemModel = new OrdemModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as ordens de serviços',
        ];

        return view('Ordens/index', $data);
    }

    public function recuperaOrdens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $ordens = $this->ordemModel->recuperOrdens();
        
        $data = [];

        foreach ($ordens as $ordem) {

            $ordemCodigo = esc($ordem->codigo);

            $data[] = [
                'codigo' => anchor("ordens/exibir/$ordemCodigo", $ordemCodigo, "title='Exibir ordem $ordemCodigo'"),
                'cliente' => esc($ordem->nome),
                'cpf' => $this->ordemModel->formatarCPF(esc($ordem->cpf)),
                'created_at' => esc($ordem->created_at->humanize()),
                'situacao' => $ordem->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

}
