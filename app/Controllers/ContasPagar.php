<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ContaPagarModel;
use App\Models\FornecedorModel;
use CodeIgniter\Exceptions\PageNotFoundException;
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

    public function exibir(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContasOu404($id);

        $data = [
            'titulo' => "Detalhando a conta do fornecedor $conta->razao",
            'conta' => $conta,
        ];

        return view('ContasPagar/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContasOu404($id);

        $data = [
            'titulo' => "Editando a conta do fornecedor $conta->razao",
            'conta' => $conta,
        ];

        return view('ContasPagar/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $conta = $this->contaPagarModel->buscaContasOu404($post['id']);

        $post['valor_conta'] = $this->formataValorParaDB($post['valor_conta']);

        $conta->fill($post);

        if (!$conta->hasChanged()) {
            $retorno['info'] = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }

        if ($this->contaPagarModel->save($conta)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function formataValorParaDB($valor)
    {
        return str_replace(',', '.', str_replace('.', '', $valor));
    }

}
