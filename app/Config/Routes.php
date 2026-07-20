<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===== Authentification =====
$routes->get('/', 'Auth\HomeController::index');
$routes->post('/login', 'Auth\LoginController::verify');
$routes->get('/deconnexion', 'Auth\LoginController::deconnexion');

// ===== Espace client =====
$routes->get('/client/accueil', 'Client\AccueilController::index');

$routes->get('/client/operation', 'Client\OperationController::index');
$routes->post('/client/operation', 'Client\OperationController::createOperation');
$routes->get('/client/historique', 'Client\OperationController::historiques');

$routes->get('/client/depot', 'Client\OperationController::formulaire/depot');
$routes->get('/client/retrait', 'Client\OperationController::formulaire/retrait');
$routes->get('/client/transfert', 'Client\OperationController::formulaire/transfert');

// ===== Espace admin =====
$routes->get('/admin/dashboard', 'Admin\DashboardController::index');

$routes->get('/admin/configuration', 'Admin\BaremeFraisController::index');
$routes->post('/admin/bareme-frais', 'Admin\BaremeFraisController::createBaremeFrais');
$routes->post('/admin/bareme-frais/(:num)/delete', 'Admin\BaremeFraisController::deleteBaremeFrais/$1');

$routes->post('/admin/prefixe', 'Admin\BaremeFraisController::createPrefixe');
$routes->post('/admin/prefixe/(:num)/toggle', 'Admin\BaremeFraisController::togglePrefixe/$1');
$routes->post('/admin/prefixe/(:num)/delete', 'Admin\BaremeFraisController::deletePrefixe/$1');

$routes->post('/admin/commission', 'Admin\BaremeFraisController::createCommission');
$routes->post('/admin/commission/(:num)/delete', 'Admin\BaremeFraisController::deleteCommission/$1');

$routes->get('/admin/clients', 'Admin\SituationClientController::index');

$routes->get('/admin/historique', 'Admin\HistoriqueController::index');
