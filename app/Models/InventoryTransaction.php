<?php
namespace App\Models;

use App\Core\Model;

class InventoryTransaction extends Model {
    
    public function log($productId, $type, $qty, $reason, $userId) {
        $sql = "INSERT INTO inventory_transactions (product_id, transaction_type, quantity, reason, user_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$productId, $type, $qty, $reason, $userId]);
    }

    public function getHistoryByProduct($productId) {
        $sql = "SELECT t.*, u.first_name FROM inventory_transactions t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.product_id = ? ORDER BY t.transaction_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
}