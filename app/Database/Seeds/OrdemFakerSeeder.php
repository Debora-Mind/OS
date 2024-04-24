<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use App\Models\OrdemModel;
use App\Models\OrdemResponsavelModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;

class OrdemFakerSeeder extends Seeder
{
    public function run()
    {
        $clienteModel = new ClienteModel();
        $ordemModel = new OrdemModel();
        $ordemResponsavelModel = new OrdemResponsavelModel();

        $clientes = $clienteModel->select('id')->findAll();

        $clientesIDs = array_column($clientes, 'id');

        $faker = Factory::create('pt-BR');

        helper('text');

        for ($i = 0; $i < count($clientesIDs); $i ++) {
            $ordem = [
                'cliente_id' => $faker->randomElement($clientesIDs),
                'codigo' => $ordemModel->geraCodigoOrdem(),
                'situacao' => 'aberta',
                'equipamento' => $faker->name(),
                'defeito' => $faker->realText(),
            ];

            $ordemModel->skipValidation()->insert($ordem);

            $ordemResponsavel = [
                'ordem_id' => $ordemModel->getInsertID(),
                'usuario_abertura_id' => 1, // Admin
            ];

            $ordemResponsavelModel->skipValidation()->insert($ordemResponsavel);
        }

        echo count($clientesIDs) . " ordens criadas com sucesso.\n";
    }
}
