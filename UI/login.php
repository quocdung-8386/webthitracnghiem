<?php
session_start();

if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'giangvien') {
    header("Location: giangvien/quanlynganhangcauhoi.php");
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- DỮ LIỆU GIẢ ĐĂNG NHẬP ---
    if ($username === 'giangvien' && $password === '123456') {
        $_SESSION['vai_tro'] = 'giangvien';
        $_SESSION['ho_ten'] = 'GV. Nguyễn Văn A';

        // ĐỔI DÒNG NÀY: Trỏ về index.php (Trang Dashboard mới)
        header("Location: giangvien/index.php");
        exit();
    }
    // Trường hợp sai tài khoản/mật khẩu
    else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
 if ($username === 'thisinh' && $password === '123456') {
        
        $_SESSION['vai_tro'] = 'thisinh';
        $_SESSION['ho_ten'] = 'TS. Trần Thị B';
        
        // Đăng nhập thành công -> Chuyển hướng
        header("Location: thisinh/timkiemvathamgiathi.php");
        exit();
    }
    // Trường hợp sai tài khoản/mật khẩu
    else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
    if ($username === 'admin' && $password === '123456') {
        
        $_SESSION['vai_tro'] = 'admin';
        $_SESSION['ho_ten'] = 'Admin. Phạm Văn C';
        
        // Đăng nhập thành công -> Chuyển hướng
        header("Location: admin/bangdieukhientongquan.php");
        exit();
    }
    // Trường hợp sai tài khoản/mật khẩu
    else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - EduQuiz</title>
    <link rel="stylesheet" href="../asset/css/login.css">
</head>

<body>

    <div class="header-logo">🎓 EduQuiz</div>
    <div class="top-right-nav">
        Đăng nhập <a href="register.php" class="btn-register-top">Đăng ký</a>
    </div>

    <div class="login-container">
        <div class="left-col">
            <div class="img-placeholder">📺 Hình minh họa</div>
            <h2>Trải nghiệm học tập mới</h2>
            <p>Hệ thống thi trắc nghiệm trực tuyến thông minh, giúp bạn đánh giá năng lực một cách chính xác nhất.</p>
        </div>

        <div class="right-col">
            <h1>Chào mừng trở lại</h1>
            <p>Vui lòng nhập thông tin để tiếp tục bài thi của bạn</p>

            <?php if (!empty($error_message)): ?>
                <div id="errorMsg" style="display: block;">
                    <?php echo $error_message; ?>
                </div>
            <?php else: ?>
                <div id="errorMsg"></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>Email hoặc Tên đăng nhập</label>
                    <span class="form-icon">✉️</span>
                    <input type="text" name="username" placeholder="Nhập tên đăng nhập (VD: giangvien)" required>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <a href="#" class="forgot-pw">Quên mật khẩu?</a>
                    <span class="form-icon">🔒</span>
                    <input type="password" name="password" placeholder="Nhập mật khẩu (VD: 123456)" required>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember">
                    <label for="remember">Ghi nhớ phiên đăng nhập</label>
                </div>

                <button type="submit" class="btn-submit">Đăng nhập ngay →</button>
            </form>

            <div style="text-align: center; margin: 20px 0; color: #a0aec0; font-size: 12px; position: relative;">
                HOẶC TIẾP TỤC VỚI
            </div>
            <div class="social-login">
                <button class="btn-social">Google</button>
                <button class="btn-social">Facebook</button>
            </div>

            <p style="text-align: center; margin-top: 25px; margin-bottom: 0;">
                Bạn là thành viên mới? <a href="register.php"
                    style="color: #2563eb; font-weight: bold; text-decoration: none;">Tạo tài khoản miễn phí</a>
            </p>
        </div>
    </div>
</body>

</html>