<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Env;
use App\Core\Application;

// 1. Load environment variables
Env::load(__DIR__ . '/../.env');

// 2. Load Autoloaders (Optional if using Composer)
if (file_exists(__DIR__ . '/../app/Core/Autoloader.php')) {
    require_once __DIR__ . '/../app/Core/Autoloader.php';
    \App\Core\Autoloader::register();
}

// 3. Initialize Router & Load Routes from web.php
$router = require_once __DIR__ . '/../routes/web.php';

// 4. Start Application
$app = new Application($router);
$app->run();