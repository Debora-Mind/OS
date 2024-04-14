<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model
{
    protected $table = 'fornecedores';
    protected $returnType = 'App\Entities\Fornecedor';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'razao',
        'cnpj',
        'ie',
        'telefone',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'ativo',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'razao' => 'required|max_length[255]|is_unique[fornecedores.razao,id,{id}]',
        'cnpj' => 'required|validaCNPJ|min_length[14]|max_length[14]|is_unique[fornecedores.cnpj,id,{id}]',
        'ie' => 'required|max_length[30]|is_unique[fornecedores.ie,id,{id}]',
        'telefone' => 'required|min_length[10]|max_length[20]|is_unique[fornecedores.telefone,id,{id}]',
        'cep' => 'required|min_length[8]|max_length[8]',
        'endereco' => 'required|max_length[255]',
        'numero' => 'max_length[50]',
        'bairro' => 'required|max_length[128]',
        'cidade' => 'required|max_length[128]',
        'estado' => 'required|min_length[2]|max_length[2]',
    ];
    protected $validationMessages = [
        'razao' => [
            'required' => 'O campo Razão Social é obrigatório.',
            'max_length' => 'O campo Razão Social não pode ser maior que 255 caractéres.',
            'is_unique' => 'A Razão Social informada já está sendo utilizada.'
        ],
        'cnpj' => [
            'required' => 'O campo CNPJ é obrigatório.',
            'validaCNPJ' => 'O CNPJ informado não é válido.',
            'min_length' => 'O campo CNPJ deve conter 15 caractéres.',
            'max_length' => 'O campo CNPJ deve conter 15 caractéres.',
            'is_unique' => 'O CNPJ informado já está sendo utilizado.',
        ],
        'ie' => [
            'required' => 'O campo IE é obrigatório.',
            'max_length' => 'O campo Razão Social não pode ser maior que 30 caractéres.',
            'is_unique' => 'O IE informado já está sendo utilizado.',
        ],
        'telefone' => [
            'required' => 'O campo Telefone é obrigatório.',
            'min_length' => 'O campo Telefone deve conter pelo menos 10 caractéres.',
            'max_length' => 'O campo Telefone não pode ser maior que 20 caractéres.',
            'is_unique' => 'O Telefone informado já está sendo utilizado.',
        ],
        'cep' => [
            'required' => 'O campo CEP é obrigatório.',
            'min_length' => 'O campo CEP deve conter 9 caractéres.',
            'max_length' => 'O campo CEP deve conter 9 caractéres.',
        ],
        'endereco' => [
            'required' => 'O campo Endereço é obrigatório.',
            'max_length' => 'O campo Endereço não pode ser maior que 255 caractéres.',
        ],
        'numero' => [
            'max_length' => 'O campo Endereço não pode ser maior que 50 caractéres.'
        ],
        'bairro' => [
            'required' => 'O campo Bairro é obrigatório.',
            'max_length' => 'O campo Bairro não pode ser maior que 128 caractéres.',
        ],
        'cidade' => [
            'required' => 'O campo Cidade é obrigatório.',
            'max_length' => 'O campo Cidade não pode ser maior que 128 caractéres.',
        ],
        'estado' => [
            'required' => 'O campo Estado é obrigatório.',
            'min_length' => 'O campo Estado deve conter 2 caractéres.',
            'max_length' => 'O campo Estado deve conter 2 caractéres.',
        ]
    ];
}
