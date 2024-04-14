<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class FornecedorNotaFiscalModel extends Model
{
    protected $table            = 'fornecedores_notas_fiscais';
    protected $returnType       = 'object';
    protected $allowedFields    = [
        'fornecedor_id',
        'valor_nota',
        'descricao_itens',
        'nota_fiscal',
        'data_emissao',
        'created_at',
    ];

    // Dates
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

}
