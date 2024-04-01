<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\GrupoModel;
use App\Entities\Grupo;
use CodeIgniter\HTTP\ResponseInterface;

class Grupos extends BaseController
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os grupos de acesso ao sistema',
        ];

        return view('Grupos/index', $data);
    }

    public function recuperaGrupos()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'descricao',
            'tecnico',
            'deleted_at'
        ];

        $grupos = $this->grupoModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' => anchor("grupos/exibir/$grupo->id", esc($grupo->nome), "title='Exibir grupo '" . esc($grupo->nome),),
                'descricao' => esc($grupo->descricao),
                'tecnico' => $grupo->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        $data= [
            'titulo' => 'Detalhando o grupo de acesso' . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id <= 1) {
            return redirect()->back()->with('atencao', 'O grupo ' . esc($grupo->nome) .
                ' não pode ser editado ou excluído conforme descrito na exibição do mesmo.' );
        }

        $data= [
            'titulo' => 'Editando o grupo' . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        if ($grupo->id <= 1) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo' => 'O grupo <b class="text-white">' . esc($grupo->nome) .
                '</b> não pode ser editado ou excluído conforme descrito na exibição do mesmo.'];

            return $this->response->setJSON($retorno);
        }

        $grupo->fill($post);

        if (!$grupo->hasChanged()) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }

        return $grupo;
    }

//
//
    public function criar()
    {
        $grupo = new Usuario();

        $data= [
            'titulo' => 'Criando novo usuário',
            'usuario' => $grupo
        ];

        return view('Usuarios/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = new Usuario($post);

        if ($this->grupoModel->protect(false)->insert($grupo)) {

            $btnCriar = anchor("usuarios/criar", "Cadastrar novo usuário", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }




    public function editarImagem(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        $data= [
            'titulo' => 'Alterando a imagem do usuário' . esc($grupo->nome),
            'usuario' => $grupo
        ];

        return view('Usuarios/editar_imagem', $data);
    }

    public function upload()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $validacao = $this->validarImagem();

        if (!$validacao->withRequest($this->request)->run()) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if ($largura < "300" || $altura < "300") {
            $retorno['erro'] = 'Por favor verifique os erros abaixo';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels'];

            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        $this->manipulaImagem($caminhoImagem, $grupo);

        $this->removeImagemDoFileSystem($grupo);

        $grupo->imagem = $imagem->getName();

        $this->grupoModel->save($grupo);

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso');

        return $this->response->setJSON($retorno);
    }

    public function imagem(string $imagem = null)
    {
        if ($imagem != null) {
            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    public function excluir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse usuário já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {
            $this->removeImagemDoFileSystem($grupo);

            $grupo->imagem = null;
            $grupo->ativo = false;
            $this->grupoModel->protect(false)->save($grupo);

            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("usuarios"))
                ->with('sucesso', "Usuário $grupo->nome excluido com sucesso!");
        }

        $data= [
            'titulo' => 'Excluindo o usuário' . esc($grupo->nome),
            'usuario' => $grupo
        ];

        return view('Usuarios/excluir', $data);
    }

    public function restaurar(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deleted_at == null) {
            return redirect()->back()->with('info', 'Apenas usuários excluídos podem ser recuperados');
        }

        $grupo->deleted_at = null;

        $this->grupoModel->protect(false)->save($grupo);

        return redirect()->back()->with('sucesso', "Usuário $grupo->nome recuperado com sucesso!");
    }



    private function removeImagemDoFileSystem($grupo)
    {
        $imagemAntiga = $grupo->imagem;

        if ($imagemAntiga != null) {
            $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagemAntiga";

            if (is_file($caminhoImagem)) {
                unlink($caminhoImagem);
            }
        }
    }

    private function manipulaImagem(string $caminhoImagem, $grupo): void
    {
        // Redimensionar imagem
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        // Adicionar marca d'água de texto
        $anoAtual = date('Y');

        // Adiciona marca d'água de texto
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - User-ID $grupo->id", [
                'color' => '#fff',
                'opacity' => 0.5,
                'withShadow' => false,
                'hAlign' => 'center',
                'vAlign' => 'bottom',
                'fontSize' => 10,
            ])
            ->save($caminhoImagem);
    }

    public function validarImagem(): ?object
    {
        $validacao = service('validation');

        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
        ];

        $mensagens = [
            'imagem' => [
                'uploaded' => 'Por favor escolha uma imagem',
                'max_size' => 'Por favor selecione uma imagem de no máximo 1024',
                'ext_in' => 'Por favor escolha uma imagem png, jpg, jpeg ou webp',

            ]
        ];

        $validacao->setRules($regras, $mensagens);
        return $validacao;
    }
}
