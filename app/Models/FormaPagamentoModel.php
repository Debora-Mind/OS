<?php

namespace App\Models;

use CodeIgniter\Model;

class FormaPagamentoModel extends Model
{
    protected $table            = 'formas_pagamentos';
    protected $returnType       = 'App\Entities\FormaPagamento';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'descricao',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'nome' => 'required|max_length[128]|is_unique[formas_pagamentos.nome,id,{id}]',
        'descricao' => 'required|max_length[255]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
            'max_length' => 'O campo Nome não pode ser maior que 128 caractéres.',
            'is_unique' => 'O nome informado já está sendo utilizado.',
        ],
        'descricao' => [
            'required' => 'O campo Descrição é obrigatório.',
            'max_length' => 'O campo Descrição não pode ser maior que 255 caractéres.',
        ],
    ];
}
