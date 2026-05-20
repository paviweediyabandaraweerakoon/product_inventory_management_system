<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryTransaction;
use App\Helpers\FileUploadHelper;
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
        $product = $this->productModel->find($productId);
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

    /**
     * Handle business logic for creating a product
     */
    public function createProduct(array $data, array $file): string|false
    {
        // 1. Generate SKU if not provided (Business Logic)
        $data['sku'] = !empty($data['sku']) ? $data['sku'] : 'SKU-' . strtoupper(bin2hex(random_bytes(4)));

        // 2. Handle product image upload (Business Logic)
        $upload = FileUploadHelper::uploadProductImage($file);
        $data['image_path'] = $upload['success'] ? $upload['path'] : null;

        // 3. Set audit data from session
        $data['created_by'] = $_SESSION['user_id'] ?? 1;

        // 4. Delegate database insertion to Model
        return $this->productModel->create($data);
    }

    /**
     * Update an existing product by handling data formatting and type-casting (SRP Compliant)
     *
     * @param int $id
     * @param array $data Raw input data from the controller
     * @return bool
     */
    public function updateProduct(int $id, array $data): bool
    {
        $existingProduct = $this->productModel->find($id);
        if (!$existingProduct) {
            throw new Exception("Product not found.");
        }

        $updateData = [
            'product_name'        => trim($data['product_name']),
            'sku'                 => trim($data['sku']),
            'description'         => !empty($data['description']) ? trim($data['description']) : null,
            'category_id'         => (int) $data['category_id'],
            'price'               => (float) $data['price'],
            'stock_quantity'      => (int) $data['stock_quantity'],
            'low_stock_threshold' => (int) $data['low_stock_threshold'],
            'status'              => $data['status'] ?? 'active'
        ];

        return $this->productModel->updateProduct($id, $updateData);
    }
}