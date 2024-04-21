<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

class ContaPagarModel extends Model
{
    protected $table            = 'contas_pagar';
    protected $returnType       = 'App\Entities\ContaPagar';
    protected $allowedFields    = [
        'fornecedor_id',
        'valor_conta',
        'data_vencimento',
        'descricao_conta',
        'situacao',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'fornecedor_id' => 'required',
        'valor_conta' => 'required|greater_than[0]',
        'data_vencimento' => 'required',
        'descricao_conta' => 'required',
    ];
    protected $validationMessages = [
        'fornecedor_id' => [
            'required' => 'O campo Fornecedor é obrigatório.',
        ],
        'valor_conta' => [
            'required' => 'O campo Valor da Conta é obrigatório.',
            'greater_than' => 'O Valor da Conta deve ser maior do que 0.',
        ],
        'data_vencimento' => [
            'required' => 'O campo Data de Vencimento é obrigatório.',
        ],
        'descricao_conta' => [
            'required' => 'O campo Descrição da Conta é obrigatório.',
        ],
    ];

    //Callbacks
    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    protected function removeVirgulaValores(array $data)
    {
        if (isset($data['data']['valor_conta'])) {
            $data['data']['valor_conta'] = str_replace('.', '', $data['data']['valor_conta']);
            $data['data']['valor_conta'] = str_replace(',', '.', $data['data']['valor_conta']);
        }

        return $data;
    }

    public function recuperaContasPagar()
    {
        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*'
        ];
        
        return $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->orderBy('contas_pagar.situacao', 'ASC')
            ->findAll();
    }

    public function buscaContasOu404(int $id = null)
    {
        if ($id === null) {
            throw PageNotFoundException::forPageNotFound("Não encontramos a conta a pagar $id");
        }

        $atributos = [
            'fornecedores.razao',
            'fornecedores.cnpj',
            'contas_pagar.*'
        ];

        $conta = $this->select($atributos)
            ->join('fornecedores', 'fornecedores.id = contas_pagar.fornecedor_id')
            ->find($id);

        if ($conta === null) {
            throw PageNotFoundException::forPageNotFound("Não encontramos a conta a pagar $id");
        }

        return $conta;
    }
}
