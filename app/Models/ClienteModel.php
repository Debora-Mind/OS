<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table            = 'clientes';

    protected $returnType       = 'App\Entities\Cliente';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'usuario_id',
        'nome',
        'cpf',
        'telefone',
        'email',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'nome' => 'required|min_length[3]|max_length[125]',
        'email' => 'required|valid_email|max_length[230]|is_unique[clientes.email,id,{id}]',
        'telefone' => 'required|exact_length[11]|is_unique[clientes.telefone,id,{id}]',
        'cpf' => 'required|exact_length[11]|validaCPF|is_unique[clientes.cpf,id,{id}]',
        'cep' => 'required|exact_length[8]|',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
            'min_length' => 'O campo Nome precisa ter pelo menos 3 caractéres.',
            'max_length' => 'O campo Nome não pode ser maior que 125 caractéres.',
        ],
        'email' => [
            'required' => 'O campo E-mail é obrigatório.',
            'max_length' => 'O campo E-mail não pode ser maior que 230 caractéres.',
            'valid_email' => 'O campo E-mail precisa conter um e-mail válido.',
            'is_unique' => 'O e-mail informado já está sendo utilizado.',
        ],
        'telefone' => [
            'required' => 'O campo Telefone é obrigatório.',
            'exact_length' => 'O campo Telefone deve conter 11 caractéres.',
            'is_unique' => 'O Telefone informado já está sendo utilizado.',
        ],
        'cpf' => [
            'required' => 'O campo CPF é obrigatório.',
            'exact_length' => 'O campo CPF deve conter 11 caractéres.',
            'is_unique' => 'O CPF informado já está sendo utilizado.',
            'validaCPF' => 'O CPF informado não é válido.'
        ],
        'cep' => [
            'required' => 'O campo CEP é obrigatório.',
            'exact_length' => 'O campo CEP deve conter 8 caractéres.',
        ],
    ];
}
