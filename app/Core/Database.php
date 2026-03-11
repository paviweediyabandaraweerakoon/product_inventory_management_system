<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Singleton pattern to ensure only one database connection exists.
 */
class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct() {
        $host = Env::get('DB_HOST', '127.0.0.1');
        $db   = Env::get('DB_DATABASE', 'product_inventory_management_system');
        $user = Env::get('DB_USERNAME', 'root');
        $pass = Env::get('DB_PASSWORD', '');

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            die("Database connection error. Please check your logs.");
        }
    }

    public static function getInstance(): Database {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}