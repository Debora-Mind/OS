<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Ordem;
use App\Models\ClienteModel;
use App\Models\OrdemModel;
use App\Models\OrdemResponsavelModel;
use App\Models\TransacaoModel;
use App\Traits\OrdemTrait;
use CodeIgniter\HTTP\ResponseInterface;

class Ordens extends BaseController
{
    use OrdemTrait;
    private $ordemModel;
    private $transacaoModel;
    private $clienteModel;
    private $ordemReponsavelModel;

    public function __construct()
    {
        $this->ordemModel = new OrdemModel();
        $this->transacaoModel = new TransacaoModel();
        $this->clienteModel = new ClienteModel();
        $this->ordemReponsavelModel = new OrdemResponsavelModel();
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

    public function criar()
    {
        $ordem = new Ordem();

        $ordem->codigo = $this->ordemModel->geraCodigoOrdem();

        $data = [
            'titulo' => 'Cadastrando nova ordem de serviço',
            'ordem' => $ordem,
        ];

        return view('Ordens/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $ordem = new Ordem($post);

        if ($this->ordemModel->save($ordem)) {

            $this->finalizaCadastroDaOrdem($ordem);

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            $retorno['codigo'] = $ordem->codigo;

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemModel->errors();

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
            'CONCAT(nome, " CPF: ", cpf) AS nome',
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

    private function enviaOrdemEmAndamentoParaCliente(object $ordem): void
    {
        $email = service('email');

        $email->setFrom(env('email.SMTPUser'), env('email.user'));
        $email->setTo($ordem->cliente->email);

        $email->setSubject("OS | Ordem de serviço $ordem->codigo em andamento");

        $data = [
            'ordem' => $ordem
        ];

        $mensagem = view('Ordens/ordem_andamento_email', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function finalizaCadastroDaOrdem(object $ordem): void
    {
        $ordemAberta = [
            'ordem_id' => $this->ordemModel->getInsertID(),
            'usuario_abertura_id' => usuario_logado()->id
        ];

        $ordem->situacao = 'aberta';
        $ordem->created_at = date('Y-m-d H:i');

        $this->ordemReponsavelModel->insert($ordemAberta);
        $ordem->cliente = $this->clienteModel->select('nome, email')->find($ordem->cliente_id);

        $this->enviaOrdemEmAndamentoParaCliente($ordem);
    }

}
