<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_type' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_user_source' => ['type' => 'INT', 'constraint' => 11],
            'id_user_destination' => ['type' => 'INT', 'constraint' => 11],
            'montant' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'frais' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'date_creation' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_type');
        $this->forge->addKey('id_user_source');
        $this->forge->addKey('id_user_destination');

        $this->forge->addForeignKey('id_type', 'type', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_user_source', 'user', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_user_destination', 'user', 'id', 'RESTRICT', 'RESTRICT');

        $this->forge->createTable('operation');
    }

    public function down()
    {
        $this->forge->dropTable('operation');
    }
}
