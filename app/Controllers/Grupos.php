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

    public function criar()
    {
        $grupo = new Grupo();

        $data= [
            'titulo' => 'Criando novo grupo de acesso',
            'grupo' => $grupo
        ];

        return view('Grupos/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()){
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

    public function excluir(int $id = null)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id <= 1) {
            return redirect()->back()->with('atencao', 'O grupo ' . esc($grupo->nome) .
                ' não pode ser editado ou excluído conforme descrito na exibição do mesmo.' );
        }

        if ($grupo->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse grupo de acesso já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {

            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url("grupos"))
                ->with('sucesso', "Grupo $grupo->nome excluído com sucesso!");
        }

        $data= [
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

    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)){
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo de acesso $id");
        }

        return $grupo;
    }

//
//






}
