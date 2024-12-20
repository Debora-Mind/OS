<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('login', 'Login::novo');
$routes->get('logout', 'Login::logout');
$routes->get('esqueci', 'Password::esqueci');

//TODO CRIAR ROTA PARA ordens/minhas que é enviado no e-mail para o cliente


$routes->group('contas', function($routes)
{
    $routes->add('/', 'ContasPagar::index');
    $routes->add('recuperacontas', 'ContasPagar::recuperaContas');
    $routes->add('buscaFornecedores/(:any)', 'ContasPagar::buscaFornecedores/$1');
    $routes->add('exibir/(:segment)', 'ContasPagar::exibir/$1');
    $routes->add('editar/(:segment)', 'ContasPagar::editar/$1');
    $routes->post('atualizar', 'ContasPagar::atualizar');
    $routes->add('criar', 'ContasPagar::criar');
    $routes->post('cadastrar', 'ContasPagar::cadastrar');
    $routes->match(['get', 'post'], 'excluir/(:segment)', 'ContasPagar::excluir/$1');
});