<?php
$title = "Nhật Ký Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "logs";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* =========================
   LẤY FILTER
========================= */

$search = $_GET['search'] ?? '';
$user = $_GET['user'] ?? '';
$date = $_GET['date'] ?? '';

/* =========================
   QUERY DATABASE
========================= */

$sql = "SELECT * FROM nhat_ky_he_thong WHERE 1=1";
$params = [];

if($search != ''){
    $sql .= " AND noi_dung LIKE ?";
    $params[] = "%$search%";
}

if($user != ''){
    $sql .= " AND ten_nguoi_dung = ?";
    $params[] = $user;
}

if($date != ''){
    $sql .= " AND DATE(thoi_gian) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY thoi_gian DESC LIMIT 100";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   CHUYỂN DATA HIỂN THỊ
========================= */

$logs = [];

foreach ($data as $row) {

    $badge = "bg-slate-200 text-slate-700";

    if ($row['hanh_dong'] == "LOGIN") {
        $badge = "bg-green-100 text-green-700";
    } elseif ($row['hanh_dong'] == "CREATE") {
        $badge = "bg-blue-100 text-blue-700";
    } elseif ($row['hanh_dong'] == "UPDATE") {
        $badge = "bg-orange-100 text-orange-700";
    } elseif ($row['hanh_dong'] == "DELETE") {
        $badge = "bg-red-100 text-red-700";
    }

    $logs[] = [
        'time' => date("H:i:s", strtotime($row['thoi_gian'])),
        'date' => date("d/m/Y", strtotime($row['thoi_gian'])),
        'initial' => strtoupper(substr($row['ten_nguoi_dung'],0,2)),
        'name' => $row['ten_nguoi_dung'],
        'role' => $row['vai_tro'],
        'action' => $row['hanh_dong'],
        'badge' => $badge,
        'desc' => $row['noi_dung'],
        'ip' => $row['ip_address']
    ];
}

/* =========================
   EXPORT EXCEL
========================= */

if(isset($_GET['export'])){

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=logs.xls");

echo "Time\tUser\tRole\tAction\tDescription\tIP\n";

$stmt = $conn->query("SELECT * FROM nhat_ky_he_thong ORDER BY thoi_gian DESC");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

echo $row['thoi_gian']."\t".
$row['ten_nguoi_dung']."\t".
$row['vai_tro']."\t".
$row['hanh_dong']."\t".
$row['noi_dung']."\t".
$row['ip_address']."\n";

}

exit;
}

/* =========================
   LẤY DANH SÁCH USER FILTER
========================= */

$users = $conn->query("SELECT DISTINCT ten_nguoi_dung FROM nhat_ky_he_thong")
              ->fetchAll(PDO::FETCH_ASSOC);

include 'components/header.php';
include 'components/sidebar.php';
?>
=======
<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Nhật ký hệ thống</span>
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
        
        <div class="bg-white dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end transition-colors">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">TÌM KIẾM</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm theo nội dung..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">KHOẢNG THỜI GIAN</label>
                <select id="dateFilter" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    <option value="all">Tất cả thời gian</option>
                    <option value="today">Hôm nay</option>
                    <option value="week">Tuần này</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">NGƯỜI THỰC HIỆN</label>
                <select id="roleFilter" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    <option value="Tất cả vai trò">Tất cả vai trò</option>
                    <option value="Quản trị viên">Quản trị viên</option>
                    <option value="Giảng viên">Giảng viên</option>
                    <option value="System Bot">System Bot</option>
                </select>
            </div>
            <div>
                <button id="btnFilter" class="w-full py-2 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg flex items-center justify-center gap-2 font-medium text-sm shadow-sm hover:bg-blue-800 dark:hover:bg-[#254ada] transition">
                    <span class="material-icons text-[18px]">filter_alt</span> Áp dụng bộ lọc
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors flex flex-col">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-white">Chi tiết lịch sử thao tác</h3>
                <div class="flex gap-2">
                    <button onclick="showToast('info', 'Đang tải xuống', 'Hệ thống đang xuất file Excel. Vui lòng đợi...')" class="w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition" title="Xuất báo cáo"><span class="material-icons text-[20px]">download</span></button>
                    <button onclick="showToast('success', 'Đã làm mới', 'Dữ liệu nhật ký đã được cập nhật mới nhất.')" class="w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition" title="Tải lại dữ liệu"><span class="material-icons text-[20px]">refresh</span></button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Thời gian</th>
                            <th class="px-6 py-4">Người thực hiện</th>
                            <th class="px-6 py-4">Hành động</th>
                            <th class="px-6 py-4">Nội dung chi tiết</th>
                            <th class="px-6 py-4 text-right">Địa chỉ IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="logsTableBody">
                        <?php foreach($logs as $log): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition log-row">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white"><?php echo $log['time']; ?></div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5"><?php echo $log['date']; ?></div>
                            </td>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center font-bold text-xs shrink-0"><?php echo $log['initial']; ?></div>
                                <div>
                                    <div class="font-semibold text-slate-800 dark:text-white log-name"><?php echo $log['name']; ?></div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 log-role"><?php echo $log['role']; ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 <?php echo $log['badge']; ?> dark:bg-opacity-20 text-[10px] font-bold rounded uppercase inline-block"><?php echo $log['action']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 log-desc"><?php echo $log['desc']; ?></td>
                            <td class="px-6 py-4 text-right text-slate-500 dark:text-slate-400 font-mono text-xs"><?php echo $log['ip']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <span class="font-medium text-slate-800 dark:text-white">0</span> - <span class="font-medium text-slate-800 dark:text-white">0</span> của <span class="font-medium text-slate-800 dark:text-white">0</span> bản ghi</p>
                <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
            </div>
        </div>
    </div>
>>>>>>> 0e15295 (update admin CN)
</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"><span class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
/* =================================================================
   1. HÀM HIỂN THỊ THÔNG BÁO (TOAST)
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
   SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
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

    // 3. CHỨC NĂNG TÌM KIẾM, LỌC & PHÂN TRANG
    const rowsPerPage = 3; // Giới hạn 3 dòng 1 trang để test
    let currentPage = 1;
    let filteredRows = []; 
    
    const allRows = Array.from(document.querySelectorAll('.log-row'));
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const btnFilter = document.getElementById('btnFilter');

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
        if(paginationInfo) {
            paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}</span> - <span class="font-medium text-slate-800 dark:text-white">${displayEnd}</span> của <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> bản ghi`;
        }

        renderPaginationButtons(totalPages);
    }

    function renderPaginationButtons(totalPages) {
        if(!paginationControls) return;
        paginationControls.innerHTML = ''; 

        const prevBtn = document.createElement('button');
        prevBtn.className = `w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
        prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
        prevBtn.onclick = () => { if(currentPage > 1) { currentPage--; updatePagination(); } };
        paginationControls.appendChild(prevBtn);

        for(let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            if(i === currentPage) {
                pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-[#1e3bb3] dark:bg-[#254ada] text-white rounded-md font-medium shadow-sm';
            } else {
                pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-md font-medium text-slate-600 dark:text-slate-300 transition';
            }
            pageBtn.innerText = i;
            pageBtn.onclick = () => { currentPage = i; updatePagination(); };
            paginationControls.appendChild(pageBtn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = `w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
        nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
        nextBtn.onclick = () => { if(currentPage < totalPages) { currentPage++; updatePagination(); } };
        paginationControls.appendChild(nextBtn);
    }

    function applyFilters() {
        const text = searchInput.value.toLowerCase();
        const role = roleFilter.value;
        
        filteredRows = allRows.filter(row => {
            // Lấy nội dung chữ trong cột (Tên, Email, Nội dung mô tả...)
            const contentString = row.innerText.toLowerCase();
            const rowRole = row.querySelector('.log-role').textContent.trim();
            
            const matchesText = contentString.includes(text);
            const matchesRole = (role === 'Tất cả vai trò' || rowRole === role);
            
            return matchesText && matchesRole;
        });
        
        currentPage = 1; 
        updatePagination();
    }

    // Gắn sự kiện Lọc dữ liệu
    searchInput?.addEventListener('input', applyFilters);
    roleFilter?.addEventListener('change', applyFilters);
    if(btnFilter) {
        btnFilter.addEventListener('click', () => {
            applyFilters();
            showToast('success', 'Đã lọc dữ liệu', `Tìm thấy ${filteredRows.length} kết quả phù hợp.`);
        });
    }

    // Chạy lần đầu khi load trang
    filteredRows = [...allRows];
    updatePagination();
});
</script>