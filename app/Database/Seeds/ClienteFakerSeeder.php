<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use App\Models\GrupoUsuarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;

class ClienteFakerSeeder extends Seeder
{
    public function run()
    {
        $clienteModel = new ClienteModel();
        $usuarioModel = new UsuarioModel();
        $grupoUsuarioModel = new GrupoUsuarioModel();

        $faker = Factory::create('pt-BR');
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new PhoneNumber($faker));

        $criarQuantosClientes = 1000;

        for ($i = 0; $i < $criarQuantosClientes; $i++) {
            $nomeGerado = $faker->unique()->name;
            $emailGerado = $faker->unique()->email;

            $cliente = [
                'nome' => $nomeGerado,
                'cpf' => str_replace(['.', '-'], '', $faker->unique()->cpf),
                'email' => $emailGerado,
                'telefone' => str_replace([' ', '(', ')', '-'], '', $faker->unique()->cellphoneNumber),
                'endereco' => $faker->streetName,
                'numero' => $faker->buildingNumber,
                'bairro' => $faker->city,
                'cidade' => $faker->city,
                'estado' => $faker->stateAbbr,
                'cep' =>
                    str_pad(
                        substr(
                            str_replace('-', '', $faker->postcode())
                            , 0, 8),
                        8, '0', STR_PAD_RIGHT),
            ];

            $clienteModel->skipValidation()
                ->protect(false)
                ->insert($cliente);
            
            $usuario = [
                'nome' => $nomeGerado,
                'email' => $emailGerado,
                'password' => '123456',
                'ativo' => true,
            ];
            
            $usuarioModel->skipValidation()->protect(false)->insert($usuario);

            $grupoUsuario = [
                'grupo_id' => 2,
                'usuario_id' => $usuarioModel->getInsertID(),
            ];

            $grupoUsuarioModel->protect(false)->insert($grupoUsuario);

            $clienteModel
                ->protect(false)
                ->where('id', $clienteModel->getInsertID())
                ->set('usuario_id', $usuarioModel->getInsertID())
                ->update();
        }

        echo $criarQuantosClientes . " clientes e usuários criados com sucesso\n";
    }
}
