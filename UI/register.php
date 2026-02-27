<?php
session_start();

if (isset($_SESSION['vai_tro'])) {
    if ($_SESSION['vai_tro'] === 'giangvien') {
        header("Location: giangvien/quanlynganhangcauhoi.php");
    } else {
        header("Location: thisinh/index.php"); // Sau này trỏ về trang của thí sinh
    }
    exit();
}

$error_message = "";
$success_message = "";

// XỬ LÝ KHI BẤM NÚT ĐĂNG KÝ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Kiểm tra logic cơ bản: Mật khẩu và Xác nhận mật khẩu có khớp không?
    if ($password !== $confirm_password) {
        $error_message = "Mật khẩu xác nhận không khớp. Vui lòng thử lại!";
    } 
    // 2. Chỗ này sau này sẽ viết code INSERT vào Database
    else {
        // TẠM THỜI GIẢ LẬP ĐĂNG KÝ THÀNH CÔNG
        $success_message = "Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...";
        
        // Chờ 2 giây rồi tự động chuyển về trang login
        header("refresh:2;url=login.php"); 
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - EduQuiz</title>
    <link rel="stylesheet" href="../asset/css/login.css">
</head>
<body>

    <div class="header-logo">🎓 EduQuiz</div>
    <div class="top-right-nav">
        <a href="login.php" style="color: #4a5568; margin-right: 15px;">Đăng nhập</a> 
        <a href="register.php" class="btn-register-top">Đăng ký</a>
    </div>

    <div class="login-container" style="width: 1000px;"> <div class="left-col-blue">
            <h2>Kiến tạo tương lai qua từng đề thi</h2>
            <p>Tham gia ngay để trải nghiệm kho tàng tri thức với hàng ngàn bộ đề trắc nghiệm chuẩn hóa và công cụ học tập thông minh.</p>
            
            <div class="feature-item">
                <div class="feature-icon">🛡️</div>
                <div class="feature-text">
                    <h4>Hệ thống uy tín</h4>
                    <p>Được tin dùng bởi hơn 10,000 học sinh và giáo viên trên toàn quốc.</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">📊</div>
                <div class="feature-text">
                    <h4>Phân tích chuyên sâu</h4>
                    <p>Theo dõi tiến độ học tập và điểm mạnh yếu của bản thân qua biểu đồ.</p>
                </div>
            </div>

            <div class="img-register">📷 Ảnh chàng trai mỉm cười</div>
        </div>

        <div class="right-col">
            <h1>Tạo tài khoản mới</h1>
            <p>Hãy bắt đầu hành trình chinh phục tri thức cùng chúng tôi</p>

            <?php if (!empty($error_message)): ?>
                <div id="errorMsg" style="display: block;"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div id="successMsg" style="display: block;"><?php echo $success_message; ?></div>
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
                        <select name="role" required>
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
                    <label for="terms">Tôi đồng ý với các <a href="#" style="color: #2563eb; text-decoration: none;">Điều khoản & Chính sách</a></label>
                </div>

                <button type="submit" class="btn-submit">Đăng ký tài khoản</button>
            </form>

            <p style="text-align: center; margin-top: 25px; margin-bottom: 0; font-size: 14px; color: #718096;">
                Đã có tài khoản? <a href="login.php" style="color: #2563eb; font-weight: bold; text-decoration: none;">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</body>
</html>