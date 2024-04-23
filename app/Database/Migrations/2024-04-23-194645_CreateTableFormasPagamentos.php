<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableFormasPagamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'unique' => true,
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'ativo' => [
                'type' => 'BOOLEAN',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('formas_pagamentos', true);
    }

    public function down()
    {
        $this->forge->dropTable('formas_pagamentos');
    }
}
