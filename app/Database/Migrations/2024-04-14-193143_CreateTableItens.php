<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableItens extends Migration
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
            'codigo_interno' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true,
            ],
            'marca' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'preco_custo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'preco_venda' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'estoque' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['produto', 'serviço'],
            ],
            'ativo' => [
                'type' => 'BOOLEAN',
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'controla_estoque' => [
                'type' => 'BOOLEAN',
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

        $this->forge->createTable('itens', true);
    }

    public function down()
    {
        $this->forge->dropTable('itens');
    }
}
