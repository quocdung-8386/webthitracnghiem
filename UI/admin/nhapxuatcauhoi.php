<?php
$title = "Nhập/Xuất câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "import_q"; 
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

$sql = "
SELECT 
    c.ma_cau_hoi,
    c.noi_dung,
    c.muc_do,
    d.ten_danh_muc
FROM cau_hoi c
LEFT JOIN danh_muc d ON c.ma_danh_muc = d.ma_danh_muc
ORDER BY c.ma_cau_hoi DESC
LIMIT 50
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$preview_data = [];
$stt = 1;

foreach($questions as $q){

    // Xử lý màu mức độ
    $level_color = "";
    if($q['muc_do'] == 'de'){
        $level = "Dễ";
        $level_color = "bg-green-50 text-green-600 dark:bg-opacity-20 dark:text-green-400";
    }
    elseif($q['muc_do'] == 'trung_binh'){
        $level = "Trung bình";
        $level_color = "bg-blue-50 text-blue-600 dark:bg-opacity-20 dark:text-blue-400";
    }
    else{
        $level = "Khó";
        $level_color = "bg-orange-50 text-orange-600 dark:bg-opacity-20 dark:text-orange-400";
    }

    $preview_data[] = [
        'stt' => $stt++,
        'content' => $q['noi_dung'],
        'category' => $q['ten_danh_muc'] ?? 'Chưa phân loại',
        'level' => $level,
        'level_color' => $level_color,
        'status' => 'Sẵn sàng',
        'status_icon' => 'check_circle',
        'status_color' => 'text-green-500 dark:text-green-400'
    ];
}
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Nhập/Xuất câu hỏi</span>
        </div>
        
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm tính năng..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none w-56 transition focus:ring-1 focus:ring-[#254ada]">
            </div>

            <button class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg flex items-center gap-1 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <span class="material-icons text-[18px]">help_outline</span> Hướng dẫn
            </button>
            
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
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa phân tích xong file Excel.</p>
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
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Nhập/Xuất câu hỏi từ File</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý và đồng bộ dữ liệu ngân hàng câu hỏi thông qua các tệp tin Excel hoặc Word.</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6 transition-colors">
            
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-icons text-slate-700 dark:text-slate-400">upload_file</span> Nhập câu hỏi từ tập tin
                    </h3>
                    <div class="flex gap-3">
                        <button onclick="showToast('info', 'Đang tải...', 'Đang tải file Excel mẫu về máy.')" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 transition">
                            <span class="material-icons text-[18px]">download</span> Tải file Excel mẫu
                        </button>
                        <button onclick="showToast('info', 'Đang tải...', 'Đang tải file Word mẫu về máy.')" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 transition">
                            <span class="material-icons text-[18px]">download</span> Tải file Word mẫu
                        </button>
                    </div>
                </div>

                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-10 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-900/30 hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition mb-6">
                    <div class="w-16 h-16 bg-[#254ada]/10 text-[#254ada] dark:bg-[#4b6bfb]/20 dark:text-[#4b6bfb] rounded-full flex items-center justify-center mb-4 transition-colors">
                        <span class="material-icons text-3xl">cloud_upload</span>
                    </div>
                    <h4 class="font-bold text-slate-800 dark:text-white text-lg mb-1">Kéo và thả file tại đây</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Hỗ trợ định dạng .xlsx, .xls, .docx, .doc (Tối đa 20MB)</p>
                    <input type="file" id="fileUpload" class="hidden" accept=".xlsx, .xls, .docx, .doc">
                    <button onclick="document.getElementById('fileUpload').click()" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-medium rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition">Chọn file từ máy tính</button>
                </div>

                <div>
                    <div class="flex justify-between text-sm text-slate-600 dark:text-slate-400 font-medium mb-2">
                        <span>Đang xử lý file: NganHangCauHoi_Toan_L12.xlsx</span>
                        <span class="font-bold text-slate-800 dark:text-white">75%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="bg-[#254ada] dark:bg-[#4b6bfb] h-1.5 rounded-full transition-all duration-500" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-icons text-slate-700 dark:text-slate-400 text-[20px]">visibility</span> Xem trước và kiểm tra dữ liệu
                    </h3>
                    <div class="text-sm font-bold flex gap-4">
                        <span class="text-green-500 dark:text-green-400 flex items-center gap-1"><span class="material-icons text-[16px]">circle</span> 48 Hợp lệ</span>
                        <span class="text-red-500 dark:text-red-400 flex items-center gap-1"><span class="material-icons text-[16px]">circle</span> 2 Lỗi</span>
                    </div>
                </div>

                <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-4 w-16 text-center">STT</th>
                                <th class="px-5 py-4">Nội dung câu hỏi</th>
                                <th class="px-5 py-4">Phân loại</th>
                                <th class="px-5 py-4 text-center">Mức độ</th>
                                <th class="px-5 py-4 text-center">Trạng thái</th>
                                <th class="px-5 py-4 text-center w-20">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach($preview_data as $row): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                <td class="px-5 py-4 text-center font-medium text-slate-600 dark:text-slate-400"><?php echo $row['stt']; ?></td>
                                <td class="px-5 py-4 font-medium text-slate-800 dark:text-white"><?php echo $row['content']; ?></td>
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 italic text-[13px]"><?php echo $row['category']; ?></td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-md <?php echo $row['level_color']; ?> text-[11px] font-bold"><?php echo $row['level']; ?></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-[12px] <?php echo $row['status_color']; ?>">
                                        <span class="material-icons text-[18px]"><?php echo $row['status_icon']; ?></span> <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button class="text-slate-400 hover:text-red-500 transition" title="Chỉnh sửa dòng này"><span class="material-icons text-[18px]">edit</span></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button class="px-6 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-600 dark:text-slate-400 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition">Hủy bỏ</button>
                    <button class="px-8 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg font-bold cursor-not-allowed">Lưu dữ liệu</button> 
                </div>
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
        <button class="toast-close text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"><span class="material-icons text-[16px]">close</span></button>
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

    // 3. Giả lập thông báo khi chọn file
    const fileUpload = document.getElementById('fileUpload');
    if(fileUpload) {
        fileUpload.addEventListener('change', function(e) {
            if(this.files.length > 0) {
                const fileName = this.files[0].name;
                showToast('info', 'Đang xử lý file', `Hệ thống đang đọc dữ liệu từ file: ${fileName}`);
            }
        });
    }
});
</script>