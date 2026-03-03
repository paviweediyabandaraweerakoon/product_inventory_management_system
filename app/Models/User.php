<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role_id, status) 
                VALUES (:first_name, :last_name, :email, :password_hash, :role_id, :status)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}