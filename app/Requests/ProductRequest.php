<?php

namespace App\Requests;

class ProductRequest
{
    protected $errors = [];
    protected $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    // Check if the request data is valid according to our rules
    
    public function validate()
    {
        // 1. Product Name must not be empty
        if (empty($this->data['product_name'])) {
            $this->errors['product_name'] = "Product name is required.";
        }

        // 2. Selected Category ID 
        if (empty($this->data['category_id'])) {
            $this->errors['category_id'] = "Please select a category.";
        }

        // 3. Price must be a positive number
        if (!isset($this->data['price']) || !is_numeric($this->data['price']) || $this->data['price'] < 0) {
            $this->errors['price'] = "Please enter a valid price.";
        }

        // 4. Stock Quantity must be a number
        if (!isset($this->data['stock_quantity']) || !is_numeric($this->data['stock_quantity'])) {
            $this->errors['stock_quantity'] = "Stock quantity must be a number.";
        }

        return empty($this->errors);
    }

    
      //Errors get
     
    public function getErrors()
    {
        return $this->errors;
    }
}