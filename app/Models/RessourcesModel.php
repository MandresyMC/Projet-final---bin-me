<?php

namespace App\Models;

use CodeIgniter\Model;

class RessourcesModel extends Model
{
    protected $table = 'ressources';
    protected $primaryKey = 'id';

    // Champs autorisés pour insert/update
    protected $allowedFields = [
        'nom',
        'type',
        'capacite',
        'description'
    ];

    // (Optionnel mais conseillé)
    protected $useTimestamps = false;

    public function recupererToutes()
    {
        return $this->findAll();
    }

    public function ajouter(array $data)
    {
        return $this->insert($data);
    }

    public function supprimer($id)
    {
        return $this->delete($id);
    }

    public function modifier($id, array $data)
    {
        return $this->update($id, $data);
    }

    public function recupererParType($type)
    {
        return $this->where('type', $type)->findAll();
    }

    public function recupererTypes()
    {
        return $this->select('type')->distinct()->findAll();
    }
}