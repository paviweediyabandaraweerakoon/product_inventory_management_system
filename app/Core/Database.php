<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance;
    /** @var PDO|null */
    private $pdo;

    private function __construct()
    {
        $configPath = __DIR__ . '/../../config/config.php';
        if (!file_exists($configPath)) {
            throw new \RuntimeException('Config file not found: ' . $configPath);
        }
        $config = require $configPath;
        $db = $config['db'] ?? [];

        $host = $db['host'] ?? '127.0.0.1';
        $name = $db['name'] ?? 'product_inventory_management_system';
        $user = $db['user'] ?? '';
        $pass = $db['pass'] ?? '';

        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // In production, handle this more gracefully
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Return underlying PDO connection
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
