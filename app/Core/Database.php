<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Singleton pattern to ensure only one database connection exists.
 */
class Database {
    /** @var Database|null */
    private static ?Database $instance = null;

    /** @var PDO */
    private PDO $connection;

    /**
     * Database constructor.
     * Private to prevent direct instantiation.
     */
    private function __construct() {
        // Fetch values from Env class (configured via .env file)
        $host = Env::get('DB_HOST', '127.0.0.1');
        $db   = Env::get('DB_DATABASE', 'product_inventory_management_system');
        $user = Env::get('DB_USERNAME', 'root');
        $pass = Env::get('DB_PASSWORD', '');
        $port = Env::get('DB_PORT', '3306');

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            
            $this->connection = new PDO($dsn, $user, $pass);
            
            // Setting PDO attributes for professional error handling and data fetching
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            // Log connection error for debugging
            error_log(sprintf(
                "[%s] Database Connection failed: %s",
                date('Y-m-d H:i:s'),
                $e->getMessage()
            ));
            
            // Display a clean error message to the user and stop execution (important for security)
            http_response_code(500);
            die("Database connection error. Please check your logs.");
        }
    }

    /**
     * Get the singleton instance of the Database class.
     * * @return Database
     */
    public static function getInstance(): Database {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection instance.
     * * @return PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
}