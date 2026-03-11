<?php

namespace App\Core;

use App\Core\Database;
use PDO;

class Model
{
    protected PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // all recods get for index page and dashboard widgets
    public function all(): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE deleted_at IS NULL";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Active records count for dashboard widgets
    public function countActiveRecords(): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE deleted_at IS NULL";
        return (int)$this->query($sql)->fetchColumn();
    }

    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string|false
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $sql = "INSERT INTO `{$this->table}` ({$fields}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = "";
        foreach ($data as $key => $value) { $fields .= "`{$key}` = ?, "; }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE `{$this->table}` SET {$fields} WHERE id = ? AND deleted_at IS NULL";
        $values = array_values($data);
        $values[] = $id;
        return (bool)$this->query($sql, $values);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE `{$this->table}` SET deleted_at = NOW() WHERE id = ?";
        return (bool)$this->query($sql, [$id]);
    }
}