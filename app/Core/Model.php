<?php

namespace App\Core;

use App\Core\Database;
use PDO;
use PDOStatement;

/**
 * Class Model
 * Base class for all models, providing common database operations with soft-delete support.
 */
class Model
{
    /** @var PDO Database connection instance */
    protected PDO $db;

    /** @var string The table associated with the model */
    protected string $table;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Prepare and execute a SQL query safely.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get all active records.
     */
    public function all(): array
    {
        return $this->query("SELECT * FROM `{$this->table}` WHERE deleted_at IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the total count of active records for Dashboard.
     */
    public function countActiveRecords(): int
    {
        return (int)$this->query("SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NULL")->fetchColumn();
    }

    /**
     * Find a single active record by ID.
     */
    public function find(int $id): array|false
    {
        return $this->query("SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL", [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new record and return last ID.
     */
    public function create(array $data): string|false
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update an active record.
     */
    public function update(int $id, array $data): bool
    {
        $fields = "";
        foreach ($data as $key => $value) { 
            $fields .= "`{$key}` = ?, "; 
        }
        $fields = rtrim($fields, ", ");
        
        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE id = ? AND deleted_at IS NULL";
        $values = array_values($data);
        $values[] = $id;
        
        return $this->query($sql, $values)->rowCount() > 0;
    }

    /**
     * Perform a soft delete.
     */
    public function delete(int $id): bool
    {
        return $this->query("UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?", [$id])->rowCount() > 0;
    }
}