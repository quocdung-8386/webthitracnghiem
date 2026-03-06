<?php
$title = "Sao Lưu và Phục Hồi - Hệ Thống Thi Trực Tuyến";
$active_menu = "backup";

$backups = [
    ['name' => 'FULL_BACKUP_20231027_0100', 'format' => 'Định dạng: SQL GZipped', 'date' => '27/10/2023', 'time' => '01:00:05', 'size' => '450 MB', 'status' => 'AN TOÀN', 'status_badge' => 'bg-green-100 text-green-700 border border-green-200 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400'],
    ['name' => 'FULL_BACKUP_20231026_0100', 'format' => 'Định dạng: SQL GZipped', 'date' => '26/10/2023', 'time' => '01:00:12', 'size' => '448 MB', 'status' => 'AN TOÀN', 'status_badge' => 'bg-green-100 text-green-700 border border-green-200 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400'],
    ['name' => 'DAILY_DB_20231025', 'format' => 'Định dạng: SQL GZipped', 'date' => '25/10/2023', 'time' => '01:00:08', 'size' => '445 MB', 'status' => 'ĐÃ LƯU TRỮ', 'status_badge' => 'bg-slate-100 text-slate-600 border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">

    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Sao lưu và Phục hồi dữ liệu</span>
        </div>

        <div class="flex items-center gap-5">
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
                            class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu
                            đã đọc</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#"
                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                    class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa hoàn tất
                                sao lưu.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                    class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                        </a>
                    </div>
                    <a href="#"
                        class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">
                        Xem tất cả
                    </a>
                </div>
            </div>

            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div
        class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">

        <div
            class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-4 flex gap-3 mb-6 transition-colors">
            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">info</span>
            <div>
                <h4 class="font-bold text-[#254ada] dark:text-[#4b6bfb] text-sm mb-1">Trạng thái hệ thống an toàn</h4>
                <p class="text-sm text-blue-800 dark:text-blue-200">Bản sao lưu gần nhất được tạo vào lúc 01:00 AM hôm
                    nay. Bạn nên thực hiện sao lưu trước khi thực hiện các thay đổi lớn về cấu hình hoặc dữ liệu.</p>
            </div>
        </div>

        <div class="flex gap-4 mb-6">
            <button onclick="handleCreateBackup(this)"
                class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm shadow-sm transition">
                <span class="material-icons text-[20px]">cloud_upload</span> Tạo bản sao lưu mới
            </button>
            <button onclick="openModal('restoreModal')"
                class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg flex items-center gap-2 font-medium text-sm shadow-sm transition">
                <span class="material-icons text-[20px]">upload_file</span> Phục hồi từ file
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div
                class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
                <div class="p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white">Danh sách các bản sao lưu</h3>
                    <div
                        class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 cursor-pointer hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition">
                        Sắp xếp: Mới nhất <span class="material-icons text-[18px]">filter_list</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-4">Tên bản sao lưu</th>
                                <th class="px-5 py-4">Ngày tạo</th>
                                <th class="px-5 py-4 text-center">Dung lượng</th>
                                <th class="px-5 py-4 text-center">Trạng thái</th>
                                <th class="px-5 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach ($backups as $bk): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-5 py-4 flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-300 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-600">
                                            <span class="material-icons text-[20px]">sd_storage</span></div>
                                        <div>
                                            <div class="font-semibold text-slate-800 dark:text-white">
                                                <?php echo $bk['name']; ?></div>
                                            <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                                <?php echo $bk['format']; ?></div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                        <div><?php echo $bk['date']; ?></div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                            <?php echo $bk['time']; ?></div>
                                    </td>
                                    <td class="px-5 py-4 text-center text-slate-600 dark:text-slate-300 font-medium">
                                        <?php echo $bk['size']; ?></td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="px-2.5 py-1 <?php echo $bk['status_badge']; ?> text-[10px] font-bold rounded-full uppercase inline-block"><?php echo $bk['status']; ?></span>
                                    </td>
                                    <td class="px-5 py-4 text-right text-slate-400 dark:text-slate-500 space-x-1">
                                        <button
                                            onclick="showToast('success', 'Đang tải xuống', 'File sao lưu <?php echo $bk['name']; ?> đang được tải về máy.')"
                                            class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"
                                            title="Tải xuống"><span
                                                class="material-icons text-[18px]">download</span></button>
                                        <button onclick="confirmRestore('<?php echo $bk['name']; ?>')"
                                            class="hover:text-orange-500 dark:hover:text-orange-400 p-1.5 transition rounded-md hover:bg-orange-50 dark:hover:bg-slate-700"
                                            title="Phục hồi lại bản này"><span
                                                class="material-icons text-[18px]">restore</span></button>
                                        <button
                                            onclick="showToast('error', 'Đã xóa', 'Bản sao lưu đã bị xóa khỏi hệ thống')"
                                            class="hover:text-red-600 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700"
                                            title="Xóa vĩnh viễn"><span
                                                class="material-icons text-[18px]">delete</span></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-colors">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">schedule</span> Lịch sao lưu tự
                        động
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div
                            class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400">Sao lưu hàng ngày</span>
                            <span class="text-slate-800 dark:text-white font-medium">01:00 AM</span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400">Sao lưu hàng tuần</span>
                            <span class="text-slate-800 dark:text-white font-medium">CN, 03:00 AM</span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-slate-600 dark:text-slate-400">Tự động đẩy lên Google Drive</span>
                            <span class="text-green-600 font-bold text-[12px] uppercase">Đang bật</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 dark:text-slate-400">Giữ lại tối đa</span>
                            <span class="text-slate-800 dark:text-white font-medium">30 bản gần nhất</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 transition-colors">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">dns</span> Dung lượng lưu trữ
                    </h3>
                    <div class="mb-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-[#254ada] dark:bg-[#4b6bfb] h-2 rounded-full transition-all duration-1000"
                            style="width: 45%"></div>
                    </div>
                    <div class="flex justify-between text-sm mb-5">
                        <span class="text-slate-500 dark:text-slate-400">Đã sử dụng: 4.5 GB / 10 GB</span>
                        <span class="font-bold text-[#254ada] dark:text-[#4b6bfb]">45%</span>
                    </div>
                    <div
                        class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/50 rounded-lg p-3 text-xs text-orange-800 dark:text-orange-200 mb-4 leading-relaxed">
                        <span class="font-bold text-orange-600 dark:text-orange-400">Gợi ý:</span> Xóa các bản sao lưu
                        cũ hơn 30 ngày để giải phóng không gian lưu trữ nếu cần thiết.
                    </div>
                    <button onclick="handleCleanStorage(this)"
                        class="w-full py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-600 dark:text-slate-300 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center justify-center gap-2">
                        <span class="material-icons text-[18px]">cleaning_services</span> Dọn dẹp bộ nhớ
                    </button>
                </div>

            </div>
        </div>
    </div>
</main>

<div id="restoreModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-orange-500">restore_page</span> Phục hồi dữ liệu
            </h3>
            <button onclick="closeModal('restoreModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form id="formRestore" onsubmit="event.preventDefault(); submitRestore();" class="p-6">

            <div
                class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:bg-slate-50 dark:hover:bg-slate-700/50 transition cursor-pointer mb-5">
                <span class="material-icons text-[40px] text-slate-400 mb-2">upload_file</span>
                <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Kéo thả file SQL/ZIP vào đây hoặc
                    <span class="text-[#254ada] dark:text-[#4b6bfb] hover:underline">Chọn file</span></p>
                <p class="text-[11px] text-slate-400 mt-2">Dung lượng tải lên tối đa: 500MB</p>
            </div>

            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg p-4 flex gap-3 mb-2">
                <span class="material-icons text-red-500 mt-0.5 text-[20px]">warning</span>
                <p class="text-[12px] text-red-800 dark:text-red-200 leading-relaxed font-medium">
                    Cảnh báo: Việc phục hồi sẽ ghi đè lên toàn bộ dữ liệu hiện tại của hệ thống. Bạn chắc chắn muốn tiếp
                    tục chứ?
                </p>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('restoreModal')"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                    bỏ</button>
                <button type="submit"
                    class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <span class="material-icons text-[18px]">warning</span> Tiến hành phục hồi
                </button>
            </div>
        </form>
    </div>
</div>

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
       CÁC HÀM XỬ LÝ SỰ KIỆN SAO LƯU
       ================================================================= */
    function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }

    // Giả lập tạo bản sao lưu mới
    function handleCreateBackup(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[20px]">autorenew</span> Đang sao lưu...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            showToast('success', 'Hoàn tất sao lưu', 'Đã tạo thành công bản sao lưu mới nhất.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
        }, 2000);
    }

    // Xử lý nút Phục hồi (Từ Bảng)
    function confirmRestore(fileName) {
        // Trong thực tế sẽ dùng Modal Confirm thay vì Alert của trình duyệt
        if (confirm(`Bạn có chắc chắn muốn phục hồi hệ thống về bản ghi [ ${fileName} ] ?\n\nCẢNH BÁO: Dữ liệu hiện tại sẽ bị mất và ghi đè hoàn toàn!`)) {
            showToast('info', 'Đang phục hồi', 'Hệ thống đang tiến hành nạp lại cơ sở dữ liệu. Vui lòng không đóng trình duyệt.');
        }
    }

    // Xử lý Upload file phục hồi (Từ Modal)
    function submitRestore() {
        closeModal('restoreModal');
        showToast('info', 'Đang xử lý', 'Đang tải file lên và tiến hành phục hồi hệ thống...');
    }

    // Xử lý Dọn dẹp bộ nhớ
    function handleCleanStorage(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang dọn dẹp...';

        setTimeout(() => {
            showToast('success', 'Đã dọn dẹp', 'Đã xóa các bản sao lưu cũ. Giải phóng 2.1 GB dung lượng.');
            btn.innerHTML = originalText;
        }, 1500);
    }

    /* =================================================================
       KHỞI TẠO DOM (Chế độ Tối & Dropdown Chuông)
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
    });
</script>