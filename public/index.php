<?php
// Autoloading (PSR-4 use)
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// Initialize Router  
$router = new Router();

// Load Routes
require_once __DIR__ . '/../routes/web.php';

// Start Engine
$router->resolve();