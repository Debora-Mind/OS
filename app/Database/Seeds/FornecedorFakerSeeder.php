<?php

namespace App\Database\Seeds;

use App\Models\FornecedorModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\PhoneNumber;

class FornecedorFakerSeeder extends Seeder
{
    public function run()
    {
        $fornecedorModel = new FornecedorModel();

        $faker = Factory::create('pt-BR');
        $faker->addProvider(new Company($faker));
        $faker->addProvider(new PhoneNumber($faker));

        $criarQuantosFornecedores = 2000;

        $fornecedoresPush = [];

        for ($i = 0; $i < $criarQuantosFornecedores; $i++) {
            $fornecedoresPush[] = [
                'razao' => $faker->unique()->company,
                'cnpj' => str_replace(['.', '/', '-'], '', $faker->unique()->cnpj),
                'ie' => $faker->unique()->numberBetween(1000000, 9000000),
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
                'ativo' => $faker->numberBetween(0, 1),
                'created_at' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
            ];
        }

        $fornecedorModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($fornecedoresPush);

        echo $criarQuantosFornecedores . " fornecedores criados com sucesso\n";
    }
}
