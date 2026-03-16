<?php

namespace App\Core;

/**
 * Class Controller
 * Base controller that provides common functionality like view rendering.
 */
class Controller
{
    /**
     * Constructor added to fix "Cannot call constructor" error.
     * This allows child controllers to call parent::__construct().
     */
    public function __construct()
    {
        // common initialization code can go here, e.g., starting sessions, loading common models, etc.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Render a view file and extract data for use in the template.
     * * @param string $view Path to the view file (e.g., 'categories/index')
     * @param array $data Data to be passed to the view
     * @return void
     */
    public function view(string $view, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        $file = __DIR__ . "/../Views/{$view}.php";

        if (file_exists($file)) {
            require_once $file;
        } else {
            // Error handling for missing views
            http_response_code(500);
            die("Error: View file [{$view}] not found in: " . $file);
        }
    }
}