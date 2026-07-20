<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RessourcesSeeder extends Seeder
{
    public function run(): void
    {
        $datas = [

            [
                'nom' => 'Yoga detente',

                'type' => 'COURS',

                'capacite' => '30',

                'description' => 'Salle Zen · 2e étage'
            ],

            [
                'nom' => 'Salle musculation',

                'type' => 'SALLE',

                'capacite' => '25',

                'description' => 'Bloc Muscu · 1er étage'
            ]

        ];

        $this->db
            ->table('ressources')
            ->insertBatch($datas);
    }
}