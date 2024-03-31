<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableGrupos extends Migration
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
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'tecnico' => [
                'type' => 'BOOLEAN',
                'null' => false
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nome');

        $this->forge->createTable('grupos', true);
    }

    public function down()
    {
        $this->forge->dropTable('grupos');
    }
}
