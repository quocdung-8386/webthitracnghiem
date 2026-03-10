<?php

$title = "Tạo đề thi mới - Hệ Thống Thi Trực Tuyến";
$active_menu = "create_exam";

require_once __DIR__ . '/../../app/config/Database.php';

session_start();

$conn = Database::getConnection();

$success = "";
$error = "";

/* ================================
   XỬ LÝ TẠO ĐỀ THI
================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_exam'])) {

    $tieu_de   = trim($_POST['tieu_de'] ?? '');
    $thoi_gian = intval($_POST['thoi_gian'] ?? 0);

    $topic_ids = $_POST['topic_id'] ?? [];
    $easy      = $_POST['easy'] ?? [];
    $medium    = $_POST['medium'] ?? [];
    $hard      = $_POST['hard'] ?? [];

    $ma_giao_vien = $_SESSION['user_id'] ?? 1;

    if ($tieu_de === "" || $thoi_gian <= 0) {
        $error = "Vui lòng nhập đầy đủ thông tin đề thi.";
    } else {

        try {

            $conn->beginTransaction();

            /* ================================
               1. LƯU ĐỀ THI
            ================================ */

            $sql = "
                INSERT INTO de_thi (ma_giao_vien, tieu_de, thoi_gian_lam)
                VALUES (:gv, :title, :time)
            ";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':gv'    => $ma_giao_vien,
                ':title' => $tieu_de,
                ':time'  => $thoi_gian
            ]);

            $ma_de_thi = $conn->lastInsertId();

            /* ================================
               2. PREPARE INSERT CHI TIẾT ĐỀ
            ================================ */

            $sqlInsert = "
                INSERT INTO chi_tiet_de_thi (ma_de_thi, ma_cau_hoi, diem)
                VALUES (:de, :cau, 1)
            ";

            $stmtInsert = $conn->prepare($sqlInsert);

            /* ================================
               3. DUYỆT MA TRẬN CÂU HỎI
            ================================ */

            for ($i = 0; $i < count($topic_ids); $i++) {

                $topic = $topic_ids[$i];

                $levels = [
                    'de'          => intval($easy[$i] ?? 0),
                    'trung_binh'  => intval($medium[$i] ?? 0),
                    'kho'         => intval($hard[$i] ?? 0)
                ];

                foreach ($levels as $level => $limit) {

                    if ($limit <= 0) continue;

                    $sqlQ = "
                        SELECT ma_cau_hoi
                        FROM cau_hoi
                        WHERE ma_danh_muc = :topic
                        AND muc_do = :level
                        AND trang_thai_duyet = 'da_duyet'
                        ORDER BY RAND()
                        LIMIT $limit
                    ";

                    $stmtQ = $conn->prepare($sqlQ);

                    $stmtQ->execute([
                        ':topic' => $topic,
                        ':level' => $level
                    ]);

                    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($questions as $q) {

                        $stmtInsert->execute([
                            ':de'  => $ma_de_thi,
                            ':cau' => $q['ma_cau_hoi']
                        ]);
                    }
                }
            }

            $conn->commit();

            $success = "Tạo đề thi thành công!";

        } catch (Exception $e) {

            $conn->rollBack();
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}


/* ================================
   LẤY DANH SÁCH CHỦ ĐỀ
================================ */
/* lấy danh sách chủ đề cho ma trận */
$sqlTopic = "
SELECT 
    dm.ma_danh_muc,
    dm.ten_danh_muc,
    COUNT(ch.ma_cau_hoi) AS tong_cau
FROM danh_muc dm
LEFT JOIN cau_hoi ch 
    ON dm.ma_danh_muc = ch.ma_danh_muc
    AND ch.trang_thai_duyet='da_duyet'
GROUP BY dm.ma_danh_muc
ORDER BY dm.ten_danh_muc
";

$stmtTopic = $conn->query($sqlTopic);

$topics = $stmtTopic->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   LOAD UI
================================ */

include 'components/header.php';
include 'components/sidebar.php';

?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
    class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 flex items-center justify-between z-10 shrink-0 transition-colors">

    <!-- Breadcrumb -->
    <div class="text-sm text-slate-500 dark:text-slate-400">
        Kỳ thi & Đề thi 
        <span class="mx-2">›</span> 
        <span class="text-slate-800 dark:text-white font-medium">
            Tạo đề thi mới
        </span>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-5">

        <!-- Notification -->
        <div class="relative">
            <button id="notifButton" type="button"
                class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons">notifications</span>

                <!-- Dot -->
                <span
                    class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800">
                </span>
            </button>

            <!-- Dropdown -->
            <div id="notifDropdown"
                class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">

                <div
                    class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <span class="font-bold text-sm text-slate-800 dark:text-white">
                        Thông báo mới
                    </span>

                    <a href="#"
                        class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">
                        Đánh dấu đã đọc
                    </a>
                </div>

                <div class="max-h-[300px] overflow-y-auto custom-scrollbar">

                    <a href="#"
                        class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">

                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">
                            <span class="font-semibold text-slate-800 dark:text-white">
                                Hệ thống
                            </span>
                            vừa cập nhật lại ngân hàng câu hỏi môn Toán học.
                        </p>

                        <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                            <span class="material-icons text-[12px]">schedule</span>
                            15 phút trước
                        </span>

                    </a>

                </div>

                <a href="#"
                    class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">
                    Xem tất cả
                </a>

            </div>
        </div>

        <!-- Dark mode -->
        <button id="darkModeToggle"
            class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
            <span class="material-icons" id="darkModeIcon">dark_mode</span>
        </button>

        <!-- Save button -->
        <button type="submit" name="save_exam"
            class="px-5 py-2 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg flex items-center gap-2 hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-sm font-medium shadow-sm transition ml-2">
            <span class="material-icons text-[18px]">save</span>
            Lưu đề thi
        </button>

    </div>

</header>
    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
    <form method="POST">
<div class="max-w-5xl mx-auto space-y-6">

<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">

<h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6">
<span class="material-icons text-slate-400 dark:text-slate-500 text-[20px]">info</span>
Thông tin cơ bản
</h3>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-5">

<!-- TÊN ĐỀ THI -->
<div class="md:col-span-2">

<label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
Tên đề thi <span class="text-red-500">*</span>
</label>

<input 
type="text"
name="tieu_de"
required
placeholder="Nhập tên đề thi..."
class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#254ada] focus:border-[#254ada] transition">

</div>

<!-- DANH MỤC -->
<div class="md:col-span-1">

<label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
Danh mục <span class="text-red-500">*</span>
</label>

<select 
name="ma_danh_muc"
required
class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#254ada] focus:border-[#254ada] transition">

<option value="">-- Chọn danh mục --</option>

<?php foreach ($danhmucs as $dm): ?>

<option value="<?= $dm['ma_danh_muc'] ?>">
<?= htmlspecialchars($dm['ten_danh_muc']) ?>
</option>

<?php endforeach; ?>

</select>

</div>

<!-- THỜI GIAN -->
<div class="md:col-span-1">

<label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
Thời gian (phút) <span class="text-red-500">*</span>
</label>

<input 
type="number"
name="thoi_gian"
required
min="1"
placeholder="VD: 60"
class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#254ada] focus:border-[#254ada] transition">

</div>

</div>

<!-- SỐ CÂU -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-5">

<div class="md:col-span-1">

<label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
Số lượng câu hỏi <span class="text-red-500">*</span>
</label>

<input 
type="number"
id="targetTotalQuestions"
name="so_cau"
required
min="1"
placeholder="VD: 40"
class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#254ada] focus:border-[#254ada] transition font-bold text-[#254ada] dark:text-[#4b6bfb]">

</div>

</div>

<!-- MÔ TẢ -->
<div>

<label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
Mô tả đề thi
</label>

<textarea 
name="mo_ta"
rows="3"
placeholder="Ghi chú thêm về đề thi..."
class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#254ada] focus:border-[#254ada] transition text-slate-600 dark:text-slate-300 resize-none"></textarea>

</div>

</div>
</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    class="border-2 border-[#254ada] dark:border-[#4b6bfb] bg-blue-50/30 dark:bg-blue-900/20 rounded-xl p-5 flex items-start gap-4 cursor-pointer shadow-sm relative overflow-hidden transition-colors">
                    <div
                        class="w-12 h-12 bg-blue-100 dark:bg-blue-800/50 text-[#254ada] dark:text-[#4b6bfb] rounded-lg flex items-center justify-center shrink-0 transition-colors">
                        <span class="material-icons text-[24px]">grid_view</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#254ada] dark:text-[#4b6bfb] mb-1">Tạo tự động từ ma trận</h4>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">Hệ thống tự động chọn
                            ngẫu nhiên các câu hỏi dựa theo tỉ lệ độ khó và chủ đề đã thiết lập.</p>
                    </div>
                </div>

                <div
                    class="border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl p-5 flex items-start gap-4 cursor-pointer hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                    <div
                        class="w-12 h-12 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg flex items-center justify-center shrink-0 transition-colors">
                        <span class="material-icons text-[24px]">touch_app</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-1">Chọn câu hỏi thủ công</h4>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed">Tự tay duyệt và chọn
                            từng câu hỏi từ danh sách ngân hàng câu hỏi để đưa vào đề thi.</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-icons text-slate-600 dark:text-slate-400 text-[20px]">settings</span> Cấu
                        hình ma trận đề thi
                    </h3>
                    <div class="text-[13px] text-slate-500 dark:text-slate-400">
                        Tổng điểm: <span class="font-bold text-slate-800 dark:text-white">10.0</span>
                        <span class="mx-3 text-slate-300 dark:text-slate-600">|</span>
                        Đã cấu hình: <span id="currentTotalSpan" class="font-bold text-[#254ada] dark:text-[#4b6bfb]">25
                            / 40 câu</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="matrixTable">
                        <thead
                            class="text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="pb-4 pt-2 px-2">Cấp độ / Chủ đề</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24 text-green-600 dark:text-green-500">Dễ</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24 text-orange-500 dark:text-orange-400">Trung
                                    bình</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24 text-red-500 dark:text-red-400">Khó</th>
                                <th class="pb-4 pt-2 px-2 text-center w-32 text-[#254ada] dark:text-[#4b6bfb]">Tổng số
                                    câu</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">

<?php foreach($topics as $t): ?>

<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition">

<td class="py-4 px-2">

<div class="font-bold text-slate-700 dark:text-slate-300">
<?php echo $t['ten_danh_muc']; ?>
</div>

<div class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">
Kho lưu trữ:
<span class="font-semibold text-slate-500 dark:text-slate-400">
<?php echo $t['tong_cau']; ?>
</span> câu
</div>

<input type="hidden" name="topic_id[]" value="<?php echo $t['ma_danh_muc']; ?>">

</td>

<td class="py-4 px-2 text-center">

<input type="number" name="easy[]" value="0" min="0"
class="matrix-input w-16 h-10 text-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm">

</td>

<td class="py-4 px-2 text-center">

<input type="number" name="medium[]" value="0" min="0"
class="matrix-input w-16 h-10 text-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm">

</td>

<td class="py-4 px-2 text-center">

<input type="number" name="hard[]" value="0" min="0"
class="matrix-input w-16 h-10 text-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm">

</td>

<td class="py-4 px-2 text-center font-bold text-slate-800 dark:text-white row-total">
0
</td>

<td class="py-4 px-2 text-center">
-
</td>

</tr>

<?php endforeach; ?>
                    </table>
                </div>

                <div class="mt-6 flex justify-center">
                    <button onclick="showToast('info', 'Thêm chủ đề', 'Mở popup chọn chủ đề từ kho dữ liệu...')"
                        class="flex items-center gap-1.5 text-[#254ada] dark:text-[#4b6bfb] font-medium hover:text-[#1e3bb3] dark:hover:text-[#254ada] transition px-4 py-2 bg-blue-50/50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg">
                        <span class="material-icons text-[20px]">add</span> Thêm chương/chủ đề từ ngân hàng
                    </button>
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
</div>
</form>
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

    // Hàm Xử lý Lưu Đề Thi
    function handleSaveExam(btn) {
        const targetTotal = parseInt(document.getElementById('targetTotalQuestions').value) || 0;
        const currentTotal = parseInt(document.getElementById('grandTotal').textContent) || 0;

        if (currentTotal !== targetTotal) {
            showToast('warning', 'Chưa khớp dữ liệu', `Tổng số câu bạn cấu hình (${currentTotal}) đang lệch so với mục tiêu đề thi (${targetTotal}). Vui lòng kiểm tra lại!`);
            return;
        }

        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xử lý...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('success', 'Tạo đề thành công', 'Đề thi đã được lưu và đưa vào hệ thống.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 1500);
    }

    /* =================================================================
       HÀM TÍNH TOÁN MA TRẬN ĐỀ THI REAL-TIME
       ================================================================= */
    function calculateMatrix() {
        const table = document.getElementById('matrixTable');
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr:not(:last-child)'); // Lấy tất cả dòng trừ dòng Tổng
        let totalEasy = 0, totalMedium = 0, totalHard = 0, grandTotal = 0;

        rows.forEach(row => {
            const inputs = row.querySelectorAll('.matrix-input');
            if (inputs.length === 3) {
                const easy = parseInt(inputs[0].value) || 0;
                const medium = parseInt(inputs[1].value) || 0;
                const hard = parseInt(inputs[2].value) || 0;

                const rowSum = easy + medium + hard;
                row.querySelector('.row-total').textContent = rowSum; // Cập nhật tổng theo hàng ngang

                totalEasy += easy;
                totalMedium += medium;
                totalHard += hard;
                grandTotal += rowSum;
            }
        });

        // Cập nhật dòng Tổng cộng dưới cùng
        document.getElementById('colEasyTotal').textContent = totalEasy;
        document.getElementById('colMediumTotal').textContent = totalMedium;
        document.getElementById('colHardTotal').textContent = totalHard;
        document.getElementById('grandTotal').textContent = grandTotal;

        // Cập nhật Span trên góc Header
        const targetTotal = document.getElementById('targetTotalQuestions').value || 0;
        const spanInfo = document.getElementById('currentTotalSpan');
        spanInfo.textContent = `${grandTotal} / ${targetTotal} câu`;

        // Đổi màu cảnh báo nếu số câu bị lệch
        if (grandTotal > targetTotal) {
            spanInfo.classList.add('text-red-500');
            spanInfo.classList.remove('text-[#254ada]', 'dark:text-[#4b6bfb]', 'text-green-500');
        } else if (grandTotal == targetTotal) {
            spanInfo.classList.add('text-green-500');
            spanInfo.classList.remove('text-[#254ada]', 'dark:text-[#4b6bfb]', 'text-red-500');
        } else {
            spanInfo.classList.add('text-[#254ada]', 'dark:text-[#4b6bfb]');
            spanInfo.classList.remove('text-red-500', 'text-green-500');
        }
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

        // 3. Gắn sự kiện lắng nghe việc nhập số trong Ma trận
        const matrixInputs = document.querySelectorAll('.matrix-input');
        matrixInputs.forEach(input => {
            input.addEventListener('input', calculateMatrix);
        });

        const targetTotalInput = document.getElementById('targetTotalQuestions');
        if (targetTotalInput) {
            targetTotalInput.addEventListener('input', calculateMatrix);
        }

        // Tính toán lần đầu khi load trang
        calculateMatrix();
    });
</script>