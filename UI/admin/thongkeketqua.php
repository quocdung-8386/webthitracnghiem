<?php
// 1. Cấu hình thông tin trang
$title = "Thống kê tổng quan - Hệ Thống Thi Trực Tuyến";
$active_menu = "stat_result"; // Biến active menu ở thanh sidebar

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="flex items-center gap-3">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Thống kê & Báo cáo <span class="mx-2">›</span> <span
                    class="text-slate-800 dark:text-white font-medium">Xuất báo cáo dữ liệu thi</span>
            </div>
            <span
                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[10px] font-bold rounded uppercase flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Báo cáo thời gian thực
            </span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" id="searchInput" placeholder="Tìm kiếm dữ liệu..."
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
                            <a href="#"
                                class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                                dấu đã đọc</a>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Báo cáo tháng 10
                                    đã được tổng hợp thành công.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 1 giờ trước</span>
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
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 relative overflow-hidden transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center">
                            <span class="material-icons text-[20px]">library_books</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[11px] font-bold rounded-md">Tháng
                            này: +15%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Tổng lượt thi</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2">48,250</p>
                    <p class="text-[11px] text-slate-400">Dữ liệu tính từ đầu năm 2024</p>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">calculate</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[11px] font-bold rounded-md uppercase">Hệ
                            10</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Điểm trung bình</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2">7.42</p>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-orange-500 dark:bg-orange-400 rounded-full" style="width: 74.2%"></div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">check_circle</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[11px] font-bold rounded-md">Tăng
                            2%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Tỷ lệ đỗ</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2">82.5%</p>
                    <p class="text-[11px] text-slate-400">Dựa trên 5.0 điểm liệt</p>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 border-l-4 border-l-red-500 dark:border-l-red-500 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">warning_amber</span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Câu hỏi khó nhất</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white mb-2">ID: #Q-9942</p>
                    <p class="text-[12px] text-slate-500 dark:text-slate-400">Tỷ lệ trả lời sai: <span
                            class="font-bold text-red-500 dark:text-red-400">88%</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-[16px]">Xu hướng thí sinh tham gia
                            </h3>
                            <p class="text-[12px] text-slate-500 dark:text-slate-400">Thống kê theo từng tháng trong năm
                                2024</p>
                        </div>
                        <button
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Năm
                            2024</button>
                    </div>

                    <div class="flex-1 relative mt-4 min-h-[200px]">
                        <div class="absolute inset-0 flex flex-col justify-between text-[10px] text-slate-400 pb-6">
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">10k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">7.5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">2.5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">0</span>
                                <div class="flex-1 border-t border-slate-200 dark:border-slate-600"></div>
                            </div>
                        </div>

                        <div class="absolute inset-0 flex justify-between items-end pl-10 pr-4 pb-6 pt-2">
                            <?php
                            $chart_data = [
                                ['label' => 'T.1', 'height' => '30%'],
                                ['label' => 'T.2', 'height' => '45%'],
                                ['label' => 'T.3', 'height' => '35%'],
                                ['label' => 'T.4', 'height' => '65%'],
                                ['label' => 'T.5', 'height' => '85%'],
                                ['label' => 'T.6', 'height' => '40%']
                            ];
                            foreach ($chart_data as $data): ?>
                                <div class="flex flex-col items-center h-full justify-end w-10 relative group">
                                    <div class="w-2.5 bg-blue-100 dark:bg-blue-900/40 relative rounded-t-sm transition-all duration-300 group-hover:w-3"
                                        style="height: <?php echo $data['height']; ?>;">
                                        <div class="absolute bottom-0 left-0 w-full bg-[#254ada] dark:bg-[#4b6bfb] rounded-t-sm transition-all duration-500"
                                            style="height: 100%;"></div>
                                    </div>
                                    <span
                                        class="absolute -bottom-6 text-[11px] font-semibold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors"><?php echo $data['label']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px] mb-8">Tỷ lệ xếp loại kết quả</h3>

                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="w-48 h-48 rounded-full relative flex items-center justify-center mb-8 shadow-sm transition-transform hover:scale-105 duration-300"
                            style="background: conic-gradient(#3b82f6 0% 25%, #22c55e 25% 65%, #f97316 65% 85%, #ef4444 85% 100%);">
                            <div
                                class="w-36 h-36 bg-white dark:bg-slate-800 rounded-full flex flex-col items-center justify-center shadow-inner transition-colors">
                                <span class="text-4xl font-black text-slate-800 dark:text-white leading-none">85%</span>
                                <span class="text-[9px] font-bold text-slate-400 mt-1">TRÊN TRUNG BÌNH</span>
                            </div>
                        </div>

                        <div class="w-full space-y-3 px-2 text-[13px] font-medium text-slate-600 dark:text-slate-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-blue-500"></span> Giỏi</div>
                                <span class="font-bold text-slate-800 dark:text-white">25%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-green-500"></span> Khá</div>
                                <span class="font-bold text-slate-800 dark:text-white">40%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-orange-500"></span> Trung bình</div>
                                <span class="font-bold text-slate-800 dark:text-white">20%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-red-500"></span> Yếu</div>
                                <span class="font-bold text-slate-800 dark:text-white">15%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px]">Dữ liệu chi tiết theo Kỳ thi gần
                        đây</h3>
                    <button onclick="handleDownload(this)"
                        class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 flex items-center gap-2 transition">
                        <span class="material-icons text-[18px]">download</span> Tải báo cáo
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Tên kỳ thi</th>
                                <th class="px-6 py-4 text-center">Tổng lượt thi</th>
                                <th class="px-6 py-4 text-center">Điểm TB</th>
                                <th class="px-6 py-4 w-[25%]">Tỷ lệ đỗ</th>
                                <th class="px-6 py-4 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition data-row">
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-[13px] d-name">Kỳ thi
                                    Tiếng Anh Chuyên ngành B1</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600 dark:text-slate-300">1,250
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-white">8.2</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500 rounded-full" style="width: 92%"></div>
                                        </div>
                                        <span
                                            class="text-[12px] font-bold text-slate-600 dark:text-slate-300">92%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        class="text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition"
                                        title="Xem chi tiết"><span
                                            class="material-icons text-[20px]">visibility</span></button>
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition data-row">
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-[13px] d-name">Lý
                                    thuyết Lập trình C++ nâng cao</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600 dark:text-slate-300">840
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-white">6.5</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: 75%"></div>
                                        </div>
                                        <span
                                            class="text-[12px] font-bold text-slate-600 dark:text-slate-300">75%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        class="text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition"
                                        title="Xem chi tiết"><span
                                            class="material-icons text-[20px]">visibility</span></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                    <p id="paginationInfo">Hiển thị 1 - 2 trên tổng số 45,800 bản ghi</p>
                    <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
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

    // Xử lý nút tải báo cáo
    function handleDownload(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xuất file...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('success', 'Hoàn tất', 'Báo cáo đã được tải xuống dưới dạng Excel (.xlsx).');
            btn.innerHTML = originalText;
            btn.disabled = false;
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

        // 3. Phân trang thông minh & Tìm kiếm
        const rowsPerPage = 2; // Demo: Hiển thị 2 dòng mỗi trang
        let currentPage = 1;
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        let filteredRows = [...allRows];

        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            // GIẢ LẬP SỐ LƯỢNG LỚN ĐỂ HIỂN THỊ DẤU "..." (GIỐNG THIẾT KẾ)
            const isDemoMode = true;
            const fakeTotalPages = 458;
            const fakeTotalRows = 45800;

            const totalRows = filteredRows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (isDemoMode && searchInput && searchInput.value.trim() === '') {
                totalPages = fakeTotalPages;
            }

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Ẩn/Hiện dòng
            allRows.forEach(row => row.style.display = 'none');
            if (currentPage === 1 || !isDemoMode || (searchInput && searchInput.value.trim() !== '')) {
                filteredRows.slice(start, end).forEach(row => row.style.display = '');
            }

            // Cập nhật text hiển thị
            let displayStart = totalRows === 0 ? 0 : start + 1;
            let displayEnd = Math.min(end, (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows);
            let displayTotal = (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows;

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart} - ${displayEnd}</span> trên tổng số <span class="font-medium text-slate-800 dark:text-white">${displayTotal.toLocaleString()}</span> bản ghi`;
            }

            // Vẽ nút phân trang
            if (paginationControls) {
                paginationControls.innerHTML = '';

                // Nút Prev
                const prevBtn = document.createElement('button');
                prevBtn.className = `w-8 h-8 flex items-center justify-center border rounded transition ${currentPage === 1 ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600'}`;
                prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
                prevBtn.disabled = currentPage === 1;
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
                paginationControls.appendChild(prevBtn);

                const createPageBtn = (i) => {
                    const btn = document.createElement('button');
                    if (i === currentPage) {
                        btn.className = 'w-8 h-8 flex items-center justify-center bg-[#254ada] text-white rounded font-medium shadow-sm transition transform scale-105';
                    } else {
                        btn.className = 'w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-transparent hover:bg-slate-50 dark:hover:bg-slate-700 rounded font-medium text-slate-600 dark:text-slate-300 transition';
                    }
                    btn.innerText = i;
                    btn.onclick = () => { currentPage = i; updatePagination(); };
                    return btn;
                };

                const createDots = () => {
                    const span = document.createElement('span');
                    span.className = 'text-slate-400 px-1 tracking-widest text-xs';
                    span.innerText = '...';
                    return span;
                };

                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) paginationControls.appendChild(createPageBtn(i));
                } else {
                    paginationControls.appendChild(createPageBtn(1));
                    if (currentPage > 3) paginationControls.appendChild(createDots());

                    let startPage = Math.max(2, currentPage - 1);
                    let endPage = Math.min(totalPages - 1, currentPage + 1);

                    if (currentPage === 1) endPage = 3;
                    if (currentPage === totalPages) startPage = totalPages - 2;

                    for (let i = startPage; i <= endPage; i++) {
                        paginationControls.appendChild(createPageBtn(i));
                    }

                    if (currentPage < totalPages - 2) paginationControls.appendChild(createDots());
                    paginationControls.appendChild(createPageBtn(totalPages));
                }

                // Nút Next
                const nextBtn = document.createElement('button');
                nextBtn.className = `w-8 h-8 flex items-center justify-center border rounded transition ${currentPage === totalPages ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600'}`;
                nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
        }

        function applyFilters() {
            const text = searchInput ? searchInput.value.toLowerCase() : '';
            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.d-name').textContent.toLowerCase();
                return name.includes(text);
            });
            currentPage = 1;
            updatePagination();
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);

        updatePagination();
    });
</script>