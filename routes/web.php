<?php
use App\Controllers\ProductController;
use App\Controllers\DashboardController;

// Dashboard
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// Product Management
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/store', [ProductController::class, 'store']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit']); 
$router->post('/products/update/{id}', [ProductController::class, 'update']);
$router->get('/products/delete/{id}', [ProductController::class, 'delete']);