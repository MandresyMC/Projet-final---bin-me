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
        $numero = trim($this->request->getPost('numero_telephone'));

        // Vérifie que le numéro ne contient que des chiffres
        if (!ctype_digit($numero)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le numéro de téléphone ne doit contenir que des chiffres.');
        }

        // Vérifie la longueur
        if (strlen($numero) !== 9) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le numéro de téléphone doit contenir exactement 9 chiffres.');
        }

        // Vérifie le préfixe
        if (!preg_match('/^(32|33|34|37|38)/', $numero)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Le numéro doit commencer par 32, 33, 34, 37 ou 38.');
        }

        try {
            $user = $this->userModel
                ->where('numero_telephone', $numero)
                ->first();

            if (!$user) {

                $data = [
                    'numero_telephone' => $numero,
                    'solde'            => 0,
                    'role'             => 'client'
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

        session()->set([
            'user_id'           => $user['id'],
            'numero_telephone'  => $user['numero_telephone'],
            'solde'  => $user['solde'],
            'logged_in'         => true
        ]);

        return redirect()->to('/accueil');
    }

    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to('/page-login');
    }
}