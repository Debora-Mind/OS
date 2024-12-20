<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemImagemModel extends Model
{
    protected $table            = 'itens_imagens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';

    protected $allowedFields    = [
        'item_id',
        'imagem',
    ];

}
