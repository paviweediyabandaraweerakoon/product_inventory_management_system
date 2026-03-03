<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Router;

// Load environment variables
Env::load(__DIR__ . '/../.env');

$router = new Router();
// Routes temprary
$router->add('GET', '/', 'DashboardController@index');

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/product_inventory_management_system/public', '', $uri);

$router->handle($method, $uri);