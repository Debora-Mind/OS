<?php

namespace App\Controllers;

use App\Entities\FormaPagamento;
use App\Models\FormaPagamentoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class FormasPagamentos extends BaseController
{
    private $formaPagamentoModel;
    private $quantidadeGruposPadroes = 2;

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

    public function exibir(int $id = null)
    {
        $forma = $this->buscaFormaPagamentoOu404($id);

        $data = [
            'titulo' => "Detalhando a forma de pagamento $forma->nome",
            'forma' => $forma,
        ];

        return view('FormasPagamentos/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $forma = $this->buscaFormaPagamentoOu404($id);

        if ($forma->id < 3) {
            return redirect()
                ->to(site_url("formas/exibir/$forma->id"))
                ->with("info",
                "A forma de pagamento <b class='text-white'>$forma->nome</b> não pode ser editara ou excluída.");
        }

        $data = [
            'titulo' => "Editando a forma de pagamento $forma->nome",
            'forma' => $forma,
        ];

        return view('FormasPagamentos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $forma = $this->buscaFormaPagamentoOu404($post['id']);

        if ($forma->id <= $this->quantidadeGruposPadroes) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['forma' => 'A forma de pagamento <b class="text-white">' . esc($forma->nome) .
                '</b> não pode ser editada ou excluída.'];

            return $this->response->setJSON($retorno);
        }

        $forma->fill($post);

        if (!$forma->hasChanged()) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->formaPagamentoModel->save($forma)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $forma = new FormaPagamento();

        $data = [
            'titulo' => "Criando nova forma de pagamento",
            'forma' => $forma,
        ];

        return view('FormasPagamentos/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $forma = new FormaPagamento($post);

        if ($this->formaPagamentoModel->insert($forma)) {

            $btnCriar = anchor("formaspagamentos/criar", "Cadastrar nova forma de pagamento", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->formaPagamentoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->formaPagamentoModel->errors();

        return $this->response->setJSON($retorno);
    }

    private function buscaFormaPagamentoOu404(int $id = null)
    {
        if (!$id || !$forma = $this->formaPagamentoModel->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos a forma de pagamento $id");
        }

        return $forma;
    }

}
