<?php

namespace App\Database\Seeds;

use App\Models\GrupoModel;
use CodeIgniter\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new GrupoModel();
        $currentTimestamp = date('Y-m-d H:i:s');

        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema.',
                'tecnico' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                ],
            [
                'nome' => 'Técnico',
                'descricao' => 'Grupo para técnicos.',
                'tecnico' => true,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ],
            [
                'nome' => 'Atendentes',
                'descricao' => 'Esse grupo acessa o sistema para realizar atendimento aos clientes.',
                'tecnico' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ],
            [
                'nome' => 'Clientes',
                'descricao' => 'Esse grupo é destinado para atribuição de clientes pois os mesmos poderão logar no sistema para acessar as suas ordens de serviços.',
                'tecnico' => false,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
            ]
        ];

        $grupoModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($grupos);

        echo "Grupos criados com sucesso.\n";
    }
}
