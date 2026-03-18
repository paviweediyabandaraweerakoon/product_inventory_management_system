<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Exception;

/**
 * Class ProductService
 * Handles complex business logic for products to keep Controllers clean.
 */
class ProductService
{
    private Product $productModel;
    private InventoryTransaction $transactionModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->transactionModel = new InventoryTransaction();
    }

    /**
     * Standardized way to handle stock movement with logging
     */
    public function adjustStock(int $productId, string $type, int $qty, string $reason, ?int $userId = null): void
    {
        $product = $this->productModel->findById($productId);
        if (!$product) {
            throw new Exception("Product not found.");
        }

        $currentStock = (int)$product['stock_quantity'];
        $newStock = ($type === 'IN') ? ($currentStock + $qty) : ($currentStock - $qty);

        if ($newStock < 0) {
            throw new Exception("Insufficient stock! Available: $currentStock");
        }

        // 1. Update the actual stock
        $this->productModel->updateStock($productId, $newStock);

        // 2. Log this as a transaction for history/dashboard
        $this->transactionModel->log($productId, $type, $qty, $reason, $userId);
    }
}