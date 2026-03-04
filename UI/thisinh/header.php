<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy tên file hiện tại để tự động active menu
$current_page = basename($_SERVER['PHP_SELF']);

// --- LOGIC XỬ LÝ ĐĂNG NHẬP ---
$isLoggedIn = false;
$thongTinThiSinh = [];

// Kiểm tra xem session 'user' đã tồn tại chưa (do trang Đăng nhập tạo ra)
if (isset($_SESSION['user'])) {
    $isLoggedIn = true;
    // Gán dữ liệu thật từ Session vào biến. 
    // Giả sử mảng session của bạn có dạng: ['ten' => 'Vũ Hồng Quang', 'id' => '10293', 'avatar' => '...']
    $thongTinThiSinh = $_SESSION['user']; 
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Hệ Thống Thi Trực Tuyến - Admin Portal'; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="../../asset/images/favicon.png">
    <link rel="stylesheet" href="../../asset/css/thisinh.css">
    
    <style>
        body { font-family: 'Inter', sans-serif !important; }
        
        /* CSS CHO MENU DROP-DOWN TÀI KHOẢN */
        .user-dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            background-color: var(--bg-card);
            min-width: 180px;
            box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            z-index: 1000;
            overflow: hidden;
            animation: fadeIn 0.2s ease-out;
        }
        .user-dropdown:hover .dropdown-content {
            display: block; /* Hiện menu khi di chuột vào */
        }
        .dropdown-content a {
            color: var(--text-main);
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .dropdown-content a:hover {
            background-color: var(--bg-body);
            color: var(--primary);
        }
        
        /* CSS CHO NÚT ĐĂNG NHẬP (Khi chưa login) */
        .btn-login {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s, transform 0.2s;
            display: inline-block;
        }
        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="timkiemvathamgiathi.php" class="nav-brand" style="display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="font-size: 32px; color: var(--primary);">school</span>
            <span style="font-weight: 700; font-size: 20px; letter-spacing: -0.5px;">Hệ Thống Thi Trắc Nghiệm</span>
        </a>
        
        <ul class="nav-links">
            <li class="<?php echo ($current_page == 'timkiemvathamgiathi.php') ? 'active' : ''; ?>">
                <a href="timkiemvathamgiathi.php">Trang chủ</a>
            </li>
            <li class="<?php echo ($current_page == 'lambaithi.php') ? 'active' : ''; ?>">
                <a href="lambaithi.php">Kỳ thi của tôi</a>
            </li>
            <li class="<?php echo ($current_page == 'xemketqua.php') ? 'active' : ''; ?>">
                <a href="xemketqua.php">Kết quả</a>
            </li>
            <li class="<?php echo ($current_page == 'phuckhaokhieunai.php') ? 'active' : ''; ?>">
                <a href="phuckhaokhieunai.php">Khiếu nại</a>
            </li>
        </ul>
        
        <div class="nav-user">
            <span id="btnToggleTheme" class="material-icons" style="font-size: 24px; cursor:pointer; color: var(--text-muted); margin-right: 15px;">dark_mode</span>
            
            <?php if ($isLoggedIn): ?>
                <div class="user-dropdown">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="user-info">
                            <span class="user-name"><?php echo $thongTinThiSinh['ten']; ?></span>
                            <span class="user-role">Thí sinh <?php echo $thongTinThiSinh['id']; ?></span>
                        </div>
                        <img src="<?php echo $thongTinThiSinh['avatar']; ?>" alt="Avatar" class="avatar" style="object-fit: cover;">
                    </div>
                    
                    <div class="dropdown-content">
                        <a href="#"><span class="material-icons" style="font-size: 20px;">person</span> Hồ sơ cá nhân</a>
                        <a href="#"><span class="material-icons" style="font-size: 20px;">lock</span> Đổi mật khẩu</a>
                        <div style="border-top: 1px solid var(--border-color); margin: 4px 0;"></div>
                        <a href="logout.php" style="color: var(--danger);">
                            <span class="material-icons" style="font-size: 20px;">logout</span> Đăng xuất
                        </a>
                    </div>
                </div>

            <?php else: ?>
               <a href="../login.php" class="btn-login">Đăng nhập</a>
            <?php endif; ?>

        </div>
    </nav>