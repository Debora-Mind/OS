<?php

namespace App\Database\Seeds;

use App\Models\GrupoModel;
use CodeIgniter\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new GrupoModel();

        $grupos[] = [
            'nome' => 'Administrador',
            'descricao' => 'Grupo com acesso total ao sistema.',
            'tecnico' => false,
        ];

        $grupos[] = [
            'nome' => 'Técnico',
            'descricao' => 'Grupo para técnicos.',
            'tecnico' => true,
        ];

        $grupos[] = [
            'nome' => 'Cliente',
            'descricao' => 'Grupo com acesso mínimo ao sistema.',
            'tecnico' => false,
        ];

        $grupoModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($grupos);
    }
}
