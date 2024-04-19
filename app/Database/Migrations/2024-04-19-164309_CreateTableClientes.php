<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableClientes extends Migration
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
                'null' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
            ],
            'cpf' => [
                'type' => 'VARCHAR',
                'constraint' => '11',
                'unique' => true,
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
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
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id');

        $this->forge->createTable('clientes', true);
    }

    public function down()
    {
        $this->forge->dropTable('clientes');
    }
}
