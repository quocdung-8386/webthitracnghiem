<?php
// Bật session nếu muốn dùng sau này
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/config/Database.php';

$errors = [];
$success_message = "";

$fullname = "";
$email = "";
$role = "thi_sinh";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // 1. Kiểm tra dữ liệu rỗng
    if (empty($fullname) || empty($email) || empty($password)) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    }

    // 2. Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    }

    // 3. Kiểm tra khớp mật khẩu
    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp.";
    }

    if (empty($errors)) {
        try {
            // Kết nối DB
            $conn = Database::getConnection();

            // 4. Kiểm tra xem email đã tồn tại trong DB chưa
            $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $errors[] = "Email này đã được sử dụng. Vui lòng dùng email khác!";
            } else {
                // 5. Xác định mã vai trò (Sửa lại cho khớp với HTML: giang_vien)
                $ma_vai_tro = ($role === "giang_vien") ? 2 : 3;

                // 6. Tạo username từ email và đảm bảo nó không bị trùng
                $username = explode("@", $email)[0];
                
                // Kiểm tra xem username này đã có ai dùng chưa
                $checkUser = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?");
                $checkUser->execute([$username]);
                if ($checkUser->rowCount() > 0) {
                    // Nếu đã có người dùng username này, tự động gắn thêm 3 số ngẫu nhiên vào đuôi
                    $username = $username . rand(100, 999);
                }

                // 7. THỰC THI LƯU VÀO DATABASE
                $stmt_insert = $conn->prepare("
                    INSERT INTO nguoi_dung
                    (ma_vai_tro, ho_ten, ten_dang_nhap, email, mat_khau)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt_insert->execute([
                    $ma_vai_tro,
                    $fullname,
                    $username,
                    $email,
                    $password
                ]);

                // Thông báo thành công và nhắc user tên đăng nhập của họ
                $success_message = "Đăng ký thành công! Tên đăng nhập của bạn là: <b>$username</b>. Đang chuyển trang...";
            }

        } catch (PDOException $e) {
            // Nếu có lỗi từ MySQL, in ra thay vì làm sập trắng trang
            $errors[] = "Lỗi Cơ sở dữ liệu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống thi trực tuyến</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .bg-gradient-blue { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

<header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-2 text-blue-700 font-bold text-lg">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L1 9l11 7 11-7-11-7z"/>
                </svg>
            </div>
            <span class="hidden sm:inline-block">Hệ thống thi trực tuyến</span>
        </div>
        <div class="flex items-center space-x-3">
            <a href="login.php" class="text-sm text-gray-500 hover:text-blue-600">Đăng nhập</a>
            <div class="h-4 w-[1px] bg-gray-200"></div>
            <a href="register.php" class="text-blue-600 text-sm font-bold">Đăng ký</a>
        </div>
    </div>
</header>

<main class="flex-grow flex items-center justify-center p-6">
    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col lg:flex-row">

        <div class="lg:w-[40%] bg-gradient-blue p-10 text-white">
            <h1 class="text-3xl font-bold mb-4">Gia nhập cộng đồng tri thức</h1>
            <p class="text-blue-100 text-sm mb-10">
                Hệ thống thi trắc nghiệm trực tuyến chuyên nghiệp
            </p>
        </div>

        <div class="lg:w-[60%] p-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Tạo tài khoản</h2>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                    <?php foreach ($errors as $err): ?>
                        <div>• <?= $err ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div id="successMsg" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded">
                    <?= $success_message ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs text-gray-500">Họ và tên</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($fullname ?? '') ?>" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"
                               placeholder="Nguyễn Văn A">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"
                               placeholder="email@gmail.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs text-gray-500">Số điện thoại</label>
                        <input type="text" name="phone"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500"
                               placeholder="09xxxxxxx">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Bạn là ai</label>
                        <select name="role"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="thi_sinh" <?= ($role ?? '')=='thi_sinh' ? 'selected' : '' ?>>Thí sinh</option>
                            <option value="giang_vien" <?= ($role ?? '')=='giang_vien' ? 'selected' : '' ?>>Giảng viên</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="text-xs text-gray-500">Mật khẩu</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">
                    Đăng ký tài khoản
                </button>
            </form>

            <p class="text-center mt-6 text-gray-500">
                Đã có tài khoản?
                <a href="login.php" class="text-blue-600 font-bold">Đăng nhập</a>
            </p>

        </div>
    </div>
</main>

<script>
    const success = document.getElementById("successMsg");
    if(success){
        // Kéo dài thời gian chuyển trang lên 3 giây để user kịp đọc Tên Đăng Nhập
        setTimeout(() => {
            window.location.href="login.php";
        }, 3000);
    }
</script>

</body>
</html>