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

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- DỮ LIỆU GIẢ ĐĂNG NHẬP ---
    if ($username === 'giangvien' && $password === '123456') {
        $_SESSION['vai_tro'] = 'giangvien';
        $_SESSION['ho_ten'] = 'GV. Nguyễn Văn A';
        header("Location: giangvien/index.php");
        exit();
    } else {
        $error_message = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống thi trắc nghiệm</title>
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
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 9l11 7 11-7-11-7z"/><path d="M1 19l11 7 11-7M1 14l11 7 11-7"/></svg>
                </div>
                <span>Hệ thống thi trắc nghiệm</span>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-400 hidden sm:inline">Chưa có tài khoản?</span>
                <a href="register.php" class="text-blue-600 text-sm font-bold hover:underline">Đăng ký ngay</a>
            </div>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6 lg:p-10">
        <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row border border-gray-50">
            
            <div class="lg:w-1/2 bg-gradient-blue p-10 text-white flex flex-col justify-center relative overflow-hidden hidden md:flex">
                <div class="relative z-10">
                    <h1 class="text-3xl font-extrabold mb-4 leading-tight">Chào mừng trở lại!</h1>
                    <p class="text-blue-100 text-sm mb-8 opacity-90">Tiếp tục hành trình chinh phục tri thức và đánh giá năng lực của bạn.</p>
                    
                    <div class="bg-white/10 p-6 rounded-2xl border border-white/20 backdrop-blur-md">
                        <img src="https://images.unsplash.com/photo-1510070112810-d4e9a46d9e91?auto=format&fit=crop&q=80&w=500" 
                             class="rounded-xl shadow-lg w-full h-48 object-cover mb-4" alt="Login Illustration">
                        <p class="text-xs text-center italic text-blue-100">"Học vấn là chìa khóa của tương lai"</p>
                    </div>
                </div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <div class="lg:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Đăng nhập</h2>
                    <p class="text-gray-500 text-sm mt-1">Vui lòng nhập thông tin tài khoản</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded-r-xl animate-shake">
                        ⚠️ <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tên đăng nhập / Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">✉️</span>
                            <input type="text" name="username" required
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="giangvien">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center px-1">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mật khẩu</label>
                            <a href="#" class="text-[11px] text-blue-600 font-semibold hover:underline">Quên mật khẩu?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">🔒</span>
                            <input type="password" name="password" required
                                class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                        <label for="remember" class="text-xs text-gray-500 cursor-pointer select-none">Ghi nhớ phiên đăng nhập</label>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-blue-700 active:scale-[0.98] transition-all shadow-xl shadow-blue-100">
                        Đăng nhập ngay →
                    </button>
                </form>

                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase"><span class="bg-white px-4 text-gray-400 tracking-tighter">Hoặc tiếp tục với</span></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button class="flex items-center justify-center py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-4 h-4 mr-2" alt="Google"> Google
                    </button>
                    <button class="flex items-center justify-center py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        <img src="https://www.svgrepo.com/show/475647/facebook-color.svg" class="w-4 h-4 mr-2" alt="Facebook"> Facebook
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-6 text-gray-400 text-xs">
        &copy; 2026 HeThongThiTracNghiem System. All rights reserved.
    </footer>

</body>
</html>