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

/**
 * Load the Router instance from the web.php file
 * This ensures all registered routes are preserved.
 */
$router = require_once __DIR__ . '/../routes/web.php';


try {
    $router->resolve();
} catch (Exception $e) {

    // Systematic logging for debugging and monitoring
    
    error_log("System Error: " . $e->getMessage());
    http_response_code(500);
    echo "System Error: " . $e->getMessage();
}