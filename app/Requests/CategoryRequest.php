<?php

namespace App\Requests;

/**
 * Class CategoryRequest
 * Handles validation logic for category-related requests.
 */
class CategoryRequest 
{
    /**
     * @var array Stores validation error messages.
     */
    private array $errors = [];

    /**
     * Validates category data and stores error messages if any.
     * * @param array $data The input data from $_POST or sanitized array.
     * @return bool Returns true if validation passes, false otherwise.
     */
    public function validate(array $data): bool 
    {
        $name = trim($data['category_name'] ?? '');
        $description = trim($data['description'] ?? '');

        // 1. Category Name Validation
        if (empty($name)) {
            $this->errors['category_name'] = "Category name is required.";
        } elseif (strlen($name) < 3) {
            $this->errors['category_name'] = "Category name must be at least 3 characters long.";
        } elseif (strlen($name) > 50) {
            $this->errors['category_name'] = "Category name cannot exceed 50 characters.";
        }

        // 2. Description Validation (Optional but can have a limit)
        if (strlen($description) > 255) {
            $this->errors['description'] = "Description is too long (max 255 characters).";
        }

        // Returns true only if the errors array is empty
        return empty($this->errors);
    }

    /**
     * Retrieves all validation error messages.
     * * @return array
     */
    public function getErrors(): array 
    {
        return $this->errors;
    }
}