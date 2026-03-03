<?php

namespace App\Core;

use App\Core\Database;
use PDO;

class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        // Database connection, get Singleton pattern
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Raw Query execution for custom SQLs
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get all records from the table
     */
    public function all()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a record by ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    
     // Insert Data and return Last Insert ID
     
    
    public function insert($data)
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        
        // Return the ID of the newly inserted row
        return $this->db->lastInsertId();
    }

    /**
     * Update Record by ID
     */
    public function updateRecord($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = ?, ";
        }
        $fields = rtrim($fields, ", ");
        
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        return $this->query($sql, $values);
    }

    /**
     * Generic Soft Delete
     */
    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}