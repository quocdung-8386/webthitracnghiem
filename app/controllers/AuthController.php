<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{

    public function login()
    {
        $error_message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new AuthService();
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($auth->login($email, $password)) {

                $ma_vai_tro = $_SESSION['user']['ma_vai_tro'] ?? 0;

                if ($ma_vai_tro == 2) {
                    header("Location: ?url=giangvien");
                } elseif ($ma_vai_tro == 3) {
                    header("Location: ?url=thisinh");
                } elseif ($ma_vai_tro == 1) {
                    header("Location: ?url=admin");
                }
                exit();
            } else {
                $error_message = "Email hoặc mật khẩu không chính xác!";
            }
        }
        require __DIR__ . '/../../UI/login.php';
    }

    public function register()
    {
        $error_message = "";
        $success_message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $role = $_POST['role'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($password !== $confirm_password) {
                $error_message = "Mật khẩu xác nhận không khớp!";
            } else {
                $auth = new AuthService();
                if ($auth->register($fullname, $email, $role, $password)) {
                    $success_message = "Đăng ký thành công! Đang chuyển hướng...";
                } else {
                    $error_message = "Đăng ký thất bại! Email có thể đã tồn tại.";
                }
            }
        }

        require __DIR__ . '/../../UI/register.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: ?url=login");
        exit();
    }
}
?>