<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    public function verify()
    {
        $data = $this->request->getPost();

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

        return view('index', ['success' => "Mety!"]);
    }
}