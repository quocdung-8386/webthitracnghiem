<?php
$title = "Quản lý phiên bản - Hệ Thống Thi Trực Tuyến";
$active_menu = "version_q"; // Sáng menu ở sidebar

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

$sql = "
SELECT 
    ch.ma_cau_hoi AS code,
    LEFT(pv.noi_dung,120) AS content,
    pv.version,
    nd.ho_ten AS author,
    pv.thoi_gian_cap_nhat AS time,
    pv.trang_thai
FROM phien_ban_cau_hoi pv
JOIN cau_hoi ch ON pv.ma_cau_hoi = ch.ma_cau_hoi
JOIN nguoi_dung nd ON pv.ma_nguoi_cap_nhat = nd.ma_nguoi_dung
ORDER BY pv.thoi_gian_cap_nhat DESC
";

$result = $conn->query($sql);

$versions = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

    if ($row['trang_thai'] == 'current') {
        $status = "Bản hiện tại";
        $bg = "bg-green-100";
        $text = "text-green-700";
    } else {
        $status = "Bản cũ";
        $bg = "bg-slate-100";
        $text = "text-slate-600";
    }

    $versions[] = [
        'code' => $row['code'],
        'content' => $row['content'],
        'version' => $row['version'],
        'author' => $row['author'],
        'time' => date("H:i - d/m/Y", strtotime($row['time'])),
        'status' => $status,
        'status_bg' => $bg,
        'status_text' => $text
    ];
}

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản lý phiên bản</span>
        </div>
        
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm mã câu hỏi..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

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
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Câu hỏi <span class="font-semibold text-slate-800 dark:text-white">MATH-101-001</span> vừa được cập nhật lên phiên bản v2.1.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                        </a>
                    </div>
                    <a href="#" class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">Xem tất cả</a>
                </div>
            </div>

            <button id="darkModeToggle" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">
        
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Lịch sử & Quản lý phiên bản</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Theo dõi các thay đổi nội dung câu hỏi và khôi phục dữ liệu khi cần thiết.</p>
            </div>
            <button onclick="openModal('historyModal')" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                <span class="material-icons text-[20px]">manage_history</span> Nhật ký chỉnh sửa
            </button>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4">Nội dung tóm tắt</th>
                            <th class="px-6 py-4 text-center">Phiên bản</th>
                            <th class="px-6 py-4">Người cập nhật</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach($versions as $v): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 font-bold text-[#254ada] dark:text-[#4b6bfb]"><?php echo $v['code']; ?></td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 truncate max-w-xs" title="<?php echo $v['content']; ?>"><?php echo $v['content']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono text-[11px] font-bold rounded"><?php echo $v['version']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 dark:text-white"><?php echo $v['author']; ?></div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5"><?php echo $v['time']; ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 <?php echo $v['status_bg']; ?> dark:bg-opacity-20 <?php echo $v['status_text']; ?> text-[11px] font-bold rounded-full inline-block text-center"><?php echo $v['status']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                                <button onclick="showToast('info', 'Lịch sử', 'Đang tải chi tiết lịch sử cho mã <?php echo $v['code']; ?>...')" class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700" title="Xem lịch sử chi tiết"><span class="material-icons text-[18px]">history</span></button>
                                <?php if($v['status'] == 'Bản cũ'): ?>
                                <button onclick="handleRestore(this, '<?php echo $v['code']; ?>')" class="hover:text-orange-500 dark:hover:text-orange-400 p-1.5 transition rounded-md hover:bg-orange-50 dark:hover:bg-slate-700" title="Khôi phục bản này"><span class="material-icons text-[18px]">restore</span></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p>Hiển thị 1 - 3 của 45 câu hỏi có lịch sử chỉnh sửa</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 rounded-md text-slate-300 dark:text-slate-500 cursor-not-allowed"><span class="material-icons text-[18px]">chevron_left</span></button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md text-slate-600 dark:text-slate-300 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="historyModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">
        
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">manage_history</span> Nhật ký chỉnh sửa câu hỏi
            </h3>
            <button type="button" onclick="closeModal('historyModal')" class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span class="material-icons">close</span></button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
            
            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700">
                <div class="absolute w-3 h-3 bg-[#254ada] dark:bg-[#4b6bfb] rounded-full -left-[7px] top-1.5 ring-4 ring-white dark:ring-slate-800"></div>
                <p class="text-xs font-bold text-[#254ada] dark:text-[#4b6bfb] mb-1.5">Hôm nay, 10:30</p>
                <div class="bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-800/50 transition">
                    <p class="text-sm text-slate-800 dark:text-white leading-relaxed">
                        <span class="font-semibold">Trần Thị Hoa</span> đã cập nhật câu hỏi <span class="font-bold text-[#254ada] dark:text-[#4b6bfb]">MATH-101-001</span> lên <span class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-[11px] rounded font-mono">v2.1</span>
                    </p>
                    <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-2 italic border-l-2 border-slate-300 dark:border-slate-600 pl-2">"Sửa lại công thức nghiệm ở đáp án B để tránh gây nhầm lẫn cho học sinh."</p>
                </div>
            </div>

            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700">
                <div class="absolute w-3 h-3 bg-slate-300 dark:bg-slate-600 rounded-full -left-[7px] top-1.5 ring-4 ring-white dark:ring-slate-800"></div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">Hôm qua, 15:45</p>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition">
                    <p class="text-sm text-slate-800 dark:text-white leading-relaxed">
                        <span class="font-semibold">Nguyễn Văn An</span> đã cập nhật câu hỏi <span class="font-bold text-[#254ada] dark:text-[#4b6bfb]">PHYS-12-254</span> lên <span class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-[11px] rounded font-mono">v1.4</span>
                    </p>
                    <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-2 italic border-l-2 border-slate-300 dark:border-slate-600 pl-2">"Bổ sung thêm hình ảnh minh họa cho con lắc lò xo treo thẳng đứng."</p>
                </div>
            </div>

            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700">
                <div class="absolute w-3 h-3 bg-slate-300 dark:bg-slate-600 rounded-full -left-[7px] top-1.5 ring-4 ring-white dark:ring-slate-800"></div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">20/10/2023, 09:15</p>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition">
                    <p class="text-sm text-slate-800 dark:text-white leading-relaxed">
                        <span class="font-semibold">Lê Hữu Trí</span> đã tạo mới câu hỏi <span class="font-bold text-[#254ada] dark:text-[#4b6bfb]">CHEM-11-042</span> bản <span class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-[11px] rounded font-mono">v1.0</span>
                    </p>
                </div>
            </div>
            
            <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700 border-transparent">
                <div class="absolute w-3 h-3 bg-slate-300 dark:bg-slate-600 rounded-full -left-[7px] top-1.5 ring-4 ring-white dark:ring-slate-800"></div>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5">18/10/2023, 14:00</p>
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition">
                    <p class="text-sm text-slate-800 dark:text-white leading-relaxed">
                        <span class="font-semibold">Trần Thị Hoa</span> đã khôi phục câu hỏi <span class="font-bold text-[#254ada] dark:text-[#4b6bfb]">MATH-101-001</span> về bản <span class="px-1.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-[11px] rounded font-mono">v1.0</span>
                    </p>
                </div>
            </div>

        </div>
        
        <div class="p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center shrink-0">
            <a href="#" class="text-[13px] text-[#254ada] dark:text-[#4b6bfb] font-medium hover:underline flex items-center gap-1 transition">
                <span class="material-icons text-[16px]">open_in_new</span> Truy cập trung tâm nhật ký
            </a>
            <button type="button" onclick="closeModal('historyModal')" class="px-5 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition shadow-sm">Đóng</button>
        </div>
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
   HÀM MỞ / ĐÓNG MODAL
   ================================================================= */
function openModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.remove('hidden'); 
}

function closeModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.add('hidden'); 
}

/* =================================================================
   HÀM HIỂN THỊ THÔNG BÁO (TOAST)
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

// Xử lý nút khôi phục phiên bản
function handleRestore(btn, code) {
    if(confirm(`Bạn có chắc chắn muốn khôi phục câu hỏi [${code}] về phiên bản này không?\nBản hiện tại sẽ bị ghi đè!`)) {
        showToast('success', 'Khôi phục thành công', `Đã khôi phục câu hỏi ${code} về phiên bản được chọn.`);
    }
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
});
</script>