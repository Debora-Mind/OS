<?php

namespace App\Database\Seeds;

use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new UsuarioModel();
        $grupoUsuarioModel = new GrupoUsuarioModel();

        $usuario = [
            'nome' => 'admin',
            'email' => 'admin@admin.com',
            'password_hash' => 'Admin123',
            'ativo' => 1,
        ];

        $usuarioModel->skipValidation()
            ->protect(false)
            ->insert($usuario);

        echo "Usuário admin criado com sucesso\n";

        $grupoUsuario = [
            'grupo_id' => 1, // 1 = Administrador
            'usuario_id' => $usuarioModel->getInsertID(),
        ];

        $grupoUsuarioModel->skipValidation()
            ->protect(false)
            ->insert($grupoUsuario);

        echo "Usuário admin inserido no grupo Administrador com sucesso\n";
    }
}
