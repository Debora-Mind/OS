<?php

namespace App\Controllers;

use App\Entities\Fornecedor;
use App\Models\FornecedorModel;
use App\Models\FornecedorNotaFiscalModel;
use App\Traits\ValidacoesTrait;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;

class Fornecedores extends BaseController
{
    use ValidacoesTrait;

    private $fornecedorModel;
    private $fornecedorNotaFiscalModel;

    public function __construct()
    {
        $this->fornecedorModel = new FornecedorModel();
        $this->fornecedorNotaFiscalModel = new FornecedorNotaFiscalModel();
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
            'titulo' => 'Detalhando o fornecedor ' . esc($fornecedor->razao),
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

    public function notas(int $id = null)
    {
        $fornecedor = $this->buscaFornecedorOu404($id);
        $fornecedor->cnpj = $fornecedor->formatarCNPJ();
        $fornecedor->telefone = $fornecedor->formatarTelefone();

        $fornecedor->notas_fiscais = $this->fornecedorNotaFiscalModel->where('fornecedor_id', $fornecedor->id)->paginate(10);

        if ($fornecedor->notas_fiscais != null) {
            $fornecedor->pager = $this->fornecedorNotaFiscalModel->pager;
        }

        $data = [
            'titulo' => 'Gerenciando as notas fiscais do fornecedor ' . esc($fornecedor->razao),
            'fornecedor' => $fornecedor
        ];

        return view('Fornecedores/notas_fiscais', $data);
    }

    public function cadastrarNotaFiscal()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $valorNota = str_replace([',', '.'], '', $post['valor_nota']);

        if ($valorNota < 1) {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['valor_nota' => 'O valor da nota deve ser maior que zero'];
            return $this->response->setJSON($retorno);
        }

        $validacao = $this->validacoes();

        if (!$validacao->withRequest($this->request)->run()) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $fornecedor = $this->buscaFornecedorOu404($post['id']);

        $notaFiscal = $this->request->getFile('nota_fiscal');
        $notaFiscalCaminho = $notaFiscal->store('fornecedores/notasfiscais');

        $nota = [
            'fornecedor_id' => $fornecedor->id,
            'nota_fiscal' => $notaFiscal->getName(),
            'descricao_itens' => $post['descricao_itens'],
            'valor_nota' => str_replace(',', '.', str_replace('.', '', $post['valor_nota'])),
            'data_emissao' => $post['data_emissao'],
            'created_at' => Time::now(),
        ];

        $this->fornecedorNotaFiscalModel->insert($nota);

        session()->setFlashdata('sucesso', 'Nota fiscal cadastrada com sucesso!');

        return $this->response->setJSON($retorno);
    }

    private function validacoes(): ?object
    {
        $validacao = service('validation');

        $regras = [
            'valor_nota' => 'required',
            'data_emissao' => 'required',
            'nota_fiscal' => 'uploaded[nota_fiscal]|max_size[nota_fiscal,5120]|ext_in[nota_fiscal,pdf]',
            'descricao_itens' => 'required',
        ];

        $mensagens = [
            'valor_nota' => [
                'required' => 'Por favor insira o Valor da nota fiscal'
            ],
            'data_emissao' => [
                'required' => 'Por favor insira a Data de emissão da nota'
            ],
            'nota_fiscal' => [
                'max_size' => 'Por favor selecione uma nota fiscal de no máximo 5MB',
                'uploaded' => 'Por favor escolha uma nota fiscal',
                'ext_in' => 'Por favor escolha uma nota fiscal que seja em PDF',
            ],
            'descricao_itens' => [
                'required' => 'Por favor insira uma Breve descrição dos itens da nota'
            ],
        ];

        $validacao->setRules($regras, $mensagens);

        return $validacao;
    }

    public function exibirNota(string $nota = null)
    {
        if ($nota === null) {
            return redirect()->to(site_url('fornecedores'))->with('atencao', "Mão encontramos a nota fiscal $nota");
        }

        $this->exibeArquivo('fornecedores/notasfiscais', $nota);
    }

    public function removeNota(string $notaFiscal = null)
    {
        if ($this->request->getMethod() === 'post') {
            $objetNota = $this->buscaNotaFiscalOu404($notaFiscal);

            $this->fornecedorNotaFiscalModel->delete($objetNota->id);

            $caminhoNotaFiscal = WRITEPATH . "uploads/fornecedores/notasfiscais/$notaFiscal";

            if (is_file($caminhoNotaFiscal)) {
                unlink($caminhoNotaFiscal);
            }

            return redirect()->back()->with("sucesso", "Nota fiscal removida com sucesso!");
        }

        return redirect()->back();
    }

    private function buscaNotaFiscalOu404(string $nota_fiscal = null)
    {
        if (!$nota_fiscal || !$objetoNota = $this->fornecedorNotaFiscalModel->where('nota_fiscal', $nota_fiscal)->first()) {
            throw PageNotFoundException::forPageNotFound("Não encontramos a nota fiscal $nota_fiscal");
        }

        return $objetoNota;
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
