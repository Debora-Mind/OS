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
}
