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

    public function index()
    {
        return view('auth/login');
    }

    public function verify()
    {
        $data = $this->request->getPost();

        $email = $data['email'];
        $password = $data['password'];

        try {
            $user = $this->userModel->where('email', $email)->first();
        } catch (\Exception $e) {
            return redirect()->to('/page-login')
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        // email inexistant
        if (!$user) {
            return redirect()->to('/page-login')
                ->withInput()
                ->with('error', 'Votre email est inexistant');
        }

        // vérifier le mot de passe hashé
        if (!password_verify($password, $user['password'])) {
            return redirect()->to('/page-login')
                ->withInput()
                ->with('error', 'Email ou mot de passe incorrect');
        }

        session()->set([
            'user_id' => $user['id'],
            'nom'     => $user['nom'],
            'role'    => $user['role'] ?? 'client',
            'logged_in' => true
        ]);

        return redirect()->to('/dashboard');
    }

    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to('/page-login');
    }
}