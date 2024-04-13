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

        if ($usuario === null) {
            return redirect()->to(site_url('password/esqueci'))
                ->with('atencao', 'Link inválido ou expirado');
        }

        $data = [
            'titulo' => 'Crie a sua nova senha de acesso.',
            'token' => $token,
        ];

        return view('Password/reset', $data);
    }

    public function processaReset()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $usuario = $this->usuarioModel->buscaUsuarioPorToken($post['token']);

        if ($usuario === null) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['link_invalido' => 'Link inválido ou expirado'];

            return $this->response->setJSON($retorno);
        }

        $usuario->fill($post);

        $usuario->finalizaPasswordReset();

        if ($this->usuarioModel->save($usuario)) {
            session()->setFlashdata('sucesso', 'Nova senha criada com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
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
