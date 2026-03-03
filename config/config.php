<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/Core/Env.php';

use App\Core\Env;

// Load .env file
Env::load(dirname(__DIR__) . '/.env');

// App config
define('APP_NAME', Env::get('APP_NAME', 'Inventory Pro'));
define('APP_URL', Env::get('APP_URL', 'http://localhost/product_inventory_management_system'));
define('APP_ENV', Env::get('APP_ENV', 'production'));
define('APP_DEBUG', Env::get('APP_DEBUG', 'false') === 'true');

// Database config
define('DB_HOST', Env::get('DB_HOST', '127.0.0.1'));
define('DB_PORT', Env::get('DB_PORT', '3306'));
define('DB_DATABASE', Env::get('DB_DATABASE', 'product_inventory_management_system'));
define('DB_USERNAME', Env::get('DB_USERNAME', 'root'));
define('DB_PASSWORD', Env::get('DB_PASSWORD', ''));

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', ROOT_PATH . '/storage/uploads');
define('LOG_PATH', ROOT_PATH . '/storage/logs');
define('VIEW_PATH', APP_PATH . '/Views');

// Upload config
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set('Asia/Colombo');