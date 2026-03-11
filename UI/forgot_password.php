<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error_message = "Vui lòng nhập email của bạn!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Địa chỉ email không hợp lệ!";
    } else {
        // Here you would typically connect to the database, check if the email exists,
        // generate a reset token, save it to the database, and send a reset email.
        // For this demo, we'll just show a success message.
        $success_message = "Hướng dẫn khôi phục mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư đến (và thư mục rác).";
    }
}

$title = 'Quên mật khẩu - Hệ Thống Thi Trực Tuyến';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo htmlspecialchars($title); ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="../asset/images/favicon.png">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .bg-gradient-blue { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
    </style>
</head>

<body class="min-h-screen flex flex-col">

<header class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <a href="login.php" class="flex items-center space-x-2 text-blue-700 font-bold text-lg hover:opacity-80 transition">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg flex items-center justify-center">
                <span class="material-icons" style="font-size: 20px;">school</span>
            </div>
            <span>Hệ Thống Thi Trực Tuyến</span>
        </a>
        <a href="login.php" class="text-sm text-gray-600 font-medium hover:text-blue-600 flex items-center gap-1 transition">
            <span class="material-icons" style="font-size: 18px;">arrow_back</span>
            Quay lại Đăng nhập
        </a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-icons" style="font-size: 32px;">lock_reset</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Quên mật khẩu?</h2>
            <p class="text-sm text-gray-500 mt-2">
                Nhập email liên kết với tài khoản của bạn để nhận hướng dẫn đặt lại mật khẩu.
            </p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl flex items-start gap-3">
                <span class="material-icons text-red-500 mt-0.5" style="font-size: 18px;">error_outline</span>
                <span><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl flex items-start gap-3">
                <span class="material-icons text-green-500 mt-0.5" style="font-size: 18px;">check_circle_outline</span>
                <span><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Địa chỉ Email</label>
                <div class="relative w-full mt-2">
                    <span class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" style="font-size: 20px;">mail_outline</span>
                    <input type="email" name="email" required
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-transparent outline-none transition-all"
                           placeholder="Nhập email của bạn"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                <span>Gửi yêu cầu khôi phục</span>
                <span class="material-icons" style="font-size: 18px;">arrow_forward</span>
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500 flex items-center justify-center gap-1">
            Chưa có tài khoản?
            <a href="register.php" class="text-blue-600 font-semibold hover:underline">
                Đăng ký ngay
            </a>
        </div>

    </div>
</main>

<footer class="text-center py-6 text-gray-400 text-xs">
    © 2026 Hệ Thống Thi Trực Tuyến. All rights reserved.
</footer>

</body>
</html>
