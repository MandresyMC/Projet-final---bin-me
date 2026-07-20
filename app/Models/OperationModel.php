<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_type',
        'id_user_source',
        'id_user_destination',
        'montant',
        'frais',
        'date_creation',
    ];

    protected $useTimestamps = false;
}