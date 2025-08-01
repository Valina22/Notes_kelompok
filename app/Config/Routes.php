<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/notes', 'Notes::index');
$routes->get('/notes/create', 'Notes::create');
$routes->post('/notes/store', 'Notes::store');
$routes->get('/notes/edit/(:num)', 'Notes::edit/$1');
$routes->post('/notes/update/(:num)', 'Notes::update/$1');
$routes->get('/notes/delete/(:num)', 'Notes::delete/$1');
