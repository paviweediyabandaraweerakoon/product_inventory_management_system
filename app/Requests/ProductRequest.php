<?php
namespace App\Requests;

class ProductRequest
{
    protected array $errors = [];
    protected array $data = [];
    protected array $files = [];

    public function __construct($data, $files = [])
    {
        $this->data = $data;
        $this->files = $files;
    }

    public function validate(): bool
    {
        // Name Validation
        if (empty($this->data['product_name']) || strlen($this->data['product_name']) < 3) {
            $this->errors['product_name'] = "Product name is required (min 3 chars).";
        }

        // Category Validation
        if (empty($this->data['category_id'])) {
            $this->errors['category_id'] = "Please select a category.";
        }

        // Price Validation
        if (!isset($this->data['price']) || !is_numeric($this->data['price']) || $this->data['price'] <= 0) {
            $this->errors['price'] = "Price must be a positive number.";
        }

        // Stock Validation
        if (!isset($this->data['stock_quantity']) || !is_numeric($this->data['stock_quantity']) || $this->data['stock_quantity'] < 0) {
            $this->errors['stock_quantity'] = "Stock quantity cannot be negative.";
        }

        // Secure Image Validation (Requirement 6)
        if (!empty($this->files['image']['name'])) {
            $file = $this->files['image'];
            $allowedExts = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Check MIME Type (Mandatory requirement)
            if (file_exists($file['tmp_name'])) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($ext, $allowedExts) || strpos($mime, 'image/') !== 0) {
                    $this->errors['image'] = "Only JPG, JPEG & PNG files are allowed.";
                } elseif ($file['size'] > 5000000) { // 5MB limit
                    $this->errors['image'] = "File size must be less than 5MB.";
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array { return $this->errors; }
    public function getData(): array { return $this->data; }
}