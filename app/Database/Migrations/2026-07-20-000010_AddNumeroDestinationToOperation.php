<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNumeroDestinationToOperationTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('operation', [
            'numero_destination' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'id_operateur',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('operation', ['numero_destination']);
    }
}
