<?php
// register custom autoloader in case composer isn't installed yet
require_once __DIR__ . '/../app/Core/Autoloader.php';
\App\Core\Autoloader::register();

// try composer autoloader if available
$composer = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composer)) {
    require_once $composer;
}

use App\Core\Router;

// Initialize Router  
$router = new Router();

// Load Routes
require_once __DIR__ . '/../routes/web.php';

// Start Engine
$router->resolve();