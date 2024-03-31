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

        $grupos[] = [
            'nome' => 'Administrador',
            'descricao' => 'Grupo com acesso total ao sistema.',
            'tecnico' => false,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ];

        $grupos[] = [
            'nome' => 'Técnico',
            'descricao' => 'Grupo para técnicos.',
            'tecnico' => true,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ];

        $grupos[] = [
            'nome' => 'Cliente',
            'descricao' => 'Grupo com acesso mínimo ao sistema.',
            'tecnico' => false,
            'created_at' => $currentTimestamp,
            'updated_at' => $currentTimestamp,
        ];

        $grupoModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($grupos);
    }
}
