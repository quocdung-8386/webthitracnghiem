<?php
// Bắt đầu session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 1. KIỂM TRA BẢO MẬT (ROUTE PROTECTION) ---
// Kiểm tra xem người dùng đã đăng nhập và có đúng vai trò 'thisinh' chưa
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'thisinh') {
    // Nếu chưa đăng nhập, lập tức đẩy về trang login
    header("Location: ../login.php");
    exit();
}

// --- 2. LẤY DỮ LIỆU TỪ PHIÊN ĐĂNG NHẬP ---
// Nếu đã đăng nhập thành công, lấy thông tin đã lưu từ trang login.php
$thongTinThiSinh = isset($_SESSION['user']) ? $_SESSION['user'] : [
    'ten' => $_SESSION['ho_ten'] ?? 'Thí sinh',
    'id' => '#98765',
    'avatar' => 'https://i.pravatar.cc/150?img=5'
];

// Lấy tên file hiện tại để tự động active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Hệ Thống Thi Trực Tuyến'; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="../../asset/images/favicon.png">
    <link rel="stylesheet" href="../../asset/css/thisinh.css">
    
    <style>
        body { font-family: 'Inter', sans-serif !important; }
        
        /* CSS MENU TÀI KHOẢN THẢ XUỐNG */
        .user-dropdown { position: relative; display: inline-block; cursor: pointer; }
        .dropdown-content {
            display: none; position: absolute; right: 0; top: 120%;
            background-color: var(--bg-card); min-width: 180px;
            box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.1);
            border-radius: 8px; border: 1px solid var(--border-color);
            z-index: 1000; overflow: hidden; animation: fadeIn 0.2s ease-out;
        }
        .user-dropdown:hover .dropdown-content { display: block; }
        .dropdown-content a {
            color: var(--text-main); padding: 12px 16px; text-decoration: none;
            display: flex; align-items: center; gap: 12px;
            font-size: 14px; font-weight: 500; transition: background 0.2s;
        }
        .dropdown-content a:hover { background-color: var(--bg-body); color: var(--primary); }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="timkiemvathamgiathi.php" class="nav-brand" style="display: flex; align-items: center; gap: 8px;">
            <span class="material-icons" style="font-size: 32px; color: var(--primary);">school</span>
            <span style="font-weight: 700; font-size: 20px; letter-spacing: -0.5px;">Hệ Thống Thi Trực Tuyến</span>
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
                    <a href="../logout.php" style="color: var(--danger);">
                       <span class="material-icons" style="font-size: 20px;">logout</span> Đăng xuất
                    </a>
                </div>
            </div>

        </div>
    </nav>