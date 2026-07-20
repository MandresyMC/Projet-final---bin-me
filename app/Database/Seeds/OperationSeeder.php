<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_type'              => 1,
                'id_user_source'       => null,
                'id_user_destination'  => 1,
                'montant'              => 500,
                'frais'                => 0,
                'date_creation'        => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'id_type'              => 2,
                'id_user_source'       => 1,
                'id_user_destination'  => null,
                'montant'              => 1000,
                'frais'                => 0,
                'date_creation'        => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'id_type'              => 3,
                'id_user_source'       => 1,
                'id_user_destination'  => 2,
                'montant'              => 250,
                'frais'                => 50,
                'date_creation'        => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'id_type'              => 1,
                'id_user_source'       => null,
                'id_user_destination'  => 2,
                'montant'              => 750,
                'frais'                => 0,
                'date_creation'        => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'id_type'              => 3,
                'id_user_source'       => 2,
                'id_user_destination'  => 1,
                'montant'              => 150,
                'frais'                => 50,
                'date_creation'        => date('Y-m-d H:i:s', strtotime('-1 days'))
            ],
        ];

        $this->db->table('operation')->insertBatch($data);
    }
}
