<?php

namespace App\Core;

class Database
{
    private static $instance;

    private function __construct()
    {
        // Initialize DB connection
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
