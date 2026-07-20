<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    // Champs autorisés pour insert/update
    protected $allowedFields = [
        'numero_telephone',
        'solde'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'numero_telephone' => 'required|min_length[10]|max_length[15]',
    ];

    protected $validationMessages = [
        'numero_telephone' => [
            'required' => 'Le numéro de téléphone est obligatoire.',
            'min_length' => 'Le numéro de téléphone doit contenir au moins 10 caractères.',
            'max_length' => 'Le numéro de téléphone ne doit pas dépasser 15 caractères.'
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