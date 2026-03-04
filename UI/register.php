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
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation cơ bản
    if (empty($fullname)) $errors[] = "Vui lòng nhập họ và tên.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không đúng định dạng.";
    if (!preg_match('/^[0-9]{10,11}$/', $phone)) $errors[] = "Số điện thoại không hợp lệ.";
    if (strlen($password) < 6) $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    if ($password !== $confirm_password) $errors[] = "Mật khẩu xác nhận không khớp.";

    if (empty($errors)) {
        // GIẢ LẬP: Lưu vào DB tại đây
        $success_message = "Đăng ký thành công! Đang chuyển hướng sau 2 giây...";
        // Xóa trắng form sau khi thành công
        $fullname = $email = $phone = $role = ""; 
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống thi trắc nghiệm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .bg-gradient-blue { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .form-input:focus { border-color: #2563eb; ring-color: #2563eb; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2 text-blue-700 font-bold text-lg">
                <div class="bg-blue-600 text-white p-1.5 rounded-lg shadow-md shadow-blue-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 9l11 7 11-7-11-7z"/><path d="M1 19l11 7 11-7M1 14l11 7 11-7"/></svg>
                </div>
                <span class="hidden sm:inline-block tracking-tight">Hệ thống thi trắc nghiệm</span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="login.php" class="text-sm text-gray-500 hover:text-blue-600 font-medium transition">Đăng nhập</a>
                <div class="h-4 w-[1px] bg-gray-200"></div>
                <a href="register.php" class="text-blue-600 text-sm font-bold">Đăng ký</a>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-10">
        <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row border border-gray-50">
            
            <div class="lg:w-[40%] bg-gradient-blue p-8 sm:p-12 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-extrabold mb-4 leading-tight">Gia nhập cộng đồng tri thức.</h1>
                    <p class="text-blue-100 text-sm mb-10 opacity-90">Hệ thống quản lý và thi trắc nghiệm trực tuyến chuyên nghiệp, bảo mật và hiệu quả.</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4 bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-2xl">🛡️</span>
                            <div>
                                <p class="font-bold text-sm">Bảo mật tuyệt đối</p>
                                <p class="text-blue-200 text-xs">Dữ liệu thi được mã hóa an toàn.</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/10">
                            <span class="text-2xl">⚡</span>
                            <div>
                                <p class="font-bold text-sm">Nhanh chóng</p>
                                <p class="text-blue-200 text-xs">Phản hồi kết quả ngay sau khi nộp.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 opacity-40 hover:opacity-100 transition duration-500 hidden lg:block">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=500" 
                         class="rounded-2xl shadow-2xl border-4 border-white/20 object-cover h-44 w-full" alt="Study">
                </div>
            </div>

            <div class="lg:w-[60%] p-8 sm:p-12 lg:p-16">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Tạo tài khoản</h2>
                    <p class="text-gray-500 text-sm">Điền thông tin để bắt đầu hành trình của bạn</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-xl">
                        <?php foreach ($errors as $err) echo "• " . $err . "<br>"; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div id="successMsg" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl animate-pulse">
                        ✅ <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Họ và tên</label>
                            <input type="text" name="fullname" value="<?php echo $fullname; ?>" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="VD: Nguyễn Văn A">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Email liên hệ</label>
                            <input type="email" name="email" value="<?php echo $email; ?>" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="email@vi-du.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Số điện thoại</label>
                            <input type="text" name="phone" value="<?php echo $phone; ?>" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="09xx xxx xxx">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Bạn là ai?</label>
                            <div class="relative">
                                <select name="role" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm appearance-none cursor-pointer transition-all">
                                    <option value="thisinh" <?php if($role=='thisinh') echo 'selected'; ?>>Thí sinh</option>
                                    <option value="giangvien" <?php if($role=='giangvien') echo 'selected'; ?>>Giảng viên</option>
                                </select>
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-t border-gray-50 pt-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Mật khẩu</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="••••••••">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Xác nhận lại</label>
                            <input type="password" name="confirm_password" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="terms" required class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                        <label for="terms" class="text-[13px] text-gray-500 cursor-pointer">
                            Tôi đồng ý với các <a href="#" class="text-blue-600 font-semibold hover:underline">điều khoản sử dụng</a>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-blue-700 active:scale-[0.98] transition-all shadow-xl shadow-blue-100 mt-4">
                        Đăng ký tài khoản
                    </button>
                </form>

                <p class="text-center mt-8 text-gray-500 text-sm">
                    Đã có tài khoản? <a href="login.php" class="text-blue-600 font-bold hover:underline">Đăng nhập</a>
                </p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMsg = document.getElementById('successMsg');
            if (successMsg) {
                // Đợi 2 giây rồi chuyển hướng
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            }
        });
    </script>
</body>
</html>