<?php
$title = "Duyệt câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "approve_q"; 

$pending_q = [
    ['code' => 'BIO-10-005', 'content' => 'Quá trình quang hợp ở thực vật diễn ra chủ yếu ở bào quan nào?', 'subject' => 'Sinh học 10', 'author' => 'Giảng viên A', 'time' => '10 phút trước', 'level' => 'Nhận biết'],
    ['code' => 'HIS-12-112', 'content' => 'Sự kiện nào đánh dấu bước ngoặt của cuộc kháng chiến chống Mỹ?', 'subject' => 'Lịch sử 12', 'author' => 'Giảng viên B', 'time' => '1 giờ trước', 'level' => 'Thông hiểu'],
    ['code' => 'ENG-9-045', 'content' => 'Rewrite the sentence without changing its meaning: "Although it rained..."', 'subject' => 'Tiếng Anh 9', 'author' => 'Giảng viên C', 'time' => 'Hôm qua', 'level' => 'Vận dụng'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="text-sm text-slate-500">Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Duyệt câu hỏi</span></div>
        <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">notifications</span></button>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Duyệt câu hỏi mới</h2>
            <p class="text-sm text-slate-500 mt-1">Kiểm duyệt nội dung câu hỏi trước khi đưa vào Ngân hàng dữ liệu chính thức.</p>
        </div>

        <div class="grid grid-cols-3 gap-6 mb-6">
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center"><span class="material-icons text-2xl">pending_actions</span></div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase">Đang chờ duyệt</p>
                    <p class="text-2xl font-black text-slate-800">24</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center"><span class="material-icons text-2xl">task_alt</span></div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase">Đã duyệt hôm nay</p>
                    <p class="text-2xl font-black text-slate-800">156</p>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center"><span class="material-icons text-2xl">edit_note</span></div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase">Yêu cầu sửa đổi</p>
                    <p class="text-2xl font-black text-slate-800">8</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2"><span class="material-icons text-[18px] text-green-500">done_all</span> Duyệt tất cả</button>
                </div>
                <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#1e3bb3]">
                    <option>Sắp xếp: Mới nhất</option>
                    <option>Sắp xếp: Cũ nhất</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4 w-1/3">Nội dung</th>
                            <th class="px-6 py-4">Môn học</th>
                            <th class="px-6 py-4">Người tải lên</th>
                            <th class="px-6 py-4 text-center">Thao tác duyệt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($pending_q as $q): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-bold text-[#1e3bb3] text-[13px]"><?php echo $q['code']; ?></td>
                            <td class="px-6 py-4">
                                <p class="text-slate-700 text-[13px] font-medium mb-1"><?php echo $q['content']; ?></p>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] rounded font-bold uppercase"><?php echo $q['level']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium text-[13px]"><?php echo $q['subject']; ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800 text-[13px]"><?php echo $q['author']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $q['time']; ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-500 hover:text-white transition flex items-center justify-center" title="Phê duyệt"><span class="material-icons text-[18px]">check</span></button>
                                    <button class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center" title="Từ chối/Yêu cầu sửa"><span class="material-icons text-[18px]">close</span></button>
                                    <button class="w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-200 transition flex items-center justify-center" title="Xem chi tiết"><span class="material-icons text-[18px]">visibility</span></button>
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
<?php include 'components/footer.php'; ?>