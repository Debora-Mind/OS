<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableItensHistorico extends Migration
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
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'acao' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'atributos_alterados' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'itens', 'id', 'CASCADE', 'CASCADE');


        $this->forge->createTable('itens_historico', true);
    }

    public function down()
    {
        $this->forge->dropTable('itens_historico');
    }
}
