<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Item;
use App\Models\ItemHistoricoModel;
use App\Models\ItemImagemModel;
use App\Models\ItemModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
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

        if ($item->tipo === 'produto') {
            $itemImagem = $this->itemImagemModel->select('imagem')->where('item_id', $item->id)->first();

            if ($itemImagem != null) {
                $item->imagem = $itemImagem->imagem;
            }
        }

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

    public function imagem(string $imagens = null)
    {
        if ($imagens != null) {
            $this->exibeArquivo('itens', $imagens);
        }
    }

    public function editarImagem(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        if ($item->tipo === 'serviço') {
            return redirect()->back()->with('info', "Você poderá alterar as imagens apenas de um item do tipo Produto");
        }

        $item->imagens = $this->itemImagemModel->where('item_id', $item->id)->findAll();

        $data = [
            'titulo' => "Gerenciando as imagens do produto $item->nome",
            'item' => $item,
        ];

        return view('Itens/editar_imagem', $data);
    }

    public function upload()
    {
        if (!$this->request->isAJAX()) {
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

        $item = $this->buscaItemOu404($post['id']);

        $quantidaDeImagens = $this->defineQuantidadeImagens($item->id);

        if ($quantidaDeImagens['total'] > 10) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo';
            $retorno['erros_model'] = ['total_imagens' => "O produto pode ter no máximo 10 imagens. "
                . $quantidaDeImagens['mensagem']];

            return $this->response->setJSON($retorno);
        }

        $imagens = $this->request->getFiles('imagens');

        foreach ($imagens['imagens'] as $imagem) {
            list($largura, $altura) = getimagesize($imagem->getPathName());

            if ($largura < "300" || $altura < "300") {
                $retorno['erro'] = 'Por favor verifique os erros abaixo';
                $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels'];

                return $this->response->setJSON($retorno);
            }
        }

        $arrayImagens = [];

        foreach ($imagens['imagens'] as $imagem) {
            $caminhoImagem = $imagem->store('itens');
            $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

            $this->manipulaImagem($caminhoImagem, $item->id);

            $arrayImagens[] = [
                'item_id' => $item->id,
                'imagem' => $imagem->getName(),
            ];
        }

//        $this->removeImagemDoFileSystem($item);

        $this->itemImagemModel->insertBatch($arrayImagens);

        session()->setFlashdata('sucesso', 'Imagens salvas com sucesso');

        return $this->response->setJSON($retorno);
    }

    public function removeImagem(string $imagem = null)
    {
        if ($this->request->getMethod() === 'post') {
            $objetoImagem = $this->buscaImagemOu404($imagem);

            $this->itemImagemModel->delete($objetoImagem->id);

            $caminhoImagem = WRITEPATH . "uploads/itens/$imagem";

            if (is_file($caminhoImagem)) {
                unlink($caminhoImagem);
            }

            return redirect()->back()->with("sucesso", "Imagem removida com sucesso!");
        }

        return redirect()->back();
    }

    public function excluir(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        if ($item->deleted_at != null) {
            return redirect()->back()->with('info', 'Esse item já encontra-se excluído');
        }

        if ($this->request->getMethod() === 'post') {

            $item->ativo = false;

            $this->itemModel->protect(false)->save($item);

            if ($this->itemModel->delete($item->id)) {
                $this->persisteHistoricoItem($item, 'Deletado');
            }

            if ($item->tipo === 'produto') $this->excluiItemImagens($item);

            return redirect()->to(site_url("itens"))
                ->with('sucesso', "Item $item->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => 'Excluindo o item' . esc($item->nome),
            'item' => $item
        ];

        return view('Itens/excluir', $data);
    }

    public function restaurar(int $id = null)
    {
        $item = $this->buscaItemOu404($id);

        if ($item->deleted_at == null) {
            return redirect()->back()->with('info', 'Apenas itens excluídos podem ser recuperados');
        }

        $item->deleted_at = null;

        if ($this->itemModel->protect(false)->save($item)) {
            $this->persisteHistoricoItem($item, 'Restaurado');
        }

        return redirect()->back()->with('sucesso', "Item " . esc($item->nome) . " recuperado com sucesso!");
    }

    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }

    private function buscaImagemOu404(string $imagem = null)
    {
        if (!$imagem || !$objetoImagem = $this->itemImagemModel->where('imagem', $imagem)->first()) {
            throw PageNotFoundException::forPageNotFound("Não encontramos a imagem $imagem");
        }

        return $objetoImagem;
    }

    private function buscaHistoricoItem($item)
    {
        $historicoItem = $this->itemHistoricoModel->recuperaHistoricoItem($item->id);

        if ($historicoItem != null) {
            foreach ($historicoItem as $key => $hist) {
                $historicoItem[$key]['atributos_alterados'] = unserialize($hist['atributos_alterados']);
            }

            return $historicoItem;
        }
    }

    private function persisteHistoricoItem($item, string $acao)
    {
        if ($acao == 'Atualizado') {
            $item->formataValorParaDB();
        }

        $historico = [
            'usuario_id' => usuario_logado()->id,
            'item_id' => $item->id,
            'acao' => $acao,
            'atributos_alterados' => $item->recuperaAtributosAlterados(),
        ];

        $this->itemHistoricoModel->insert($historico);
    }

    private function validarImagem(): ?object
    {
        $validacao = service('validation');

        $regras = [
            'imagens' => 'uploaded[imagens]|max_size[imagens,1024]|ext_in[imagens,png,jpg,jpeg,webp]',
        ];

        $mensagens = [
            'imagens' => [
                'uploaded' => 'Por favor escolha uma imagem ou mais imagens',
                'max_size' => 'Por favor selecione uma imagem de no máximo 1MB',
                'ext_in' => 'Por favor escolha uma imagem png, jpg, jpeg ou webp',

            ]
        ];

        $validacao->setRules($regras, $mensagens);
        return $validacao;
    }

    private function manipulaImagem(string $caminhoImagem, $item_id): void
    {
        // Redimensionar imagem
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        // Adicionar marca d'água de texto
        $anoAtual = date('Y');

        // Adiciona marca d'água de texto
        Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - Produto-ID $item_id", [
                'color' => '#fff',
                'opacity' => 0.5,
                'withShadow' => false,
                'hAlign' => 'center',
                'vAlign' => 'bottom',
                'fontSize' => 10,
            ])
            ->save($caminhoImagem);
    }

    private function defineQuantidadeImagens($itemId): array
    {
        $quantidade['atual'] = $this->itemImagemModel->where('item_id', $itemId)->countAllResults();

        $quantidade['recebida'] = count(array_filter($_FILES['imagens']['name']));

        $quantidade['disponivel'] = 10 - $quantidade['atual'];

        $quantidade['total'] = $quantidade['atual'] + $quantidade['recebida'];

        if ($quantidade['disponivel'] <= 0) {
            $quantidade['mensagem'] = 'Você não pode adicionar mais imagens!';
        }
        if ($quantidade['disponivel'] == 1) {
            $quantidade['mensagem'] = 'Você pode adicionar mais 1 imagem.';
        }
        if ($quantidade['disponivel'] > 1) {
            $quantidade['mensagem'] = 'Você pode adicionar mais ' . $quantidade['disponivel'] . ' imagens.';
        }

        return $quantidade;
    }

    private function excluiItemImagens($item): void
    {
        $imagens = $this->itemImagemModel->where('item_id', $item->id)->findAll();
        foreach ($imagens as $imagem) {
            $this->removeImagem($imagem->imagem);
        }
    }

}
