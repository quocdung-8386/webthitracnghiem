<?php
$title = "Ngân hàng câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "update_q"; // Sẽ làm sáng menu Cập nhật câu hỏi ở Sidebar
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

$sql = "
SELECT 
    ch.ma_cau_hoi,
    ch.noi_dung,
    ch.muc_do,
    ch.loai_cau_hoi,
    dm.ten_danh_muc,
    ch.ngay_tao
FROM cau_hoi ch
LEFT JOIN danh_muc dm ON ch.ma_danh_muc = dm.ma_danh_muc
ORDER BY ch.ma_cau_hoi DESC
";

$result = $conn->query($sql);

$questions = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

    // xử lý hiển thị mức độ
    $level = $row['muc_do'];
    $level_bg = '';
    $level_text = '';

    if ($level == 'de') {
        $level = 'Dễ';
        $level_bg = 'bg-green-50';
        $level_text = 'text-green-600';
    } elseif ($level == 'trung_binh') {
        $level = 'Trung bình';
        $level_bg = 'bg-blue-50';
        $level_text = 'text-blue-600';
    } else {
        $level = 'Khó';
        $level_bg = 'bg-red-50';
        $level_text = 'text-red-600';
    }

    $questions[] = [
        'code' => 'Q-' . $row['ma_cau_hoi'],
        'content' => $row['noi_dung'],
        'subject' => $row['ten_danh_muc'] ?? 'Chưa phân loại',
        'grade' => $row['loai_cau_hoi'],
        'level' => $level,
        'level_bg' => $level_bg,
        'level_text' => $level_text,
        'status' => 'Đã duyệt',
        'status_bg' => 'bg-green-100',
        'status_text' => 'text-green-700',
        'dot' => ''
    ];
}

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Cập nhật câu hỏi</span>
        </div>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" id="quickSearch" placeholder="Tìm kiếm câu hỏi..."
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:ring-1 focus:ring-[#254ada] focus:outline-none w-64 transition">
            </div>

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
                                    class="font-semibold text-slate-800 dark:text-white">Giảng viên A</span> vừa thêm 10
                                câu hỏi mới.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                    class="material-icons text-[12px]">schedule</span> 5 phút trước</span>
                        </a>
                    </div>
                    <a href="#"
                        class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">Xem
                        tất cả</a>
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

        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-3">
                <button onclick="openModal('addQuestionModal')"
                    class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg flex items-center gap-2 hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">add_circle</span> Thêm câu hỏi mới
                </button>
                <button
                    class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">dynamic_feed</span> Thao tác hàng loạt
                </button>
            </div>
            <div class="text-sm text-slate-600 dark:text-slate-400 flex gap-6">
                <span>Tổng cộng: <b class="text-slate-800 dark:text-white">1,248</b> câu hỏi</span>
                <span>Đã duyệt: <b class="text-green-600 dark:text-green-400">942</b></span>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">

            <div class="p-5 flex gap-4 border-b border-slate-100 dark:border-slate-700">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Môn
                        học</label>
                    <select
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                        <option>Tất cả môn học</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Khối
                        lớp</label>
                    <select
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                        <option>Tất cả các khối</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Mức độ
                        khó</label>
                    <select
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                        <option>Tất cả mức độ</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Trạng
                        thái</label>
                    <select
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                        <option>Tất cả trạng thái</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-white dark:bg-slate-800 text-[11px] text-slate-400 dark:text-slate-500 uppercase font-semibold border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 w-14 text-center">
                                <input type="checkbox" id="selectAllBtn"
                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                            </th>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4 w-[35%]">Nội dung câu hỏi</th>
                            <th class="px-6 py-4">Danh mục/Môn</th>
                            <th class="px-6 py-4 text-center">Mức độ</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="questionsTableBody">
                        <?php foreach ($questions as $q): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition question-row">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                                </td>
                                <td class="px-6 py-4 font-semibold text-[#254ada] dark:text-[#4b6bfb] text-[13px] q-code">
                                    <?php echo $q['code']; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-[13px] truncate max-w-xs q-content"
                                    title="<?php echo $q['content']; ?>"><?php echo $q['content']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-white text-[13px]">
                                        <?php echo $q['subject']; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        <?php echo $q['grade']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-md <?php echo $q['level_bg']; ?> dark:bg-opacity-20 <?php echo $q['level_text']; ?> text-[11px] font-semibold"><?php echo $q['level']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($q['dot'] != ''): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 text-[12px] font-semibold <?php echo $q['status_text']; ?>">
                                            <div class="w-1.5 h-1.5 rounded-full <?php echo $q['dot']; ?>"></div>
                                            <?php echo $q['status']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="px-3 py-1.5 rounded-full <?php echo $q['status_bg']; ?> dark:bg-opacity-20 <?php echo $q['status_text']; ?> text-[11px] font-bold inline-flex items-center text-center leading-tight max-w-[80px]"><?php echo $q['status']; ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <span class="font-medium text-slate-800 dark:text-white">0</span> -
                    <span class="font-medium text-slate-800 dark:text-white">0</span> trong tổng số <span
                        class="font-medium text-slate-800 dark:text-white">0</span> câu hỏi
                </p>
                <div id="paginationControls" class="flex items-center gap-1.5">
                </div>
            </div>
        </div>
    </div>
    <div id="addQuestionModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] max-h-[95vh] flex flex-col overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">

            <div class="flex justify-between items-start p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex gap-4 items-center">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-600">
                        <span class="material-icons text-[20px]">post_add</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white leading-tight">Thêm câu hỏi mới</h3>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-0.5">Ngân hàng câu hỏi</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('addQuestionModal')"
                    class="text-slate-400 hover:text-red-500 transition focus:outline-none">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <form id="formAddQuestionData" onsubmit="event.preventDefault(); submitAddQuestion();"
                class="flex flex-col flex-1 overflow-hidden">
                <div class="p-5 overflow-y-auto custom-scrollbar flex-1">

                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Danh
                                mục môn học</label>
                            <select required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                                <option value="toan_cc">Toán Cao Cấp A1</option>
                                <option value="tin_dc">Tin học đại cương</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mức
                                độ khó</label>
                            <select required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                                <option value="easy">Dễ (Easy)</option>
                                <option value="medium">Trung bình (Medium)</option>
                                <option value="hard">Khó (Hard)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nội
                            dung câu hỏi <span class="text-red-500">*</span></label>
                        <div
                            class="border border-slate-300 dark:border-slate-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-[#254ada] transition">
                            <div
                                class="bg-slate-50 dark:bg-slate-700 border-b border-slate-300 dark:border-slate-600 px-2 py-1.5 flex gap-1 overflow-x-auto">
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded font-bold text-sm">B</button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded italic text-sm">I</button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded underline text-sm">U</button>
                                <div class="w-px bg-slate-300 dark:bg-slate-600 my-1 mx-1"></div>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded"><span
                                        class="material-icons text-[16px]">image</span></button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded"><span
                                        class="material-icons text-[16px]">functions</span></button>
                            </div>
                            <textarea required rows="3" placeholder="Nhập câu hỏi..."
                                class="w-full bg-white dark:bg-slate-800 text-slate-800 dark:text-white px-3.5 py-2 text-sm outline-none resize-y"></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Đáp án
                            (Chọn nút tròn nếu là đáp án đúng)</label>
                        <div class="space-y-2.5">
                            <?php
                            $options = ['A', 'B', 'C', 'D'];
                            foreach ($options as $index => $opt):
                                ?>
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_answer" value="<?php echo $opt; ?>" <?php echo $opt === 'A' ? 'checked' : ''; ?>
                                        class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                    <div
                                        class="flex-1 flex items-center border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 rounded-lg overflow-hidden focus-within:border-[#254ada] focus-within:ring-1 focus-within:ring-[#254ada] transition">
                                        <div
                                            class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-600 font-bold text-[13px] text-slate-500 dark:text-slate-400 shrink-0">
                                            <?php echo $opt; ?>
                                        </div>
                                        <input type="text" placeholder="Nhập đáp án <?php echo $opt; ?>..." required
                                            class="flex-1 bg-transparent px-3 py-1.5 text-slate-800 dark:text-white outline-none text-sm">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giải
                            thích (Tùy chọn)</label>
                        <textarea rows="2" placeholder="Giải thích đáp án..."
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition resize-y"></textarea>
                    </div>

                </div>

                <div
                    class="flex justify-between items-center p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 shrink-0">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="keepAddingBtn"
                            class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                        <span class="text-[13px] font-medium text-slate-600 dark:text-slate-400">Tiếp tục thêm</span>
                    </label>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('addQuestionModal')"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                            bỏ</button>
                        <button type="submit" id="submitQuestionBtn"
                            class="px-5 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                            <span class="material-icons text-[18px]">save</span> Lưu câu hỏi
                        </button>
                    </div>
                </div>

            </form>
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
       KHỞI TẠO DOM VÀ SỰ KIỆN
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

        // 3. Chức năng Checkbox All
        const selectAllBtn = document.getElementById('selectAllBtn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        selectAllBtn?.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                const someChecked = Array.from(rowCheckboxes).some(c => c.checked);
                if (selectAllBtn) {
                    selectAllBtn.checked = allChecked;
                    selectAllBtn.indeterminate = someChecked && !allChecked;
                }
            });
        });

        // 4. Chức năng Tìm kiếm & Phân trang (Kết hợp)
        const rowsPerPage = 2; // Hiển thị 2 dòng / 1 trang để test
        let currentPage = 1;
        let filteredRows = [];

        const allRows = Array.from(document.querySelectorAll('.question-row'));
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const quickSearch = document.getElementById('quickSearch');

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
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}</span> - <span class="font-medium text-slate-800 dark:text-white">${displayEnd}</span> trong tổng số <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> câu hỏi`;
            }

            renderPaginationButtons(totalPages);
        }

        function renderPaginationButtons(totalPages) {
            if (!paginationControls) return;
            paginationControls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = `w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
            prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
            paginationControls.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                if (i === currentPage) {
                    pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-[#254ada] text-white rounded-md font-medium shadow-sm';
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
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
            paginationControls.appendChild(nextBtn);
        }

        // Bắt sự kiện gõ ô tìm kiếm
        quickSearch?.addEventListener('input', function (e) {
            const text = e.target.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                const code = row.querySelector('.q-code').textContent.toLowerCase();
                const content = row.querySelector('.q-content').textContent.toLowerCase();
                return code.includes(text) || content.includes(text);
            });
            currentPage = 1;
            updatePagination();
        });

        // Chạy lần đầu
        filteredRows = [...allRows];
        updatePagination();

    });

    // Hàm mở Modal
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
    }

    // Hàm đóng Modal
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    // Hàm xử lý lưu câu hỏi
    function submitAddQuestion() {
        const btn = document.getElementById('submitQuestionBtn');
        const originalText = btn.innerHTML;

        // Đổi trạng thái nút thành Đang lưu
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang lưu...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        // Giả lập xử lý lưu vào CSDL mất 1 giây
        setTimeout(() => {
            // Thông báo thành công
            showToast('success', 'Lưu thành công', 'Câu hỏi mới đã được thêm vào Ngân hàng.');

            // Kiểm tra xem người dùng có tích vào ô "Tiếp tục thêm" không
            const keepAdding = document.getElementById('keepAddingBtn').checked;

            if (keepAdding) {
                // Nếu có: Xóa trắng form để nhập tiếp, không đóng Modal
                document.getElementById('formAddQuestionData').reset();
            } else {
                // Nếu không: Đóng Modal
                closeModal('addQuestionModal');
                document.getElementById('formAddQuestionData').reset();
            }

            // Trả nút về trạng thái ban đầu
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70');

        }, 1000);
    }

</script>