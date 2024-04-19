<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemHistoricoModel extends Model
{
    protected $table            = 'itens_historico';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'usuario_id',
        'item_id',
        'acao',
        'atributos_alterados',
    ];

    protected $beforeInsert = ['addCreatedAt'];
    protected function addCreatedAt($data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');

        return $data;
    }

    public function recuperaHistoricoItem(int $item_id)
    {
        $atributos = [
            'usuario_id',
            'usuarios.nome as usuario_nome',
            'acao',
            'atributos_alterados',
            'itens_historico.created_at',
        ];

        return $this->select($atributos)
            ->join('usuarios', 'itens_historico.usuario_id = usuarios.id')
            ->where('item_id', $item_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
