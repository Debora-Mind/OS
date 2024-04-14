<?php

namespace App\Controllers;

use App\Entities\Fornecedor;
use App\Models\FornecedorModel;
use App\Traits\ValidacoesTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

class Fornecedores extends BaseController
{
    use ValidacoesTrait;

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

    public function criar()
    {
        $fornecedor = new Fornecedor();

        $data = [
            'titulo' => 'Cadastrar novo fornecedor',
            'fornecedor' => $fornecedor
        ];

        return view('Fornecedores/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $fornecedor = new Fornecedor($post);
        $fornecedor->removeFormatacao();

        if ($this->fornecedorModel->save($fornecedor)) {
            $btnCriar = anchor("fornecedores/criar", "Cadastrar fornecedor", ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->fornecedorModel->getInsertID();
            return $this->response->setJSON($retorno);
        }
        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $fornecedor = $this->buscaFornecedorOu404($id);
        $fornecedor->cnpj = $fornecedor->formatarCNPJ();
        $fornecedor->telefone = $fornecedor->formatarTelefone();

        $data = [
            'titulo' => 'Detalhando o fornecedor' . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        return view('Fornecedores/exibir', $data);
    }

    private function buscaFornecedorOu404(int $id = null)
    {
        if (!$id || !$fornecedor = $this->fornecedorModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o fornecedor $id");
        }

        return $fornecedor;
    }

    public function editar(int $id = null)
    {
        $fornecedor = $this->buscaFornecedorOu404($id);

        $data = [
            'titulo' => 'Editando o fornecedor ' . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        return view('Fornecedores/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $fornecedor = $this->buscaFornecedorOu404($post['id']);

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido'];

            return $this->response->setJSON($retorno);
        }

        $fornecedor->fill($post);
        $fornecedor->removeFormatacao();

        if (!$fornecedor->hasChanged()) {
            $retorno['info'] = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }

        if ($this->fornecedorModel->save($fornecedor)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->fornecedorModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $fornecedor = $this->buscaFornecedorOu404($id);

        if ($fornecedor->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse fornecedor já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {
            $fornecedor->ativo = false;
            $this->fornecedorModel->protect(false)->save($fornecedor);

            $this->fornecedorModel->delete($fornecedor->id);

            return redirect()->to(site_url("fornecedores"))
                ->with('sucesso', "Fornecedor $fornecedor->razao excluído com sucesso!");
        }

        $data = [
            'titulo' => 'Excluindo o fornecedor' . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        return view('Fornecedores/excluir', $data);
    }

    public function restaurar(int $id = null)
    {
        $fornecedor = $this->buscaFornecedorOu404($id);

        if ($fornecedor->deleted_at == null) {
            return redirect()->back()->with('info', 'Apenas fornecedores excluídos podem ser recuperados');
        }

        $fornecedor->deleted_at = null;

        $this->fornecedorModel->protect(false)->save($fornecedor);

        return redirect()->back()->with('sucesso', "Fornecedor " . esc($fornecedor->razao) . " recuperado com sucesso!");
    }

    public function consultaCep()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));
    }

}
