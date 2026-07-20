<?php

namespace App\Controllers;

use App\Models\UserModel;

class InscriptionController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('auth/register');
    }

    public function verify()
    {
        $data = $this->request->getPost();
        $nomComplet = trim($data['prenom'] . ' ' . $data['nom']);
        $data['nom'] = $nomComplet;
        unset($data['prenom']);

        $validation = service('validation');
        $validation->setRules(
            $this->userModel->getValidationRulesArray(),
            $this->userModel->getValidationMessagesArray()
        );

        if (!$validation->run($data)) {
            return redirect()->to('/page-inscription')
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        if ($data['password'] !== $data['password_confirm']) {
            return redirect()->to('/page-inscription')
                ->withInput()
                ->with('errors', ['password_confirm' => 'Les mots de passe ne correspondent pas.']);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password_confirm']);

        // insertion à la bd
        try {
            $this->userModel->insert($data);
        } catch (\Exception $e) {
            return redirect()->to('/page-inscription')
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        }
        $user = $this->userModel->where('email', $data['email'])->first();
        session()->set([
            'user_id' => $user['id'],
            'nom'     => $user['nom'],
            'role'    => $user['role'],
            'logged_in' => true
        ]);

        return redirect()->to('/dashboard')
            ->with('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
    }
}