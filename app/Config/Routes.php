<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('login', 'Login::novo');
$routes->get('logout', 'Login::logout');
$routes->get('esqueci', 'Password::esqueci');


$routes->group('contas', function($routes)
{
    $routes->add('/', 'ContasPagar::index');
    $routes->add('recuperacontas', 'ContasPagar::recuperaContas');
});