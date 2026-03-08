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
        // {id} වගේ ඒවා regex එකට හරවනවා
        return "@^" . preg_replace('/\{([a-zA-r0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path) . "$@";
    }

    public function resolve() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];
        
        // URL එකේ Query parameters (?id=1) අයින් කරනවා
        $path = explode('?', $uri)[0];

        // XAMPP වල subfolders වලදී එන base path එක අයින් කරන කෑල්ල
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = str_replace('/index.php', '', $scriptName);
        
        if ($basePath !== '' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // path එක හිස් වුණොත් ඒක root එක "/" කරනවා
        if (empty($path)) {
            $path = '/';
        }

        // Routes පීරලා බලනවා (Loop)
        foreach ($this->routes[$method] as $route => $handler) {
            if (preg_match($route, $path, $matches)) {
                // Named parameters ටික විතරක් පෙරලා ගන්නවා
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($handler)) {
                    $className = $handler[0];
                    $methodName = $handler[1];

                    if (class_exists($className)) {
                        $controller = new $className();
                        
                        if (method_exists($controller, $methodName)) {
                            // මෙන්න මෙතනදී තමයි Controller එකේ method එකට params ටික යවන්නේ
                            return call_user_func_array([$controller, $methodName], $params);
                        }
                        
                        die("Error: Method '$methodName' not found in class '$className'");
                    }
                    
                    die("Error: Controller class '$className' not found. Make sure namespaces are correct and 'composer dump-autoload' is run.");
                }

                // Handler එක simple function එකක් (Closure) වුණොත්
                if (is_callable($handler)) {
                    return call_user_func_array($handler, $params);
                }
            }
        }

        // මොන රූට් එකක්වත් මැච් වුණේ නැත්නම් 404
        http_response_code(404);
        echo "404 - Page Not Found (Router could not find match for: " . htmlspecialchars($path) . ")";
    }
}