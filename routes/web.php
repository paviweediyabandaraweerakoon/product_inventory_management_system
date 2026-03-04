<?php
use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\DashboardController;
use App\Controllers\InventoryController;

$router = new Router();

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/', [DashboardController::class, 'index']); // Default route

// Product Management
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/store', [ProductController::class, 'store']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit']);
$router->post('/products/update/{id}', [ProductController::class, 'update']);
$router->get('/products/delete/{id}', [ProductController::class, 'delete']);

// Inventory Management (Stock In/Out)
$router->get('/inventory/adjust/{id}', [InventoryController::class, 'adjust']);
$router->post('/inventory/update', [InventoryController::class, 'update']);

return $router;