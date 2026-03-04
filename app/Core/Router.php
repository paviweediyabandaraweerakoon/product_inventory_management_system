<?php



namespace App\Core;

class Router
{
    protected $routes = [];

    // Register a GET route (stored with lowercase method key)
    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    // Register a POST route (stored with lowercase method key)
    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    // Resolve incoming request, support dynamic {param} segments
    public function resolve()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $method = strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // If app runs in a subfolder, remove it from the path (adjust if needed)
        $baseFolder = '/product_inventory_management_system/public';
        $path = str_replace($baseFolder, '', $path);

        // Strip query string
        $path = explode('?', $path)[0];

        // If no routes for this HTTP method
        if (!isset($this->routes[$method])) {
            http_response_code(405);
            return "405 Method Not Allowed";
        }

        foreach ($this->routes[$method] as $routePath => $callback) {
            // Convert {param} tokens into a capturing regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // remove full match

                // Use numeric values only to avoid named-parameter issues
                $params = array_values($matches);

                if (is_array($callback)) {
                    $controller = new $callback[0]();
                    return call_user_func_array([$controller, $callback[1]], $params);
                }

                if (is_callable($callback)) {
                    return call_user_func_array($callback, $params);
                }
            }
        }

        http_response_code(404);
        return "404 Not Found";
    }
}
