<?php
// 1. Cấu hình thông tin trang
$title = "Trung tâm Điều phối Phiên thi - Hệ Thống Thi Trực Tuyến";
$active_menu = "nav_bar"; // Làm sáng menu "Thanh điều hướng" theo đúng ảnh chụp

// Dữ liệu mô phỏng cho 6 phòng thi
$rooms = [
    [
        'name' => 'Phòng thi 01',
        'subject' => 'LẬP TRÌNH JAVA',
        'students' => '42/45',
        'time' => '15:30',
        'time_sec' => 930,
        'status' => 'CẢNH BÁO',
        'status_icon' => 'warning',
        'type' => 'warning',
        'alert_icon' => 'person_off',
        'alert_text' => '02 Thí sinh mất kết nối > 5 phút'
    ],
    [
        'name' => 'Phòng thi 02',
        'subject' => 'CƠ SỞ DỮ LIỆU',
        'students' => '30/30',
        'time' => '45:12',
        'time_sec' => 2712,
        'status' => 'ĐANG DIỄN RA',
        'status_icon' => '',
        'type' => 'normal',
        'alert_icon' => 'check_circle',
        'alert_text' => 'Không có cảnh báo hoạt động'
    ],
    [
        'name' => 'Phòng thi 03',
        'subject' => 'KINH TẾ VĨ MÔ',
        'students' => '118/120',
        'time' => '03:45',
        'time_sec' => 225,
        'status' => 'SẮP KẾT THÚC',
        'status_icon' => '',
        'type' => 'ending',
        'alert_icon' => 'assignment_turned_in',
        'alert_text' => '112 thí sinh đã nộp bài'
    ],
    [
        'name' => 'Phòng thi 04',
        'subject' => 'TOÁN RỜI RẠC',
        'students' => '25/25',
        'time' => '82:20',
        'time_sec' => 4940,
        'status' => 'ĐANG DIỄN RA',
        'status_icon' => '',
        'type' => 'normal',
        'alert_icon' => 'info',
        'alert_text' => 'Đã bắt đầu được 7 phút'
    ],
    [
        'name' => 'Phòng thi 05',
        'subject' => 'MẠNG MÁY TÍNH',
        'students' => '55/56',
        'time' => '22:05',
        'time_sec' => 1325,
        'status' => 'VI PHẠM',
        'status_icon' => 'error',
        'type' => 'danger',
        'alert_icon' => 'visibility_off',
        'alert_text' => '01 Thí sinh thoát trình duyệt'
    ],
    [
        'name' => 'Phòng thi 06',
        'subject' => 'TRIẾT HỌC',
        'students' => '18/20',
        'time' => '110:00',
        'time_sec' => 6600,
        'status' => 'ĐANG DIỄN RA',
        'status_icon' => '',
        'type' => 'normal',
        'alert_icon' => 'lock',
        'alert_text' => 'Mã phòng: TRIET24A'
    ]
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
                Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Trung tâm Điều phối Phiên thi</span>    

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm phòng thi..."
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
                                        class="font-semibold text-red-500">Phòng thi 05</span> có thí sinh vi phạm quy
                                    chế.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> Vừa xong</span>
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

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Trung tâm Điều phối Phiên thi</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Hiện có 6 phòng thi đang diễn ra đồng thời</p>
            </div>
            <div class="flex items-center gap-3">
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full text-sm font-semibold border border-green-100 dark:border-green-800/50 transition-colors">
                    <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full animate-pulse"></div> Hệ thống ổn
                    định
                </div>
                <button onclick="showToast('success', 'Làm mới', 'Dữ liệu các phòng thi đã được cập nhật mới nhất.')"
                    class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-full flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[18px]">refresh</span> Làm mới
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-6">
            <?php foreach ($rooms as $room): ?>
                <?php
                // Cài đặt style linh hoạt dựa trên Type của phòng (Hỗ trợ Dark Mode)
                $card_class = "bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-500";
                $badge_class = "bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 border-green-100 dark:border-green-800/50";
                $time_color = "text-slate-800 dark:text-white";
                $alert_color = "text-slate-500 dark:text-slate-400";

                if ($room['type'] == 'warning') {
                    $card_class = "bg-orange-50/50 dark:bg-orange-900/10 border-orange-200 dark:border-orange-500/30 shadow-sm relative";
                    $badge_class = "bg-orange-100 dark:bg-orange-900/40 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-500/50";
                    $time_color = "text-orange-500 dark:text-orange-400";
                    $alert_color = "text-orange-600 dark:text-orange-400 font-medium";
                } elseif ($room['type'] == 'danger') {
                    $card_class = "bg-red-50/50 dark:bg-red-900/20 border-red-300 dark:border-red-500/40 shadow-sm relative z-10 scale-[1.02]";
                    $badge_class = "bg-red-500 text-white border-red-600 shadow-sm animate-pulse";
                    $time_color = "text-slate-800 dark:text-white";
                    $alert_color = "text-red-600 dark:text-red-400 font-bold";
                } elseif ($room['type'] == 'ending') {
                    $badge_class = "bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 border-yellow-100 dark:border-yellow-800/50";
                    $time_color = "text-yellow-600 dark:text-yellow-400";
                }
                ?>

                <div class="rounded-2xl border <?php echo $card_class; ?> p-6 flex flex-col transition-all duration-200">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-lg"><?php echo $room['name']; ?></h3>
                            <p
                                class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1 border-b border-slate-200 dark:border-slate-600 pb-2 w-fit">
                                MÔN: <?php echo $room['subject']; ?></p>
                        </div>
                        <span
                            class="px-2.5 py-1 text-[10px] font-bold uppercase rounded flex items-center gap-1 border <?php echo $badge_class; ?>">
                            <?php if ($room['status_icon']): ?>
                                <span class="material-icons text-[14px]"><?php echo $room['status_icon']; ?></span>
                            <?php endif; ?>
                            <?php echo $room['status']; ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div
                            class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                            <span
                                class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">THÍ
                                SINH</span>
                            <span
                                class="text-2xl font-black text-slate-800 dark:text-white"><?php echo $room['students']; ?></span>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                            <span
                                class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">THỜI
                                GIAN CÒN</span>
                            <span class="text-2xl font-black time-countdown <?php echo $time_color; ?>"
                                data-sec="<?php echo $room['time_sec']; ?>"><?php echo $room['time']; ?></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-[13px] mb-6 mt-auto <?php echo $alert_color; ?>">
                        <span class="material-icons text-[18px]"><?php echo $room['alert_icon']; ?></span>
                        <?php echo $room['alert_text']; ?>
                    </div>

                    <div class="flex gap-3">
                        <button onclick="window.location.href='giamsattructuyen.php'"
                            class="flex-1 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white font-semibold rounded-xl text-[14px] shadow-md shadow-blue-900/20 transition-all">
                            Vào giám sát
                        </button>
                        <button
                            onclick="showToast('info', 'Cài đặt phòng thi', 'Mở tùy chọn cấu hình cho <?php echo $room['name']; ?>')"
                            class="w-12 h-[42px] bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            <span class="material-icons text-[20px]">settings</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
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
       HÀM FORMAT THỜI GIAN (SS -> MM:SS)
       ================================================================= */
    function formatTime(seconds) {
        if (seconds <= 0) return "00:00";
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return (m < 10 ? "0" : "") + m + ":" + (s < 10 ? "0" : "") + s;
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

        // 3. Khởi tạo Đồng hồ đếm ngược (Realtime Countdown)
        const timeElements = document.querySelectorAll('.time-countdown');
        setInterval(() => {
            timeElements.forEach(el => {
                let currentSec = parseInt(el.getAttribute('data-sec'));
                if (currentSec > 0) {
                    currentSec--;
                    el.setAttribute('data-sec', currentSec);
                    el.textContent = formatTime(currentSec);
                }
            });
        }, 1000);
    });
</script>