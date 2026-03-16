<?php

namespace App\Core;

use Exception;

/**
 * Class Router
 * Responsible for handling HTTP request routing, method spoofing, 
 * and dynamic controller resolution.
 */
class Router
{
    /** @var array Holds the registered routes categorized by HTTP method */
    protected array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, array $handler): void 
    { 
        $this->routes['GET'][$this->convertToRegex($path)] = $handler; 
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array $handler): void 
    { 
        $this->routes['POST'][$this->convertToRegex($path)] = $handler; 
    }

    /**
     * Register a DELETE route (Supports method spoofing).
     */
    public function delete(string $path, array $handler): void 
    { 
        $this->routes['DELETE'][$this->convertToRegex($path)] = $handler; 
    }

    /**
     * Convert URI path to a Regular Expression for dynamic matching.
     */
    private function convertToRegex(string $path): string
    {
        $path = trim($path, '/');
        if ($path === "") return "#^/$#";
        
        // Convert {id} or {slug} placeholders to named regex groups
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $path);
        return "#^/" . $regex . "/?$#";
    }

    /**
     * Resolve the incoming request and dispatch to the appropriate controller.
     * * @throws Exception If controller or method is missing.
     */
    public function resolve(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // --- RESTful Method Spoofing ---
        // Allows HTML forms to perform DELETE/PUT requests via a hidden '_method' field.
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $path = parse_url($uri, PHP_URL_PATH);
        
        // Handle subdirectory deployments by removing the base path from the request URI
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $basePath = str_replace('index.php', '', $scriptName);
        
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        $path = '/' . trim($path, '/');

        // Check if the requested HTTP method is registered
        if (!isset($this->routes[$method])) {
            $this->abort404($path);
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                // Extract only the named parameters from the regex matches
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controllerClass, $methodName] = $handler;

                // --- High-Level Validation ---
                if (!class_exists($controllerClass)) {
                    throw new Exception("Router Error: Controller class '$controllerClass' not found.");
                }
                
                $controller = new $controllerClass();
                
                if (!method_exists($controller, $methodName)) {
                    throw new Exception("Router Error: Method '$methodName' does not exist in $controllerClass.");
                }

                /**
                 * Dispatch the request.
                 * array_values($params) ensures only the data is passed to the method arguments.
                 */
                call_user_func_array([$controller, $methodName], array_values($params));
                return;
            }
        }

        $this->abort404($path);
    }

    /**
     * Terminate the request with a 404 response.
     */
    private function abort404(string $path): void
    {
        http_response_code(404);
        // Production style: avoid dumping full debug info here for security.
        echo "<h1>404 - Page Not Found</h1>";
        exit;
    }
}