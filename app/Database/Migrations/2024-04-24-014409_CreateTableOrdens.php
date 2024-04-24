<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableOrdens extends Migration
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
            'cliente_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'forma_pagamento' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ],
            'situacao' => [
                'type' => 'ENUM',
                'constraint' => ['aberta', 'encerrada', 'aguardando', 'cancelada', 'nao_pago'],
                'default' => 'aberta',
            ],
            'itens' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_produtos' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'null' => true,
            ],
            'valor_servicos' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'null' => true,
            ],
            'valor_desconto' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'null' => true,
            ],
            'valor_ordem' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'null' => true,
            ],
            'equipamento' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
            ],
            'defeito' => [
                'type' => 'TEXT',
                'constraint' => '500',
            ],
            'observacoes' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'parecer_tecnico' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
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
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('ordens', true);
    }

    public function down()
    {
        $this->forge->dropTable('ordens');
    }
}
