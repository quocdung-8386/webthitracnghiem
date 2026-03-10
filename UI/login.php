<?php
session_start();

require_once '../app/config/Database.php';

if (isset($_SESSION['user'])) {
    $ma_vai_tro = $_SESSION['user']['ma_vai_tro'];
    if ($ma_vai_tro == 2) {
        header("Location: giangvien/index.php");
    } elseif ($ma_vai_tro == 3) {
        header("Location: thisinh/timkiemvathamgiathi.php");
    } elseif ($ma_vai_tro == 1) {
        header("Location: admin/bangdieukhientongquan.php");
    }
    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (password_verify($password, $user['mat_khau']) || $password === $user['mat_khau'])) {
            
            $_SESSION['user'] = $user;
            
            $ma_vai_tro = $user['ma_vai_tro'];
            if ($ma_vai_tro == 2) {
                header("Location: giangvien/index.php");
            } elseif ($ma_vai_tro == 3) {
                header("Location: thisinh/timkiemvathamgiathi.php");
            } elseif ($ma_vai_tro == 1) {
                header("Location: admin/bangdieukhientongquan.php");
            }
            exit();
        } else {
            $error_message = "Tên đăng nhập, Email hoặc mật khẩu không đúng!";
        }
    } catch (PDOException $e) {
        $error_message = "Lỗi kết nối Database. Vui lòng kiểm tra lại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống thi trắc nghiệm</title>
    <link rel="stylesheet" href="../asset/css/login.css">
    
    <style>
        .top-header {
            position: absolute; top: 0; left: 0; width: 100%;
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 40px; background: #fff; border-bottom: 1px solid #e2e8f0;
            z-index: 10;
        }
        /* CSS CHO LOGO MỚI */
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

        /* NÚT BẤM GÓC PHẢI */
        .top-right-nav { display: flex; gap: 10px; }
        .btn-nav-login { padding: 8px 15px; background: #f1f5f9; color: #4a5568; font-weight: bold; text-decoration: none; border-radius: 6px; }
        .btn-nav-register { padding: 8px 15px; background: #2563eb; color: #ffffff; font-weight: bold; text-decoration: none; border-radius: 6px; }
        
        /* Chỉnh lại body để chừa chỗ cho Header */
        body { padding-top: 80px; }
        .header-logo { display: none; } /* Ẩn logo cũ của file login.css đi */
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

    <div class="login-container">
        <div class="left-col" style="background-color: #fff9ed;">
            <div class="img-placeholder" style="background-color: #d6bc97;">📺 Hình minh họa</div>
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
                Bạn là thành viên mới? <a href="register.php" style="color: #2563eb; font-weight: bold; text-decoration: none;">Tạo tài khoản miễn phí</a>
            </p>
        </div>
    </div>

</body>
</html>