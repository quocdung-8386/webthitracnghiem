<?php
require_once __DIR__ . '/../../app/config/Database.php';

$conn = Database::getConnection();

/* Hàm đếm số user theo vai trò */
function countUsersByRole($conn, $role_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM nguoi_dung WHERE ma_vai_tro = ?");
    $stmt->execute([$role_id]);
    return $stmt->fetchColumn();
}

// 1. Cấu hình thông tin trang
$title = "Quản Trị Phân Quyền - Hệ Thống Thi Trực Tuyến";
$active_menu = "roles";

// Mảng vai trò
$roles = [
    [
        'id' => 1,
        'name' => 'Quản trị viên (Admin)',
        'desc' => 'Toàn quyền truy cập hệ thống.',
        'users' => countUsersByRole($conn, 1),
        'badge_bg' => 'bg-blue-50 dark:bg-blue-900/30',
        'badge_text' => 'text-[#254ada] dark:text-[#4b6bfb]'
    ],
    [
        'id' => 2,
        'name' => 'Giảng viên',
        'desc' => 'Quản lý ngân hàng câu hỏi, tạo đề thi, xem điểm.',
        'users' => countUsersByRole($conn, 2),
        'badge_bg' => 'bg-purple-50 dark:bg-purple-900/30',
        'badge_text' => 'text-purple-600 dark:text-purple-400'
    ],
    [
        'id' => 3,
        'name' => 'Thí sinh',
        'desc' => 'Tham gia thi, xem kết quả cá nhân.',
        'users' => countUsersByRole($conn, 3),
        'badge_bg' => 'bg-slate-100 dark:bg-slate-700',
        'badge_text' => 'text-slate-600 dark:text-slate-300'
    ],
];

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản trị phân quyền</span>
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
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Giảng viên <span class="font-semibold text-slate-800 dark:text-white">Trần Thị Hoa</span> vừa tạo đề thi mới.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">schedule</span> 15 phút trước</span>
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

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Quản lý Vai trò & Phân quyền</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Thiết lập nhóm quyền và giới hạn truy cập cho các chức năng trong hệ thống.</p>
            </div>
            <button onclick="openModal('addRoleModal')" class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 text-sm font-medium shadow-sm transition">
                <span class="material-icons text-[20px]">person_add_alt_1</span> Thêm vai trò mới
            </button>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col mb-6 transition-colors">
            
            <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                <div class="relative w-full max-w-md">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm vai trò hoặc mô tả..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] transition">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5">Tên vai trò</th>
                            <th class="px-6 py-5">Mô tả quyền hạn</th>
                            <th class="px-6 py-5 text-center">Số thành viên</th>
                            <th class="px-6 py-5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        <?php foreach ($roles as $role): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group role-row">
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1.5 <?php echo $role['badge_bg']; ?> <?php echo $role['badge_text']; ?> rounded-full font-semibold text-[12px] inline-block border border-transparent dark:border-slate-600 r-name">
                                        <?php echo $role['name']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-[13px] r-desc">
                                    <?php echo $role['desc']; ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-center">
                                    <?php echo $role['users']; ?>
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                                    <button onclick="showToast('info', 'Cấu hình quyền', 'Đang mở bảng cấu hình quyền cho <?php echo $role['name']; ?>')" class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700" title="Cấu hình quyền">
                                        <span class="material-icons text-[18px]">security</span>
                                    </button>
                                    <button onclick="showToast('info', 'Sửa tên', 'Chức năng sửa tên vai trò')" class="hover:text-slate-700 dark:hover:text-white p-1.5 transition rounded-md hover:bg-slate-100 dark:hover:bg-slate-700" title="Sửa tên">
                                        <span class="material-icons text-[18px]">edit</span>
                                    </button>
                                    <button onclick="showToast('error', 'Cảnh báo', 'Không thể xóa vai trò hệ thống mặc định')" class="hover:text-red-600 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700" title="Xóa">
                                        <span class="material-icons text-[18px]">delete_outline</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <span class="font-medium text-slate-800 dark:text-white">0</span> - <span class="font-medium text-slate-800 dark:text-white">0</span> của <span class="font-medium text-slate-800 dark:text-white">0</span> vai trò</p>
                <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 p-5 rounded-xl transition-colors">
                <div class="flex items-center gap-2 text-[#254ada] dark:text-[#4b6bfb] font-bold mb-3">
                    <span class="material-icons text-[20px]">info</span> Về vai trò
                </div>
                <p class="text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed">Vai trò giúp nhóm các quyền hạn lại với nhau để dễ dàng quản lý quyền truy cập cho nhiều người dùng cùng lúc.</p>
            </div>

            <div class="bg-orange-50/50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/50 p-5 rounded-xl transition-colors">
                <div class="flex items-center gap-2 text-orange-600 dark:text-orange-400 font-bold mb-3">
                    <span class="material-icons text-[20px]">warning</span> Lưu ý bảo mật
                </div>
                <p class="text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed">Việc thay đổi quyền hạn của một vai trò sẽ áp dụng ngay lập tức cho tất cả người dùng thuộc vai trò đó.</p>
            </div>

            <div class="bg-emerald-50/50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 p-5 rounded-xl transition-colors">
                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold mb-3">
                    <span class="material-icons text-[20px]">bolt</span> Phân quyền nhanh
                </div>
                <p class="text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed">Sử dụng các mẫu (template) có sẵn để thiết lập nhanh vai trò cho các bộ phận mới trong tổ chức.</p>
            </div>
        </div>

    </div>
</main>

<div id="addRoleModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">person_add_alt_1</span> Thêm vai trò mới
            </h3>
            <button onclick="closeModal('addRoleModal')" class="text-slate-400 hover:text-red-500 transition focus:outline-none">
                <span class="material-icons">close</span>
            </button>
        </div>
        <form id="formAddRole" onsubmit="event.preventDefault(); submitAddRole();" class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-5">
            <div>
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tên vai trò <span class="text-red-500">*</span></label>
                <input type="text" placeholder="VD: Trợ giảng, Giám thị..." required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-1 focus:ring-[#254ada] focus:outline-none transition">
            </div>

            <div>
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mô tả quyền hạn</label>
                <textarea rows="3" placeholder="Mô tả ngắn gọn về nhiệm vụ của vai trò này..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-800 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-1 focus:ring-[#254ada] focus:outline-none resize-y transition"></textarea>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-lg p-4 flex gap-3 transition-colors">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[18px] shrink-0">info</span>
                <p class="text-[12px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    Sau khi tạo, bạn có thể thiết lập chi tiết từng quyền (đọc, thêm, sửa, xóa) cho vai trò này ở bảng danh sách bên ngoài.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('addRoleModal')" class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy bỏ</button>
                <button type="submit" class="px-6 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    Tạo vai trò
                </button>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
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
       HÀM GLOBAL (MODAL & TOAST)
       ================================================================= */
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    function submitAddRole() {
        closeModal('addRoleModal');
        showToast('success', 'Tạo vai trò thành công', 'Vai trò mới đã được thêm vào hệ thống.');
        document.getElementById('formAddRole').reset();
    }

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

        // 3. Chức năng Tìm kiếm & Phân trang thông minh
        const rowsPerPage = 2; // Test phân trang (Hiển thị 2 vai trò mỗi trang)
        let currentPage = 1;
        let filteredRows = [];

        const allRows = Array.from(document.querySelectorAll('.role-row'));
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

            // Ẩn tất cả và chỉ hiện những row thuộc trang hiện tại
            allRows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            // Cập nhật text hiển thị
            const displayStart = totalRows === 0 ? 0 : start + 1;
            const displayEnd = Math.min(end, totalRows);
            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}</span> - <span class="font-medium text-slate-800 dark:text-white">${displayEnd}</span> của <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> vai trò`;
            }

            // Vẽ nút phân trang thông minh (Có dấu ...)
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
                        btn.className = 'w-8 h-8 flex items-center justify-center bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded font-medium shadow-sm transition transform scale-105';
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

        // Bắt sự kiện tìm kiếm
        searchInput?.addEventListener('input', function (e) {
            const text = e.target.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.r-name').textContent.toLowerCase();
                const desc = row.querySelector('.r-desc').textContent.toLowerCase();
                return name.includes(text) || desc.includes(text);
            });
            currentPage = 1;
            updatePagination();
        });

        // Khởi chạy phân trang lần đầu
        filteredRows = [...allRows];
        updatePagination();
    });
</script>