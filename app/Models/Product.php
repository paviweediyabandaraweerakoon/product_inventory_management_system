<?php
namespace App\Models;
use App\Core\Model;

class Product extends Model {
    protected $table = 'products';

    public function getAll($limit, $offset, $search = '') {
        $sql = "SELECT p.*, c.category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.product_name LIKE :search OR p.sku LIKE :search 
                ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':search', "%$search%");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCount($search = '') {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE product_name LIKE :search OR sku LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['search' => "%$search%"]);
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_name, sku, description, price, stock_quantity, image_path, category_id) 
                VALUES (:product_name, :sku, :description, :price, :stock_quantity, :image_path, :category_id)";
        return $this->db->prepare($sql)->execute($data);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $fields = "";
        foreach($data as $key => $value) { $fields .= "$key = :$key, "; }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE {$this->table} SET $fields WHERE id = :id";
        $data['id'] = $id;
        return $this->db->prepare($sql)->execute($data);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}