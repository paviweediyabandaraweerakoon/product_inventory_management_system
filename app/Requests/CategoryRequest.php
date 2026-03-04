<?php
namespace App\Requests;

class CategoryRequest {
    public function validate($data) {
        $name = trim($data['category_name'] ?? '');

        // If the name is empty or less than 3 characters, it's not valid
        if (empty($name) || strlen($name) < 3) {
            return false;
        }

        return true;
    }
}