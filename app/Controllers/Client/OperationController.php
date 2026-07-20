<?php

namespace App\Controllers;

use App\Models\OperationModel;
use App\Models\BaremeFraisModel;
use App\Models\TypeModel;
use App\Models\UserModel;

class OperationController extends BaseController
{
    protected $operationModel;
    protected $baremeFraisModel;
    protected $typeModel;
    protected $userModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->typeModel = new TypeModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('client/operation');
    }

    public function createOperation() {
        $this->verificationConnexion();
        
        $typeOperation = $this->request->getPost('type_operation');
        $numeroUserSource = $this->request->getPost('numero_user_source');
        $numeroUserDestination = $this->request->getPost('numero_user_destination');
        $montant = $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le montant doit être supérieur à zéro.');
        }

        try {
            $id_type = $this->typeModel->where('nom', $typeOperation)->first()['id'];

            $numeroUserSource = trim($numeroUserSource);
            $numeroUserSource = str_replace(' ', '', $numeroUserSource);
            $verification = $this->verifyNumeroTelephone($numeroUserSource);
            if ($verification !== true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $verification);
            }

            $numeroUserDestination = trim($numeroUserDestination);
            $numeroUserDestination = str_replace(' ', '', $numeroUserDestination);
            $verification = $this->verifyNumeroTelephone($numeroUserDestination);
            if ($verification !== true) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $verification);
            }

            $idUserSource = $this->userModel->where('numero_telephone', $numeroUserSource)->first()['id'];
            $idUserDestination = $this->userModel->where('numero_telephone', $numeroUserDestination)->first()['id'];

            if (!$idUserSource) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Utilisateur source introuvable.');
            }

            if (!$idUserDestination) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Utilisateur destination introuvable.');
            }

            $frais = 0.00;
            if ($typeOperation != 'depot') {
                $sql = "SELECT frais FROM bareme_frais WHERE ? BETWEEN montant_min AND montant_max";
                $frais = $this->operationModel->db->query($sql, [$montant])->getRowArray()['frais'];
            }

            $this->operationModel->insert([
                'id_type' => $id_type,
                'id_user_source' => $idUserSource,
                'id_user_destination' => $idUserDestination,
                'montant' => $montant,
                'frais' => $frais,
                'date_creation' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de l\'opération.');
        }
    }

    public function historiques() {
        $this->verificationConnexion();
        
        $userId = session()->get('user_id');

        $operations = $this->operationModel
            ->select('operation.*, type.nom as type_nom')
            ->join('type', 'operation.id_type = type.id')
            ->where('id_user_source', $userId)
            ->orWhere('id_user_destination', $userId)
            ->orderBy('date_creation', 'DESC')
            ->findAll();

        return view('client/historiques', ['operations' => $operations]);
    }
}