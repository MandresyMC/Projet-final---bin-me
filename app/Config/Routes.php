<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');
$routes->post('/login', 'LoginController::verify');

$routes->get('/client/accueil', static function () {
    return view('client/accueil', [
        'numero' => session('numero_telephone'),
        'solde'  => session('solde'),
    ]);
});

$routes->get('/client/operation', 'OperationController::index');

$routes->get('/client/depot', static function () {
    return view('client/transaction_form', [
        'type'        => 'depot',
        'title'       => "DEPOT D'ARGENT",
        'description' => "Déposez de l'argent gratuitement dans votre compte MVola auprès de notre réseau de PoP MVola, Cash Point et Yas Store, partout à Madagascar.",
        'solde'       => 'XXXXX',
    ]);
});

$routes->get('/client/retrait', static function () {
    return view('client/transaction_form', [
        'type'        => 'retrait',
        'title'       => "RETRAIT D'ARGENT",
        'description' => "Retirez votre argent facilement auprès des PoP MVola, Yas Store, Cash Point ou auprès des DAB de la banque BRED Madagasikara BP dans toute la Grande Île.",
        'solde'       => 'XXXXX',
    ]);
});

$routes->get('/client/transfert', static function () {
    return view('client/transaction_form', [
        'type'        => 'transfert',
        'title'       => "TRANSFERT D'ARGENT",
        'description' => "Retirez votre argent facilement auprès des PoP MVola, Yas Store, Cash Point ou auprès des DAB de la banque BRED Madagasikara BP dans toute la Grande Île.",
        'solde'       => 'XXXXX',
    ]);
});

$routes->get('/client/historique', static function () {
    return view('client/historique');
});

$routes->get('/admin/dashboard', static function () {
    return view('admin/dashboard');
});

$routes->get('/admin/configuration', static function () {
    return view('admin/configuration');
});

$routes->get('/admin/clients', static function () {
    return view('admin/clients');
});
