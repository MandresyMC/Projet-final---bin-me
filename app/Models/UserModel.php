<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    // Champs autorisés pour insert/update
    protected $allowedFields = [
        'nom',
        'email',
        'password'
    ];

    // (Optionnel mais conseillé)
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'nom' => 'required|min_length[3]|max_length[255]',
        'email' => 'required',
        'password' => 'required|min_length[8]'
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le prénom et nom sont obligatoire.',
            'min_length' => 'Votre nom doit contenir au moins 3 caractères.',
            'max_length' => 'Votre nom ne doit pas dépasser 255 caractères.'
        ],
        'email' => [
            'required' => 'L\'email est obligatoire.'
        ],
        'password' => [
            'required' => 'Le mot de passe est obligatoire.',
            'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.'
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