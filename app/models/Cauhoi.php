<?php
require_once __DIR__ . '/../app/config/Database.php';

class Cauhoi {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $sql = "SELECT c.*, d.ten_danh_muc 
                FROM cau_hoi c 
                LEFT JOIN danh_muc d ON c.ma_danh_muc = d.ma_danh_muc 
                ORDER BY c.ngay_tao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>