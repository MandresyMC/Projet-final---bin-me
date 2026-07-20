<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    public function verificationConnexion()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/page-login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
    }

    public function verificationConnexionAdmin()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'admin') {
            return redirect()->to('/page-login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
    }
}
