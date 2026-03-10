<?php
session_start();

// 1. Nếu đã đăng nhập thì đá về trang tương ứng
if (isset($_SESSION['vai_tro'])) {
    if ($_SESSION['vai_tro'] === 'giangvien') header("Location: giangvien/index.php");
    elseif ($_SESSION['vai_tro'] === 'thisinh') header("Location: thisinh/timkiemvathamgiathi.php");
    elseif ($_SESSION['vai_tro'] === 'admin') header("Location: admin/bangdieukhientongquan.php");
    exit();
}

$error_message = "";
$success_message = "";

// 2. Xử lý logic Đăng ký giả lập (để test UI)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Mật khẩu xác nhận không khớp! Vui lòng kiểm tra lại.";
    } else {
        $success_message = "Đăng ký thành công! Đang chuyển hướng đến đăng nhập...";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Hệ thống thi trắc nghiệm</title>
    <link rel="stylesheet" href="../asset/css/login.css">
    
    <style>
        .top-header {
            position: absolute; top: 0; left: 0; width: 100%;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 40px; background: #fff; border-bottom: 1px solid #e2e8f0;
            z-index: 10;
        }
        .logo-container { display: flex; align-items: center; gap: 12px; }
        .logo-icon-bg {
            position: relative; background-color: #2563eb; color: #ffffff;
            display: flex; justify-content: center; align-items: center;
            width: 35px; height: 35px; border-radius: 8px;
        }
        .logo-graduation-cap { font-size: 16px; z-index: 1; margin-top: -6px; }
        .logo-book-pages {
            position: absolute; bottom: 6px; width: 22px; height: 10px;
            background-color: transparent; display: flex; justify-content: center; align-items: flex-end;
        }
        .logo-book-pages::before, .logo-book-pages::after {
            content: ""; width: 10px; height: 8px; background-color: #ffffff;
            border-radius: 2px; transform: rotate(-10deg); margin: 0 -1px;
        }
        .logo-book-pages::after { transform: rotate(10deg); }
        .logo-text { color: #1a202c; font-weight: 800; font-size: 18px; }

        .top-right-nav { display: flex; gap: 10px; }
        .btn-nav-login { padding: 8px 15px; background: #f1f5f9; color: #4a5568; font-weight: bold; text-decoration: none; border-radius: 6px; }
        .btn-nav-register { padding: 8px 15px; background: #2563eb; color: #ffffff; font-weight: bold; text-decoration: none; border-radius: 6px; }
        
        body { padding-top: 80px; }
        .header-logo { display: none; }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="logo-container">
            <div class="logo-icon-bg">
                <span class="logo-graduation-cap">&#127891;</span>
                <div class="logo-book-pages"></div>
            </div>
            <span class="logo-text">Hệ thống thi trắc nghiệm</span>
        </div>
        <div class="top-right-nav">
            <a href="login.php" class="btn-nav-login">Đăng nhập</a>
            <a href="register.php" class="btn-nav-register">Đăng ký</a>
        </div>
    </header>

    <div class="login-container" style="width: 1000px;">
        <div class="left-col-blue">
            <h2>Kiến tạo tương lai qua từng đề thi</h2>
            <p>Tham gia ngay để trải nghiệm kho tàng tri thức với hàng ngàn bộ đề trắc nghiệm chuẩn hóa.</p>
            <div class="feature-item">
                <div class="feature-icon">🛡️</div>
                <div class="feature-text">
                    <h4>Hệ thống uy tín</h4>
                    <p>Được tin dùng bởi hơn 10,000 học sinh và giáo viên trên toàn quốc.</p>
                </div>
            </div>
            <img src="../asset/images/register_illustration.jpg" alt="Minh Họa" class="img-register">
        </div>

        <div class="right-col">
            <h1>Tạo tài khoản mới</h1>
            <p>Hãy bắt đầu hành trình chinh phục tri thức cùng chúng tôi</p>

            <?php if (!empty($error_message)): ?>
                <div id="errorMsg" style="display: block; background: #fed7d7; color: #c53030; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; font-weight: bold; text-align: center;">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div id="successMsg" style="display: block; background: #c6f6d5; color: #276749; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; font-weight: bold; text-align: center;">
                    <?php echo $success_message; ?>
                </div>
                <script>setTimeout(() => window.location.href = 'login.php', 2000);</script>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <span class="form-icon">👤</span>
                        <input type="text" name="fullname" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <span class="form-icon">✉️</span>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <span class="form-icon">📞</span>
                        <input type="text" name="phone" placeholder="0901234567" required>
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <span class="form-icon">🎓</span>
                        <select name="role">
                            <option value="thisinh">Thí sinh</option>
                            <option value="giangvien">Giảng viên</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <span class="form-icon">🔒</span>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <span class="form-icon">🔒</span>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>

                <div class="remember-me" style="margin-top: 10px;">
                    <input type="checkbox" id="terms" required>
                    <label for="terms" style="font-size: 13px; color: #4a5568;">Tôi đồng ý với các <a href="#" style="color: #2563eb; text-decoration: none;">Điều khoản & Chính sách</a></label>
                </div>

                <button type="submit" class="btn-submit" style="margin-top: 15px;">Đăng ký tài khoản</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 14px;">
                Đã có tài khoản? <a href="login.php" style="color: #2563eb; font-weight: bold; text-decoration: none;">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</body>
</html>