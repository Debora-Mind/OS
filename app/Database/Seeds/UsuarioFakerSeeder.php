<?php

namespace App\Database\Seeds;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class UsuarioFakerSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new UsuarioModel();

        $faker = Factory::create();

        $criarQuantosUsuarios = 100000;

        $usuariosPush = [];

        for ($i = 0; $i < $criarQuantosUsuarios; $i++){
            $usuariosPush[] = [
                'nome' => $faker->unique()->name,
                'email' => $faker->unique()->email,
                'password_hash' => '123456',
                'ativo' => $faker->numberBetween(0, 1),
            ];
        }

        $usuarioModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($usuariosPush);

    echo $criarQuantosUsuarios . "usuários criados com sucesso";

    }
}
