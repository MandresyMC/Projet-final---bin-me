<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperationModel;
use App\Models\UserModel;

class AdminDashboardController extends BaseController
{
    protected $operationModel;
    protected $userModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $db = $this->operationModel->db;

        $nombreUtilisateurs = $this->userModel->countAll();

        $totauxOperations = $db->query(
            "SELECT COUNT(*) as nombre, COALESCE(SUM(montant), 0) as volume, COALESCE(SUM(frais), 0) as gains
             FROM operation"
        )->getRowArray();

        $nombreEchecs = $db->query(
            "SELECT COUNT(*) as nombre FROM operation WHERE statut = 'ECHEC'"
        )->getRowArray();

        $tauxEchec = $totauxOperations['nombre'] > 0
            ? round(($nombreEchecs['nombre'] / $totauxOperations['nombre']) * 100, 1)
            : 0;

        // evolution des 6 derniers mois (nombre d'operations par mois)
        $evolution = $db->query(
            "SELECT strftime('%Y-%m', date_creation) as mois, COUNT(*) as nombre
             FROM operation
             GROUP BY mois
             ORDER BY mois ASC
             LIMIT 6"
        )->getResultArray();

        // repartition par type d'operation
        $repartition = $db->query(
            "SELECT t.nom as type_nom, COUNT(o.id) as nombre
             FROM operation o
             JOIN type t ON o.id_type = t.id
             GROUP BY t.id, t.nom"
        )->getResultArray();

        $gainsParType = $db->query(
            "SELECT t.nom as type_nom, COUNT(o.id) as nombre_operation, SUM(o.frais) as total_gain
             FROM operation o
             JOIN type t ON o.id_type = t.id
             WHERE t.nom IN ('retrait', 'transfert')
             GROUP BY t.id, t.nom
             ORDER BY total_gain DESC"
        )->getResultArray();

        // Montant de commission pris par chaque operateur non proprietaire (transferts externes)
        $commissionParOperateur = $db->query(
            "SELECT op.nom as operateur_nom,
                    ROUND(SUM(o.montant * o.pourcentage_commission / 100), 2) as montant_commission
             FROM operation o
             JOIN operateur op ON o.id_operateur = op.id
             WHERE o.pourcentage_commission > 0
             GROUP BY op.id, op.nom
             ORDER BY montant_commission DESC"
        )->getResultArray();

        // Repartition du montant transfere vers chaque operateur externe
        $repartitionParOperateur = $db->query(
            "SELECT op.nom as operateur_nom, SUM(o.montant) as volume, COUNT(o.id) as nombre
             FROM operation o
             JOIN operateur op ON o.id_operateur = op.id
             WHERE o.id_operateur IS NOT NULL
             GROUP BY op.id, op.nom
             ORDER BY volume DESC"
        )->getResultArray();

        return view('admin/dashboard', [
            'nombreUtilisateurs'      => $nombreUtilisateurs,
            'volumeTotal'             => $totauxOperations['volume'],
            'nombreOperations'        => $totauxOperations['nombre'],
            'gainsTotal'              => $totauxOperations['gains'],
            'tauxEchec'               => $tauxEchec,
            'evolution'               => $evolution,
            'repartition'             => $repartition,
            'gains'                   => $gainsParType,
            'commissionParOperateur'  => $commissionParOperateur,
            'repartitionParOperateur' => $repartitionParOperateur,
        ]);
    }
}