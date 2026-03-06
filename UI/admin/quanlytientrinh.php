<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý tiến trình - Hệ Thống Thi Trực Tuyến";
$active_menu = "progress"; // Làm sáng menu "Quản lý tiến trình" trong Sidebar

// Dữ liệu mô phỏng cho thẻ thống kê (Stats)
$stats = [
    ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => '68.5%', 'icon' => 'trending_up', 'color' => 'blue'],
    ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => '74.2%', 'icon' => 'check_circle_outline', 'color' => 'green'],
    ['title' => 'THỜI GIAN TB', 'value' => '42 phút', 'icon' => 'history', 'color' => 'orange'],
    ['title' => 'ĐANG HỌC', 'value' => '1,204', 'icon' => 'groups', 'color' => 'purple'],
];

// Dữ liệu mô phỏng danh sách tiến trình của thí sinh
$progress_data = [
    [
        'id' => 'SV2023001',
        'name' => 'Nguyễn Văn An',
        'avatar' => 'NA',
        'avatar_bg' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400',
        'dept' => 'CNTT',
        'class' => 'K20A',
        'completed' => 12,
        'total_tasks' => 15,
        'percent' => 80,
        'bar_color' => 'bg-blue-600 dark:bg-blue-500',
        'score' => '8.5',
        'status' => 'VƯỢT TIẾN ĐỘ',
        'status_bg' => 'bg-green-100 dark:bg-green-900/30',
        'status_text' => 'text-green-700 dark:text-green-400'
    ],
    [
        'id' => 'SV2023042',
        'name' => 'Trần Thị Hoa',
        'avatar' => 'TH',
        'avatar_bg' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400',
        'dept' => 'CNTT',
        'class' => 'K20A',
        'completed' => 6,
        'total_tasks' => 15,
        'percent' => 40,
        'bar_color' => 'bg-orange-500',
        'score' => '6.2',
        'status' => 'CHẬM TIẾN ĐỘ',
        'status_bg' => 'bg-orange-100 dark:bg-orange-900/30',
        'status_text' => 'text-orange-700 dark:text-orange-400'
    ],
    [
        'id' => 'SV2023115',
        'name' => 'Lê Hoàng Minh',
        'avatar' => 'LM',
        'avatar_bg' => 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'dept' => 'Kinh tế',
        'class' => 'K21',
        'completed' => 14,
        'total_tasks' => 15,
        'percent' => 93,
        'bar_color' => 'bg-green-500 dark:bg-green-400',
        'score' => '9.1',
        'status' => 'HOÀN THÀNH TỐT',
        'status_bg' => 'bg-green-100 dark:bg-green-900/30',
        'status_text' => 'text-green-700 dark:text-green-400'
    ],
    [
        'id' => 'SV2023204',
        'name' => 'Phạm Anh Tuấn',
        'avatar' => 'PT',
        'avatar_bg' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400',
        'dept' => 'Ngôn ngữ Anh',
        'class' => 'K19',
        'completed' => 10,
        'total_tasks' => 15,
        'percent' => 66,
        'bar_color' => 'bg-blue-600 dark:bg-blue-500',
        'score' => '7.4',
        'status' => 'ĐÚNG LỘ TRÌNH',
        'status_bg' => 'bg-blue-100 dark:bg-blue-900/30',
        'status_text' => 'text-blue-700 dark:text-blue-400'
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
            Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản lý tiến trình học tập</span>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..."
                    class="pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="notifButton" type="button"
                        class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
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
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Hệ thống đang cập
                                    nhật dữ liệu tiến trình học tập mới nhất.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                            </a>
                        </div>
                        <a href="#"
                            class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Xem
                            tất cả</a>
                    </div>
                </div>
                <button id="darkModeToggle"
                    class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <?php foreach ($stats as $stat): ?>
                <?php
                // Xử lý class màu cho thẻ thống kê trong Dark Mode
                $bgClass = "bg-{$stat['color']}-50 dark:bg-{$stat['color']}-900/20";
                $textClass = "text-{$stat['color']}-600 dark:text-{$stat['color']}-400";
                ?>
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5 transition-colors">
                    <div
                        class="w-14 h-14 rounded-full <?php echo $bgClass; ?> <?php echo $textClass; ?> flex items-center justify-center shrink-0">
                        <span class="material-icons text-[28px]"><?php echo $stat['icon']; ?></span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                            <?php echo $stat['title']; ?></p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $stat['value']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">

            <div
                class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <div class="relative min-w-[200px]">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_alt</span>
                        <select id="classFilter"
                            class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition">
                            <option value="all">Tất cả Lớp/Đơn vị</option>
                            <option value="K20A">CNTT K20A</option>
                            <option value="K21">Kinh tế K21</option>
                            <option value="K19">Ngôn ngữ Anh K19</option>
                        </select>
                        <span
                            class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>

                    <div class="relative flex-1 lg:min-w-[300px]">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" id="searchInput" placeholder="Tìm tên thí sinh, MSSV..."
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="showToast('info', 'Xuất báo cáo', 'Hệ thống đang chuẩn bị dữ liệu xuất Excel...')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-600 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                    </button>
                    <button
                        onclick="showToast('success', 'Đã gửi thông báo', 'Đã gửi email nhắc nhở những thí sinh chậm tiến độ.')"
                        class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg flex items-center gap-2 hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">mail</span> Nhắc nhở thí sinh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="progressTable">
                    <thead
                        class="bg-slate-50/50 dark:bg-slate-900/30 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5">Thí sinh</th>
                            <th class="px-6 py-5">Lớp / Ngành</th>
                            <th class="px-6 py-5 text-center">Bài<br>luyện tập</th>
                            <th class="px-6 py-5 w-[25%]">Tiến độ</th>
                            <th class="px-6 py-5 text-center">Điểm<br>trung bình</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Chi<br>tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                        <?php foreach ($progress_data as $row): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition progress-row">

                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full <?php echo $row['avatar_bg']; ?> flex items-center justify-center font-bold text-[13px] shrink-0 border border-slate-100 dark:border-slate-700">
                                        <?php echo $row['avatar']; ?>
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px] leading-tight">
                                        <span class="p-name"><?php echo str_replace(' ', '<br>', $row['name']); ?></span>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 font-normal mt-1 p-id">
                                            ID: <?php echo $row['id']; ?></div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700 dark:text-slate-300 text-[13px] p-dept">
                                        <?php echo $row['dept']; ?></div>
                                    <div class="text-[12px] text-slate-500 dark:text-slate-400 p-class">
                                        <?php echo $row['class']; ?></div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-0.5">Đã hoàn thành</div>
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px]">
                                        <?php echo $row['completed']; ?>/<?php echo $row['total_tasks']; ?></div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?php echo $row['bar_color']; ?> transition-all duration-1000"
                                                style="width: <?php echo $row['percent']; ?>%"></div>
                                        </div>
                                        <span
                                            class="font-bold text-slate-700 dark:text-slate-300 text-[13px] w-8 text-right"><?php echo $row['percent']; ?>%</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-bold text-slate-800 dark:text-white text-[14px]"><?php echo $row['score']; ?></span><span
                                        class="text-slate-400 dark:text-slate-500 text-[12px]"> / 10</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight <?php echo $row['status_bg']; ?> <?php echo $row['status_text']; ?>">
                                        <?php echo str_replace(' ', '<br>', $row['status']); ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button
                                        onclick="showToast('info', 'Chi tiết học tập', 'Đang mở hồ sơ học tập của <?php echo $row['name']; ?>')"
                                        class="w-8 h-8 rounded-full border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 hover:text-[#1e3bb3] dark:hover:text-white hover:border-[#1e3bb3] dark:hover:border-slate-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition flex items-center justify-center mx-auto"
                                        title="Xem chi tiết">
                                        <span class="material-icons text-[18px]">visibility</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị 1-4 trong số 4 sinh viên</p>
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

        // 3. Logic Tìm kiếm & Lọc Real-time
        const searchInput = document.getElementById('searchInput');
        const classFilter = document.getElementById('classFilter');
        const allRows = Array.from(document.querySelectorAll('.progress-row'));
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');

        let rowsPerPage = 5;
        let currentPage = 1;
        let filteredRows = [...allRows];

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
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}-${displayEnd}</span> trong số <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> sinh viên`;
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

        function applyFilters() {
            const text = searchInput.value.toLowerCase();
            const selectedClass = classFilter.value;

            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.p-name').textContent.toLowerCase();
                const id = row.querySelector('.p-id').textContent.toLowerCase();
                const cClass = row.querySelector('.p-class').textContent.trim();
                const dept = row.querySelector('.p-dept').textContent.trim();

                const matchSearch = name.includes(text) || id.includes(text) || dept.toLowerCase().includes(text);
                const matchClass = (selectedClass === 'all' || cClass === selectedClass);

                return matchSearch && matchClass;
            });

            currentPage = 1;
            updatePagination();
        }

        // Bắt sự kiện Lọc qua thanh tìm kiếm & Dropdown
        searchInput?.addEventListener('input', applyFilters);
        classFilter?.addEventListener('change', applyFilters);

        // Chạy lần đầu
        updatePagination();
    });
</script>