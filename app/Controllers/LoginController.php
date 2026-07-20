<?php

namespace App\Controllers;

use App\Models\UserModel;

class LoginController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function verify()
    {
        $numero = $this->request->getPost('numero_telephone');
        $numero = trim($numero);
        $numero = str_replace(' ', '', $numero);

        $verification = $this->verifyNumeroTelephone($numero);

        if ($verification !== true) {
            return redirect()->back()
                ->withInput()
                ->with('error', $verification);
        }

        try {
            $user = $this->userModel
                ->where('numero_telephone', $numero)
                ->first();

            if (!$user) {

                $data = [
                    'numero_telephone' => $numero,
                    'solde'            => 0
                ];

                $this->userModel->insert($data);

                $user = $this->userModel
                    ->where('numero_telephone', $numero)
                    ->first();
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        if (!$user) {
            return redirect()->back()
                ->with('error', 'Impossible de créer ou récupérer l\'utilisateur.');
        }

        session()->set([
            'user_id'           => $user['id'],
            'numero_telephone'  => $user['numero_telephone'],
            'solde'             => $user['solde'],
            'logged_in'         => true
        ]);

        return redirect()->to('/client/accueil');
    }

    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}