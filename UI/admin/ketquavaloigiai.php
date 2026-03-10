<?php
// 1. Cấu hình thông tin trang
$title = "Kết quả & Lời giải - Hệ Thống Thi Trực Tuyến";
$active_menu = "results"; // Biến này dùng để làm sáng menu "Kết quả & Lời giải" trong Sidebar

// Dữ liệu mô phỏng danh sách kết quả thi
$results = [
    [
        'id' => 'SV2023001',
        'name' => 'Nguyễn Văn An',
        'avatar' => 'NA',
        'avatar_bg' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400',
        'exam' => 'Thi giữa kỳ I - Toán cao cấp',
        'score' => '8.5',
        'total_score' => '10.0',
        'time_spent' => '45:20',
        'time_total' => '60:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023042',
        'name' => 'Trần Thị Hoa',
        'avatar' => 'TH',
        'avatar_bg' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400',
        'exam' => 'Lập trình hướng đối tượng',
        'score' => '4.0',
        'total_score' => '10.0',
        'time_spent' => '58:12',
        'time_total' => '60:00',
        'status_type' => 'fail'
    ],
    [
        'id' => 'SV2023115',
        'name' => 'Lê Hoàng Minh',
        'avatar' => 'LM',
        'avatar_bg' => 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'exam' => 'Thi kết thúc học phần - CSDL',
        'score' => '9.2',
        'total_score' => '10.0',
        'time_spent' => '32:45',
        'time_total' => '90:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023204',
        'name' => 'Phạm Anh Tuấn',
        'avatar' => 'PT',
        'avatar_bg' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400',
        'exam' => 'Tiếng Anh chuyên ngành',
        'score' => '7.8',
        'total_score' => '10.0',
        'time_spent' => '40:00',
        'time_total' => '45:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023088',
        'name' => 'Bùi Ngọc Chi',
        'avatar' => 'BC',
        'avatar_bg' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400',
        'exam' => 'Toán học cao cấp',
        'score' => '2.5',
        'total_score' => '10.0',
        'time_spent' => '15:10',
        'time_total' => '60:00',
        'status_type' => 'fail'
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
            Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Kết quả và lời giải chi tiết</span>
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
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                            <a href="#"
                                class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                                dấu đã đọc</a>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                        class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa chấm
                                    xong 150 bài thi môn Toán.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 5 phút trước</span>
                            </a>
                        </div>
                        <a href="#"
                            class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Xem
                            tất cả</a>
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

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">

            <div
                class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-wrap lg:flex-nowrap justify-between items-start gap-4">
                <div class="flex-1 space-y-3 max-w-2xl">
                    <div class="flex gap-3">
                        <div class="relative w-1/2">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">library_books</span>
                            <select
                                class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition">
                                <option>Tất cả Môn học</option>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                        <div class="relative w-1/2">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">assignment</span>
                            <select
                                class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition">
                                <option>Tất cả Kỳ thi</option>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" id="searchInput" placeholder="Tìm theo mã hoặc tên thí sinh..."
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    </div>
                </div>

                <button onclick="handleExportReport(this)"
                    class="px-6 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-600 text-sm font-bold shadow-sm transition h-full">
                    <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="resultsTable">
                    <thead
                        class="bg-white dark:bg-slate-800 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700 transition-colors">
                        <tr>
                            <th class="px-6 py-5">Mã thí sinh</th>
                            <th class="px-6 py-5">Họ tên</th>
                            <th class="px-6 py-5 w-1/4">Kỳ thi</th>
                            <th class="px-6 py-5">Điểm số</th>
                            <th class="px-6 py-5 text-center">Thời gian<br>làm bài</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                        <?php foreach ($results as $res): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition result-row">

                                <td class="px-6 py-4 font-bold text-[#1e3bb3] dark:text-[#4b6bfb] text-[13px] r-id">
                                    <?php echo $res['id']; ?></td>

                                <td class="px-6 py-4 flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full <?php echo $res['avatar_bg']; ?> flex items-center justify-center font-bold text-[12px] border border-white dark:border-slate-700">
                                        <?php echo $res['avatar']; ?>
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-white text-[13px] leading-tight r-name">
                                        <?php echo str_replace(' ', '<br>', $res['name']); ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="font-medium text-slate-700 dark:text-slate-300 text-[13px] leading-relaxed pr-4">
                                        <?php echo $res['exam']; ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="font-bold text-slate-800 dark:text-white text-[15px]"><?php echo $res['score']; ?></span><span
                                        class="text-slate-400 dark:text-slate-500 text-[13px]">/<?php echo $res['total_score']; ?></span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="font-medium text-slate-600 dark:text-slate-300 text-[13px]">
                                        <?php echo $res['time_spent']; ?> <span
                                            class="text-slate-300 dark:text-slate-600">/</span></div>
                                    <div class="text-[12px] text-slate-400 dark:text-slate-500">
                                        <?php echo $res['time_total']; ?></div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <?php if ($res['status_type'] == 'pass'): ?>
                                        <div
                                            class="inline-block px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-center border border-green-100 dark:border-green-800/50">
                                            <div class="text-[11px] font-bold leading-tight">Đạt</div>
                                            <div class="text-[9px] font-medium opacity-80">(Passed)</div>
                                        </div>
                                    <?php else: ?>
                                        <div
                                            class="inline-block px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 rounded-full text-center border border-red-100 dark:border-red-800/50">
                                            <div class="text-[11px] font-bold leading-tight">Trượt</div>
                                            <div class="text-[9px] font-medium opacity-80">(Failed)</div>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button
                                        onclick="showToast('info', 'Chi tiết bài thi', 'Đang mở lời giải chi tiết cho thí sinh <?php echo $res['name']; ?>...')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-[#1e3bb3] dark:text-[#4b6bfb] rounded-lg text-[12px] font-bold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                                        <span class="material-icons text-[16px]">visibility</span> Xem lời giải
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị 1-3 trong số 5 kết quả</p>
                <div id="paginationControls" class="flex items-center gap-2 mt-3 md:mt-0">
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
       HÀM XỬ LÝ XUẤT BÁO CÁO (GIẢ LẬP LOADING)
       ================================================================= */
    function handleExportReport(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[20px]">autorenew</span> Đang trích xuất...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            showToast('success', 'Thành công', 'Báo cáo điểm thi đã được tải xuống thiết bị của bạn.');
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

        // 3. Logic Tìm kiếm & Phân trang Real-time
        const rowsPerPage = 3; // Hiển thị 3 dòng/trang
        let currentPage = 1;
        let filteredRows = [];

        const allRows = Array.from(document.querySelectorAll('.result-row'));
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            const displayStart = totalRows === 0 ? 0 : start + 1;
            const displayEnd = Math.min(end, totalRows);

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}-${displayEnd}</span> trong số <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> kết quả`;
            }

            if (paginationControls) {
                paginationControls.innerHTML = '';

                // Prev Button
                const prevBtn = document.createElement('button');
                prevBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
                prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
                paginationControls.appendChild(prevBtn);

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    if (i === currentPage) {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md bg-[#254ada] text-white font-bold text-xs shadow-sm';
                    } else {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium text-xs';
                    }
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => { currentPage = i; updatePagination(); };
                    paginationControls.appendChild(pageBtn);
                }

                // Next Button
                const nextBtn = document.createElement('button');
                nextBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
                nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
        }

        // Bắt sự kiện Lọc qua thanh tìm kiếm
        searchInput?.addEventListener('input', function () {
            const text = this.value.toLowerCase();

            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.r-name').textContent.toLowerCase();
                const id = row.querySelector('.r-id').textContent.toLowerCase();
                return name.includes(text) || id.includes(text);
            });

            currentPage = 1;
            updatePagination();
        });

        // Chạy lần đầu
        filteredRows = [...allRows];
        updatePagination();
    });
</script>