<?php
session_start();

/* Xóa toàn bộ dữ liệu session */
$_SESSION = [];

/* Hủy session hoàn toàn */
session_destroy();

/* Xóa cookie session nếu có (bảo mật tối đa) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Chuyển hướng về trang login (do logout.php và login.php đứng cạnh nhau nên chỉ cần gọi trực tiếp tên file) */
header("Location: login.php");
exit();
?>