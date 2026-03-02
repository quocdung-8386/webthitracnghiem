<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO nguoi_dung (ma_vai_tro, ho_ten, ten_dang_nhap, email, mat_khau)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['role'],
            $data['name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }
}