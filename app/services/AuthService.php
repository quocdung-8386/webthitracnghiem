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

    public function register($fullname, $email, $role, $password) {
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) return false;

        $usernameParts = explode('@', $email);
        $username = $usernameParts[0] . rand(10,99);

        $ma_vai_tro = 3;
        if ($role === 'giangvien') {
            $ma_vai_tro = 2;
        } elseif ($role === 'admin') {
            $ma_vai_tro = 1;
        }

        $data = [
            'role'     => $ma_vai_tro,
            'name'     => $fullname,
            'username' => $username,
            'email'    => $email,
            'password' => $password 
        ];

        return $this->userModel->create($data);
    }
}
?>