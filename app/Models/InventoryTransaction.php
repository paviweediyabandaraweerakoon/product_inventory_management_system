<?php

namespace App\Models;

use App\Core\Model;

/**
 * Class InventoryTransaction
 * Manages logs for stock movements (IN/OUT).
 */
class InventoryTransaction extends Model
{
    protected string $table = 'inventory_transactions'; 

    /**
     * Logs a new stock transaction.
     * * @param int $productId
     * @param string $type
     * @param int $quantity
     * @param string $note
     * @param int|null $userId
     * @return string|false
     */
    public function log(int $productId, string $type, int $quantity, string $note = '', ?int $userId = null): string|false
    {
        $data = [
            'product_id'       => $productId,
            'transaction_type' => strtoupper($type), 
            'quantity'         => $quantity,
            'unit_price'       => 0, 
            'reason'           => $note, 
            'transaction_date' => date('Y-m-d H:i:s'),
            'user_id'          => $userId ?? $_SESSION['user_id'] ?? 1,
            'created_at'       => date('Y-m-d H:i:s')
        ];

        return $this->create($data); 
    }
}