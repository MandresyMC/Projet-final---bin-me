<?php

namespace App\Controllers;

use App\Models\OperationModel;

class AdminSituationClientController extends BaseController
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
                u.id,
                u.numero_telephone,
                u.solde,
                COALESCE(SUM(o.montant), 0) AS total_operations
            FROM user u
            LEFT JOIN operation o ON o.id_user_source = u.id
            GROUP BY u.id;
        ";

        $gains = $this->operationModel->query($sql)->getResult();

        return view('admin/dashboard', ['gains' => $gains]);
    }

}
