<?php

namespace App\Database\Seeds;

use App\Models\PermissaoModel;
use CodeIgniter\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
        $permissaoModel = new PermissaoModel();

        $permissoes = [
            [
                'nome' => 'listar_usuarios',
            ],
            [
                'nome' => 'criar_usuarios',
            ],
            [
                'nome' => 'editar_usuarios',
            ],
            [
                'nome' => 'excluir_usuarios',
            ],
        ];

        $permissaoModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($permissoes);

        echo "Permissões criados com sucesso.\n";
    }
}
