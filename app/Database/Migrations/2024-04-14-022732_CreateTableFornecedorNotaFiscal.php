<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableFornecedorNotaFiscal extends Migration
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
            'fornecedor_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'valor_nota' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'descricao_itens' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'nota_fiscal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'data_emissao' => [
                'type' => 'DATE',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('fornecedor_id', 'fornecedores', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('fornecedores_notas_fiscais', true);
    }

    public function down()
    {
        $this->forge->dropTable('fornecedores_notas_fiscais');
    }
}
