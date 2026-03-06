<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/config/database.php';

if (isset($_SESSION['vai_tro'])) {

    switch ($_SESSION['vai_tro']) {
        case 'admin':
            header("Location: /webthitracnghiem/UI/admin/bangdieukhientongquan.php");
            break;

        case 'giang_vien':
            header("Location: /webthitracnghiem/UI/giangvien/quanlynganhangcauhoi.php");
            break;

        case 'thi_sinh':
            header("Location: /webthitracnghiem/UI/thisinh/timkiemvathamgiathi.php");
            break;
    }

    exit();
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error_message = "Vui lòng nhập đầy đủ thông tin!";
    } else {

        try {

            $conn = Database::getConnection();

            $sql = "SELECT nd.*, vt.ten_vai_tro 
                    FROM nguoi_dung nd
                    JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
                    WHERE nd.ten_dang_nhap = :username 
                       OR nd.email = :username
                    LIMIT 1";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $error_message = "Tài khoản không tồn tại!";
            }
            elseif ($password !== $user['mat_khau']) {
                $error_message = "Sai mật khẩu!";
            }
            elseif ($user['trang_thai'] !== 'hoat_dong') {
                $error_message = "Tài khoản đã bị khóa!";
            }
            else {

                session_regenerate_id(true);

                $_SESSION['ma_nguoi_dung'] = $user['ma_nguoi_dung'];
                $_SESSION['ho_ten'] = $user['ho_ten'];
                $_SESSION['vai_tro'] = $user['ten_vai_tro'];

                switch ($user['ten_vai_tro']) {

                    case 'admin':
                        header("Location: /webthitracnghiem/UI/admin/bangdieukhientongquan.php");
                        break;

                    case 'giang_vien':
                        header("Location: /webthitracnghiem/UI/giangvien/quanlynganhangcauhoi.php");
                        break;

                    case 'thi_sinh':
                        header("Location: /webthitracnghiem/UI/thisinh/timkiemvathamgiathi.php");
                        break;

                    default:
                        $error_message = "Vai trò không hợp lệ!";
                        break;
                }

                exit();
            }

        } catch (PDOException $e) {
            $error_message = "Lỗi hệ thống!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống thi trực tuyến</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .bg-gradient-blue { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
    </style>
</head>

<body class="min-h-screen flex flex-col">

<header class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2 text-blue-700 font-bold text-lg">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                🎓
            </div>
            <span>Hệ thống thi trực tuyến</span>
        </div>
        <a href="register.php" class="text-sm text-blue-600 font-semibold hover:underline">
            Đăng ký
        </a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center p-6">
    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row">

        <!-- LEFT -->
        <div class="lg:w-1/2 bg-gradient-blue p-10 text-white hidden lg:flex flex-col justify-center">
            <h1 class="text-3xl font-extrabold mb-4">Chào mừng trở lại!</h1>
            <p class="text-blue-100 text-sm mb-6">
                Hệ thống thi trắc nghiệm trực tuyến thông minh giúp bạn đánh giá năng lực chính xác.
            </p>

            <div class="bg-white/10 p-6 rounded-2xl">
                <img src="https://images.unsplash.com/photo-1510070112810-d4e9a46d9e91?auto=format&fit=crop&q=80&w=500"
                     class="rounded-xl shadow-lg w-full h-48 object-cover mb-4" alt="">
                <p class="text-xs text-center italic text-blue-100">
                    "Học vấn là chìa khóa của tương lai"
                </p>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="lg:w-1/2 p-10 flex flex-col justify-center">

            <h2 class="text-2xl font-bold text-gray-800 mb-6">Đăng nhập</h2>

            <?php if (!empty($error_message)): ?>
                <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    ⚠️ <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Tên đăng nhập / Email</label>
                    <input type="text" name="username" required
                           class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="Nhập username hoặc email">
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase">Mật khẩu</label>
                    <input type="password" name="password" required
                           class="w-full mt-2 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" id="remember" class="w-4 h-4">
                    <label for="remember" class="text-sm text-gray-500">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                    Đăng nhập
                </button>

            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                Chưa có tài khoản?
                <a href="register.php" class="text-blue-600 font-semibold hover:underline">
                    Đăng ký ngay
                </a>
            </div>

        </div>
    </div>
</main>

<footer class="text-center py-6 text-gray-400 text-xs">
    © 2026 HeThongThiTracNghiem. All rights reserved.
</footer>

</body>
</html>