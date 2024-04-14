<?php

namespace App\Database\Seeds;

use App\Models\ItemModel;
use CodeIgniter\Database\Seeder;
use Faker\Factory;
use Faker\Provider\pt_BR\Person;

class ItemFakerSeeder extends Seeder
{
    public function run()
    {
        $itemModel = new ItemModel();

        $faker = Factory::create('pt-BR');
        $faker->addProvider(new Person($faker));

        helper('text');

        $criarQuantosItens = 5000;

        $itensPush = [];

        for ($i = 0; $i < $criarQuantosItens; $i++) {

            $tipo = $faker->randomElement($array = array('produto', 'serviço'));
            $controlaEstoque = $faker->numberBetween(0, 1);
            $itensPush[] = [
                'codigo_interno' => $itemModel->geraCodigoInternoItem(),
                'nome' => $faker->unique()->words(3, true),
                'marca' => $tipo === 'produto' ? $faker->word : null,
                'modelo' => $tipo === 'produto' ? $faker->unique()->words(2, true) : null,
                'preco_custo' => $faker->randomFloat(2, 10, 100),
                'preco_venda' => $faker->randomFloat(2, 101, 1000),
                'estoque' => $tipo === 'produto' ? $faker->randomDigitNotZero() : null,
                'controla_estoque' => $tipo === 'produto' ? $controlaEstoque : null,
                'tipo' => $tipo,
                'ativo' => $faker->numberBetween(0, 1),
                'descricao' => $faker->text(300),
                'created_at' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
                'updated_at' => $faker->dateTimeBetween('-2 month', '-1 days')->format('Y-m-d H:i:s'),
            ];
        }

        $itemModel->skipValidation(true)
            ->insertBatch($itensPush);

        echo $criarQuantosItens . " itens criados com sucesso\n";
    }
}
