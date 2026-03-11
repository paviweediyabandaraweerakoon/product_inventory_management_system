<?php
/**
 * @author Pavi Weerakoon
 * @version 1.0.0
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Router;
use App\Controllers\DashboardController;
use App\Controllers\CategoryController;

Env::load(__DIR__ . '/../.env');

$router = new Router();

// Dashboard
$router->get('/', [DashboardController::class, 'index']);

// 1. Category Listing
$router->get('/categories', [CategoryController::class, 'index']);

// 2. Category Create Form (Static Route)
$router->get('/categories/create', [CategoryController::class, 'create']);

// 3. Category Store (POST /categories)
$router->post('/categories', [CategoryController::class, 'store']); 

// --- Parameterized Routes ---
// 4. Edit Form
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit']);

// 5. Update (POST /categories/{id})
$router->post('/categories/{id}', [CategoryController::class, 'update']);

// 6. Delete (DELETE /categories/{id})
$router->delete('/categories/{id}', [CategoryController::class, 'destroy']);
try {
    $router->resolve();
} catch (Exception $e) {
    http_response_code(500);
    echo "System Error: " . $e->getMessage();
}