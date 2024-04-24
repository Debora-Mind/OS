<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrdemModel;
use App\Models\TransacaoModel;
use App\Traits\OrdemTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Ordens extends BaseController
{
    use OrdemTrait;
    private $ordemModel;
    private $transacaoModel;

    public function __construct()
    {
        $this->ordemModel = new OrdemModel();
        $this->transacaoModel = new TransacaoModel();
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
                'codigo' => anchor("ordens/detalhes/$ordemCodigo", $ordemCodigo, "title='Exibir ordem $ordemCodigo'"),
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

    public function detalhes(string $codigo = null)
    {
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $ordem = $this->preparaItensDaOrdem($ordem);

        $transacao = $this->transacaoModel->where('ordem_id', $ordem->id)->first();

        if ($transacao !== null) {
            $ordem->transacao = $transacao;
        }

        $data = [
            'titulo' => "Detalhando a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/detalhes', $data);
    }
}
