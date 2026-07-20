<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        $autres = $this->db->table('operateur')
            ->select('operateur.id')
            ->join('proprietaire', 'proprietaire.id = operateur.id_proprietaire')
            ->where('proprietaire.nom', 'Autres')
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($autres as $operateur) {
            $data[] = [
                'id_operateur'  => $operateur['id'],
                'pourcentage'   => 1.00,
                'date_creation' => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($data)) {
            $this->db->table('commission')->insertBatch($data);
        }
    }
}
