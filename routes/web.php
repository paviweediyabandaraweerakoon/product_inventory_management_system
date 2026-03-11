<?php

use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;

$router = new Router();

// --- Dashboard Routes ---
// Added both '/' and '/dashboard' to point to the same controller method for better UX and flexibility in URL structure.
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']); 

// --- Category Routes ---
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories/store', [CategoryController::class, 'store']);
$router->get('/categories/edit/{id}', [CategoryController::class, 'edit']);
$router->post('/categories/update/{id}', [CategoryController::class, 'update']);
$router->get('/categories/destroy/{id}', [CategoryController::class, 'destroy']);

// --- Product Routes ---
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/store', [ProductController::class, 'store']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit']);
$router->post('/products/update/{id}', [ProductController::class, 'update']);
$router->get('/products/destroy/{id}', [ProductController::class, 'destroy']);

return $router;