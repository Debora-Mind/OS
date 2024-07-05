<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\OrdemItemModel;
use App\Models\OrdemModel;
use App\Traits\OrdemTrait;
use CodeIgniter\HTTP\ResponseInterface;
use PhpParser\Node\Stmt\DeclareDeclare;

class OrdensItens extends BaseController
{
    use OrdemTrait;

    private $ordemModel;
    private $ordemItemModel;
    private $itemModel;

    public function __construct()
    {
        $this->ordemModel = new OrdemModel();
        $this->ordemItemModel = new OrdemItemModel();
        $this->itemModel = new ItemModel();
    }

    public function itens(string $codigo = null)
    {
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);

        $this->preparaItensDaOrdem($ordem);

        $data = [
            'titulo' => "Gerenciando os itens da ordem $ordem->codigo",
            'ordem' => $ordem
        ];

        return view('Ordens/itens', $data);
    }

    public function pesquisaItens()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $term = $this->request->getGet('term');

        $itens = $this->itemModel->pesquisaItens($term);

        $retorno = [];

        foreach ($itens as $item) {
            $data['id'] = $item->id;
            $data['item_preco'] = number_format($item->preco_venda, 2, ',', '.');

            $itemTipo = ucfirst($item->tipo);

            if ($item->tipo === 'produto'){
                if ($item->imagem != null) {
                    $caminhoImagem = "itens/imagem/$item->imagem";
                    $altImagem = $item->nome;
                }
                else {
                    $caminhoImagem = "recursos/img/item_sem_imagem.png";
                    $altImagem = "$item->nome não possuí imagem";
                }

                $data['value'] = "[ Código $item->codigo_interno ] [ $itemTipo ] [ Estoque $item->estoque ] $item->nome";
            }
            else {
                $caminhoImagem = "recursos/img/item_servico.jpg";
                $altImagem = $item->nome;

                $data['value'] = "[ Código $item->codigo_interno ] [ $itemTipo ] $item->nome";
            }

            $imagem = [
                'src' => $caminhoImagem,
                'class' => 'img-fluid img-thumbnail',
                'alt' => $altImagem,
                'width' => '50'
            ];

            $data['label'] = '<span>' .img($imagem) . ' ' . $data['value'] . '</span>';

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    public function adicionarItem()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $retorno['token'] = csrf_hash();

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'quantidade' => 'required|greater_than[0]',
        ];

        $mensagens = [
            'item_id' => [
                'required' => 'Por favor pesquise um item e tente novamente.'
            ],
            'quantidade' => [
                'required' => 'Por favor pesquise um item e escolha a quantidade maior que zero.',
                'greater_than' => 'Por favor pesquise um item e escolha a quantidade maior que zero.'
            ]
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = $validacao->getErrors();

            return $this->response->setJSON($retorno);
        }

        $post = $this->request->getPost();

        $ordem = $this->ordemModel->buscaOrdemOu404($post['codigo']);

        $item = $this->buscaItemOu404($post['item_id']);

        if ($item->tipo === 'produto' && $post['quantidade'] > $item->estoque) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Temos apenas <b class='text-white'>$item->estoque</b> em estoque do item $item->nome."];

            return $this->response->setJSON($retorno);
        }

        if ($this->verificaSeOrdemPossuiItem($ordem->id, $item->id)) {
            $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['estoque' => "Essa ordem já possui o item  <b class='text-white'>$item->nome</b>."];

            return $this->response->setJSON($retorno);
        }

        $ordemItem = [
            'ordem_id' => (int) $ordem->id,
            'item_id' => (int) $item->id,
            'item_quantidade' => (int) $post['quantidade'],
        ];

        if ($this->ordemItemModel->insert($ordemItem)){
            session()->setFlashdata('sucesso', "$item->nome adicionado com sucesso!");

            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor verifique os erros abaixo e tente novamente';
        $retorno['erros_model'] = $this->ordemItemModel->errors();

        return $this->response->setJSON($retorno);
    }

    public function atualizarQuantidade(string $codigo = null)
    {
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'quantidade' => 'required|greater_than[0]',
            'id_principal' => 'required|greater_than[0]',
        ];

        $mensagens = [
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser atualizado.'
            ],
            'quantidade' => [
                'required' => 'Por favor escolha a quantidade maior que zero.',
                'greater_than' => 'Por favor escolha a quantidade maior que zero.'
            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição. Escolha a quantidade e tente novamente.',
                'greater_than' => 'Não conseguimos processar a sua requisição. Escolha a quantidade e tente novamente.'
            ]
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {
            return redirect()
                ->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente.')
                ->with('erros_model', $validacao->getErrors());
        }

        $post = $this->request->getPost();
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);
        $ordemItem = $this->buscaOrdemItemOu404($post['id_principal'], $ordem->id);

        $item = $this->buscaItemOu404($post['item_id']);

        if ($item->tipo === 'produto' && $post['quantidade'] > $item->estoque) {
            return redirect()
                ->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente.')
                ->with('erros_model', ['estoque' => "Temos apenas <b class='text-white'>$item->estoque</b> em estoque do item $item->nome."]);
        }

        if ($post['quantidade'] === $ordemItem->item_quantidade){
            return redirect()
                ->back()
                ->with('info', 'Informe a quantidade diferente da anterior.');
        }

        $ordemItem->item_quantidade = $post['quantidade'];

        if($this->ordemItemModel->atualizaQuantidadeItem($ordemItem)){
            return redirect()->back()->with('sucesso', 'Quantidade atualizada com sucesso!');
        }

        return redirect()
            ->back()
            ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente.')
            ->with('erros_model', $this->ordemItemModel->errors());
    }

    public function removerItem(string $codigo = null)
    {
        if ($this->request->getMethod() != 'post') {
            return redirect()->back();
        }

        $validacao = service('validation');

        $regras = [
            'item_id' => 'required',
            'id_principal' => 'required|greater_than[0]',
        ];

        $mensagens = [
            'item_id' => [
                'required' => 'Não conseguimos identificar qual é o item a ser excluído.'
            ],
            'id_principal' => [
                'required' => 'Não conseguimos processar a sua requisição. Escolha novamente o item a ser removido.',
                'greater_than' => 'Não conseguimos processar a sua requisição. Escolha novamente o item a ser removido.'
            ]
        ];

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() === false) {
            return redirect()
                ->back()
                ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente.')
                ->with('erros_model', $validacao->getErrors());
        }

        $post = $this->request->getPost();
        $ordem = $this->ordemModel->buscaOrdemOu404($codigo);
        $ordemItem = $this->buscaOrdemItemOu404($post['id_principal'], $ordem->id);

        if($this->ordemItemModel->delete($ordemItem->id)){
            return redirect()->back()->with('sucesso', 'Item removido com sucesso!');
        }

        return redirect()
            ->back()
            ->with('atencao', 'Por favor verifique os erros abaixo e tente novamente.')
            ->with('erros_model', $this->ordemItemModel->errors());
    }

    private function buscaItemOu404(int $id = null)
    {
        if (!$id || !$item = $this->itemModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o item $id");
        }

        return $item;
    }

    private function buscaOrdemItemOu404(int $idPrincipal = null, int $ordemId = null)
    {
        if (!$idPrincipal || !$ordemItem = $this->ordemItemModel
                                                ->where('id', $idPrincipal)
                                                ->where('ordem_id', $ordemId)
                                                ->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro principal $idPrincipal");
        }

        return $ordemItem;
    }

    private function verificaSeOrdemPossuiItem(int $ordem_id, int $item_id): bool
    {
        $possuiItem = $this->ordemItemModel->where('ordem_id', $ordem_id)->where('item_id', $item_id)->first();

        return !empty($possuiItem);
    }

}
