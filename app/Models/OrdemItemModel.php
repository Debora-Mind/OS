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
            'item.id',
            'item.nome',
            'item.preco_venda',
            'item.tipo',
            'item.estoque',
            'ordens_itens.id AS id_principal',
            'ordens_itens.item_quantidade',
        ];

        return$this->select($atributos)
            ->join('itens', 'itens.id = ordem_itens.item_id')
            ->where('ordens_itens.ordem_id', $ordemId)
            ->groupBy('itens.nome')
            ->orderBy('itens.tipo', 'ASC')
            ->findAll();
    }
}
