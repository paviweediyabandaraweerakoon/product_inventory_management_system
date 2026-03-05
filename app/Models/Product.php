<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Product extends Model
{
    protected $table = 'products';

    /**
     * Database Connection provider (for transactions)
     */
    public function getDb() {
        return $this->db;
    }

    /**
     * Pagination and Search with all Products
     */
    public function getAll($limit = 20, $offset = 0, $search = '') {
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
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search results count (for pagination)
     */
    public function getCount($search = '') {
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
     * Soft Delete, Update, Insert, Find functions (previously defined)
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT p.*, c.category_name FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert new Product (returns new product ID)
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    /**
     * Update Product (only updates provided fields)
     */
    public function updateProduct($id, $data) {
        $fields = "";
        $values = [];
        foreach ($data as $key => $value) {
            $fields .= "{$key} = ?, ";
            $values[] = $value;
        }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        $values[] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Soft Delete (deleted_at)
     */
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Auto-generate SKU if not provided 
     */
    public function generateSKU($categoryId) {
        return "PRD-" . strtoupper(substr(uniqid(), 7));
    }

    /**
     * Update stock quantity with transaction logging
     */
    public function updateStockWithTransaction($productId, $type, $qty, $reason, $userId) {
        $product = $this->find($productId);
        if (!$product) throw new \Exception("Product not found.");

        $currentStock = (int)$product['stock_quantity'];
        $newStock = ($type === 'IN') ? ($currentStock + $qty) : ($currentStock - $qty);

        if ($newStock < 0) {
            throw new \Exception("Insufficient stock!");
        }

        // Product stock update
        $stmt = $this->db->prepare("UPDATE {$this->table} SET stock_quantity = ? WHERE id = ?");
        $stmt->execute([$newStock, $productId]);
        
        // Transaction logging
        $transactionModel = new \App\Models\InventoryTransaction();
        $transactionModel->insert([
            'product_id' => $productId,
            'transaction_type' => $type,
            'quantity' => $qty,
            'reason' => $reason,
            'user_id' => $userId
        ]);
    }
}