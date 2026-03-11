<?php

namespace App\Core;

use App\Core\Database;
use PDO;

/**
 * Class Model
 * Base class for all models providing common database operations.
 */
class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Executes a SQL query with parameters.
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Find a record by its primary ID.
     * @param int $id
     * @return array|false
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new record in the database.
     * @param array $data
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
     * Update an existing record while checking for soft delete.
     * @param int $id
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
        
        // Consider deleted_at IS NULL before update
        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE id = ? AND deleted_at IS NULL";
        
        $values = array_values($data);
        $values[] = $id;
        
        return (bool)$this->query($sql, $values);
    }

    /**
     * Performs a soft delete on a record.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?";
        return (bool)$this->query($sql, [$id]);
    }
}