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
     * Handle business logic for updating a product including image replacement
     */
    public function updateProduct(int $id, array $data, array $file): bool
    {
        $existingProduct = $this->productModel->findById($id);
        if (!$existingProduct) {
            throw new Exception("Product not found.");
        }

        // Handle Image Update Logic
        if (!empty($file['name'])) {
            $upload = FileUploadHelper::uploadProductImage($file);
            if ($upload['success']) {
                // Delete old image if a new one is uploaded successfully
                FileUploadHelper::deleteProductImage($existingProduct['image_path']);
                $data['image_path'] = $upload['path'];
            }
        } else {
            // Keep existing image if no new file is uploaded
            $data['image_path'] = $existingProduct['image_path'];
        }

        return $this->productModel->updateProduct($id, $data);
    }
}