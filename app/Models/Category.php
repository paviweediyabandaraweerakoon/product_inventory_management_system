<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Category extends Model {
    protected $table = 'categories';

    /**
     * Get all categories with product count
     * Assignment Requirement: Database Interaction
     */
    public function getAll() {
        $sql = "SELECT c.*, COUNT(p.id) as productCount 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE c.deleted_at IS NULL 
                GROUP BY c.id 
                ORDER BY c.id DESC";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single category by ID
     * Assignment Requirement: PDO Prepared Statements
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new category
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (category_name, description, status, created_by) 
                VALUES (:category_name, :description, :status, :created_by)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'category_name' => $data['category_name'],
            'description'   => $data['description'],
            'status'        => $data['status'] ?? 'active',
            'created_by'    => $data['created_by']
        ]);
    }

    /**
     * Update an existing category
     * Assignment Requirement: OOP concepts
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET category_name = :category_name, 
                    description = :description, 
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'            => $id,
            'category_name' => $data['category_name'],
            'description'   => $data['description'],
            'status'        => $data['status']
        ]);
    }

    /**
     * Soft delete a category
     * Assignment Requirement: Database design standards
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}