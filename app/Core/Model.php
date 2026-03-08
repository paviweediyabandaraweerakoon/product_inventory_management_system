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
        $this->db = Database::getInstance()->getConnection();
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function all()
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE deleted_at IS NULL";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    public function countActiveRecords()
    {
        // Table name එක check කරලා SQL එක හදනවා
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}` WHERE status = 'active' AND deleted_at IS NULL";
        $stmt = $this->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    public function create($data)
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "`{$key}` = ?, ";
        }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        return $this->query($sql, $values);
    }

    public function delete($id)
    {
        $sql = "UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}