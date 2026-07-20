<?php

namespace App\Controllers;

use App\Models\OperationModel;

class AdminDashboardController extends BaseController
{
    protected $operationModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
    }

    public function index()
    {
        $sql = "
            SELECT 
                t.nom AS type_nom,
                COUNT(o.id) AS nombre_operation,
                SUM(o.frais) AS total_gain
            FROM operation o
            JOIN type t ON o.id_type = t.id
            WHERE o.id_type IN (2, 3)
            GROUP BY t.id, t.nom
            ORDER BY total_gain DESC
        ";

        $gains = $this->operationModel->query($sql)->getResult();

        return view('admin/dashboard', ['gains' => $gains]);
    }

}