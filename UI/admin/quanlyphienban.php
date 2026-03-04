<?php
$title = "Quản lý phiên bản - Hệ Thống Thi Trực Tuyến";
$active_menu = "version_q"; // Sáng menu ở sidebar

$versions = [
    ['code' => 'MATH-101-001', 'content' => 'Cho hàm số y = ax^2 + bx + c. Tìm điều kiện...', 'version' => 'v2.1', 'author' => 'Trần Thị Hoa', 'author_bg' => 'bg-purple-100', 'author_text' => 'text-purple-700', 'time' => '10:30 - 24/10/2023', 'status' => 'Bản hiện tại', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'],
    ['code' => 'PHYS-12-254', 'content' => 'Một vật dao động điều hòa với phương trình x =...', 'version' => 'v1.4', 'author' => 'Nguyễn Văn An', 'author_bg' => 'bg-blue-100', 'author_text' => 'text-blue-700', 'time' => '15:45 - 23/10/2023', 'status' => 'Bản hiện tại', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'],
    ['code' => 'CHEM-11-042', 'content' => 'Hòa tan hoàn toàn 10g hỗn hợp X gồm Mg và Fe...', 'version' => 'v1.0', 'author' => 'Lê Hữu Trí', 'author_bg' => 'bg-orange-100', 'author_text' => 'text-orange-700', 'time' => '09:15 - 20/10/2023', 'status' => 'Bản cũ', 'status_bg' => 'bg-slate-100', 'status_text' => 'text-slate-600'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="text-sm text-slate-500">Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Quản lý phiên bản</span></div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm mã câu hỏi..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">notifications</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Lịch sử & Quản lý phiên bản</h2>
                <p class="text-sm text-slate-500 mt-1">Theo dõi các thay đổi nội dung câu hỏi và khôi phục dữ liệu khi cần thiết.</p>
            </div>
            <button class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                <span class="material-icons text-[20px]">manage_history</span> Nhật ký chỉnh sửa
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Mã câu hỏi</th>
                            <th class="px-6 py-4">Nội dung tóm tắt</th>
                            <th class="px-6 py-4 text-center">Phiên bản</th>
                            <th class="px-6 py-4">Người cập nhật</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($versions as $v): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-bold text-[#1e3bb3]"><?php echo $v['code']; ?></td>
                            <td class="px-6 py-4 text-slate-600 truncate max-w-xs" title="<?php echo $v['content']; ?>"><?php echo $v['content']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-mono text-[11px] font-bold rounded"><?php echo $v['version']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800"><?php echo $v['author']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $v['time']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 <?php echo $v['status_bg']; ?> <?php echo $v['status_text']; ?> text-[11px] font-bold rounded-full inline-block text-center"><?php echo $v['status']; ?></span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 text-slate-400">
                                <button class="hover:text-[#1e3bb3] p-1.5 transition rounded-md hover:bg-blue-50" title="Xem lịch sử chi tiết"><span class="material-icons text-[18px]">history</span></button>
                                <?php if($v['status'] == 'Bản cũ'): ?>
                                <button class="hover:text-orange-500 p-1.5 transition rounded-md hover:bg-orange-50" title="Khôi phục bản này"><span class="material-icons text-[18px]">restore</span></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị 1 - 3 của 45 câu hỏi có lịch sử chỉnh sửa</p>
                </div>
        </div>
    </div>
</main>
<?php include 'components/footer.php'; ?>