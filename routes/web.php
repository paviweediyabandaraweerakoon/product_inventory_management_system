<?php
use App\Controllers\ProductController;
use App\Controllers\DashboardController;
use App\Core\Router;
use App\Controllers\CategoryController;
use App\Controllers\AuthController;
use App\Controllers\UserController;

/**
 * Global Router Instance
 * Defining all application routes for RESTful interaction.
 */
$router = new Router();

// --- Auth Routes ---
/**
 * Authentication management routes.
 * AuthController handles both GET (show form) and POST (submit form)
 * inside the same methods using request method checks.
 */
$router->get('/', [AuthController::class, 'login']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'destroy']);

// --- Dashboard Routes ---
/**
 * Dashboard route pointing to the main analytics overview.
 */
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

/**
 * Using POST for delete to improve security and follow REST principles.
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
$router->post('/products/import', [ProductController::class, 'import']);

/**
 * Following REST principles: avoid using GET for state-changing actions like delete.
 */
$router->post('/products/delete/{id}', [ProductController::class, 'destroy']);

/**
 * Return the configured router instance to the application lifecycle.
 */

// --- User Management Routes ---
/**
 * Standard RESTful naming conventions for User CRUD.
 */
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users/store', [UserController::class, 'store']);
$router->get('/users/edit/{id}', [UserController::class, 'edit']);
$router->post('/users/update/{id}', [UserController::class, 'update']);

/**
 * Using POST for delete for security (Soft Delete).
 */
$router->post('/users/delete/{id}', [UserController::class, 'delete']);

return $router;
