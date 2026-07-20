<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');
$routes->post('/login', 'LoginController::verify');

$routes->group('admin', function($adminRoutes) {
    $adminRoutes->get('/', 'AdminController::index');
});

$routes->group('client', function($clientRoutes) {
    $clientRoutes->get('/', 'ClientController::index');
});
