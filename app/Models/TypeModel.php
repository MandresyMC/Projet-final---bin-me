<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeModel extends Model
{
    protected $table = 'type';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nom',
    ];

    protected $useTimestamps = false;
}