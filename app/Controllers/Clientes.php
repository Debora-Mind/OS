<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Traits\ValidacoesTrait;
use CodeIgniter\HTTP\ResponseInterface;

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

    public function editar(int $id = null)
    {
        $cliente = $this->buscaClienteOu404($id);

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
                <br>Importante: informe ao cliente o novo e-mail de acesso ao sistema: 
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

    private function buscaClienteOu404(int $id = null)
    {
        if (!$id || !$cliente = $this->clienteModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o cliente $id");
        }

        return $cliente;
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

}
