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

        $data = [
            'type'        => $type,
            'title'       => $titres[$type],
            'description' => $descriptions[$type],
            'solde'       => $user['solde'] ?? 0,
        ];

        if ($type === 'transfert') {
            // Bareme des prefixes envoye au JS pour detecter en direct si un numero
            // saisi est "local" ou "autre operateur" (active/desactive la case
            // "frais de retrait" et bloque le melange local/autre en multi-destinataires).
            $data['prefixes'] = $this->operationModel->db->table('prefixe')
                ->select('prefixe.prefixe, proprietaire.nom as proprietaire_nom')
                ->join('operateur', 'operateur.id = prefixe.id_operateur')
                ->join('proprietaire', 'proprietaire.id = operateur.id_proprietaire')
                ->where('prefixe.actif', 1)
                ->get()
                ->getResultArray();
        }

        return view('client/transaction_form', $data);
    }

    /**
     * Calcule les frais applicables pour un type et un montant donnes.
     */
    private function calculerFrais(int $idType, float $montant): float
    {
        $sql = "SELECT frais FROM bareme_frais WHERE id_type = ? AND ? BETWEEN montant_min AND montant_max LIMIT 1";
        $row = $this->operationModel->db->query($sql, [$idType, $montant])->getRowArray();

        return $row['frais'] ?? 0.00;
    }

    /**
     * Frais de retrait (barème du type "retrait") applicable a un montant donne.
     * Utilise pour l'option "ajouter les frais de retrait" d'un transfert local :
     * le montant credite au destinataire est majore de ce frais, pour qu'il puisse
     * retirer le montant initial sans frais supplementaire a sa charge.
     */
    private function calculerFraisRetrait(float $montant): float
    {
        $typeRetrait = $this->typeModel->where('nom', 'retrait')->first();
        if (!$typeRetrait) {
            return 0.00;
        }

        return $this->calculerFrais($typeRetrait['id'], $montant);
    }

    /**
     * Normalise le(s) numero(s) destinataire poste(s) par le formulaire.
     *
     * @return string[]
     */
    private function extraireNumerosDestination(): array
    {
        $brut = $this->request->getPost('numero_user_destination') ?? [];
        if (!is_array($brut)) {
            $brut = [$brut];
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

        if ($typeOperation === 'transfert') {
            return $this->executerTransfert($type, $montant, $userId, $user);
        }

        return $this->executerOperationSimple($type, $typeOperation, $montant, $userId, $user);
    }

    /**
     * Depot ou retrait : un seul mouvement, sur le compte du client connecte.
     */
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
                'solde' => $user['solde'] - $montant - ($frais * $nbDestinations),
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

    /**
     * Transfert vers un ou plusieurs numeros.
     *
     * Regles metier :
     * - Tous les destinataires d'un meme envoi doivent appartenir au meme groupe
     *   (tous "local", ou tous "autre operateur"), pas de melange.
     * - Le montant saisi est reparti a parts egales entre les destinataires.
     * - Le frais d'envoi (bareme "transfert") est calcule une seule fois sur le
     *   montant total saisi, peu importe le nombre de destinataires.
     * - Si le groupe est "autre operateur" : une commission (bareme "commission"
     *   de l'operateur) s'ajoute au frais, par destinataire, sur sa part de montant.
     * - Si le groupe est "local" et que l'option "frais de retrait" est cochee :
     *   le frais de retrait (bareme "retrait") de la part de chaque destinataire
     *   est ajoute au montant qui lui est credite (le destinataire pourra ainsi
     *   retirer le montant initial sans frais a sa charge). Cette option est
     *   ignoree (jamais appliquee) si le groupe est "autre operateur".
     */
    private function executerTransfert(array $type, float $montant, int $userId, array $user)
    {
        $numeros = $this->extraireNumerosDestination();
        $ajouterFraisRetrait = (bool) $this->request->getPost('ajouter_frais_retrait');

        if (empty($numeros)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Veuillez saisir au moins un numéro de destination.');
        }

        $nbDestinations = count($numeros);
        $montantParDestinataire = $montant / $nbDestinations;
        $fraisEnvoi = $this->calculerFrais($type['id'], $montant);

        $destinataires = [];
        $groupe = null;

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
            $groupeNumero = strtolower($prefixeInfo['proprietaire_nom']) === 'local' ? 'local' : 'autre';

            if ($groupe === null) {
                $groupe = $groupeNumero;
            } elseif ($groupe !== $groupeNumero) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Impossible de mélanger des numéros locaux et d'autres opérateurs dans un même envoi. Faites un transfert séparé pour chaque groupe d'opérateurs.");
            }

            $compteDestinataire = $this->userModel->where('numero_telephone', $numeroDestination)->first();

            if ($groupeNumero === 'local' && !$compteDestinataire) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Le numéro {$numeroDestination} n'a pas de compte MVola.");
            }

            $pourcentageCommission = 0.00;
            $idOperateurDestination = null;

            if ($groupeNumero === 'autre') {
                $idOperateurDestination = (int) $prefixeInfo['id_operateur'];
                $pourcentageCommission = $this->commissionModel->getPourcentagePourOperateur($idOperateurDestination);
            }

            $fraisRetrait = 0.00;
            if ($groupeNumero === 'local' && $ajouterFraisRetrait) {
                $fraisRetrait = $this->calculerFraisRetrait($montantParDestinataire);
            }

            $commission = round($montantParDestinataire * $pourcentageCommission / 100, 2);

            $destinataires[] = [
                'numero_destination'     => $numeroDestination,
                'id_user_destination'    => $compteDestinataire['id'] ?? null,
                'id_operateur'           => $idOperateurDestination,
                'pourcentage_commission' => $pourcentageCommission,
                'montant_envoye'         => $montantParDestinataire + $fraisRetrait,
                'frais'                  => round(($fraisEnvoi / $nbDestinations) + $commission, 2),
            ];
        }

        $fraisTotal = array_sum(array_column($destinataires, 'frais'));
        $montantTotalEnvoye = array_sum(array_column($destinataires, 'montant_envoye'));
        $debitTotal = $montantTotalEnvoye + $fraisTotal;

        if ($user['solde'] < $debitTotal) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant pour effectuer ce(s) transfert(s).');
        }

        $db = $this->operationModel->db;
        $db->transStart();

        $this->userModel->update($userId, [
            'solde' => $user['solde'] - $debitTotal,
        ]);

        foreach ($destinataires as $destinataire) {
            if ($destinataire['id_user_destination'] !== null) {
                $compteActuel = $this->userModel->find($destinataire['id_user_destination']);
                $this->userModel->update($destinataire['id_user_destination'], [
                    'solde' => $compteActuel['solde'] + $destinataire['montant_envoye'],
                ]);
            }

            $this->operationModel->insert([
                'id_type'                => $type['id'],
                'id_user_source'         => $userId,
                'id_user_destination'    => $destinataire['id_user_destination'],
                'id_operateur'           => $destinataire['id_operateur'],
                'numero_destination'     => $destinataire['numero_destination'],
                'montant'                => $destinataire['montant_envoye'],
                'frais'                  => $destinataire['frais'],
                'pourcentage_commission' => $destinataire['pourcentage_commission'],
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
