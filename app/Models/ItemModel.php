<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'itens';
    protected $returnType = 'App\Entities\Item';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'codigo_interno',
        'nome',
        'marca',
        'modelo',
        'preco_custo',
        'preco_venda',
        'estoque',
        'tipo',
        'ativo',
        'descricao',
        'controla_estoque',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'nome' => 'required|max_length[128]|is_unique[itens.nome,id,{id}]',
        'preco_venda' => 'required',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
            'is_unique' => 'O Nome já está sendo utilizado por outro item.',
            'max_length' => 'O campo Nome não pode ser maior que 128 caractéres.',
        ],
        'preco_venda' => [
            'required' => 'O campo Preço de Venda é obrigatório.',
        ],
    ];

    protected $beforeInsert = ['removeVirgulaValores'];
    protected $beforeUpdate = ['removeVirgulaValores'];

    protected function removeVirgulaValores(array $data)
    {
        if (isset($data['data']['preco_custo'])) {
            $data['data']['preco_custo'] = str_replace('.', '', $data['data']['preco_custo']);
            $data['data']['preco_custo'] = str_replace(',', '.', $data['data']['preco_custo']);
        }
        if (isset($data['data']['preco_venda'])) {
            $data['data']['preco_venda'] = str_replace('.', '', $data['data']['preco_venda']);
            $data['data']['preco_venda'] = str_replace(',', '.', $data['data']['preco_venda']);
        }

        return $data;
    }

    public function geraCodigoInternoItem(): string
    {
        do {
            $codigoInterno = random_string('numeric', 15);
            $this->where('codigo_interno', $codigoInterno);

        } while ($this->countAllResults() > 1);

        return $codigoInterno;
    }
}
