<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\OrdemModel;
use App\Models\TransacaoModel;
use App\Traits\OrdemTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Ordens extends BaseController
{
    use OrdemTrait;
    private $ordemModel;
    private $transacaoModel;
    private $clienteModel;

    public function __construct()
    {
        $this->ordemModel = new OrdemModel();
        $this->transacaoModel = new TransacaoModel();
        $this->clienteModel = new ClienteModel();
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

    public function editar(string $codigo = null)
    {
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        if ($ordem == 'encerrada'){
            return
                redirect()
                ->back()
                ->with('info', "Essa ordem não pode ser editada, pois encontra-se " .
                    ucfirst($ordem->situacao) . '.');
        }

        $data = [
            'titulo' => "Editando a ordem de serviço $ordem->codigo",
            'ordem' => $ordem,
        ];

        return view('Ordens/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        if ($ordem == 'encerrada'){

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['situacao' => "Essa ordem não pode ser editada, pois encontra-se " .
                ucfirst($ordem->situacao) . '.'];

            return $this->response->setJSON($retorno);
        }

        $ordem->fill($post);

        if (!$ordem->hasChanged()) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->ordemModel->protect(false)->save($ordem)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function buscaClientes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(nome, " CPF: ", cpf) AS cliente',
            'cpf'
        ];

        $termo = $this->request->getGet('termo');

        $clientes = $this->clienteModel
            ->select($atributos)
            ->asArray()
            ->like('nome', $termo)
            ->orLike('cpf', $termo)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return $this->response->setJSON($clientes);
    }

}
