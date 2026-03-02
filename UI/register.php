<?php
session_start();

// 1. CHẶN NGƯỜI ĐÃ ĐĂNG NHẬP
if (isset($_SESSION['vai_tro'])) {
    if ($_SESSION['vai_tro'] === 'giangvien') {
        header("Location: giangvien/quanlynganhangcauhoi.php");
    } else {
        header("Location: thisinh/index.php");
    }
    exit();
}

$fullname = $email = $phone = $role = "";
$errors = []; 
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- A. SANITIZE ---
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $phone    = htmlspecialchars(trim($_POST['phone']));
    $role     = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- B. VALIDATION ---
    
    // 1. Validate cơ bản
    if (empty($fullname)) { $errors[] = "Vui lòng nhập họ và tên."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Email không đúng định dạng."; }
    if (!preg_match('/^[0-9]{10,11}$/', $phone)) { $errors[] = "Số điện thoại không hợp lệ."; }
    if (strlen($password) < 6) { $errors[] = "Mật khẩu phải có ít nhất 6 ký tự."; }
    if ($password !== $confirm_password) { $errors[] = "Mật khẩu xác nhận không khớp."; }

    // 2. [TỐI ƯU BẢO MẬT] VALIDATE VAI TRÒ (WHITELIST CHECK)
    // Chỉ chấp nhận nếu role là 'thisinh' hoặc 'giangvien'
    $allowed_roles = ['thisinh', 'giangvien'];
    if (!in_array($role, $allowed_roles)) {
        $errors[] = "Vai trò không hợp lệ! Vui lòng không can thiệp vào hệ thống.";
    }

    // --- C. XỬ LÝ DATABASE ---
    if (empty($errors)) {

        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if (empty($errors)) {
            $success_message = "Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...";
            
            // Reset form
            $fullname = $email = $phone = "";
        }
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
            <div class="left-col-blue">
                <img src="https://i.pinimg.com/1200x/88/49/00/88490055748805b6b9ef7b036fa13446.jpg" alt="EduQuiz Registration Illustration"
                    class="img-register">
            </div>
        </div>

        <div class="right-col">
            <h1>Tạo tài khoản mới</h1>
            <p>Hãy bắt đầu hành trình chinh phục tri thức cùng chúng tôi</p>

            <?php if (!empty($errors)): ?>
                <div id="errorMsg" style="display: block; text-align: left;">
                    <ul style="margin-left: 15px;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo $err; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div id="successMsg" style="display: block;"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ và tên</label>
                        <span class="form-icon">👤</span>
                        <input type="text" name="fullname" value="<?php echo $fullname; ?>"
                            placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <span class="form-icon">✉️</span>
                        <input type="text" name="email" value="<?php echo $email; ?>" placeholder="example@email.com">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <span class="form-icon">📞</span>
                        <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="Ví dụ: 0123456789">
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <span class="form-icon">🎓</span>
                        <select name="role">
                            <option value="thisinh" <?php echo ($role == 'thisinh') ? 'selected' : ''; ?>>Thí sinh
                            </option>
                            <option value="giangvien" <?php echo ($role == 'giangvien') ? 'selected' : ''; ?>>Giảng viên
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <span class="form-icon">🔒</span>
                    <input type="password" name="password" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label>Xác nhận mật khẩu</label>
                    <span class="form-icon">🔒</span>
                    <input type="password" name="confirm_password" placeholder="••••••••">
                </div>

                <div class="remember-me" style="margin-top: 10px;">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">Tôi đồng ý với các <a href="#" style="color: #2563eb;">Điều khoản & Chính
                            sách</a></label>
                </div>

                <button type="submit" class="btn-submit">Đăng ký tài khoản</button>
            </form>

            <p style="text-align: center; margin-top: 25px; margin-bottom: 0; font-size: 14px; color: #718096;">
                Đã có tài khoản? <a href="login.php" style="color: #2563eb; font-weight: bold;">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
    <script>
        // Kiểm tra xem PHP có in ra thông báo thành công không
        const successMsg = document.getElementById('successMsg');
        if (successMsg) {
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 2000); // Chờ 2 giây rồi chuyển
        }
    </script>
</body>

</html>