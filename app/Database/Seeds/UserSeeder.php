<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'numero_telephone' => '320123456',
                'solde'            => 5000.00
            ],
            [
                'numero_telephone' => '330987654',
                'solde'            => 10000.00
            ],
            [
                'numero_telephone' => '341234567',
                'solde'            => 2500.50
            ],
            [
                'numero_telephone' => '370555666',
                'solde'            => 7800.00
            ],
            [
                'numero_telephone' => '381111222',
                'solde'            => 1500.25
            ],
        ];

        $this->db->table('user')->insertBatch($data);
    }
}
