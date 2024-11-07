<?php

require_once '../vendor/autoload.php';

use App\System\Core\Router;
use App\System\Providers\ServiceProvider;
use App\System\Util\Log;
use App\System\Container\Container;
use Dotenv\Dotenv;

Log::init('app');
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
ServiceProvider::initialize($container);

$router = new Router($container);
global $router;

require_once __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = trim($_SERVER['REQUEST_URI'], '/');

try {
    $router->dispatch($method, $uri);
} catch (Exception $e) {
    Log::log('error', 1, (array)$e->getMessage());
}
