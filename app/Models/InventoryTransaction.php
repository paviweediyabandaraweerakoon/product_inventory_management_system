<?php

namespace App\Core;
namespace App\Models;

use App\Core\Model;

/**
 * Class InventoryTransaction
 * Manages logs for stock movements (IN/OUT) with high-level data integrity.
 */
class InventoryTransaction extends Model
{
    protected string $table = 'inventory_transactions'; 

    /**
     * Logs a new stock transaction using standard model handlers.
     */
    public function log(int $productId, string $type, int $quantity, string $note = '', float $unitPrice = 0.0, ?int $userId = null): string|false
    {
        $data = [
            'product_id'       => $productId,
            'transaction_type' => strtoupper($type), 
            'quantity'         => $quantity,
            'unit_price'       => $unitPrice, 
            'reason'           => $note, 
            'transaction_date' => date('Y-m-d H:i:s'),
            'user_id'          => $userId ?? $_SESSION['user_id'] ?? 1,
            'created_at'       => date('Y-m-d H:i:s')
        ];

        // Core logging logic is handled by the base Model's create method, ensuring consistency and error handling
        return $this->create($data); 
    }
}