<?php

namespace App\Core;

use Throwable;

/**
 * Class Application
 * The main entry point of the framework that orchestrates routing and error handling.
 */
class Application
{
    /** @var Router */
    protected Router $router;

    /**
     * Application constructor.
     * Initializes the router instance.
     */
    public function __construct()
    {
        // Keeping it consistent with your project's current structure
        $this->router = new Router();
    }

    /**
     * Get the router instance.
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Run the application, resolve routes, and handle global exceptions.
     * @return void
     */
    public function run(): void
    {
        try {
            // Resolving the current route
            $this->router->resolve();
            
        } catch (Throwable $e) {
            // 1. Professional Detailed Logging 
            // Format: %datetime% > %class_name% > %method_name% > %message%
            $logMessage = sprintf(
                "[%s] %s > Application::run > %s | File: %s | Line: %d | Trace: %s\n",
                date('Y-m-d H:i:s'),
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString() // Added for deeper debugging
            );

            error_log($logMessage);

            // 2. Set HTTP Response Code (With a safety check)
            if (!headers_sent()) {
                http_response_code(500);
            }

            // 3. Render a clean error view for the user
            $errorPage = __DIR__ . '/../Views/errors/500.php';
            if (file_exists($errorPage)) {
                require_once $errorPage;
            } else {
                echo "<h1>500 Internal Server Error</h1>";
                echo "An unexpected error occurred. Please check the logs.";
            }

            // 4. Safer to stop execution
            exit; 
        }
    }
}