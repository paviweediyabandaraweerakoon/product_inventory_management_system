<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class Product
 * Standard Database interactions for Products table.
 */
class Product extends Model
{
    protected string $table = 'products';

    /**
     * Get all products with essential category info
     */
    public function all(): array
    {
        $sql = "SELECT p.*, c.category_name 
                FROM {$this->table} p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL 
                ORDER BY p.created_at DESC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find specific product by ID
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update stock level only
     */
    public function updateStock(int $id, int $newStock): bool
    {
        $sql = "UPDATE {$this->table} SET stock_quantity = ? WHERE id = ? AND deleted_at IS NULL";
        return $this->query($sql, [$newStock, $id])->rowCount() > 0;
    }

    /**
     * Dashboard Helper: Count active products
     */
    public function countActiveRecords(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE deleted_at IS NULL";
        return (int)$this->query($sql)->fetchColumn();
    }

    /**
     * Soft delete product
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        return $this->query($sql, [$id])->rowCount() > 0;
    }

    /**
     * Create new product record
     * Return type matched with Core\Model (string|false) for consistency, but actually returns lastInsertId which is string on success.
     */
    public function create(array $data): string|false
    {
        $sql = "INSERT INTO {$this->table} (product_name, category_id, price, stock_quantity, status, created_at) 
                VALUES (:product_name, :category_id, :price, :stock_quantity, :status, NOW())";
        
        $this->query($sql, $data);
        
        // Parent class create method returns lastInsertId on success, false on failure. This is consistent with the expected return type.
        return $this->db->lastInsertId();
    }
}