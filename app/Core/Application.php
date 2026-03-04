<?php

namespace App\Core;

class Application
{
    protected $router;

    public function __construct($router)
    {
        $this->router = $router;
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Internal Server Error: " . $e->getMessage();
        }
    }
}