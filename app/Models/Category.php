<?php

namespace App\Models;

use App\Core\Model;
use PDO;

/**
 * Class Category
 * Handles category-specific database operations.
 * Extends Core\Model to reuse database connection and basic CRUD.
 */
class Category extends Model
{
    /**
     * @var string The table associated with this model.
     */
    protected string $table = 'categories';

    /**
     * Category constructor.
     * Calls parent constructor to initialize database connection.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch all active categories with their associated product counts.
     * * @return array Returns an array of category records.
     */
    public function getAll(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as productCount 
                FROM `{$this->table}` c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE c.deleted_at IS NULL 
                GROUP BY c.id 
                ORDER BY c.id DESC";
        
        // Parameterized query is not needed here since there are no user inputs, but we can still use prepared statements for consistency.
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count the number of active categories.
     * @return int Returns the count of active categories.
     */
    public function countActive(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE status = 'active' AND deleted_at IS NULL");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Note: find(), create(), update(), and delete() methods are 
     * inherited from App\Core\Model. No need to rewrite them here 
     * unless category-specific logic is required.
     */
}