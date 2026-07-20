<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'UserController::login');
// $routes->post('/verify', 'UserController::verify');
$routes->get('/', 'HomeController::index');
$routes->get('/page-inscription', 'InscriptionController::index');
$routes->post('/page-inscription', 'InscriptionController::verify');
$routes->get('/page-login', 'LoginController::index');
$routes->post('/page-login', 'LoginController::verify');
$routes->get('/page-login/deconnexion', 'LoginController::deconnexion');
$routes->get('/page-creneaux', 'CreneauController::index');
$routes->post('/page-creneaux/ajoutCreneau', 'CreneauController::ajoutCreneau');
$routes->get('/page-calendrier', 'CreneauController::calendrier');
$routes->get('/page-mes-reservations', 'ReservationController::index');
$routes->post('/reservation/annuler', 'ReservationController::annuler');
$routes->get('/dashboard', 'DashboardController::index');

$routes->get('/admin/dashboard', 'AdminDashboardController::index');

$routes->get('/admin/creneaux', 'AdminCreneauController::index');
$routes->post('/admin/creneaux/ajouter', 'AdminCreneauController::ajouter');
$routes->get('/admin/creneaux/supprimer/(:num)', 'AdminCreneauController::supprimer/$1');
$routes->get('/admin/creneaux/editer/(:num)', 'AdminCreneauController::editer/$1');
$routes->post('/admin/creneaux/modifier/(:num)', 'AdminCreneauController::modifier/$1');

$routes->get('/admin/reservations', 'AdminReservationController::index');
$routes->get('/admin/reservations/confirmer/(:num)', 'AdminReservationController::confirmer/$1');
$routes->get('/admin/reservations/refuser/(:num)', 'AdminReservationController::refuser/$1');

$routes->get('/calendar', 'EventController::index');
$routes->get('/events', 'EventController::list');
$routes->post('/events/save', 'EventController::save');

$routes->post(
    '/creneaux/ajouter',
    'CreneauController::ajouter'
);