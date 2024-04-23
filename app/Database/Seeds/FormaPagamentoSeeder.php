<?php

namespace App\Database\Seeds;

use App\Models\FormaPagamentoModel;
use CodeIgniter\Database\Seeder;

class FormaPagamentoSeeder extends Seeder
{
    public function run()
    {
        $formaPagamentoModel = new FormaPagamentoModel();

        $formas = [
            [
                'nome' => 'Boleto bancário',
                'descricao' => 'Pagamento com boleto bancário da efí',
                'ativo' => true,
            ],
            [
                'nome' => 'Cortesia',
                'descricao' => 'Forma de pagamento destinada apenas às ordens que não geram valor',
                'ativo' => true,
            ],
            [
                'nome' => 'Cartão de crédito',
                'descricao' => 'Forma de pagamento com Cartão de Crédito. Trabalha com as bandeiras Master, Visa, ELO, etc.',
                'ativo' => true,
            ],
            [
                'nome' => 'Cartão de débito',
                'descricao' => 'Forma de pagamento com Cartão de Débito. Trabalha com as bandeiras Master, Visa, ELO, etc.',
                'ativo' => true,
            ],
            [
                'nome' => 'Mercado Pago',
                'descricao' => 'Aceita pagamentos através do Mercado Pago',
                'ativo' => true,
            ],
            [
                'nome' => 'Pix',
                'descricao' => 'Aceita pagamentos através do PIX cadastrado',
                'ativo' => true,
            ],
        ];

        foreach ($formas as $forma) {
            $formaPagamentoModel->skipValidation()->protect(false)->insert($forma);
        }

        echo "Formas de pagamentos criadas com sucesso!\n";
    }
}
