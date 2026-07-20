<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id',
        'id_type',
        'id_user_source',
        'id_user_destination',
        'id_operateur',
        'numero_destination',
        'montant',
        'frais',
        'pourcentage_commission',
        'date_creation',
    ];

    protected $useTimestamps = false;
}