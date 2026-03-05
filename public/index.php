<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\CategoryController;

// 1. Load environment variables
Env::load(__DIR__ . '/../.env');

// 2. Initialize Router
$router = new Router();

// 3. Define Routes
$router->get('/', [DashboardController::class, 'index']);

// Category Routes
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories/store', [CategoryController::class, 'store']);
$router->get('/categories/edit/{id}', [CategoryController::class, 'edit']);
$router->post('/categories/update/{id}', [CategoryController::class, 'update']);
$router->get('/categories/delete/{id}', [CategoryController::class, 'delete']);

// 4. Resolve the route
$router->resolve();