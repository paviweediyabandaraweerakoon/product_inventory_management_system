<?php

// 1. Load Autoloaders
require_once __DIR__ . '/../vendor/autoload.php';

// Composer autoloading
if (file_exists(__DIR__ . '/../app/Core/Autoloader.php')) {
    require_once __DIR__ . '/../app/Core/Autoloader.php';
    \App\Core\Autoloader::register();
}

// 2. Initialize Router & Load Routes
$router = require_once __DIR__ . '/../routes/web.php';

// 3. Start Application
$app = new \App\Core\Application($router);
$app->run();