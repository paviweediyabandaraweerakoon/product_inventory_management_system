<?php

namespace App\Requests;

/**
 * Class ProductRequest
 *
 * Responsibility:
 * Validate product input data only.
 */
class ProductRequest
{
    /**
     * Validation errors.
     *
     * @var array<string,string>
     */
    protected array $errors = [];

    /**
     * Form data.
     *
     * @var array<string,mixed>
     */
    protected array $data = [];

    /**
     * ProductRequest constructor.
     *
     * @param array<string,mixed> $data Request payload
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate product data for create/update actions.
     *
     * @param bool $isUpdate Whether validation is for update action
     *
     * @return bool
     */
    public function validate(bool $isUpdate = false): bool
    {
        $this->errors = [];

        $productName = trim((string) ($this->data['product_name'] ?? ''));
        $sku = trim((string) ($this->data['sku'] ?? ''));
        $description = trim((string) ($this->data['description'] ?? ''));
        $status = strtolower(trim((string) ($this->data['status'] ?? 'active')));
        $categoryId = $this->data['category_id'] ?? null;
        $price = $this->data['price'] ?? null;
        $stockQuantity = $this->data['stock_quantity'] ?? null;
        $lowStockThreshold = $this->data['low_stock_threshold'] ?? null;

        // Product Name
        if ($productName === '') {
            $this->errors['product_name'] = 'Product name is required.';
        } elseif (mb_strlen($productName) > 255) {
            $this->errors['product_name'] = 'Product name cannot exceed 255 characters.';
        }

        // SKU (optional, but validate format if provided)
        if ($sku !== '') {
            if (mb_strlen($sku) > 100) {
                $this->errors['sku'] = 'SKU cannot exceed 100 characters.';
            } elseif (!preg_match('/^[A-Za-z0-9\-_]+$/', $sku)) {
                $this->errors['sku'] = 'SKU may contain only letters, numbers, hyphens, and underscores.';
            }
        }

        // Description
        if ($description !== '' && mb_strlen($description) > 2000) {
            $this->errors['description'] = 'Description cannot exceed 2000 characters.';
        }

        // Category ID
        if ($categoryId === null || $categoryId === '' || !ctype_digit((string) $categoryId) || (int) $categoryId <= 0) {
            $this->errors['category_id'] = 'Please select a valid category.';
        }

        // Price
        if ($price === null || $price === '' || !is_numeric($price) || (float) $price < 0) {
            $this->errors['price'] = 'Please enter a valid non-negative price.';
        }

        // Stock Quantity
        if ($stockQuantity === null || $stockQuantity === '' || !ctype_digit((string) $stockQuantity)) {
            $this->errors['stock_quantity'] = 'Stock quantity must be a non-negative whole number.';
        }

        // Low Stock Threshold
        if ($lowStockThreshold === null || $lowStockThreshold === '' || !ctype_digit((string) $lowStockThreshold)) {
            $this->errors['low_stock_threshold'] = 'Low stock threshold must be a non-negative whole number.';
        }

        // Status
        if (!in_array($status, ['active', 'inactive'], true)) {
            $this->errors['status'] = 'Please select a valid status.';
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors.
     *
     * @return array<string,string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}