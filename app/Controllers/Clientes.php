<?php

namespace App\Controllers;

use App\Entities\Cliente;
use App\Models\ClienteModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use App\Traits\ValidacoesTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

class Clientes extends BaseController
{
    use ValidacoesTrait;

    private $clienteModel;
    private $usuarioModel;
    private $grupoUsuarioModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os clientes do sistema',
        ];

        return view('Clientes/index', $data);
    }

    public function exibir(int $id = null)
    {
        $cliente = $this->buscaClienteOu404($id);
        $cliente->cpf = $cliente->formatarCPF();
        $cliente->telefone = $cliente->formatarTelefone();
        $cliente->cep = $cliente->formatarCep();

        $data = [
            'titulo' => 'Detalhando o cliente' . esc($cliente->nome),
            'cliente' => $cliente
        ];

        return view('Clientes/exibir', $data);
    }

    public function criar()
    {
        $cliente = new Cliente();

        $data = [
            'titulo' => 'Criando novo cliente',
            'cliente' => $cliente
        ];

        return view('Clientes/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $this->removeBlockCepEmailSessao();

        $retorno['token'] = csrf_hash();

        if (session()->get('blockEmail') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['email' => 'Informe um e-mail com domínio válido'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido'];

            return $this->response->setJSON($retorno);

        }
        $post = $this->request->getPost();
        $cliente = new Cliente($post);

        $cliente->removeFormatacao();

        if ($this->clienteModel->save($cliente)) {

            $this->enviaEmailCriacaoEmailAcesso($cliente);

            $this->criaUsuarioParaCliente($cliente);

            $btnCriar = anchor("clientes/criar", "Cadastrar novo cliente", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!<br>
                <br><b>Importante: </b>informe ao cliente os dados de acesso ao sistema: 
                <br><b>Senha inicial: </b>123456
                <br><b>E-mail: </b>' . $cliente->email . '
                <br><br>Esses mesmos dados foram enviados para o e-mail do cliente.<br>' . $btnCriar);

            $retorno['id'] = $this->clienteModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editar(int $id = null)
    {
        $cliente = $this->buscaClienteOu404($id);

        $this->removeBlockCepEmailSessao();

        $data = [
            'titulo' => 'Editando o cliente ' . esc($cliente->nome),
            'cliente' => $cliente
        ];

        return view('Clientes/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $cliente = $this->buscaClienteOu404($post['id']);

        if (session()->get('blockEmail') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['email' => 'Informe um e-mail com domínio válido'];

            return $this->response->setJSON($retorno);
        }

        if (session()->get('blockCep') === true) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['cep' => 'Informe um CEP válido'];

            return $this->response->setJSON($retorno);
        }

        $cliente->fill($post);

        $cliente->removeFormatacao();

        if (!$cliente->hasChanged()) {
            $retorno['info'] = 'Não há dados para atualizar';

            return $this->response->setJSON($retorno);
        }


        if ($this->clienteModel->save($cliente)) {

            if ($cliente->hasChanged('email')) {
                $this->usuarioModel->atualizaEmailCliente($cliente->usuario_id, $cliente->email);
                $this->enviaEmailAlteracaoEmailAcesso($cliente);

                session()->setFlashdata('sucesso', 'Dados salvos com sucesso!<br>
                <br><b>Importante: </b>informe ao cliente o novo e-mail de acesso ao sistema: 
                <p><b>E-mail: </b>' . $cliente->email . '</p>
                Um e-mail de notificação foi enviado para o cliente.');

                return $this->response->setJSON($retorno);
            }

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->clienteModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function recuperaClientes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'cpf',
            'email',
            'telefone',
            'deleted_at',
        ];

        $clientes = $this->clienteModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($clientes as $cliente) {

            $nomeCliente = esc($cliente->nome);

            $data[] = [
                'nome' => anchor("clientes/exibir/$cliente->id", $nomeCliente, "title='Exibir cliente $nomeCliente'"),
                'cpf' => esc($cliente->formatarCPF()),
                'email' => esc($cliente->email),
                'telefone' => esc($cliente->formatarTelefone()),
                'ativo' => $cliente->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function consultaCep()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cep = $this->request->getGet('cep');

        return $this->response->setJSON($this->consultaViaCep($cep));
    }

    public function consultaEmail()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $email = $this->request->getGet('email');

        return $this->response->setJSON($this->checkEmail($email, true));
    }

    private function buscaClienteOu404(int $id = null)
    {
        if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o cliente $id");
        }

        return $cliente;
    }

    private function removeBlockCepEmailSessao(): void
    {
        session()->remove('blockCep');
        session()->remove('blockEmail');
    }

    private function enviaEmailAlteracaoEmailAcesso(object $cliente)
    {
        $email = service('email');

        $email->setFrom(env('email.SMTPUser'), env('email.user'));
        $email->setTo($cliente->email);

        $email->setSubject('OS | E-mail de acesso ao sistema foi alterado');

        $data = [
            'cliente' => $cliente
        ];

        $mensagem = view('Clientes/email_acesso_alterado', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function enviaEmailCriacaoEmailAcesso(object $cliente)
    {
        $email = service('email');

        $email->setFrom(env('email.SMTPUser'), env('email.user'));
        $email->setTo($cliente->email);

        $email->setSubject('OS | Dados de acesso ao sistema');

        $data = [
            'cliente' => $cliente
        ];

        $mensagem = view('Clientes/email_dados_acesso', $data);

        $email->setMessage($mensagem);

        $email->send();
    }

    private function criaUsuarioParaCliente(object $cliente)
    {
        $usuario = [
            'nome' => $cliente->nome,
            'email' => $cliente->email,
            'password' => '123456',
            'ativo' => true,
        ];

        $this->usuarioModel->skipValidation()->protect(false)->insert($usuario);

        $grupoUsuario = [
            'grupo_id' => 2,
            'usuario_id' => $this->usuarioModel->getInsertID(),
        ];

        $this->grupoUsuarioModel->protect(false)->insert($grupoUsuario);

        $this->clienteModel
            ->protect(false)
            ->where('id', $this->clienteModel->getInsertID())
            ->set('usuario_id', $this->usuarioModel->getInsertID())
            ->update();
    }

}
