<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissaoModel extends Model
{
    protected $table            = 'permissoes';
    protected $returnType       = 'Object';
    protected $allowedFields    = [];
}
