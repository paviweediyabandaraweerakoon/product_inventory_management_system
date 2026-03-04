<?php
namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$this->convertToRegex($path)] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$this->convertToRegex($path)] = $handler;
    }

    private function convertToRegex($path) {
        return "@^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path) . "$@";
    }

    public function resolve() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];
        $path = explode('?', $uri)[0];

        // Subfolder handle (XAMPP helper)
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $path = str_replace($basePath, '', $path);
        if (empty($path)) $path = '/';

        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($handler)) {
                    $controller = new $handler[0]();
                    return call_user_func_array([$controller, $handler[1]], $params);
                }
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }
}