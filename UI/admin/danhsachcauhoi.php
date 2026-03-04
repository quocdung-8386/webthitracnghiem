<?php
$title = "Ngân hàng câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "update_q"; // Sẽ làm sáng menu Cập nhật câu hỏi ở Sidebar

$questions = [
    ['code' => 'MATH-101-001', 'content' => 'Cho hàm số y = ax^2 + bx + c. Tìm điều kiện để đồ thị hàm...', 'subject' => 'Toán học', 'grade' => 'Khối 10 - Giải tích', 'level' => 'Thông hiểu', 'level_bg' => 'bg-blue-50', 'level_text' => 'text-blue-600', 'status' => 'Đã duyệt', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700', 'dot' => ''],
    ['code' => 'PHYS-12-254', 'content' => 'Một vật dao động điều hòa với phương trình x = 5cos(4πt...', 'subject' => 'Vật lý', 'grade' => 'Khối 12 - Cơ học', 'level' => 'Vận dụng', 'level_bg' => 'bg-orange-50', 'level_text' => 'text-orange-600', 'status' => 'Đang chờ duyệt', 'status_bg' => 'bg-orange-100', 'status_text' => 'text-orange-700', 'dot' => ''],
    ['code' => 'ENGL-UNI-012', 'content' => "Choose the best answer to complete the sentence: 'If I ...", 'subject' => 'Tiếng Anh', 'grade' => 'Đại học - Grammar', 'level' => 'Nhận biết', 'level_bg' => 'bg-slate-100', 'level_text' => 'text-slate-600', 'status' => 'Nháp', 'status_bg' => 'bg-transparent', 'status_text' => 'text-slate-500', 'dot' => 'bg-slate-400'],
    ['code' => 'MATH-12-099', 'content' => 'Tính nguyên hàm của hàm số f(x) = e^(2x) * sin(x)?', 'subject' => 'Toán học', 'grade' => 'Khối 12 - Giải tích', 'level' => 'Vận dụng cao', 'level_bg' => 'bg-red-50', 'level_text' => 'text-red-600', 'status' => 'Đã duyệt', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700', 'dot' => ''],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="flex items-center gap-2">
            <span class="material-icons text-slate-400">library_books</span>
            <span class="font-bold text-slate-800">Danh sách Ngân hàng câu hỏi tổng thể</span>
        </div>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm câu hỏi..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:ring-1 focus:ring-[#1e3bb3] focus:outline-none w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-3">
                <button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">add_circle</span> Thêm câu hỏi mới
                </button>
                <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">dynamic_feed</span> Thao tác hàng loạt
                </button>
            </div>
            <div class="text-sm text-slate-600 flex gap-6">
                <span>Tổng cộng: <b class="text-slate-800">1,248</b> câu hỏi</span>
                <span>Đã duyệt: <b class="text-green-600">942</b></span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="p-5 flex gap-4 border-b border-slate-100">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Môn học</label>
                    <select class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3bb3]">
                        <option>Tất cả môn học</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Khối lớp</label>
                    <select class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3bb3]">
                        <option>Tất cả các khối</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Mức độ khó</label>
                    <select class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3bb3]">
                        <option>Tất cả mức độ</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Trạng thái</label>
                    <select class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3bb3]">
                        <option>Tất cả trạng thái</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] text-slate-400 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 w-14 text-center"><span class="material-icons text-slate-300 text-[20px]">radio_button_unchecked</span></th>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4 w-[35%]">Nội dung câu hỏi</th>
                            <th class="px-6 py-4">Danh mục/Môn</th>
                            <th class="px-6 py-4 text-center">Mức độ</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($questions as $q): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-center"><span class="material-icons text-slate-300 text-[20px] cursor-pointer">radio_button_unchecked</span></td>
                            <td class="px-6 py-4 font-semibold text-[#1e3bb3] text-[13px]"><?php echo $q['code']; ?></td>
                            <td class="px-6 py-4 text-slate-600 text-[13px] truncate max-w-xs" title="<?php echo $q['content']; ?>"><?php echo $q['content']; ?></td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 text-[13px]"><?php echo $q['subject']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $q['grade']; ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md <?php echo $q['level_bg']; ?> <?php echo $q['level_text']; ?> text-[11px] font-semibold"><?php echo $q['level']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($q['dot'] != ''): ?>
                                    <span class="inline-flex items-center gap-1.5 text-[12px] font-semibold <?php echo $q['status_text']; ?>">
                                        <div class="w-1.5 h-1.5 rounded-full <?php echo $q['dot']; ?>"></div> <?php echo $q['status']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1.5 rounded-full <?php echo $q['status_bg']; ?> <?php echo $q['status_text']; ?> text-[11px] font-bold inline-flex items-center text-center leading-tight max-w-[80px]"><?php echo $q['status']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị 1 - 10 trong tổng số 1,248 câu hỏi</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-md text-slate-400 hover:bg-slate-50"><span class="material-icons text-[18px]">chevron_left</span></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded-md font-medium shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600">2</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600">3</button>
                    <span class="w-8 h-8 flex items-center justify-center text-slate-400">...</span>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600">125</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-md text-slate-600 hover:bg-slate-50"><span class="material-icons text-[18px]">chevron_right</span></button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'components/footer.php'; ?>