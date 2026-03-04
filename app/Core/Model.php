<?php

namespace App\Core;

use App\Core\Database;
use PDO;

class Model
{
    /** @var PDO */
    protected $db;
    /** @var string */
    protected $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Prepare and execute a raw query, returning the PDOStatement
     */
    public function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Return all non-deleted records
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert record and return last insert id
     */
    public function insert(array $data)
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update record by id
     */
    public function updateRecord($id, array $data)
    {
        $fields = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        return $this->query($sql, $values);
    }

    /**
     * Soft delete
     */
    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }

    /**
     * Count records by status add new
     */
    public function countActiveRecords()
    {
        // Not deleted and status is active records count
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL AND status = 'active'";
        $result = $this->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}