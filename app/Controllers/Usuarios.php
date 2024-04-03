<?php

namespace App\Controllers;

use App\Entities\Usuario;
use App\Models\GrupoModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;
    private $quantidadeGruposPorPagina = 5;
    private $quantidadeGruposPadroes = 2;
    private $grupoAdministrador = 1;
    private $grupoClientes = 2;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->grupoUsuarioModel = new GrupoUsuarioModel();
        $this->grupoModel = new GrupoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários do sistema',
        ];

        return view('Usuarios/index', $data);
    }

    public function recuperaUsuarios()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'email',
            'ativo',
            'imagem',
            'deleted_at'
        ];

        $usuarios = $this->usuarioModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($usuarios as $usuario) {

            if ($usuario->imagem != null) {
                $imagem = [
                    'src' => site_url("usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => esc($usuario->nome),
                    'width' => '50'
                ];
            }
            else {
                $imagem = [
                    'src' => site_url("recursos/img/usuario_sem_imagem.png"),
                    'class' => 'rounded-circle img-fluid',
                    'alt' => 'Usuário sem imagem',
                    'width' => '50'
                ];
            }

            $nomeUsuario = esc($usuario->nome);

            $data[] = [
                'imagem' => $usuario->imagem = img($imagem),
                'nome' => anchor("usuarios/exibir/$usuario->id", $nomeUsuario, "title='Exibir usuário $nomeUsuario'"),
                'email' => esc($usuario->email),
                'ativo' => $usuario->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $usuario = new Usuario();

        $data= [
            'titulo' => 'Criando novo usuário',
            'usuario' => $usuario
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

        $usuario = new Usuario($post);

        if ($this->usuarioModel->protect(false)->insert($usuario)) {

            $btnCriar = anchor("usuarios/criar", "Cadastrar novo usuário", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->usuarioModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $data= [
            'titulo' => 'Detalhando o usuário' . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/exibir', $data);
    }

    public function editar(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $data= [
            'titulo' => 'Editando o usuário' . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()){
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['password'])){
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        $usuario->fill($post);

        if (!$usuario->hasChanged()) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $data= [
            'titulo' => 'Alterando a imagem do usuário' . esc($usuario->nome),
            'usuario' => $usuario
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

        $usuario = $this->buscaUsuarioOu404($post['id']);

        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());

        if ($largura < "300" || $altura < "300") {
            $retorno['erro'] = 'Por favor verifique os erros abaixo';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels'];

            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        $this->manipulaImagem($caminhoImagem, $usuario);

        $this->removeImagemDoFileSystem($usuario);

        $usuario->imagem = $imagem->getName();

        $this->usuarioModel->save($usuario);

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
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse usuário já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {
            $this->removeImagemDoFileSystem($usuario);

            $usuario->imagem = null;
            $usuario->ativo = false;
            $this->usuarioModel->protect(false)->save($usuario);

            $this->usuarioModel->delete($usuario->id);

            return redirect()->to(site_url("usuarios"))
                ->with('sucesso', "Usuário $usuario->nome excluído com sucesso!");
        }

        $data= [
            'titulo' => 'Excluindo o usuário' . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/excluir', $data);
    }

    public function restaurar(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deleted_at == null) {
            return redirect()->back()->with('info', 'Apenas usuários excluídos podem ser recuperados');
        }

        $usuario->deleted_at = null;

        $this->usuarioModel->protect(false)->save($usuario);

        return redirect()->back()->with('sucesso', "Usuário " . esc($usuario->nome) . " recuperado com sucesso!");
    }

    public function grupos(int $id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $usuario->grupos = $this->grupoUsuarioModel
            ->recuperaGruposDoUsuario($usuario->id, $this->quantidadeGruposPorPagina);
        $usuario->pager = $this->grupoUsuarioModel->pager;

        $data= [
            'titulo' => 'Gerenciando os grupos de acesso do usuário ' . esc($usuario->nome),
            'usuario' => $usuario
        ];

        // Quando o usuário for do grupo de clientes (id 2), retorna para view de exibição de usuários
        if (in_array($this->grupoClientes, array_column($usuario->grupos, 'grupo_id'))) {
            return redirect()->to(site_url("usuarios/exibir/$usuario->"))
                ->with('info', 'Esse usuário é um cliente, portando não é necessário atribuí-lo ou
                 removê-lo de outros grupos de acesso');
        }

        // Verifica se o usuário está no grupo Administrador
        if (in_array($this->grupoAdministrador, array_column($usuario->grupos, 'grupo_id'))) {

            $usuario->full_control = true;
            return view('Usuarios/grupos', $data);

        }

        $usuario->full_control = false;

        if (!empty($usuario->grupos)) {
            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');

            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', $this->quantidadeGruposPadroes)
                ->whereNotIn('id', $gruposExistentes)
                ->findAll();
        }
        else {
            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', $this->quantidadeGruposPadroes)
                ->findAll();
        }

        return view('Usuarios/grupos', $data);
    }

    public function salvarGrupos()
    {
        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['grupo_id'])) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um ou mais grupos para salvar'];

            return $this->response->setJSON($retorno);
        }

        // Quando o usuário for do grupo de clientes (id 2), não permite salvar
        if (in_array($this->grupoClientes, $post['grupo_id'])){
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo de Clientes não pode ser atribuído de forma manual.'];

            return $this->response->setJSON($retorno);
        }

        // Caso o usuário selecione o grupo Administrados, apenas ser esse grupo será salvo,
        // independente dos demais selecionados
        if (in_array($this->grupoAdministrador, $post['grupo_id'])){
            $grupoAdmin[] = [
                'grupo_id' => $this->grupoAdministrador,
                'usuario_id' => $usuario->id
            ];

            $this->grupoUsuarioModel->insertBatch($grupoAdmin);

            // Após inserir o grupo Administrador, remove os demais, caso existam para esse usuário
            $this->grupoUsuarioModel
                ->where('grupo_id !=', $this->grupoAdministrador)
                ->where('usuario_id', $usuario->id)
                ->delete();
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'Notamos que o 
            <b>Grupo Administrador</b> foi informado, 
            por tanto não há necessidade de informar outros grupos, 
            pois apenas o Administrador será associado ao usuário.');
            return $this->response->setJSON($retorno);
        }

        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo){
            $grupoPush[] = [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id
            ];
        }

        $this->grupoUsuarioModel->insertBatch($grupoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
        return $this->response->setJSON($retorno);
    }

    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }

    private function removeImagemDoFileSystem($usuario)
    {
        $imagemAntiga = $usuario->imagem;

        if ($imagemAntiga != null) {
            $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagemAntiga";

            if (is_file($caminhoImagem)) {
                unlink($caminhoImagem);
            }
        }
    }

    private function manipulaImagem(string $caminhoImagem, $usuario): void
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
            ->text("Ordem $anoAtual - User-ID $usuario->id", [
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
