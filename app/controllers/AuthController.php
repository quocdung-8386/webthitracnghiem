<?php
require_once __DIR__ . '/../services/AuthService.php';

class AuthController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new AuthService();
            if ($auth->login($_POST['email'], $_POST['password'])) {
                header("Location: index.php");
                exit;
            }
            echo "Sai thông tin đăng nhập!";
        }

        require __DIR__ . '/../../UI/login.php';
    }

    public function logout() {
        session_destroy();
        header("Location: login.php");
    }
}