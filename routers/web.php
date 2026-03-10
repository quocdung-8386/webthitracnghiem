<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/GiangVienController.php';
require_once __DIR__ . '/../app/controllers/ThiSinhController.php';

$url = $_GET['url'] ?? '';

switch ($url) {
    case '':
        require_once __DIR__ . '/../UI/login.php';
        break;

    case 'login':
        (new AuthController())->login();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'admin':
        (new AdminController())->dashboard();
        break;

    case 'giangvien':
        (new GiangVienController())->dashboard();
        break;

    case 'giangvien/cauhoi':
        (new GiangVienController())->quanLyCauHoi();
        break;

    case 'thisinh':
        (new ThiSinhController())->dashboard();
        break;

    default:
        echo "404 - Không tìm thấy trang";
}