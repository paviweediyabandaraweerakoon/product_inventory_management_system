<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    /**
     * Return all categories (stubbed array for now)
     */
    public static function all()
    {
        // In a real implementation this would query the database.
        return [
            ['id' => 1, 'name' => 'Electronics'],
            ['id' => 2, 'name' => 'Books'],
        ];
    }

    /**
     * Create a new category (stubbed)
     */
    public static function create(array $data)
    {
        // Normally insert into database using Database singleton
        // Example: Database::getInstance()->query(...)
        return true;
    }
}
