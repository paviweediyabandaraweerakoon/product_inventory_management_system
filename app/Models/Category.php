<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Category extends Model {
    protected $table = 'categories';

    public function getAll() {
        $sql = "SELECT c.*, COUNT(p.id) as productCount 
                FROM `{$this->table}` c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE c.deleted_at IS NULL 
                GROUP BY c.id 
                ORDER BY c.id DESC";
        
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // create, update, delete methods දැන් Model class එකෙන් auto ලැබෙනවා.
    // අවශ්‍ය නම් custom logic මෙතන ලියන්න පුළුවන්.
}