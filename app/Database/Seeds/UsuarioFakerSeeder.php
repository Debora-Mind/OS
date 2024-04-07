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

        $criarQuantosUsuarios = 10000;

        $usuariosPush[] = [
            'nome' => 'admin',
            'email' => 'admin@admin.com',
            //Senha: Admin123
            'password_hash' => '$2y$10$tGqcJVWZZWt/9UQ1rUbOBuC.zV9blh3wZyvsU6qpfuFgutnoNIeMO',
            'ativo' => 1,
        ];

        for ($i = 0; $i < $criarQuantosUsuarios; $i++){
            $usuariosPush[] = [
                'nome' => $faker->unique()->name,
                'email' => $faker->unique()->email,
                //Senha: 123456
                'password_hash' => '$2y$10$IoGLL/k7xUiLr/rT6opP6eHGBD9dXdOTWOhl3NIHU3VQjjWMOBj1u',
                'ativo' => $faker->numberBetween(0, 1),
            ];
        }

        $usuarioModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($usuariosPush);


    echo "Usuário admin criados com sucesso\n";
    echo $criarQuantosUsuarios . "usuários criados com sucesso\n";
    }
}
