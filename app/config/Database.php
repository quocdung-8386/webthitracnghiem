<?php
class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance == null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost;port=3306;dbname=he_thong_thi_trac_nghiem;charset=utf8mb4",
                    "root",
                    ""
                );
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            } catch (PDOException $e) {
                try {
                    self::$instance = new PDO(
                        "mysql:host=localhost;port=3307;dbname=he_thong_thi_trac_nghiem;charset=utf8mb4",
                        "root",
                        ""
                    );
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e2) {
                    die("Lỗi kết nối CSDL: Máy chủ từ chối kết nối ở cả 2 cổng 3306 và 3307.");
                }
            }
        }
        return self::$instance;
    }
}
?>