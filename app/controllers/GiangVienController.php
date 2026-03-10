<?php
require_once __DIR__ . '/../models/Cauhoi.php';

class GiangVienController {

    public function dashboard() {
        require_once __DIR__ . '/../../UI/giangvien/index.php';
    }

    public function quanLyCauHoi() {
        $cauhoiModel = new Cauhoi();
        
        $danhSachCauHoi = $cauhoiModel->getAll();

        require_once __DIR__ . '/../../UI/giangvien/quanlynganhangcauhoi.php';
    }
}
?>