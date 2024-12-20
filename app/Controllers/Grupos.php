<?php

namespace App\Controllers;

use App\Entities\Grupo;
use App\Models\GrupoModel;
use App\Models\GrupoPermissaoModel;
use App\Models\PermissaoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Grupos extends BaseController
{
    private $grupoModel;
    private $grupoPermissaoModel;
    private $PermissaoModel;
    private $quantidadeGruposPadroes = 2;
    private $quantidadePermissoesPorPagina = 5;

    public function __construct()
    {
        $this->grupoModel = new GrupoModel();
        $this->grupoPermissaoModel = new GrupoPermissaoModel();
        $this->PermissaoModel = new PermissaoModel();
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
        if (!$this->request->isAJAX()) {
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

    public function criar()
    {
        $grupo = new Grupo();

        $data = [
            'titulo' => 'Criando novo grupo de acesso',
            'grupo' => $grupo
        ];

        return view('Grupos/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = new Grupo($post);

        if ($this->grupoModel->insert($grupo)) {

            $btnCriar = anchor("grupos/criar", "Cadastrar novo grupo de acesso", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->grupoModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->grupoModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        $data = [
            'titulo' => 'Detalhando o grupo de acesso' . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/exibir', $data);
    }

    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }

        return $grupo;
    }

    public function editar(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id <= $this->quantidadeGruposPadroes) {
            return redirect()->back()->with('atencao', 'O grupo ' . esc($grupo->nome) .
                ' não pode ser editado ou excluído conforme descrito na exibição do mesmo.');
        }

        $data = [
            'titulo' => 'Editando o grupo' . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        if ($grupo->id <= $this->quantidadeGruposPadroes) {

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

    public function excluir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id <= $this->quantidadeGruposPadroes) {
            return redirect()->back()->with('atencao', 'O grupo ' . esc($grupo->nome) .
                ' não pode ser editado ou excluído conforme descrito na exibição do mesmo.');
        }

        if ($grupo->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse grupo de acesso já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {

            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("grupos"))
                ->with('sucesso', "Grupo $grupo->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => 'Excluindo o grupo de acesso' . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/excluir', $data);
    }

    public function restaurar(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deleted_at == null) {
            return redirect()->back()->with('info', 'Apenas grupos de acesso excluídos podem ser recuperados');
        }

        $grupo->deleted_at = null;

        $this->grupoModel->protect(false)->save($grupo);

        return redirect()->back()->with('sucesso', "Grupo " . esc($grupo->nome) . " recuperado com sucesso!");
    }

    public function permissoes(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id <= $this->quantidadeGruposPadroes) {
            return redirect()->back()->with('info',
                'Não é necessário atribuir ou remover permissões de acesso para o grupo ' . esc($grupo->nome) .
                ', pois esse grupo é padrão do sistema');
        } else {
            $grupo->permissoes = $this->grupoPermissaoModel
                ->recuperaPermissoesDoGrupo($grupo->id, $this->quantidadePermissoesPorPagina);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }

        $data = [
            'titulo' => 'Gerenciando as permissões do grupo de acesso <b class="text-warning">' . esc($grupo->nome) . '</b>',
            'grupo' => $grupo
        ];

        if (!empty($grupo->permissoes)) {
            $permissoesExistentes = array_column($grupo->permissoes, 'permissao_id');

            $data['permissoesDisponiveis'] = $this->PermissaoModel
                ->whereNotIn('id', $permissoesExistentes)
                ->findAll();
        } else {
            $data['permissoesDisponiveis'] = $this->PermissaoModel
                ->findAll();
        }

        return view('Grupos/permissoes', $data);
    }

    public function salvarPermissoes()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $grupo = $this->buscaGrupoOu404($post['id']);

        if ($grupo->id <= $this->quantidadeGruposPadroes) {

            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['grupo' => 'O grupo <b class="text-white">' . esc($grupo->nome) .
                '</b> não pode ser editado ou excluído conforme descrito na exibição do mesmo.'];

            return $this->response->setJSON($retorno);
        }

        $grupo->fill($post);

        if (empty($post['permissao_id'])) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['permissao_id' => 'Escolha uma ou mais permissões para salvar'];

            return $this->response->setJSON($retorno);
        }

        $permissaoPush = [];

        foreach ($post['permissao_id'] as $permissao) {
            $permissaoPush[] = [
                'grupo_id' => $grupo->id,
                'permissao_id' => $permissao
            ];
        }

        $this->grupoPermissaoModel->insertBatch($permissaoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
        return $this->response->setJSON($retorno);

    }

    public function removePermissao(int $principal_id = null)
    {
        if ($this->request->getMethod() === 'post') {

            $this->grupoPermissaoModel->delete($principal_id);

            return redirect()->back()
                ->with('sucesso', "Permissão removida com sucesso!");
        }

        return redirect()->back();
    }

}
