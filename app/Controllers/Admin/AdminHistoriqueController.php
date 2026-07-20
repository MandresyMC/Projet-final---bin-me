<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperationModel;

class AdminHistoriqueController extends BaseController
{
    protected $operationModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
    }

    public function index()
    {
        $operations = $this->operationModel
            ->select("operation.*, type.nom as type_nom,
                src.numero_telephone as source_numero,
                COALESCE(dst.numero_telephone, operation.numero_destination) as destination_numero,
                operateur.nom as operateur_nom")
            ->join('type', 'operation.id_type = type.id')
            ->join('user as src', 'operation.id_user_source = src.id', 'left')
            ->join('user as dst', 'operation.id_user_destination = dst.id', 'left')
            ->join('operateur', 'operation.id_operateur = operateur.id', 'left')
            ->orderBy('operation.date_creation', 'DESC')
            ->findAll();

        foreach ($operations as &$op) {
            $op['montant_commission'] = round(((float) $op['montant']) * ((float) $op['pourcentage_commission']) / 100, 2);
            $op['frais_base'] = round(((float) $op['frais']) - $op['montant_commission'], 2);
        }
        unset($op);

        return view('admin/historique', ['operations' => $operations]);
    }
}
