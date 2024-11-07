<?php

use App\System\Controllers\AuthController;
use App\System\Core\Router;
use App\System\Http\ResponseBundle;
use App\System\Middleware\AuthMiddleware;
use App\System\Controllers\UserController;
use App\System\Middleware\LoggingMiddleware;

global $router;

$router->get('', function() {
    return new ResponseBundle(200, [
        'message' => 'Welcome to the API!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

$router->post('/login', [LoggingMiddleware::class, [AuthController::class, 'login']]);
$router->post('/user/create', [LoggingMiddleware::class, [UserController::class, 'createUser']]);
//$router->get('/userr', [LoggingMiddleware::class, [UserController::class, 'getUser']]);

$router->group('/api', function ($router) {
    $router->get('/user', [LoggingMiddleware::class, AuthMiddleware::class, [UserController::class, 'getUser']]);

});

//$router->get('/user', [AuthMiddleware::class, [UserController::class, 'getUser']]);

//$router->get('user', 'UserController@getUser');

//$router->get('user/{id}', 'UserController@getUserById');
//$router->post('user', 'UserController@createUser');

//$router->put('user/{id}', 'UserController@updateUser');
//$router->delete('user/{id}', 'UserController@deleteUser');

