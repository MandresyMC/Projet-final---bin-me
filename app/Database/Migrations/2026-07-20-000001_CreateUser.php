<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'numero_telephone' => ['type' => 'VARCHAR', 'constraint' => 100],
            'solde' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('numero_telephone', true);
        $this->forge->createTable('user');
    }

    public function down()
    {
        $this->forge->dropTable('user');
    }
}
