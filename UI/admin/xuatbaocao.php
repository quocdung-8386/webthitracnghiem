<?php
// 1. Cấu hình thông tin trang
$title = "Xuất báo cáo - Hệ Thống Thi Trực Tuyến";
$active_menu = "export_report"; // Biến active menu ở thanh sidebar

// Dữ liệu mô phỏng Lịch sử xuất báo cáo (Đã thêm class Dark Mode cho Badge)
$export_history = [
    [
        'name' => 'Bao_cao_Hoc_ky_1_Khoi_12_2023.xlsx',
        'type' => 'Báo cáo theo lớp',
        'date' => '12/05/2024 08:30',
        'format' => 'XLSX',
        'format_bg' => 'bg-green-50 dark:bg-green-900/30',
        'format_text' => 'text-green-600 dark:text-green-400',
        'size' => '2.4 MB'
    ],
    [
        'name' => 'Ket_qua_Thi_Toan_Cao_Cap_A1.pdf',
        'type' => 'Báo cáo theo môn',
        'date' => '11/05/2024 15:45',
        'format' => 'PDF',
        'format_bg' => 'bg-red-50 dark:bg-red-900/30',
        'format_text' => 'text-red-500 dark:text-red-400',
        'size' => '4.8 MB'
    ],
    [
        'name' => 'Danh_sach_Sinh_vien_Nganh_IT.csv',
        'type' => 'Báo cáo cá nhân',
        'date' => '10/05/2024 10:20',
        'format' => 'CSV',
        'format_bg' => 'bg-blue-50 dark:bg-blue-900/30',
        'format_text' => 'text-blue-600 dark:text-blue-400',
        'size' => '850 KB'
    ],
    [
        'name' => 'Tong_hop_Ky_thi_Gia_dinh_2024.xlsx',
        'type' => 'Báo cáo tổng hợp',
        'date' => '09/05/2024 14:15',
        'format' => 'XLSX',
        'format_bg' => 'bg-green-50 dark:bg-green-900/30',
        'format_text' => 'text-green-600 dark:text-green-400',
        'size' => '1.2 MB'
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
            Thống kê & Báo cáo <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Xuất báo cáo dữ liệu thi</span>
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
                            <a href="#"
                                class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                                dấu đã đọc</a>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Hệ thống đã dọn
                                    dẹp các báo cáo cũ hơn 30 ngày.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 1 ngày trước</span>
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
                <h3
                    class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                    <span class="material-icons text-slate-400 dark:text-slate-500 text-[20px]">feed</span> Cấu hình
                    xuất báo cáo mới
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Loại báo
                            cáo</label>
                        <div class="relative">
                            <select
                                class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition">
                                <option>Báo cáo theo lớp học</option>
                                <option>Báo cáo theo môn học</option>
                                <option>Báo cáo tổng hợp kỳ thi</option>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Khoảng
                            thời gian</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="date"
                                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#254ada] transition appearance-none">
                            </div>
                            <span class="text-slate-400 text-sm">đến</span>
                            <div class="relative flex-1">
                                <input type="date"
                                    class="w-full px-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#254ada] transition appearance-none">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-2">Định dạng
                            file</label>
                        <div class="flex items-center gap-2" id="formatGroup">
                            <button type="button"
                                class="format-btn active flex-1 py-2.5 border border-[#254ada] bg-blue-50 dark:bg-[#254ada]/20 text-[#254ada] dark:text-[#4b6bfb] rounded-lg text-sm font-semibold transition"
                                data-format="excel">Excel</button>
                            <button type="button"
                                class="format-btn flex-1 py-2.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition"
                                data-format="pdf">PDF</button>
                            <button type="button"
                                class="format-btn flex-1 py-2.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition"
                                data-format="csv">CSV</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button
                        class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition">Đặt
                        lại</button>
                    <button id="btnGenerateReport" onclick="handleGenerateReport(this)"
                        class="px-6 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 text-sm font-semibold shadow-sm transition">
                        <span class="material-icons text-[18px]">download</span> Tạo báo cáo
                    </button>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
                <div
                    class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-wrap gap-4 justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px] flex items-center gap-2">
                        <span class="material-icons text-slate-400 dark:text-slate-500 text-[20px]">history</span> Lịch
                        sử xuất báo cáo gần đây
                    </h3>

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            <input type="text" id="searchHistory" placeholder="Tìm theo tên file..."
                                class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition w-56">
                        </div>
                        <button onclick="showToast('info', 'Làm mới', 'Dữ liệu đã được cập nhật mới nhất.')"
                            class="text-[13px] text-[#254ada] dark:text-[#4b6bfb] font-semibold hover:underline flex items-center gap-1 transition">
                            <span class="material-icons text-[16px]">refresh</span> Làm mới
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700 transition-colors">
                            <tr>
                                <th class="px-6 py-4 w-1/3">Tên báo cáo</th>
                                <th class="px-6 py-4">Loại</th>
                                <th class="px-6 py-4">Ngày tạo</th>
                                <th class="px-6 py-4 text-center">Định dạng</th>
                                <th class="px-6 py-4 text-center">Dung lượng</th>
                                <th class="px-6 py-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="historyTableBody">
                            <?php foreach ($export_history as $row): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group history-row">
                                    <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300 text-[13px] truncate r-name"
                                        title="<?php echo $row['name']; ?>">
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-[13px]">
                                        <?php echo $row['type']; ?></td>
                                    <td class="px-6 py-4 text-slate-500 dark:text-slate-500 text-[13px] font-mono">
                                        <?php echo $row['date']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2.5 py-1 <?php echo $row['format_bg']; ?> <?php echo $row['format_text']; ?> text-[10px] font-bold rounded uppercase inline-block min-w-[50px]"><?php echo $row['format']; ?></span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center text-slate-600 dark:text-slate-400 text-[13px] font-medium">
                                        <?php echo $row['size']; ?></td>
                                    <td class="px-6 py-4 text-center text-[#254ada] dark:text-[#4b6bfb]">
                                        <button
                                            onclick="showToast('success', 'Đang tải', 'File <?php echo $row['name']; ?> đang được tải xuống.')"
                                            class="p-1.5 rounded-md hover:bg-blue-50 dark:hover:bg-slate-700 transition"
                                            title="Tải xuống">
                                            <span class="material-icons text-[20px]">file_download</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                    <p id="paginationInfo">Hiển thị 1-4 trong tổng số 28 báo cáo</p>
                    <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 p-5 rounded-xl flex gap-4 items-start transition-colors">
                    <span
                        class="material-icons text-[#254ada] dark:text-[#4b6bfb] bg-blue-100 dark:bg-blue-800/50 p-2 rounded-lg">security</span>
                    <div>
                        <h4 class="font-bold text-[#254ada] dark:text-[#4b6bfb] text-[14px] mb-1">Lưu ý bảo mật</h4>
                        <p class="text-[12px] text-blue-900/80 dark:text-blue-200/70 leading-relaxed">Các báo cáo chứa
                            dữ liệu định danh thí sinh. Vui lòng chỉ chia sẻ file cho nhân sự có thẩm quyền.</p>
                    </div>
                </div>

                <div
                    class="bg-orange-50/50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-800/50 p-5 rounded-xl flex gap-4 items-start transition-colors">
                    <span
                        class="material-icons text-orange-500 dark:text-orange-400 bg-orange-100 dark:bg-orange-800/50 p-2 rounded-lg">auto_delete</span>
                    <div>
                        <h4 class="font-bold text-orange-600 dark:text-orange-400 text-[14px] mb-1">Tự động dọn dẹp</h4>
                        <p class="text-[12px] text-orange-900/80 dark:text-orange-200/70 leading-relaxed">Hệ thống sẽ tự
                            động xóa các file báo cáo đã được xuất quá 30 ngày để tối ưu không gian lưu trữ.</p>
                    </div>
                </div>

                <div
                    class="bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 p-5 rounded-xl flex gap-4 items-start transition-colors">
                    <span
                        class="material-icons text-slate-500 dark:text-slate-400 bg-slate-200 dark:bg-slate-700 p-2 rounded-lg">support_agent</span>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 text-[14px] mb-1">Hỗ trợ kỹ thuật</h4>
                        <p class="text-[12px] text-slate-600 dark:text-slate-400 leading-relaxed">Nếu không tìm thấy dữ
                            liệu mong muốn, vui lòng liên hệ quản trị viên kỹ thuật để được hỗ trợ.</p>
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

    /* =================================================================
       HÀM XỬ LÝ TẠO BÁO CÁO (GIẢ LẬP)
       ================================================================= */
    function handleGenerateReport(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xử lý...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            showToast('success', 'Thành công', 'Báo cáo đang được xử lý ngầm. File sẽ xuất hiện ở bảng Lịch sử khi hoàn tất.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
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

        // 3. Logic chọn Định dạng file (Format Buttons)
        const formatBtns = document.querySelectorAll('.format-btn');
        formatBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                // Reset tất cả nút
                formatBtns.forEach(b => {
                    b.classList.remove('border-[#254ada]', 'bg-blue-50', 'text-[#254ada]', 'dark:bg-[#254ada]/20', 'dark:text-[#4b6bfb]');
                    b.classList.add('border-slate-200', 'bg-white', 'text-slate-600', 'dark:border-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
                });
                // Kích hoạt nút được bấm
                this.classList.remove('border-slate-200', 'bg-white', 'text-slate-600', 'dark:border-slate-600', 'dark:bg-slate-800', 'dark:text-slate-400');
                this.classList.add('border-[#254ada]', 'bg-blue-50', 'text-[#254ada]', 'dark:bg-[#254ada]/20', 'dark:text-[#4b6bfb]');
            });
        });

        // 4. Logic Tìm kiếm & Phân trang thông minh cho bảng Lịch sử
        const rowsPerPage = 3;
        let currentPage = 1;
        const allRows = Array.from(document.querySelectorAll('.history-row'));
        let filteredRows = [...allRows];

        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchHistory');

        function updatePagination() {
            // Giả lập tổng số báo cáo lớn
            const isDemoMode = true;
            const fakeTotalPages = 10;
            const fakeTotalRows = 28;

            const totalRows = filteredRows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (isDemoMode && searchInput && searchInput.value.trim() === '') {
                totalPages = fakeTotalPages;
            }

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            if (currentPage === 1 || !isDemoMode || (searchInput && searchInput.value.trim() !== '')) {
                filteredRows.slice(start, end).forEach(row => row.style.display = '');
            }

            let displayStart = totalRows === 0 ? 0 : start + 1;
            let displayEnd = Math.min(end, (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows);
            let displayTotal = (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows;

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart} - ${displayEnd}</span> trong tổng số <span class="font-medium text-slate-800 dark:text-white">${displayTotal}</span> báo cáo`;
            }

            if (paginationControls) {
                paginationControls.innerHTML = '';

                // Prev Button
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

                // Next Button
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
                const name = row.querySelector('.r-name').textContent.toLowerCase();
                return name.includes(text);
            });
            currentPage = 1;
            updatePagination();
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);

        updatePagination();
    });
</script>