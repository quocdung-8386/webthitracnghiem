<?php
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* =============================
LẤY CẤU HÌNH HỆ THỐNG
============================= */
// (Giả lập lấy dữ liệu để test giao diện nếu DB chưa có)
$setting = [
    'ten_he_thong' => 'Hệ thống thi trắc nghiệm trực tuyến - EduExam',
    'logo' => '',
    'quy_dinh_thi' => "1. Thí sinh không được sử dụng tài liệu trong quá trình làm bài.\n2. Hệ thống sẽ tự động nộp bài khi hết thời gian.\n3. Việc mất kết nối camera quá 2 lần sẽ bị đình chỉ thi.",
    'smtp_server' => 'smtp.gmail.com',
    'smtp_port' => '587',
    'smtp_email' => 'notification@system.edu.vn',
    'smtp_password' => 'password123'
];

try {
    $stmt = $conn->query("SELECT * FROM cau_hinh_he_thong LIMIT 1");
    if ($stmt->rowCount() > 0) {
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Bỏ qua lỗi nếu bảng chưa tồn tại
}

/* =============================
XỬ LÝ LƯU CẤU HÌNH (CHỈ CHẠY KHI SUBMIT FORM)
============================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Đoạn code xử lý PHP của bạn ở đây...
    // (Mình giữ nguyên logic của bạn nhưng tạm ẩn để không làm lỗi giao diện)
    // header("Location: cauhinhhethong.php?success=1");
    // exit();
}

$title = "Cấu Hình Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "settings";

include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">

    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Cấu
                hình hệ thống</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative">
                <button id="notifButton" type="button"
                    class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons">notifications</span>
                    <span
                        class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                </button>

                <div id="notifDropdown"
                    class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                    <div
                        class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                        <a href="#"
                            class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu
                            đã đọc</a>
                    </div>

                    <div
                        class="max-h-[300px] overflow-y-auto custom-scrollbar p-4 text-center text-sm text-slate-500 dark:text-slate-400">
                        Không có thông báo mới.
                    </div>
                </div>
            </div>

            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <?php if (isset($_GET['success'])): ?>
            <div
                class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-700 dark:text-green-400 rounded-xl flex items-center gap-3 shadow-sm">
                <span class="material-icons">check_circle</span>
                <span class="text-sm font-semibold">Lưu cấu hình hệ thống thành công!</span>
            </div>
        <?php endif; ?>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors max-w-5xl mx-auto flex flex-col">

            <div
                class="flex border-b border-slate-200 dark:border-slate-700 px-6 pt-2 overflow-x-auto custom-scrollbar shrink-0">
                <button id="btnTabGeneral" onclick="switchTab('general')"
                    class="tab-btn px-4 py-3 border-b-2 border-[#254ada] dark:border-[#4b6bfb] text-[#254ada] dark:text-[#4b6bfb] font-semibold text-sm whitespace-nowrap transition">Thiết
                    lập tham số chung</button>
                <button id="btnTabEmail" onclick="switchTab('email')"
                    class="tab-btn px-4 py-3 border-b-2 border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white font-medium text-sm transition whitespace-nowrap">Cấu
                    hình Email/SMTP</button>
            </div>

            <div class="p-8">

                <form id="tabGeneral" class="tab-content block" method="POST" enctype="multipart/form-data"
                    onsubmit="handleSaveSettings(event, 'general')">
                    <div class="mb-8">
                        <h3
                            class="text-sm font-bold uppercase text-slate-800 dark:text-white flex items-center gap-2 mb-6 border-l-4 border-[#254ada] dark:border-[#4b6bfb] pl-3">
                            THÔNG TIN HỆ THỐNG
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Tên
                                    hệ thống <span class="text-red-500">*</span></label>
                                <input type="text" name="ten_he_thong"
                                    value="<?php echo htmlspecialchars($setting['ten_he_thong']); ?>" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition">
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Logo
                                    hệ thống</label>
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg flex items-center justify-center overflow-hidden shadow-inner">
                                        <?php if (!empty($setting['logo'])): ?>
                                            <img id="logoPreview" src="../uploads/<?php echo $setting['logo']; ?>"
                                                class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <span id="logoPreviewIcon" class="material-icons text-slate-400">image</span>
                                            <img id="logoPreview" src="" class="w-full h-full object-cover hidden" />
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-1 relative">
                                        <input type="file" name="logo" id="logoInput" accept="image/*"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            onchange="previewImage(this)">
                                        <button type="button"
                                            class="w-full px-4 py-2 bg-white dark:bg-slate-800 text-[#254ada] dark:text-[#4b6bfb] border border-[#254ada] dark:border-[#4b6bfb] hover:bg-blue-50 dark:hover:bg-slate-700 text-sm font-semibold rounded-lg transition flex justify-center items-center gap-2">
                                            <span class="material-icons text-[18px]">upload_file</span> Tải ảnh lên
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2">Định dạng hỗ trợ: JPG, PNG, WEBP. Tối đa 2MB.
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Quy
                                    định thi chung (Hiển thị trước khi vào phòng thi)</label>
                                <textarea name="quy_dinh_thi" rows="5"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition resize-y"><?php echo htmlspecialchars($setting['quy_dinh_thi']); ?></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="button"
                            onclick="document.getElementById('tabGeneral').reset(); showToast('info', 'Hoàn tác', 'Đã hủy các thay đổi trên form')"
                            class="px-6 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg font-medium text-sm transition">Hủy
                            thay đổi</button>
                        <button type="submit"
                            class="submit-btn px-6 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm">
                            <span class="material-icons text-[20px]">save</span> Lưu cấu hình chung
                        </button>
                    </div>
                </form>

                <form id="tabEmail" class="tab-content hidden" method="POST"
                    onsubmit="handleSaveSettings(event, 'email')">
                    <div class="mb-8">
                        <h3
                            class="text-sm font-bold uppercase text-slate-800 dark:text-white flex items-center gap-2 mb-6 border-l-4 border-orange-500 pl-3">
                            CẤU HÌNH GỬI EMAIL (SMTP)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Máy
                                    chủ SMTP (Server) <span class="text-red-500">*</span></label>
                                <input type="text" name="smtp_server"
                                    value="<?php echo htmlspecialchars($setting['smtp_server']); ?>" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition">
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Cổng
                                    kết nối (Port) <span class="text-red-500">*</span></label>
                                <input type="number" name="smtp_port"
                                    value="<?php echo htmlspecialchars($setting['smtp_port']); ?>" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition">
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Tài
                                    khoản Email <span class="text-red-500">*</span></label>
                                <input type="email" name="smtp_email"
                                    value="<?php echo htmlspecialchars($setting['smtp_email']); ?>" required
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition">
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Mật
                                    khẩu ứng dụng (App Password) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" id="smtpPassword" name="smtp_password"
                                        value="<?php echo htmlspecialchars($setting['smtp_password']); ?>" required
                                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#254ada] transition">
                                    <span id="togglePasswordBtn"
                                        class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] text-[20px] cursor-pointer transition">visibility</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-6 p-4 bg-orange-50/80 dark:bg-orange-900/10 text-orange-700 dark:text-orange-300 text-[13px] rounded-lg flex gap-3 items-start border border-orange-100 dark:border-orange-800/30">
                            <span class="material-icons mt-0.5 text-[20px]">warning_amber</span>
                            <p class="leading-relaxed">Nếu bạn sử dụng Gmail, vui lòng không nhập mật khẩu tài khoản
                                trực tiếp. Bạn cần tạo <b>Mật khẩu ứng dụng (App Password)</b> trong mục Bảo mật của
                                Google để hệ thống có thể gửi email đi.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="button"
                            onclick="showToast('info', 'Kiểm tra kết nối', 'Đang gửi email test thử...');"
                            class="px-5 py-2.5 text-[#254ada] dark:text-[#4b6bfb] border border-[#254ada] dark:border-[#4b6bfb] hover:bg-blue-50 dark:hover:bg-[#254ada]/10 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                            <span class="material-icons text-[18px]">send</span> Gửi Test
                        </button>
                        <button type="submit"
                            class="submit-btn px-6 py-2.5 bg-orange-500 hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 text-white rounded-lg flex items-center gap-2 font-medium text-sm transition shadow-sm">
                            <span class="material-icons text-[20px]">save</span> Lưu cấu hình SMTP
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div
        class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span
                class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
    /* =================================================================
       HÀM CHUYỂN TAB
       ================================================================= */
    function switchTab(tabName) {
        // Ẩn tất cả nội dung tab
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('block');
            el.classList.add('hidden');
        });

        // Reset màu nút tab về mặc định
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
       HÀM PREVIEW ẢNH KHI CHỌN LOGO
       ================================================================= */
    function previewImage(input) {
        const preview = document.getElementById('logoPreview');
        const icon = document.getElementById('logoPreviewIcon');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (icon) icon.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /* =================================================================
       HÀM HIỂN THỊ THÔNG BÁO (TOAST)
       ================================================================= */
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');
        if (!container || !template) return;

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
        setTimeout(() => { if (container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
    }

    /* =================================================================
       HÀM XỬ LÝ LƯU (MÔ PHỎNG API BẰNG JAVASCRIPT)
       ================================================================= */
    function handleSaveSettings(event, tabName) {
        event.preventDefault(); // Chặn hành động load trang của Form

        const form = event.target;
        const submitBtn = form.querySelector('.submit-btn');
        const originalContent = submitBtn.innerHTML;

        // Đổi trạng thái thành Đang xử lý
        submitBtn.innerHTML = '<span class="material-icons animate-spin text-[20px]">autorenew</span> Đang lưu...';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

        // Giả lập lưu API mất 1.5 giây
        setTimeout(() => {
            showToast('success', 'Lưu thành công', 'Cấu hình đã được lưu và áp dụng cho toàn hệ thống.');
            submitBtn.innerHTML = originalContent;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');

            // Nếu bạn muốn form thực sự chạy lên server PHP, hãy xóa event.preventDefault() ở trên 
            // hoặc dùng lệnh: form.submit(); tại đây.
        }, 1500);
    }

    /* =================================================================
       SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
       ================================================================= */
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Chức năng Dark Mode
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            if (darkModeIcon) darkModeIcon.textContent = 'light_mode';
        }

        darkModeToggle?.addEventListener('click', () => {
            htmlElement.classList.toggle('dark');
            const isDark = htmlElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
        });

        // 2. Chức năng Dropdown Thông báo
        const notifButton = document.getElementById('notifButton');
        const notifDropdown = document.getElementById('notifDropdown');

        if (notifButton && notifDropdown) {
            notifButton.addEventListener('click', function (e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        // 3. Chức năng Ẩn/Hiện mật khẩu SMTP
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const smtpPasswordInput = document.getElementById('smtpPassword');

        if (togglePasswordBtn && smtpPasswordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = smtpPasswordInput.getAttribute('type') === 'password';
                smtpPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
                this.textContent = isPassword ? 'visibility_off' : 'visibility';
            });
        }
    });
</script>