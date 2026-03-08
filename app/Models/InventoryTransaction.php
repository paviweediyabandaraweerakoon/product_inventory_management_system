<?php
namespace App\Models;

use App\Core\Model;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions'; 

    public function log($productId, $type, $quantity, $note = '')
    {
        $data = [
            'product_id'       => $productId,
            'transaction_type' => strtoupper($type), // 'IN' ලෙස යයි
            'quantity'         => $quantity,
            'unit_price'       => 0, 
            'reason'           => $note, 
            'transaction_date' => date('Y-m-d H:i:s'),
            'user_id'          => $_SESSION['user_id'] ?? 1,
            'created_at'       => date('Y-m-d H:i:s')
        ];

        return $this->create($data); 
    }
}