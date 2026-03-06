<?php
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* =============================
LẤY CẤU HÌNH HỆ THỐNG
============================= */

$stmt = $conn->query("SELECT * FROM cau_hinh_he_thong LIMIT 1");
$setting = $stmt->fetch(PDO::FETCH_ASSOC);

/* =============================
XỬ LÝ LƯU CẤU HÌNH
============================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ten_he_thong = $_POST['ten_he_thong'];
    $quy_dinh_thi = $_POST['quy_dinh_thi'];
    $smtp_server = $_POST['smtp_server'];
    $smtp_port = $_POST['smtp_port'];
    $smtp_email = $_POST['smtp_email'];
    $smtp_password = $_POST['smtp_password'];

    /* Upload logo */

    $logo = $setting['logo'];

    if (!empty($_FILES['logo']['name'])) {

        $target_dir = "../uploads/";
        if(!is_dir($target_dir)){
            mkdir($target_dir,0777,true);
        }

        $logo = time() . "_" . $_FILES["logo"]["name"];
        $target_file = $target_dir . $logo;

        move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
    }

    /* UPDATE DATABASE */

    $sql = "UPDATE cau_hinh_he_thong SET
            ten_he_thong = ?,
            logo = ?,
            quy_dinh_thi = ?,
            smtp_server = ?,
            smtp_port = ?,
            smtp_email = ?,
            smtp_password = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $ten_he_thong,
        $logo,
        $quy_dinh_thi,
        $smtp_server,
        $smtp_port,
        $smtp_email,
        $smtp_password
    ]);

    header("Location: settings.php?success=1");
    exit();
}

$title = "Cấu Hình Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "settings";

include 'components/header.php';
include 'components/sidebar.php';
?>
=======
<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Cấu hình hệ thống</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative">
                <button id="notifButton" type="button" class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons">notifications</span>
                    <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                </button>

                <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                        <a href="#" class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu đã đọc</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa hoàn tất sao lưu.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                        </a>
                    </div>
                    <a href="#" class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">
                        Xem tất cả
                    </a>
                </div>
            </div>

            <button id="darkModeToggle" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors max-w-5xl mx-auto flex flex-col">
            
            <div class="flex border-b border-slate-200 dark:border-slate-700 px-6 pt-2 overflow-x-auto custom-scrollbar shrink-0">
                <button id="btnTabGeneral" onclick="switchTab('general')" class="tab-btn px-4 py-3 border-b-2 border-[#254ada] dark:border-[#4b6bfb] text-[#254ada] dark:text-[#4b6bfb] font-semibold text-sm whitespace-nowrap transition">Thiết lập tham số chung</button>
                <button id="btnTabEmail" onclick="switchTab('email')" class="tab-btn px-4 py-3 border-b-2 border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium text-sm transition whitespace-nowrap">Cấu hình Email/SMTP</button>
                <button id="btnTabRoles" onclick="switchTab('roles')" class="tab-btn px-4 py-3 border-b-2 border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium text-sm transition whitespace-nowrap">Phân quyền</button>
            </div>

            <div class="p-8">
                
                <form id="tabGeneral" class="tab-content block" onsubmit="handleSaveSettings(event, 'general')">
                    <div class="mb-8">
                        <h3 class="text-sm font-bold uppercase text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px] bg-blue-50 dark:bg-blue-900/30 p-1 rounded-full">info</span> THÔNG TIN HỆ THỐNG
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Tên hệ thống</label>
                                <input type="text" value="Hệ thống thi trắc nghiệm trực tuyến - EduExam" class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Logo hệ thống</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded flex items-center justify-center">
                                        <span class="material-icons text-slate-400">image</span>
                                    </div>
                                    <button type="button" class="px-4 py-2 bg-slate-50 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 transition">Thay đổi ảnh</button>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Quy định thi chung</label>
                                <textarea rows="4" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition resize-y">1. Thí sinh không được sử dụng tài liệu trong quá trình làm bài.&#10;2. Hệ thống sẽ tự động nộp bài khi hết thời gian.&#10;3. Việc mất kết nối camera quá 2 lần sẽ bị đình chỉ thi.</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" onclick="document.getElementById('tabGeneral').reset(); showToast('info', 'Hoàn tác', 'Đã hủy thay đổi')" class="px-6 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg font-medium text-sm transition">Hủy bỏ</button>
                        <button type="submit" class="submit-btn px-6 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm">
                            <span class="material-icons text-[20px]">save</span> Lưu cấu hình chung
                        </button>
                    </div>
                </form>

                <form id="tabEmail" class="tab-content hidden" onsubmit="handleSaveSettings(event, 'email')">
                    <div class="mb-8">
                        <h3 class="text-sm font-bold uppercase text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px] bg-blue-50 dark:bg-blue-900/30 p-1 rounded-full">email</span> CẤU HÌNH EMAIL/SMTP
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SMTP Server</label>
                                <input type="text" value="smtp.gmail.com" required class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">SMTP Port</label>
                                <input type="number" value="587" required class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Email User (Tài khoản)</label>
                                <input type="email" value="notification@system.edu.vn" required class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Mật khẩu (App Password)</label>
                                <div class="relative">
                                    <input type="password" id="smtpPassword" value="password123" required class="w-full pl-4 pr-10 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                                    <span id="togglePasswordBtn" class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-sm cursor-pointer hover:text-[#254ada] transition">visibility</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50/80 dark:bg-blue-900/20 text-[#254ada] dark:text-blue-300 text-sm rounded-lg flex gap-3 items-start border border-blue-100 dark:border-blue-800/50">
                            <span class="material-icons mt-0.5 text-[20px]">help</span>
                            <p class="leading-relaxed">Email SMTP được sử dụng để gửi thông báo kết quả thi, mã OTP khôi phục mật khẩu. Khuyến nghị sử dụng App Password thay cho mật khẩu gốc nếu dùng Gmail.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" onclick="showToast('info', 'Đang kiểm tra', 'Đang gửi email test...');" class="px-5 py-2.5 text-[#254ada] dark:text-[#4b6bfb] border border-[#254ada] dark:border-[#4b6bfb] hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg font-medium text-sm transition flex items-center gap-2">
                            <span class="material-icons text-[18px]">send</span> Gửi Test
                        </button>
                        <button type="submit" class="submit-btn px-6 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm">
                            <span class="material-icons text-[20px]">save</span> Lưu cấu hình SMTP
                        </button>
                    </div>
                </form>

                <form id="tabRoles" class="tab-content hidden" onsubmit="handleSaveSettings(event, 'roles')">
                    <div class="mb-8">
                        <h3 class="text-sm font-bold uppercase text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px] bg-blue-50 dark:bg-blue-900/30 p-1 rounded-full">security</span> CẤU HÌNH PHÂN QUYỀN CHUNG
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                <div>
                                    <p class="text-[14px] font-semibold text-slate-800 dark:text-white">Cho phép đăng ký tài khoản tự do</p>
                                    <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Người dùng có thể tự tạo tài khoản mà không cần admin thêm thủ công.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#254ada]"></div>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Vai trò mặc định khi đăng ký mới</label>
                                    <select class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:border-[#254ada] transition outline-none">
                                        <option value="student">Thí sinh</option>
                                        <option value="teacher">Giảng viên (Cần phê duyệt)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                                <div>
                                    <p class="text-[14px] font-semibold text-slate-800 dark:text-white">Bắt buộc xác thực 2 bước (2FA)</p>
                                    <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Yêu cầu tất cả Quản trị viên và Giảng viên phải xác thực qua Email khi đăng nhập.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#254ada]"></div>
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="submit-btn px-6 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm">
                            <span class="material-icons text-[20px]">save</span> Lưu cấu hình bảo mật
                        </button>
                    </div>
                </form>

            </div>
        </div>
>>>>>>> 0e15295 (update admin CN)
    </div>
</header>

<div class="flex-1 overflow-y-auto p-8 bg-slate-50">

<?php if(isset($_GET['success'])){ ?>

<div class="mb-6 p-4 bg-green-100 text-green-700 rounded">
Lưu cấu hình thành công
</div>

<?php } ?>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm">

<form method="POST" enctype="multipart/form-data" class="p-8">

<!-- ============================= -->
<!-- THÔNG TIN HỆ THỐNG -->
<!-- ============================= -->

<div class="mb-8">

<h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
<span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">info</span>
THÔNG TIN HỆ THỐNG
</h3>

<div class="grid grid-cols-2 gap-x-8 gap-y-6">

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Tên hệ thống
</label>

<input 
type="text"
name="ten_he_thong"
value="<?= htmlspecialchars($setting['ten_he_thong']) ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>

<label class="block text-sm font-semibold text-slate-700 mb-2">
Logo hệ thống
</label>

<div class="flex items-center gap-3">

<?php if(!empty($setting['logo'])){ ?>

<img 
src="../uploads/<?= $setting['logo'] ?>"
class="w-10 h-10 object-cover rounded border"
/>

<?php } ?>

<input type="file" name="logo" class="text-sm">

</div>

</div>

<div class="col-span-2">

<label class="block text-sm font-semibold text-slate-700 mb-2">
Quy định thi chung
</label>

<textarea
name="quy_dinh_thi"
rows="4"
class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
><?= htmlspecialchars($setting['quy_dinh_thi']) ?></textarea>

</div>

</div>
</div>

<!-- ============================= -->
<!-- SMTP -->
<!-- ============================= -->

<div class="pt-8 border-t border-slate-100 mb-8">

<h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
<span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">email</span>
CẤU HÌNH EMAIL / SMTP
</h3>

<div class="grid grid-cols-2 gap-x-8 gap-y-6">

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
SMTP Server
</label>

<input
type="text"
name="smtp_server"
value="<?= $setting['smtp_server'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
SMTP Port
</label>

<input
type="text"
name="smtp_port"
value="<?= $setting['smtp_port'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Email User
</label>

<input
type="text"
name="smtp_email"
value="<?= $setting['smtp_email'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Mật khẩu
</label>

<input
type="password"
name="smtp_password"
value="<?= $setting['smtp_password'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

</div>

</div>

<!-- ============================= -->
<!-- BUTTON -->
<!-- ============================= -->

<div class="flex justify-end gap-3 pt-6 border-t border-slate-100">

<button
type="submit"
class="px-6 py-2.5 bg-[#254ada] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 font-medium text-sm transition shadow-sm"
>

<span class="material-icons text-[20px]">save</span>
Lưu thay đổi

</button>

</div>

</form>

</div>
</div>
</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
/* =================================================================
   1. HÀM CHUYỂN TAB (TAB SWITCHING)
   ================================================================= */
function switchTab(tabName) {
    // Ẩn tất cả nội dung tab
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('block');
        el.classList.add('hidden');
    });
    
    // Reset màu nút tab về mặc định (Xám)
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-[#254ada]', 'dark:border-[#4b6bfb]', 'text-[#254ada]', 'dark:text-[#4b6bfb]', 'font-semibold');
        el.classList.add('border-transparent', 'text-slate-500', 'dark:text-slate-400', 'font-medium');
    });

    // Hiện nội dung tab được chọn
    const targetContent = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
    targetContent.classList.remove('hidden');
    targetContent.classList.add('block');
    
    // Đổi màu nút tab được chọn sang Xanh
    const activeBtn = document.getElementById('btnTab' + tabName.charAt(0).toUpperCase() + tabName.slice(1));
    activeBtn.classList.remove('border-transparent', 'text-slate-500', 'dark:text-slate-400', 'font-medium');
    activeBtn.classList.add('border-[#254ada]', 'dark:border-[#4b6bfb]', 'text-[#254ada]', 'dark:text-[#4b6bfb]', 'font-semibold');
}

/* =================================================================
   2. HÀM HIỂN THỊ THÔNG BÁO (TOAST)
   ================================================================= */
function showToast(type, title, message) {
    const container = document.getElementById('toastContainer');
    const template = document.getElementById('toastTemplate');
    if(!container || !template) return;
    
    const toastNode = template.content.cloneNode(true);
    const toastEl = toastNode.querySelector('.toast-item');
    const iconEl = toastNode.querySelector('.toast-icon');
    
    toastNode.querySelector('.toast-title').textContent = title;
    toastNode.querySelector('.toast-message').textContent = message;
    
    if (type === 'success') {
        toastEl.classList.add('border-green-500');
        iconEl.innerHTML = '<span class="material-icons text-green-500">check_circle</span>';
    } else if (type === 'error') {
        toastEl.classList.add('border-red-500');
        iconEl.innerHTML = '<span class="material-icons text-red-500">error</span>';
    } else if (type === 'warning') {
        toastEl.classList.add('border-orange-500');
        iconEl.innerHTML = '<span class="material-icons text-orange-500">warning</span>';
    } else {
        toastEl.classList.add('border-blue-500');
        iconEl.innerHTML = '<span class="material-icons text-blue-500">info</span>';
    }

    toastNode.querySelector('.toast-close').onclick = () => {
        toastEl.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toastEl.remove(), 300);
    };

    container.appendChild(toastNode);
    setTimeout(() => toastEl.classList.remove('translate-x-full', 'opacity-0'), 10);
    setTimeout(() => { if(container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
}

/* =================================================================
   3. HÀM XỬ LÝ LƯU (MÔ PHỎNG API)
   ================================================================= */
function handleSaveSettings(event, tabName) {
    event.preventDefault(); // Chặn load trang
    
    const form = event.target;
    const submitBtn = form.querySelector('.submit-btn');
    const originalContent = submitBtn.innerHTML;
    
    // Đổi trạng thái thành Đang xử lý
    submitBtn.innerHTML = '<span class="material-icons animate-spin text-[20px]">autorenew</span> Đang lưu...';
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-70');

    // Giả lập thời gian lưu 1 giây
    setTimeout(() => {
        showToast('success', 'Lưu thành công', 'Cấu hình đã được cập nhật vào hệ thống.');
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-70');
    }, 1000);
}

/* =================================================================
   4. SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
   ================================================================= */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Chức năng Dark Mode
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark');
        if(darkModeIcon) darkModeIcon.textContent = 'light_mode';
    }

    darkModeToggle?.addEventListener('click', () => {
        htmlElement.classList.toggle('dark');
        const isDark = htmlElement.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        if(darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
    });

    // 2. Chức năng Dropdown Thông báo
    const notifButton = document.getElementById('notifButton');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifButton && notifDropdown) {
        notifButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }

    // 3. Chức năng Ẩn/Hiện mật khẩu SMTP
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const smtpPasswordInput = document.getElementById('smtpPassword');

    if(togglePasswordBtn && smtpPasswordInput) {
        togglePasswordBtn.addEventListener('click', function() {
            const isPassword = smtpPasswordInput.getAttribute('type') === 'password';
            smtpPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    }
});
</script>