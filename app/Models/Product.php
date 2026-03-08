<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Product extends Model
{
    protected $table = 'products';

    public function getConnection() {
        return $this->db;
    }

    // Product ලැයිස්තුව ලබා ගැනීම (Search සහ Pagination එක්ක)
    public function getAll($limit = 10, $offset = 0, $search = '')
    {
        $sql = "SELECT p.*, c.category_name FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.deleted_at IS NULL";
        
        if ($search) {
            $sql .= " AND (p.product_name LIKE :s OR p.sku LIKE :s)";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :l OFFSET :o";
        $stmt = $this->db->prepare($sql);
        
        if ($search) $stmt->bindValue(':s', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':l', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':o', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // මුළු නිෂ්පාදන ගණන (Search එකට අනුව)
    public function getCount($search = '')
    {
        $sql = "SELECT COUNT(*) FROM products WHERE deleted_at IS NULL";
        if ($search) {
            $sql .= " AND (product_name LIKE ? OR sku LIKE ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(["%$search%", "%$search%"]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return (int)$stmt->fetchColumn();
    }

    // ID එක අනුව තනි Product එකක් සොයා ගැනීම (Edit වලට අවශ්‍යයි)
    public function find($id)
    {
        $sql = "SELECT * FROM products WHERE id = ? AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // අලුත් Product එකක් Create කිරීම
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO products ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    // පවතින Product එකක් Update කිරීම
    public function update($id, $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = ?, ";
        }
        $fields = rtrim($fields, ", ");
        
        $sql = "UPDATE products SET $fields WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;
        
        return $this->db->prepare($sql)->execute($values);
    }

    // Product එකක් Soft Delete කිරීම
    public function delete($id)
    {
        $sql = "UPDATE products SET deleted_at = NOW() WHERE id = ?";
        return $this->db->prepare($sql)->execute([$id]);
    }

    // Dashboard එකේ Active Records ගණන (Total Products)
    public function countActiveRecords()
    {
        return $this->getCount();
    }

    // SKU එකක් Generate කිරීම
    public function generateSKU()
    {
        return "PRD-" . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    // --- මෙන්න මේ කොටස තමයි FIX කළේ (Line 108) ---
    // Dashboard එකේ Charts සඳහා Query එකක් Run කිරීමේ පහසුකම
    public function query($sql, $params = [])
    {
        if (empty($params)) {
            return $this->db->query($sql);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}