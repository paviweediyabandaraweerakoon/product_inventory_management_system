<?php

use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;

/**
 * Global Router Instance
 * Defining all application routes for RESTful interaction.
 */
$router = new Router();

// --- Dashboard Routes ---
/**
 * Root and Dashboard routes pointing to the main analytics overview.
 */
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']); 

// --- Category Management Routes ---
/**
 * Standard RESTful naming conventions for Category CRUD.
 */
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories/store', [CategoryController::class, 'store']);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit']);
$router->post('/categories/{id}', [CategoryController::class, 'update']);
$router->post('/categories/delete/{id}', [CategoryController::class, 'destroy']);

/** * Using POST for delete to improve security and follow REST principles. 
 * This prevents accidental deletions via simple GET requests.
 */
$router->post('/categories/delete/{id}', [CategoryController::class, 'destroy']);

// --- Product Management Routes ---
/**
 * Standard RESTful naming conventions for Product CRUD.
 */
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/store', [ProductController::class, 'store']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit']);
$router->post('/products/update/{id}', [ProductController::class, 'update']);
/**
 * Following REST principles: avoid using GET for state-changing actions like delete.
 */
$router->post('/products/delete/{id}', [ProductController::class, 'destroy']);

/**
 * Return the configured router instance to the application lifecycle.
 */
return $router;