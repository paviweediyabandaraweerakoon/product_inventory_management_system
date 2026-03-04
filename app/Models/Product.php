<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Product extends Model
{
    protected $table = 'products';

    
    //Get all products with pagination and search
     
    public function getAll($limit = 20, $offset = 0, $search = '')
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
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount($search = '')
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
    
     //Find product by id
     
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.category_name FROM products p 
                INNER JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        return $this->findById($id);
    }

    
     // Stock  update method
    
    public function updateStock($id, $newStock)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET stock_quantity = ? WHERE id = ?");
        return $stmt->execute([$newStock, $id]);
    }

    
     //Inventory update
     
    public function updateStockWithTransaction($productId, $type, $qty, $reason, $userId) {
        $product = $this->find($productId);
        if (!$product) throw new \Exception("Product not found.");

        $currentStock = (int)$product['stock_quantity'];
        
        // Stock count in Model
        $newStock = ($type === 'IN') ? ($currentStock + $qty) : ($currentStock - $qty);

        if ($newStock < 0) {
            throw new \Exception("Insufficient stock! Current stock: $currentStock");
        }

        // 1. In Product Table update stock
        $this->updateStock($productId, $newStock);
        
        // 2. Note Transaction in InventoryTransaction Table
        $transactionModel = new \App\Models\InventoryTransaction();
        $transactionModel->log($productId, $type, $qty, $reason, $userId);
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function updateProduct($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = ?, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function deleteProduct($id)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function generateSKU($categoryId)
    {
        return "PRD-" . strtoupper(substr(uniqid(), 7));
    }
}