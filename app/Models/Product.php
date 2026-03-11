<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

/**
 * Class Product
 * Handles all database operations for Products.
 */
class Product extends Model
{
    protected string $table = 'products';

    /**
     * Get all products with pagination and search.
     * * @param int $limit
     * @param int $offset
     * @param string $search
     * @return array
     */
    public function getAll(int $limit = 20, int $offset = 0, string $search = ''): array
    {
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL";

        if (!empty($search)) {
            $sql .= " AND (p.product_name LIKE :search OR p.sku LIKE :search)";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of active products for pagination.
     * * @param string $search
     * @return int
     */
    public function getCount(string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM products WHERE deleted_at IS NULL";
        if (!empty($search)) {
            $sql .= " AND (product_name LIKE :search OR sku LIKE :search)";
        }
        $stmt = $this->db->prepare($sql);
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Find product by its ID with category details.
     * * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT p.*, c.category_name FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update stock quantity for a specific product.
     * * @param int $id
     * @param int $newStock
     * @return bool
     */
    public function updateStock(int $id, int $newStock): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET stock_quantity = ? WHERE id = ? AND deleted_at IS NULL");
        return $stmt->execute([$newStock, $id]);
    }

    /**
     * Update stock levels and log the transaction.
     * * @param int $productId
     * @param string $type
     * @param int $qty
     * @param string $reason
     * @param int|null $userId
     * @throws Exception
     */
    public function updateStockWithTransaction(int $productId, string $type, int $qty, string $reason, ?int $userId = null): void 
    {
        $product = $this->findById($productId);
        if (!$product) throw new Exception("Product not found.");

        $currentStock = (int)$product['stock_quantity'];
        $newStock = ($type === 'IN') ? ($currentStock + $qty) : ($currentStock - $qty);

        if ($newStock < 0) {
            throw new Exception("Insufficient stock! Current stock: $currentStock");
        }

        // 1. Update stock in products table
        $this->updateStock($productId, $newStock);
        
        // 2. Log transaction
        $transactionModel = new InventoryTransaction();
        $transactionModel->log($productId, $type, $qty, $reason, $userId);
    }
}