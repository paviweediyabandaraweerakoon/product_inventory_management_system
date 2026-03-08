<?php
namespace App\Core;

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        
        $viewFile = __DIR__ . "/../Views/{$view}.php";
        
        if (file_exists($viewFile)) {
            // View එකේ content එක විතරක් variable එකකට ගන්නවා
            ob_start();
            require $viewFile; // require_once වෙනුවට require පාවිච්චි කරන්න
            $content = ob_get_clean();
            
            // Layout එක load කරනවා. Layout එක ඇතුළේ $content variable එක print වෙනවා.
            $layoutFile = __DIR__ . "/../Views/layout.php";
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            die("Error: View file [{$view}] not found in: " . $viewFile);
        }
    }
}