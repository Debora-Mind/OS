<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Itens extends BaseController
{
    private $itemModel;

    public function __construct()
    {
        $this->itemModel = new ItemModel();
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
            'ativo'
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

    public function exibir(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        $data = [
            'titulo' => 'Detalhando o item ' . esc($item->nome),
            'item' => $item
        ];

        return view('Itens/exibir', $data);
    }

    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }
}
