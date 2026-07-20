<?php

namespace App\Controllers;

use App\Models\TypeModel;

class BaremeFraisController extends BaseController
{
    protected $typeModel;

    public function __construct()
    {
        $this->typeModel = new TypeModel();
    }

    public function index()
    {
        $types = $this->typeModel->findAll();
        return view('admin/type', ['types' => $types]);
    }

    public function createBaremeFrais()
    {
        $this->verificationConnexion();

        $id_type = $this->request->getPost('id_type');
        $montant_min = $this->request->getPost('montant_min');
        $montant_max = $this->request->getPost('montant_max');
        $frais = $this->request->getPost('frais');

        if ($montant_min >= $montant_max) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le montant minimum doit être inférieur au montant maximum.');
        }

        $data = [
            'id_type' => $id_type,
            'montant_min' => $montant_min,
            'montant_max' => $montant_max,
            'frais' => $frais,
        ];

        $this->typeModel->insert($data);

        return redirect()->to('/admin/bareme-frais')->with('success', 'Bareme de frais créé avec succès.');
    }
}