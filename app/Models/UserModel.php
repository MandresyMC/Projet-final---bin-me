<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'numero_telephone',
        'solde'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'numero_telephone' => 'required|exact_length[9]',
    ];

    protected $validationMessages = [
        'numero_telephone' => [
            'required' => 'Le numéro de téléphone est obligatoire.',
            'exact_length' => 'Le numéro de téléphone doit contenir exactement 9 chiffres.'
        ]
    ];

    public function getValidationRulesArray(): array
    {
        return $this->validationRules;
    }

    public function getValidationMessagesArray(): array
    {
        return $this->validationMessages;
    }
}