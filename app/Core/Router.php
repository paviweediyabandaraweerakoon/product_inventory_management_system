<?php

namespace App\Core;

use Exception;

/**
 * Class Router
 * Handles request routing and dispatches to controllers.
 */
class Router {
    protected array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, array $handler): void {
        $this->routes['GET'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Convert simple route paths to Regex patterns.
     */
    private function convertToRegex(string $path): string {
        return "@^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path) . "$@";
    }

    /**
     * Resolve the current request and call the appropriate controller method.
     */
    public function resolve(): mixed {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];
        $path = explode('?', $uri)[0];

        // Subfolder handling for local environments like XAMPP
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $path = str_replace($basePath, '', $path);
        if (empty($path)) $path = '/';

        // Check if the request method exists in routes
        if (!isset($this->routes[$method])) {
            return $this->abort404();
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($handler)) {
                    $controllerClass = $handler[0];
                    $methodName = $handler[1];

                    // Check if class exists before instantiating
                    if (!class_exists($controllerClass)) {
                        throw new Exception("Router Error: Controller class '$controllerClass' not found.");
                    }

                    $controller = new $controllerClass();

                    // Verify if the method exists in the controller
                    if (!method_exists($controller, $methodName)) {
                        throw new Exception("Router Error: Method '$methodName' not found in controller '$controllerClass'.");
                    }

                    return call_user_func_array([$controller, $methodName], $params);
                }
            }
        }

        return $this->abort404();
    }

    /**
     * Handle 404 response and terminate execution.
     */
    private function abort404(): void {
        http_response_code(404);
        echo "404 - Page Not Found";
        //Stop execution after 404
        return; 
    }
}