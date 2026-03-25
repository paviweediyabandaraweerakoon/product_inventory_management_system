<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class Product
 * Handles standard database interactions for the products table.
 */
class Product extends Model
{
    protected string $table = 'products';

    /**
     * Get all non-deleted products with category info.
     */
    public function all(): array
    {
        $sql = "SELECT p.*, c.category_name,
                       (p.stock_quantity <= p.low_stock_threshold) AS is_low_stock
                FROM {$this->table} p
                LEFT JOIN categories c
                    ON p.category_id = c.id
                   AND c.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                ORDER BY p.created_at DESC, p.id DESC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find specific product by ID.
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT *
                FROM {$this->table}
                WHERE id = ?
                  AND deleted_at IS NULL";

        return $this->query($sql, [$id])->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update stock level only.
     */
    public function updateStock(int $id, int $newStock): bool
    {
        $sql = "UPDATE {$this->table}
                SET stock_quantity = ?
                WHERE id = ?
                  AND deleted_at IS NULL";

        return $this->query($sql, [$newStock, $id])->rowCount() > 0;
    }

    /**
     * Update an existing product record, including status and other details.
     */
    public function updateProduct(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET product_name = :product_name,
                    sku = :sku,
                    description = :description,
                    category_id = :category_id,
                    price = :price,
                    stock_quantity = :stock_quantity,
                    low_stock_threshold = :low_stock_threshold,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id AND deleted_at IS NULL";

        // Bind the product ID for the WHERE clause
        $data['id'] = $id;

        return $this->query($sql, $data)->rowCount() > 0;
    }

    /**
     * Count active (non-deleted) products.
     */
    public function countActiveRecords(): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE deleted_at IS NULL
                  AND status = 'active'";

        return (int) $this->query($sql)->fetchColumn();
    }

    /**
     * Count low stock active products using configurable threshold.
     */
    public function countLowStockProducts(): int
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE stock_quantity <= low_stock_threshold
                  AND status = 'active'
                  AND deleted_at IS NULL";

        return (int) $this->query($sql)->fetchColumn();
    }

    /**
     * Get current total inventory value for active products.
     */
    public function getTotalInventoryValue(): float
    {
        $sql = "SELECT COALESCE(SUM(price * stock_quantity), 0) AS total_value
                FROM {$this->table}
                WHERE status = 'active'
                  AND deleted_at IS NULL";

        $result = $this->query($sql)->fetch(PDO::FETCH_ASSOC);

        return (float) ($result['total_value'] ?? 0);
    }

    /**
     * Get active product count per active category.
     */
    public function getCategoryDistribution(): array
    {
        $sql = "SELECT c.category_name AS name, COUNT(p.id) AS count
                FROM categories c
                LEFT JOIN {$this->table} p
                    ON c.id = p.category_id
                   AND p.deleted_at IS NULL
                   AND p.status = 'active'
                WHERE c.status = 'active'
                  AND c.deleted_at IS NULL
                GROUP BY c.id, c.category_name
                ORDER BY c.category_name ASC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get latest active products.
     */
    public function getRecentProducts(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        $sql = "SELECT *
                FROM {$this->table}
                WHERE deleted_at IS NULL
                  AND status = 'active'
                ORDER BY created_at DESC, id DESC
                LIMIT {$limit}";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Soft delete product.
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET deleted_at = NOW()
                WHERE id = ?
                  AND deleted_at IS NULL";

        return $this->query($sql, [$id])->rowCount() > 0;
    }

    /**
     * Create new product record.
     */
    public function create(array $data): string
    {
        $sql = "INSERT INTO {$this->table}
                    (product_name, sku, description, category_id, price, stock_quantity, 
                     low_stock_threshold, status, image_path, created_by, created_at)
                VALUES
                    (:product_name, :sku, :description, :category_id, :price, :stock_quantity, 
                     :low_stock_threshold, :status, :image_path, :created_by, NOW())";

        $this->query($sql, $data);

        return $this->db->lastInsertId();
    }
}