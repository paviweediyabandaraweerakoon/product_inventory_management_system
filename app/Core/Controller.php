<?php
namespace App\Core;

class Controller {
    // Function of view
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $view not found");
        }
    }
}