<?php
// routes/web.php path eke thiyena file eka

use App\Controllers\ProductController;
use App\Controllers\DashboardController;
use App\Controllers\AuthController;

// Home/Dashboard route
$router->get('/dashboard', [DashboardController::class, 'index']);

// Product Management Routes
$router->get('/products', [ProductController::class, 'index']);          // Show All Products
$router->get('/products/create', [ProductController::class, 'create']);  // See Form 
$router->post('/products/store', [ProductController::class, 'store']);   // Database save 
$router->get('/products/edit/{id}', [ProductController::class, 'edit']); // Edit form 
$router->post('/products/update/{id}', [ProductController::class, 'update']); // Database update
$router->get('/products/delete/{id}', [ProductController::class, 'delete']); // Delete 