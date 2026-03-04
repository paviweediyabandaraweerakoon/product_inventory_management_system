<?php

namespace App\Core;

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);

        $file = __DIR__ . "/../Views/{$view}.php";

        if (file_exists($file)) {
            require_once $file;
        } else {
            die("Error: View file [{$view}] not found in: " . $file);
        }
    }
}