<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemItemModel extends Model
{
    protected $table            = 'ordens_itens';

    protected $returnType       = 'object';

    protected $allowedFields    = [
        'ordem_id',
        'item_id',
        'item_quantidade'
    ];

    public function recuperaItensDaOrdem(int $ordemId)
    {
        $atributos = [
            'itens.id',
            'itens.nome',
            'itens.preco_venda',
            'itens.tipo',
            'itens.estoque',
            'ordens_itens.id AS id_principal',
            'ordens_itens.item_quantidade',
        ];

        return$this->select($atributos)
            ->join('itens', 'itens.id = ordens_itens.item_id')
            ->where('ordens_itens.ordem_id', $ordemId)
            ->groupBy('itens.nome')
            ->orderBy('itens.tipo', 'ASC')
            ->findAll();
    }

    public function atualizaQuantidadeItem(object $ordemItem)
    {
        return $this->set('item_quantidade', $ordemItem->item_quantidade)
                    ->where('id', $ordemItem->id)
                    ->update();
    }
}
