<?php

namespace App\Models;

use CodeIgniter\Model;

class TransacaoModel extends Model
{
    protected $table            = 'transacoes';
    protected $useAutoIncrement = true;
    protected $returnType       = 'App\Entities\Transacao';
    protected $allowedFields    = [
        'ordem_id',
        'barcode',
        'link',
        'pdf',
        'expire_at',
        'status',
        'total',
    ];
}
