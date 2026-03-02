<?php
require_once __DIR__ . '/../models/User.php';

class AuthService {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['mat_khau'])) {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }
}