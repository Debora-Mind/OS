<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;
use App\Models\ItemHistoricoModel;
use App\Models\ItemImagemModel;
use App\Models\ItemModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;

class Itens extends BaseController
{
    private $itemModel;
    private $itemHistoricoModel;
    private $itemImagemModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
        $this->itemHistoricoModel = new ItemHistoricoModel();
        $this->itemImagemModel = new ItemImagemModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os itens'
        ];

        return view('Itens/index', $data);
    }

    public function recuperaItens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = [
            'id',
            'nome',
            'tipo',
            'estoque',
            'preco_venda',
            'ativo',
            'deleted_at'
        ];

        $itens = $this->itemModel->select($atributos)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($itens as $item) {

            $nomeItem = esc($item->nome);

            $data[] = [
                'nome' => anchor("itens/exibir/$item->id", $nomeItem, "title='Exibir item $nomeItem'"),
                'tipo' => $item->exibeTipo(),
                'estoque' => $item->exibeEstoque(),
                'preco_venda' => $item->precoVendaFormatado(),
                'ativo' => $item->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data
        ];

        return $this->response->setJSON($retorno);
    }

    public function criar()
    {
        $item = new Item();

        $data = [
            'titulo' => 'Cadastrando novo item',
            'item' => $item
        ];

        return view('Itens/criar', $data);
    }

    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $item = new Item($post);
        $item->codigo_interno = $this->itemModel->geraCodigoInternoItem();

        if ($item->tipo === 'produto') {
            if ($item->marca === "" || $item->marca === null) {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model'] = ['estoque' =>
                    'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a marca do mesmo'];
                return $this->response->setJSON($retorno);
            }

            if ($item->estoque === "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model'] = ['estoque' =>
                    'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }
        }

        if ($item->tipo === 'serviço') {
            $item->removeCamposServico();
        }

        $precoCusto = str_replace(['.', ','], '', $item->preco_custo);
        $precoVenda = str_replace(['.', ','], '', $item->preco_venda);

        if ($precoCusto > $precoVenda) {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' =>
                'O preço de venda <b class="text-white">não pode ser menor</b> do que o preço de custo'];
            return $this->response->setJSON($retorno);
        }

        if ($this->itemModel->protect(false)->insert($item)) {

            $btnCriar = anchor("itens/criar", "Cadastrar novo item", ['class' => 'btn btn-danger mt-2']);

            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br> $btnCriar");

            $retorno['id'] = $this->itemModel->getInsertID();

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->itemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function exibir(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        $item->historico = $this->buscaHistoricoItem($item);

        $data = [
            'titulo' => 'Detalhando o item ' . esc($item->nome),
            'item' => $item
        ];

        return view('Itens/exibir', $data);
    }

    public function codigoBarras(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        $generator = new BarcodeGeneratorSVG();
        $item->codigo_interno = (empty($item->codigo_interno) || !$item->codigo_interno) ? '0' : $item->codigo_interno ;
        $item->codigo_barras = $generator
            ->getBarcode($item->codigo_interno , $generator::TYPE_CODE_128, 3, 80);

        $data = [
            'titulo' => 'Código de barras do item',
            'item' => $item
        ];

        return view('Itens/codigo_barras', $data);
    }

    public function editar(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        $data = [
            'titulo' => 'Editando o item ' . esc($item->nome) . ' ' . $item->exibeTipo(),
            'item' => $item
        ];

        return view('Itens/editar', $data);
    }

    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $post = $this->request->getPost();

        $item = $this->buscaItemOu404($post['id']);

        $item->fill($post);

        if (!$item->hasChanged()) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($item->tipo === 'produto') {
            if ($item->estoque === "") {
                $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
                $retorno['erros_model'] = ['estoque' =>
                    'Para um item do tipo <b class="text-white">Produto</b>, é necessário informar a quantidade em estoque'];
                return $this->response->setJSON($retorno);
            }
        }

        $precoCusto = str_replace(['.', ','], '', $item->preco_custo);
        $precoVenda = str_replace(['.', ','], '', $item->preco_venda);

        if ($precoCusto > $precoVenda) {
            $retorno['erro'] = 'Verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' =>
                'O preço de venda <b class="text-white">não pode ser menor</b> do que o preço de custo'];
            return $this->response->setJSON($retorno);
        }

        if ($this->itemModel->protect(false)->save($item)) {

            $this->persisteHistoricoItem($item, 'Atualizado');

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->itemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function editarImagem(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        if ($item->tipo === 'serviço') {
            return redirect()->back()->with('info', "Você poderá alterar as imagens apenas de um item do tipo Produto");
        }

        $item->imagens = $this->itemImagemModel->where('item_id', $item->id)->findAll();

        $data = [
            'titulo' => 'Gerenciando as imagens do item',
            'item' => $item,
        ];

        return view('Itens/editar_imagem', $data);
    }

    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }

    private function buscaHistoricoItem($item)
    {
        $atributos = [
            'usuario_id',
            'usuarios.nome as usuario_nome',
            'acao',
            'atributos_alterados',
            'itens_historico.created_at',
        ];

        $historicoItem = $this->itemHistoricoModel
            ->select($atributos)
            ->join('usuarios', 'itens_historico.usuario_id = usuarios.id')
            ->where('item_id', $item->id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        if ($historicoItem != null) {
            foreach ($historicoItem as $key => $hist) {
                $historicoItem[$key]['atributos_alterados'] = unserialize($hist['atributos_alterados']);
            }

            return $historicoItem;
        }
    }

    private function persisteHistoricoItem($item, string $acao)
    {
        $item->formataValorParaDB();

        $historico = [
            'usuario_id' => usuario_logado()->id,
            'item_id' => $item->id,
            'acao' => $acao,
            'atributos_alterados' => $item->recuperaAtributosAlterados(),
        ];

        $this->itemHistoricoModel->insert($historico);
    }
}
