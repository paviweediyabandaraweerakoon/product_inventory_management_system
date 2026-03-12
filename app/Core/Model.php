<?php

namespace App\Core;

use App\Core\Database;
use PDO;
use PDOStatement;

/**
 * Class Model
 * Base class for all models, providing common database operations.
 */
class Model
{
    /** @var PDO Database connection instance */
    protected PDO $db;

    /** @var string The table associated with the model */
    protected string $table;

    /**
     * Model constructor.
     * Initializes the database connection using Singleton pattern.
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Prepare and execute a SQL query.
     * * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get all records that are not soft-deleted.
     * * @return array
     */
    public function all(): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE deleted_at IS NULL";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the total count of active (non-deleted) records.
     * * @return int
     */
    public function countActiveRecords(): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NULL";
        return (int)$this->query($sql)->fetchColumn();
    }

    /**
     * Find a single record by its ID.
     * * @param int $id
     * @return array|false
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new record into the database.
     * * @param array $data
     * @return string|false
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
     * Update an existing record by ID.
     * * @param int $id
     * @param array $data
     * @return bool
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
        return (bool)$this->query($sql, $values);
    }

    /**
     * Perform a soft delete by setting the deleted_at timestamp.
     * * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?";
        return (bool)$this->query($sql, [$id]);
    }
}