<?php
$title = "Duyệt câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "approve_q"; 

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();


/* =========================
   XỬ LÝ DUYỆT / TỪ CHỐI
========================= */

if(isset($_GET['action']) && isset($_GET['id'])){

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == "approve"){
        $status = "da_duyet";
    }
    elseif($action == "reject"){
        $status = "tu_choi";
    }
    else{
        $status = null;
    }

    if($status){
        $sqlUpdate = "UPDATE cau_hoi 
                      SET trang_thai_duyet = :status 
                      WHERE ma_cau_hoi = :id";

        $stmtUpdate = $conn->prepare($sqlUpdate);

        $stmtUpdate->execute([
            'status' => $status,
            'id' => $id
        ]);

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}


/* =========================
   LẤY DANH SÁCH CHỜ DUYỆT
========================= */

$sql = "
SELECT 
    ch.ma_cau_hoi,
    ch.noi_dung,
    ch.muc_do,
    ch.ngay_tao,
    dm.ten_danh_muc,
    nd.ho_ten
FROM cau_hoi ch
LEFT JOIN danh_muc dm ON ch.ma_danh_muc = dm.ma_danh_muc
LEFT JOIN nguoi_dung nd ON ch.ma_giao_vien = nd.ma_nguoi_dung
WHERE ch.trang_thai_duyet = 'cho_duyet'
ORDER BY ch.ngay_tao DESC
";

$stmt = $conn->query($sql);

$pending_q = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    switch($row['muc_do']){
        case 'de': $level = "Nhận biết"; break;
        case 'trung_binh': $level = "Thông hiểu"; break;
        case 'kho': $level = "Vận dụng"; break;
        default: $level = "Khác";
    }

    $pending_q[] = [
        'id' => $row['ma_cau_hoi'],
        'code' => 'Q-'.$row['ma_cau_hoi'],
        'content' => mb_substr($row['noi_dung'],0,120,'UTF-8').'...',
        'subject' => $row['ten_danh_muc'],
        'author' => $row['ho_ten'],
        'time' => date("d/m/Y H:i", strtotime($row['ngay_tao'])),
        'level' => $level
    ];
}

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Duyệt câu hỏi</span>
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
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span class="font-semibold text-slate-800 dark:text-white">Giảng viên C</span> vừa gửi 5 câu hỏi mới chờ duyệt.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">schedule</span> 2 phút trước</span>
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
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Duyệt câu hỏi mới</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kiểm duyệt nội dung câu hỏi trước khi đưa vào Ngân hàng dữ liệu chính thức.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-5 flex items-center gap-4 shadow-sm transition-colors">
                <div class="w-12 h-12 rounded-full bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-2xl">pending_actions</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase">Đang chờ duyệt</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white">24</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-5 flex items-center gap-4 shadow-sm transition-colors">
                <div class="w-12 h-12 rounded-full bg-green-50 dark:bg-green-900/30 text-green-500 dark:text-green-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-2xl">task_alt</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase">Đã duyệt hôm nay</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white">156</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-5 flex items-center gap-4 shadow-sm transition-colors">
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-2xl">edit_note</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase">Yêu cầu sửa đổi</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white">8</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
            
            <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="handleApproveAll(this)" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 transition">
                        <span class="material-icons text-[18px] text-green-500 dark:text-green-400">done_all</span> Duyệt tất cả
                    </button>
                </div>
                <select class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    <option>Sắp xếp: Mới nhất</option>
                    <option>Sắp xếp: Cũ nhất</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4 w-1/3">Nội dung</th>
                            <th class="px-6 py-4">Môn học</th>
                            <th class="px-6 py-4">Người tải lên</th>
                            <th class="px-6 py-4 text-center">Thao tác duyệt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <?php foreach($pending_q as $q): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4 font-bold text-[#254ada] dark:text-[#4b6bfb] text-[13px]"><?php echo $q['code']; ?></td>
                            <td class="px-6 py-4">
                                <p class="text-slate-700 dark:text-slate-300 text-[13px] font-medium mb-1"><?php echo $q['content']; ?></p>
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] rounded font-bold uppercase inline-block"><?php echo $q['level']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium text-[13px]"><?php echo $q['subject']; ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 dark:text-white text-[13px]"><?php echo $q['author']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $q['time']; ?></div>
                            </td>
                          <td class="px-6 py-4 text-center">
<div class="flex items-center justify-center gap-2">

<a href="?action=approve&id=<?php echo $q['id']; ?>"
class="w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-500 hover:text-white transition flex items-center justify-center"
title="Phê duyệt">

<span class="material-icons text-[18px]">check</span>

</a>


<a href="?action=reject&id=<?php echo $q['id']; ?>"
onclick="return confirm('Bạn có chắc muốn từ chối câu hỏi này?')"
class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-500 hover:text-white transition flex items-center justify-center"
title="Từ chối">

<span class="material-icons text-[18px]">close</span>

</a>


<button onclick="showToast('info','Chi tiết','Mở chi tiết câu hỏi <?php echo $q['code']; ?>')"
class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-800 dark:hover:text-white transition flex items-center justify-center"
title="Xem chi tiết">

<span class="material-icons text-[18px]">visibility</span>

</button>

</div>
</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

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

// Xử lý nút Duyệt tất cả
function handleApproveAll(btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="material-icons text-[18px] animate-spin text-green-500">autorenew</span> Đang duyệt...';
    btn.disabled = true;

    setTimeout(() => {
        showToast('success', 'Hoàn tất', 'Đã duyệt tất cả các câu hỏi trên trang này.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 1500);
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