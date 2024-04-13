<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableFornecedores extends Migration
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
            'razao' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'unique' => true,
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => '14',
                'unique' => true,
            ],
            'ie' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'unique' => true,
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true,
            ],
            'endereco' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'bairro' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'cidade' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'cep' => [
                'type' => 'VARCHAR',
                'constraint' => '9',
            ],
            'ativo' => [
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

        $this->forge->createTable('fornecedores', true);
    }

    public function down()
    {
        $this->forge->dropTable('fornecedores');
    }
}
