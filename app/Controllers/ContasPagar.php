<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\ContaPagar;
use App\Models\ContaPagarModel;
use App\Models\EventoModel;
use App\Models\FornecedorModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class ContasPagar extends BaseController
{
    private $contaPagarModel;
    private $fornecedorModel;
    private $eventoModel;

    public function __construct()
    {
        $this->contaPagarModel = new ContaPagarModel();
        $this->fornecedorModel = new FornecedorModel();
        $this->eventoModel = new EventoModel();
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

    public function criar()
    {
        $conta = new ContaPagar();

        $data = [
            'titulo' => "Criando nova conta",
            'conta' => $conta,
        ];

        return view('ContasPagar/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $post['valor_conta'] = $this->formataValorParaDB($post['valor_conta']);

        $conta = new ContaPagar($post);

        if ($this->contaPagarModel->save($conta)) {

            if ($conta->situacao == 0){
                $this->cadastraEventoDaConta($conta);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            $retorno['id'] = $this->contaPagarModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

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

            // TODO A FUNÇÃO ALTERA APENAS A DATA, SE O VALOR FOR ALTERADO A MENSAGEM DO EVENTO VAI FICAR INCORRETA
            if ($conta->hasChanged('data_vencimento') && $conta->situacao == 0){
                $dias = $conta->defineDataVencimentoEvento();
                $this->eventoModel->atualizaEvento('conta_id', $conta->id, $dias);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->contaPagarModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function excluir(int $id = null)
    {
        $conta = $this->contaPagarModel->buscaContasOu404($id);

        if ($this->request->getMethod() === 'post') {
            $this->contaPagarModel->delete($id);

            return redirect()->to(site_url("contas"))
                ->with('sucesso', "Conta do fornecedor $conta->razao excluído com sucesso!");
        }

        $data = [
            'titulo' => 'Excluindo a conta do fornecedor ' . esc($conta->razao),
            'conta' => $conta
        ];

        return view('ContasPagar/excluir', $data);
    }

    public function buscaFornecedores()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'CONCAT(razao, " CNPJ: ", cnpj) AS razao',
            'cnpj'
        ];

        $termo = $this->request->getGet('termo');

        $fornecedores = $this->fornecedorModel
            ->select($atributos)
            ->asArray()
            ->like('razao', $termo)
            ->orLike('cnpj', $termo)
            ->where('ativo', true)
            ->orderBy('razao', 'ASC')
            ->findAll();

        return $this->response->setJSON($fornecedores);
    }

    private function formataValorParaDB($valor)
    {
        return str_replace(',', '.', str_replace('.', '', $valor));
    }

    private function cadastraEventoDaConta(object $conta): void
    {
        $fornecedor = $this->fornecedorModel->select('razao, cnpj')->find($conta->fornecedor_id);

        $razao = esc($fornecedor->razao);
        $cnpj = esc($fornecedor->cnpj);
        $valorConta = "R$ " . esc(number_format($conta->valor_conta, 2, ',', '.'));
        $tituloEvento = "Conta do Fornecedor $razao - CNPJ: $cnpj | Valor $valorConta";
        $dias = $conta->defineDataVencimentoEvento();
        $contaId = $this->contaPagarModel->getInsertID();

        $this->eventoModel->cadastraEvento('conta_id', $tituloEvento, $contaId, $dias);
    }
}
