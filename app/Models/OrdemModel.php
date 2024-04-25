<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
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
            'ordens.deleted_at',
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

    public function buscaOrdemOu404(string $codigo)
    {
        if ($codigo === null) {
            return PageNotFoundException::forPageNotFound("Não encontramos a ordem $codigo");
        }

        $atributos = [
            'ordens.*',
            'u_aber.id AS usuario_abertura_id',
            'u_aber.nome AS usuario_abertura',
            'u_resp.id AS usuario_responsavel_id',
            'u_resp.nome AS usuario_responsavel',
            'u_ence.id AS usuario_encerrameto_id',
            'u_ence.nome AS usuario_encerrameto_id',
            'clientes.usuario_id AS cliente_usuario_id',
            'clientes.nome',
            'clientes.cpf',
            'clientes.telefone',
            'clientes.email',
        ];

        $ordem = $this
            ->select($atributos)
            ->join('ordens_responsaveis', 'ordens_responsaveis.ordem_id = ordens.id')
            ->join('clientes', 'clientes.id = ordens.cliente_id')
            ->join('usuarios AS u_cliente', 'u_cliente.id = clientes.usuario_id')
            ->join('usuarios AS u_aber', 'u_aber.id = ordens_responsaveis.usuario_abertura_id')
            ->join('usuarios AS u_resp', 'u_resp.id = ordens_responsaveis.usuario_responsavel_id', 'LEFT')
            ->join('usuarios AS u_ence', 'u_ence.id = ordens_responsaveis.usuario_encerramento_id', 'LEFT')
            ->where('ordens.codigo', $codigo)
            ->withDeleted()
            ->first();

        if ($ordem === null) {
            return PageNotFoundException::forPageNotFound("Não encontramos a ordem $codigo");
        }

        return $ordem;
    }
}
