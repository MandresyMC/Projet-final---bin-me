<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\OperationModel;
use App\Models\BaremeFraisModel;
use App\Models\CommissionModel;
use App\Models\TypeModel;
use App\Models\UserModel;

class OperationController extends BaseController
{
    protected $operationModel;
    protected $baremeFraisModel;
    protected $commissionModel;
    protected $typeModel;
    protected $userModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->commissionModel = new CommissionModel();
        $this->typeModel = new TypeModel();
        $this->userModel = new UserModel();
    }

    /**
     * Page "Choisissez une operation" (depot / retrait / transfert).
     */
    public function index()
    {
        if ($r = $this->verificationConnexion()) {
            return $r;
        }

        return view('client/operation');
    }

    /**
     * Pages individuelles depot / retrait / transfert.
     */
    public function formulaire($type)
    {
        if ($r = $this->verificationConnexion()) {
            return $r;
        }

        $type = strtolower($type);

        $titres = [
            'depot'     => "DEPOT D'ARGENT",
            'retrait'   => "RETRAIT D'ARGENT",
            'transfert' => "TRANSFERT D'ARGENT",
        ];

        $descriptions = [
            'depot'     => "Déposez de l'argent gratuitement dans votre compte MVola auprès de notre réseau de PoP MVola, Cash Point et Yas Store, partout à Madagascar.",
            'retrait'   => "Retirez votre argent facilement auprès des PoP MVola, Yas Store, Cash Point ou auprès des DAB de la banque BRED Madagasikara BP dans toute la Grande Île.",
            'transfert' => "Envoyez de l'argent instantanément à un autre numéro MVola, partout à Madagascar.",
        ];

        if (!isset($titres[$type])) {
            return redirect()->to('/client/operation');
        }

        $user = $this->userModel->find(session()->get('user_id'));

        return view('client/transaction_form', [
            'type'        => $type,
            'title'       => $titres[$type],
            'description' => $descriptions[$type],
            'solde'       => $user['solde'] ?? 0,
        ]);
    }

    /**
     * Calcule les frais applicables pour un type et un montant donnés.
     */
    private function calculerFrais(int $idType, float $montant): float
    {
        $sql = "SELECT frais FROM bareme_frais WHERE id_type = ? AND ? BETWEEN montant_min AND montant_max LIMIT 1";
        $row = $this->operationModel->db->query($sql, [$idType, $montant])->getRowArray();

        return $row['frais'] ?? 0.00;
    }

    public function createOperation()
    {
        if ($r = $this->verificationConnexion()) {
            return $r;
        }

        $typeOperation = strtolower(trim((string) $this->request->getPost('type_operation')));
        $montant = (float) $this->request->getPost('montant');
        $numerosDestination = $this->request->getPost('numero_user_destination') ?? [];
        $nbDestinations = count($numerosDestination);
        $numerosDestination = array_map(function ($numero) {
            return str_replace(' ', '', trim($numero));
        }, $numerosDestination);

        if (!in_array($typeOperation, ['depot', 'retrait', 'transfert'], true)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Type d'opération invalide.");
        }

        if ($montant <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le montant doit être supérieur à zéro.');
        }

        $type = $this->typeModel->where('nom', $typeOperation)->first();
        if (!$type) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Type d'opération introuvable.");
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'Utilisateur introuvable, merci de vous reconnecter.');
        }

        $idUserSource = null;
        $idUserDestination = null;
        $idOperateurDestination = null;
        $frais = 0.00;
        $pourcentageCommission = 0.00;

        if ($typeOperation === 'depot') {
            $idUserDestination = $userId;
        } elseif ($typeOperation === 'retrait') {
            $idUserSource = $userId;
            $frais = $this->calculerFrais($type['id'], $montant);
        } else {
            // Le destinataire peut appartenir a n'importe quel operateur configure
            // (Yas, ou un autre operateur), avec ou sans compte MVola.
            foreach ($numerosDestination as $numeroDestination) {
                $verification = $this->verifyNumeroTelephone($numeroDestination, false);
                if ($verification !== true) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $verification);
                }

                if ($numeroDestination === $user['numero_telephone']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Vous ne pouvez pas vous transférer de l\'argent à vous-même.');
                }

                $prefixeInfo = $this->getPrefixeInfo($numeroDestination);
                $destinataire = $this->userModel->where('numero_telephone', $numeroDestination)->first();

                $idUserSource = $userId;
                $frais = $this->calculerFrais($type['id'], $montant);

                if ($destinataire) {
                    // Compte MVola existant (necessairement un numero de l'operateur proprietaire)
                    $idUserDestination[] = $destinataire['id'];
                } else {
                    // Transfert externe : numero valide mais sans compte MVola (autre operateur, ou Yas sans compte)
                    $idOperateurDestination = (int) $prefixeInfo['id_operateur'];

                    if (strtolower($prefixeInfo['proprietaire_nom']) !== 'local') {
                        $pourcentageCommission = $this->commissionModel->getPourcentagePourOperateur($idOperateurDestination);
                        $frais += round($montant * $pourcentageCommission / 100, 2);
                    }
                }
            }
        }

        if ($idUserSource !== null && $user['solde'] < ($montant + $frais)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant pour effectuer cette opération.');
        }

        $db = $this->operationModel->db;
        $db->transStart();

        if ($idUserSource !== null) {
            $this->userModel->update($idUserSource, [
                'solde' => $user['solde'] - $montant - $frais,
            ]);
        }

        foreach ($idUserDestination as $id) {
            $destinataireActuel = $this->userModel->find($id);
            $montantParDestinataire = $montant / $nbDestinations;
            $this->userModel->update($id, [
                'solde' => $destinataireActuel['solde'] + $montantParDestinataire,
            ]);
        

            $this->operationModel->insert([
                'id_type'                => $type['id'],
                'id_user_source'         => $idUserSource,
                'id_user_destination'    => $idUserDestination,
                'id_operateur'           => $idOperateurDestination,
                'montant'                => $montantParDestinataire,
                'frais'                  => $frais/$nbDestinations,
                'pourcentage_commission' => $pourcentageCommission,
                'date_creation'          => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Une erreur est survenue lors de l'opération. Merci de réessayer.");
        }

        $userMisAJour = $this->userModel->find($userId);
        session()->set('solde', $userMisAJour['solde']);

        return redirect()->to('/client/accueil')->with('success', 'Opération effectuée avec succès.');
    }

    public function historiques()
    {
        if ($r = $this->verificationConnexion()) {
            return $r;
        }

        $userId = session()->get('user_id');

        $operations = $this->operationModel
            ->select('operation.*, type.nom as type_nom, src.numero_telephone as source_numero, dst.numero_telephone as destination_numero')
            ->join('type', 'operation.id_type = type.id')
            ->join('user as src', 'operation.id_user_source = src.id', 'left')
            ->join('user as dst', 'operation.id_user_destination = dst.id', 'left')
            ->where('id_user_source', $userId)
            ->orWhere('id_user_destination', $userId)
            ->orderBy('date_creation', 'DESC')
            ->findAll();

        return view('client/historique', ['operations' => $operations]);
    }
}