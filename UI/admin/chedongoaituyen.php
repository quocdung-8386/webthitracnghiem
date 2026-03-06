<?php
// 1. Cấu hình thông tin trang
$title = "Chế độ ngoại tuyến - Hệ Thống Thi Trực Tuyến";
$active_menu = "offline"; // Biến này dùng để làm sáng menu "Chế độ ngoại tuyến" trong Sidebar

// Dữ liệu mô phỏng cho bảng Lịch sử đồng bộ
$sync_history = [
    [
        'time' => '14:20 - 24/10',
        'student' => 'Nguyễn Văn An',
        'id' => 'SV2023001',
        'exam' => 'Thi cuối kỳ CNTT',
        'status' => 'Thành công',
        'status_bg' => 'bg-green-50 dark:bg-green-900/30',
        'status_text' => 'text-green-600 dark:text-green-400',
        'icon' => 'visibility'
    ],
    [
        'time' => '14:15 - 24/10',
        'student' => 'Trần Thị Hoa',
        'id' => 'SV2023042',
        'exam' => 'Kiểm tra tiếng Anh K19',
        'status' => 'Đang xử lý',
        'status_bg' => 'bg-orange-50 dark:bg-orange-900/30',
        'status_text' => 'text-orange-500 dark:text-orange-400',
        'icon' => 'visibility'
    ],
    [
        'time' => '13:45 - 24/10',
        'student' => 'Lê Hoàng Minh',
        'id' => 'SV2023115',
        'exam' => 'Kinh tế học đại cương',
        'status' => 'Lỗi gói tin',
        'status_bg' => 'bg-red-50 dark:bg-red-900/30',
        'status_text' => 'text-red-500 dark:text-red-400',
        'icon' => 'refresh'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Thí sinh & Làm bài  <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Chế độ ngoại tuyến</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..."
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

            <div class="flex items-center gap-4">
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
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo hệ thống</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                        class="font-semibold text-red-500">Lỗi đồng bộ</span> - Gói tin của thí sinh Lê
                                    Hoàng Minh bị gián đoạn.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 10 phút trước</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button id="darkModeToggle"
                    class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">
        <div class="max-w-6xl mx-auto space-y-6">

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                <div
                    class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4 mb-5">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg">Cấu hình chung</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-1">Kích hoạt và thiết lập quyền truy
                            cập ngoại tuyến</p>
                    </div>
                    <span
                        class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] text-[10px] font-bold rounded uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#254ada] dark:bg-[#4b6bfb] animate-pulse"></span> Sync
                        Active
                    </span>
                </div>

                <div class="space-y-6">
                    <div class="flex justify-between items-start gap-10">
                        <div>
                            <h4 class="font-semibold text-slate-800 dark:text-white text-[14px]">Cho phép thi ngoại
                                tuyến</h4>
                            <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Khi được bật,
                                thí sinh có thể tải dữ liệu đề thi về máy và làm bài mà không cần kết nối internet liên
                                tục. Kết quả sẽ tự động đồng bộ khi có mạng trở lại.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" checked class="sr-only peer"
                                onchange="showToast(this.checked ? 'success' : 'warning', 'Trạng thái', this.checked ? 'Đã bật chế độ thi ngoại tuyến.' : 'Đã tắt chế độ thi ngoại tuyến.')">
                            <div
                                class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#254ada] dark:peer-checked:bg-[#4b6bfb]">
                            </div>
                        </label>
                    </div>

                    <div class="w-full h-px bg-slate-100 dark:bg-slate-700"></div>

                    <div class="flex justify-between items-start gap-10">
                        <div>
                            <h4 class="font-semibold text-slate-800 dark:text-white text-[14px]">Yêu cầu xác thực lại
                                khi kết nối</h4>
                            <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Bắt buộc thí
                                sinh phải đăng nhập lại sau khi hoàn thành bài thi ngoại tuyến để thực hiện bước đồng bộ
                                hóa kết quả cuối cùng nhằm đảm bảo bảo mật.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                            <input type="checkbox" class="sr-only peer"
                                onchange="showToast('info', 'Cài đặt', this.checked ? 'Yêu cầu xác thực đã bật.' : 'Yêu cầu xác thực đã tắt.')">
                            <div
                                class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#254ada] dark:peer-checked:bg-[#4b6bfb]">
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                <div
                    class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <div class="mb-5">
                        <h3 class="font-bold text-slate-800 dark:text-white text-[15px]">Gói dữ liệu ngoại tuyến</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-1">Chuẩn bị dữ liệu cho việc sử dụng
                            Offline</p>
                    </div>

                    <div
                        class="flex-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700 p-5 flex flex-col justify-center transition-colors">
                        <div
                            class="flex justify-between items-center mb-3 text-[13px] font-bold text-slate-700 dark:text-slate-300">
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px]">cloud_download</span>
                                Dữ liệu kỳ thi hiện tại
                            </div>
                            <span class="text-slate-400 font-medium">420 MB</span>
                        </div>

                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mb-5 overflow-hidden">
                            <div id="downloadProgress"
                                class="bg-[#254ada] dark:bg-[#4b6bfb] h-1.5 rounded-full transition-all duration-300"
                                style="width: 65%"></div>
                        </div>

                        <button id="btnDownload" onclick="handleDownload(this)"
                            class="w-full py-3 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg font-semibold text-sm hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] transition flex items-center justify-center gap-2 shadow-sm mb-4">
                            <span class="material-icons text-[18px] icon-dl">file_download</span> <span
                                class="text-dl">TẢI GÓI CẬP NHẬT</span>
                        </button>

                        <p class="text-center text-[11px] text-slate-400 italic">Lần cập nhật cuối: 10:45 - 24/10/2023
                        </p>
                    </div>
                </div>

                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <div class="mb-5">
                        <h3 class="font-bold text-slate-800 dark:text-white text-[15px]">Trạng thái đồng bộ</h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-1">Theo dõi dữ liệu đang chờ xử lý
                        </p>
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div
                                class="bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 rounded-xl p-4 text-center transition-colors">
                                <p class="text-[10px] font-bold text-[#254ada] dark:text-[#4b6bfb] uppercase mb-1">Chờ
                                    đồng bộ</p>
                                <p class="text-3xl font-black text-[#254ada] dark:text-[#4b6bfb]" id="pendingSync">124
                                </p>
                            </div>
                            <div
                                class="bg-green-50/50 dark:bg-green-900/10 border border-green-100 dark:border-green-800/50 rounded-xl p-4 text-center transition-colors">
                                <p class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase mb-1">Đã
                                    hoàn tất</p>
                                <p class="text-3xl font-black text-green-600 dark:text-green-400" id="successSync">8.421
                                </p>
                            </div>
                        </div>

                        <button id="btnSync" onclick="handleSync(this)"
                            class="w-full py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg font-semibold text-sm hover:bg-slate-50 dark:hover:bg-slate-600 transition flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-icons text-[18px]">sync</span> ĐỒNG BỘ NGAY BÂY GIỜ
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[15px]">Lịch sử đồng bộ gần đây</h3>
                    <a href="#" class="text-[13px] text-[#254ada] dark:text-[#4b6bfb] font-semibold hover:underline">Xem
                        tất cả</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Thời gian</th>
                                <th class="px-6 py-4">Thí sinh</th>
                                <th class="px-6 py-4">Kỳ thi</th>
                                <th class="px-6 py-4 text-center">Trạng thái</th>
                                <th class="px-6 py-4 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach ($sync_history as $history): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 text-[13px] text-slate-600 dark:text-slate-300 font-medium">
                                        <?php echo $history['time']; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-white text-[13px]">
                                            <?php echo $history['student']; ?></div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                            <?php echo $history['id']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-[13px] text-slate-600 dark:text-slate-300">
                                        <?php echo $history['exam']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight border border-transparent <?php echo $history['status_bg']; ?> <?php echo $history['status_text']; ?>">
                                            <?php echo $history['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center mx-auto"
                                            title="<?php echo $history['icon'] == 'refresh' ? 'Thử lại' : 'Xem chi tiết'; ?>">
                                            <span class="material-icons text-[18px]"><?php echo $history['icon']; ?></span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
       HÀM XỬ LÝ NÚT BẤM (Tải dữ liệu & Đồng bộ)
       ================================================================= */
    function handleDownload(btn) {
        const icon = btn.querySelector('.icon-dl');
        const text = btn.querySelector('.text-dl');
        const bar = document.getElementById('downloadProgress');

        // Reset bar & Update UI
        bar.style.width = '0%';
        icon.innerHTML = 'autorenew';
        icon.classList.add('animate-spin');
        text.innerHTML = 'ĐANG TẢI...';
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-wait');

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.floor(Math.random() * 20) + 10;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);

                // Finish
                setTimeout(() => {
                    icon.classList.remove('animate-spin');
                    icon.innerHTML = 'check';
                    text.innerHTML = 'ĐÃ TẢI XONG';
                    btn.classList.remove('bg-[#254ada]', 'dark:bg-[#4b6bfb]', 'hover:bg-[#1e3bb3]');
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');

                    showToast('success', 'Tải thành công', 'Gói dữ liệu ngoại tuyến đã được tải về máy chủ trạm.');
                }, 500);
            }
            bar.style.width = `${progress}%`;
        }, 400);
    }

    function handleSync(btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">sync</span> ĐANG ĐỒNG BỘ...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            // Cập nhật số liệu giả lập
            document.getElementById('pendingSync').textContent = '0';
            document.getElementById('successSync').textContent = '8.545'; // +124

            btn.innerHTML = originalHTML;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            showToast('success', 'Đồng bộ hoàn tất', 'Đã xử lý thành công 124 gói dữ liệu lên Cloud.');
        }, 2000);
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
    });
</script>