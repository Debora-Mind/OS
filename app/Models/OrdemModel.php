<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdemModel extends Model
{
    protected $table            = 'ordens';
    protected $returnType       = 'App\Entities\Ordem';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'cliente_id',
        'codigo',
        'forma_pagamento',
        'situacao',
        'itens',
        'valor_produtos',
        'valor_servicos',
        'valor_desconto',
        'valor_ordem',
        'equipamento',
        'defeito',
        'observacoes',
        'parecer_tecnico',
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
        'cliente_id' => 'required',
        'codigo' => 'required',
        'equipamento' => 'required|max_length[150]',
        'defeito' => 'max_length[500]',
        'observacoes' => 'required|max_length[255]',
        'parecer_tecnico' => 'max_length[255]',
    ];
    protected $validationMessages = [
        'cliente_id' => [
            'required' => 'O campo Cliente é obrigatório.',
        ],
        'codigo' => [
            'required' => 'O campo Código é obrigatório.',
        ],
        'equipamento' => [
            'required' => 'O campo Equipamento é obrigatório.',
            'max_length' => 'O campo Equipamento não pode ser maior que 150 caractéres.',
        ],
        'defeito' => [
            'max_length' => 'O campo Defeito não pode ser maior que 500 caractéres.',
        ],
        'observacoes' => [
            'required' => 'O campo Observações é obrigatório.',
            'max_length' => 'O campo Observações não pode ser maior que 255 caractéres.',
        ],
        'parecer_tecnico' => [
            'max_length' => 'O campo Parecer técnico não pode ser maior que 255 caractéres.',
        ],
    ];

    public function geraCodigoOrdem(): string
    {
        do {
            $codigoOrdem = random_string('alnum', 20);
            $this->select('codigo')->where('codigo', $codigoOrdem);

        } while ($this->countAllResults() > 1);

        return strtoupper($codigoOrdem);
    }

    public function recuperOrdens()
    {
        $atributos = [
            'ordens.codigo',
            'ordens.created_at',
            'ordens.situacao',
            'clientes.nome',
            'clientes.cpf',
        ];

        return $this
            ->select($atributos)
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->orderBy('ordens.situacao', 'ASC')
            ->orderBy('ordens.created_at', 'DESC')
            ->withDeleted()
            ->findAll();
    }

    public function formatarCPF($cpf)
    {
        // Remove qualquer caractere não numérico
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Adiciona os pontos e o traço
        $cpf_formatado = substr($cpf, 0, 3) . '.';
        $cpf_formatado .= substr($cpf, 3, 3) . '.';
        $cpf_formatado .= substr($cpf, 6, 3) . '-';
        $cpf_formatado .= substr($cpf, 9, 2);

        return $cpf_formatado;
    }

}
