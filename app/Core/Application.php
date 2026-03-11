<?php

namespace App\Core;

use Throwable;

/**
 * Class Application
 * Handles the main execution flow and global error catching.
 */
class Application
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Run the application.
     */
    public function run(): void
    {
        try {
            // Route resolution and controller dispatching
            $this->router->resolve();
        } catch (Throwable $e) {
            // Log the actual error for debugging
            error_log("Global Application Error: " . $e->getMessage());

            // Set response code and show a professional error view
            http_response_code(500);
            
            // Redirect to the error view directly
            require_once __DIR__ . '/../Views/errors/500.php';
        }
    }
}