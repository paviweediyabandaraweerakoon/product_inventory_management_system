<?php

namespace App\Core;

use Exception;

/**
 * Class Router
 * Handles request routing and dispatches to controllers with REST support.
 * Provides better security and stability.
 */
class Router {
    protected array $routes = [];

    /**
     * Register GET routes
     */
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Register POST routes
     */
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Register DELETE routes
     */
    public function delete(string $path, array $handler): void {
        $this->routes['DELETE'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Converts path with parameters (e.g., /categories/{id}) to Regex
     */
    private function convertToRegex(string $path): string {
        return "@^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path) . "$@";
    }

    /**
     *complete the code for resolve method
      * 
     */
    public function resolve(): mixed {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        /** Method Spoofing: Handles DELETE/PUT from HTML forms
         * HTML forms support only GET and POST. To use DELETE or PUT,
         * we use a hidden field named '_method'.
         */
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Extract path without query parameters and normalize it
        $path = explode('?', $uri)[0];
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $path = str_replace($basePath, '', $path);
        if (empty($path)) $path = '/';

        /**
         * Fix: Undefined index check
         */
        if (!isset($this->routes[$method])) {
            return $this->abort404();
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                // Named parameters extraction
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($handler)) {
                    [$controllerClass, $methodName] = $handler;

                    /** * Safety Check: Verify Class Existence
                     */
                    if (!class_exists($controllerClass)) {
                        throw new Exception("Controller class '$controllerClass' not found.");
                    }

                    $controller = new $controllerClass();

                    /** * Safety Check: Verify Method Existence
                     */
                    if (!method_exists($controller, $methodName)) {
                        throw new Exception("Method '$methodName' not found in '$controllerClass'.");
                    }

                    // Parameters are passed as named parameters to the controller method
                    return call_user_func_array([$controller, $methodName], $params);
                }
            }
        }
        return $this->abort404();
    }

    /**
     * Stop execution and return 404
     */
    private function abort404(): void {
        http_response_code(404);
        echo "404 - Page Not Found";
        exit; 
    }
}