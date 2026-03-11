<?php

namespace App\Core;

use Exception;

/**
 * Class Router
 * * Handles HTTP routing for the application.
 */
class Router
{
    /**
     * Holds the registered routes.
     * @var array
     */
    protected array $routes = [];

    /**
     * Register a GET route.
     * * @param string $path
     * @param array $handler
     * @return void
     */
    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Register a POST route.
     * * @param string $path
     * @param array $handler
     * @return void
     */
    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Register a DELETE route.
     * * @param string $path
     * @param array $handler
     * @return void
     */
    public function delete(string $path, array $handler): void
    {
        $this->routes['DELETE'][$this->convertToRegex($path)] = $handler;
    }

    /**
     * Convert path to a regular expression.
     * * @param string $path
     * @return string
     */
    private function convertToRegex(string $path): string
    {
        $path = trim($path, '/');
        if ($path === "") {
            return "#^/$#";
        }
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $path);
        return "#^/" . $regex . "/?$#";
    }

    /**
     * Resolve the current request and execute the handler.
     * * @return void
     * @throws Exception
     */
    public function resolve(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Method spoofing for forms
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $path = parse_url($uri, PHP_URL_PATH);
        
        // Clean XAMPP sub-folder path
        $scriptName = $_SERVER['SCRIPT_NAME']; 
        $basePath = str_replace('index.php', '', $scriptName);
        
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        $path = '/' . trim($path, '/');

        if (!isset($this->routes[$method])) {
            $this->abort404($path);
            return;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controllerClass, $methodName] = $handler;

                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller '$controllerClass' not found.");
                }

                $controller = new $controllerClass();
                
                if (!method_exists($controller, $methodName)) {
                    throw new Exception("Method '$methodName' not found in $controllerClass.");
                }

                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        $this->abort404($path);
    }

    /**
     * Terminate with a 404 error.
     * * @param string $path
     * @return void
     */
    private function abort404(string $path): void
    {
        http_response_code(404);
        echo "<h1>404 - Not Found</h1>";
        echo "Debug Info: The path <b>'$path'</b> did not match any routes.<br>";
        echo "Registered Routes: <pre>";
        print_r($this->routes);
        echo "</pre>";
        exit;
    }
}