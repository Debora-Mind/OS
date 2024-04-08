<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci a minha senha'
        ];

        return view('Password/esqueci', $data);
    }

    public function processaEsqueci()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $email = $this->request->getPost('email');

        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario == null || $usuario->ativo === false) {
            $retorno['erro'] = 'Não encontramos uma conta válida com esse e-mail';
            return $this->response->setJSON($retorno);
        }

        $usuario->iniciaPasswordReset();

        $this->usuarioModel->save($usuario);

        $this->enviaEmailRedefinicaoSenha($usuario);
        
        return $this->response->setJSON([]);
    }

    public function resetEnviado()
    {
        $data = [
            'titulo' => 'E-mail de recuperação enviado para a sua caixa de entrada'
        ];

        return view('Password/reset_enviado', $data);
    }

    public function reset($token = null)
    {
        if ($token === null) {
            return redirect()->to(site_url('password/esqueci'))
                ->with('atencao', 'Link inválido ou expirado');
        }

        $usuario = $this->usuarioModel->buscaUsuarioPorToken($token);

        dd($usuario);
    }

    private  function enviaEmailRedefinicaoSenha(object $usuario): void
    {
        $email = service('email');

        $email->setFrom('debora.almeida.de.mello@gmail.com', 'Débora Almeida');
        $email->setTo('debora.almeida.de.mello@gmail.com');

        $email->setSubject('OS | Redefinição da senha de acesso');

        $data = [
            'token' => $usuario->reset_token
        ];

        $mensagem = view('Password/reset_email', $data);

        $email->setMessage($mensagem);

        $email->send();
    }
}
