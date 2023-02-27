<?php

use Illuminate\Http\Response;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

function data()
{
  return [
    'name'      => 'CRUD - Lumen - Api-REST',
    'framework' => 'Lumen Framework',
    'database'  => 'MySQL',
    'Routes'    => [
      'products'  => 'http://localhost:8000/api/products',
    ],
  ];
}

$router->get('/', function () { return Response()->json(data()); });
$router->get('/api', function () { return Response()->json(data()); });

//* Rutas de modelo product
$router->post('/api/products',         'ProductController@create');
$router->get('/api/products',          'ProductController@index');
$router->get('/api/products/{id}',     'ProductController@show');
$router->put('/api/products/{id}',     'ProductController@update');
$router->delete('/api/products/{id}',  'ProductController@delete');

