<?php
namespace App\Requests;

class InventoryRequest {
    public function validate($data) {
        $errors = [];
        
        if (empty($data['quantity']) || $data['quantity'] <= 0) {
            $errors[] = "Quantity must be a positive number.";
        }
        
        if (empty($data['transaction_type']) || !in_array($data['transaction_type'], ['IN', 'OUT'])) {
            $errors[] = "Invalid transaction type.";
        }

        return $errors;
    }
}