<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StartSeeder extends Seeder
{
    public function run()
    {
        $this->call('GrupoSeeder');
    }
}
