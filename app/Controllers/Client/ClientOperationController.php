<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\OperationModel;
use App\Models\BaremeFraisModel;
use App\Models\CommissionModel;
use App\Models\TypeModel;
use App\Models\UserModel;

class ClientOperationController extends BaseController
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

    /**
     * Normalise le(s) numero(s) destinataire poste(s) par le formulaire.
     * Le champ transfert est un tableau (numero_user_destination[]) pour permettre
     * l'envoi vers plusieurs numeros en une seule soumission.
     *
     * @return string[]
     */
    private function extraireNumerosDestination(): array
    {
        $brut = $this->request->getPost('numero_user_destination');

        if (!is_array($brut)) {
            $brut = $brut === null ? [] : [$brut];
        }

        $numeros = [];
        foreach ($brut as $numero) {
            $numero = str_replace(' ', '', trim((string) $numero));
            if ($numero !== '') {
                $numeros[] = $numero;
            }
        }

        return array_values(array_unique($numeros));
    }

    public function createOperation()
    {
        if ($r = $this->verificationConnexion()) {
            return $r;
        }

        $typeOperation = strtolower(trim((string) $this->request->getPost('type_operation')));
        $montant = (float) $this->request->getPost('montant');

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

        if ($typeOperation !== 'transfert') {
            return $this->executerOperationSimple($type, $typeOperation, $montant, $userId, $user);
        }

        return $this->executerTransferts($type, $montant, $userId, $user);
    }

    private function executerOperationSimple(array $type, string $typeOperation, float $montant, int $userId, array $user)
    {
        $idUserSource = null;
        $idUserDestination = null;
        $frais = 0.00;

        if ($typeOperation === 'depot') {
            $idUserDestination = $userId;
        } else {
            $idUserSource = $userId;
            $frais = $this->calculerFrais($type['id'], $montant);
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

        if ($idUserDestination !== null) {
            $destinataireActuel = $this->userModel->find($idUserDestination);
            $this->userModel->update($idUserDestination, [
                'solde' => $destinataireActuel['solde'] + $montant,
            ]);
        }

        $this->operationModel->insert([
            'id_type'                => $type['id'],
            'id_user_source'         => $idUserSource,
            'id_user_destination'    => $idUserDestination,
            'montant'                => $montant,
            'frais'                  => $frais,
            'date_creation'          => date('Y-m-d H:i:s'),
        ]);

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

    private function executerTransferts(array $type, float $montant, int $userId, array $user)
    {
        $numeros = $this->extraireNumerosDestination();

        if (empty($numeros)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez saisir au moins un numéro de destination.');
        }

        // Le meme montant est envoye a chaque numero renseigne : on prepare
        // et on valide chaque transfert avant de toucher au solde.
        $transferts = [];
        foreach ($numeros as $numeroDestination) {
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

            $idUserDestination = null;
            $idOperateurDestination = null;
            $pourcentageCommission = 0.00;
            $frais = $this->calculerFrais($type['id'], $montant);

            if ($destinataire) {
                // Compte MVola existant (necessairement un numero de l'operateur proprietaire)
                $idUserDestination = $destinataire['id'];
            } else {
                // Transfert externe : numero valide mais sans compte MVola (autre operateur, ou Yas sans compte)
                $idOperateurDestination = (int) $prefixeInfo['id_operateur'];

                if (strtolower($prefixeInfo['proprietaire_nom']) !== 'local') {
                    $pourcentageCommission = $this->commissionModel->getPourcentagePourOperateur($idOperateurDestination);
                    $frais += round($montant * $pourcentageCommission / 100, 2);
                }
            }

            $transferts[] = [
                'numero_destination'     => $numeroDestination,
                'id_user_destination'    => $idUserDestination,
                'id_operateur'           => $idOperateurDestination,
                'pourcentage_commission' => $pourcentageCommission,
                'frais'                  => $frais,
            ];
        }

        $fraisTotal = 0.00;
        foreach ($transferts as $t) {
            $fraisTotal += $t['frais'];
        }
        $montantTotal = $montant * count($transferts);

        if ($user['solde'] < ($montantTotal + $fraisTotal)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant pour effectuer ce(s) transfert(s).');
        }

        $db = $this->operationModel->db;
        $db->transStart();

        $this->userModel->update($userId, [
            'solde' => $user['solde'] - $montantTotal - $fraisTotal,
        ]);

        foreach ($transferts as $t) {
            if ($t['id_user_destination'] !== null) {
                $destinataireActuel = $this->userModel->find($t['id_user_destination']);
                $this->userModel->update($t['id_user_destination'], [
                    'solde' => $destinataireActuel['solde'] + $montant,
                ]);
            }

            $this->operationModel->insert([
                'id_type'                => $type['id'],
                'id_user_source'         => $userId,
                'id_user_destination'    => $t['id_user_destination'],
                'id_operateur'           => $t['id_operateur'],
                'numero_destination'     => $t['numero_destination'],
                'montant'                => $montant,
                'frais'                  => $t['frais'],
                'pourcentage_commission' => $t['pourcentage_commission'],
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
            ->select('operation.*, type.nom as type_nom, src.numero_telephone as source_numero, COALESCE(dst.numero_telephone, operation.numero_destination) as destination_numero')
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