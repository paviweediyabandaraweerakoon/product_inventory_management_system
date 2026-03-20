<?php

namespace App\Requests;

/**
 * Class ProductRequest
 * Validates product-related request data before storing/updating in DB.
 */
class ProductRequest
{
    /**
     * @var array<string,string> Validation errors
     */
    protected array $errors = [];

    /**
     * @var array<string,mixed> Form data
     */
    protected array $data = [];

    /**
     * ProductRequest constructor.
     *
     * @param array<string,mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate request data according to rules.
     *
     * @return bool True if valid, false if errors exist
     */
    public function validate(): bool
    {
        $this->errors = []; // Reset errors

        // 1. Product Name
        if (empty($this->data['product_name'])) {
            $this->errors['product_name'] = 'Product name is required.';
        } elseif (strlen($this->data['product_name']) > 255) {
            $this->errors['product_name'] = 'Product name cannot exceed 255 characters.';
        }

        // 2. Category ID
        if (empty($this->data['category_id']) || !is_numeric($this->data['category_id'])) {
            $this->errors['category_id'] = 'Please select a valid category.';
        }

        // 3. Price
        if (!isset($this->data['price']) || !is_numeric($this->data['price']) || (float)$this->data['price'] < 0) {
            $this->errors['price'] = 'Please enter a valid positive price.';
        }

        // 4. Stock Quantity
        if (!isset($this->data['stock_quantity']) || !is_numeric($this->data['stock_quantity']) || (int)$this->data['stock_quantity'] < 0) {
            $this->errors['stock_quantity'] = 'Stock quantity must be a non-negative number.';
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