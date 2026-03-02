<?php
$title = "Sao Lưu và Phục Hồi - Hệ Thống Thi Trực Tuyến";
$active_menu = "backup"; 

$backups = [
    ['name' => 'FULL_BACKUP_20231027_0100', 'format' => 'Định dạng: SQL GZipped', 'date' => '27/10/2023', 'time' => '01:00:05', 'size' => '450 MB', 'status' => 'AN TOÀN', 'status_badge' => 'bg-green-100 text-green-700 border border-green-200'],
    ['name' => 'FULL_BACKUP_20231026_0100', 'format' => 'Định dạng: SQL GZipped', 'date' => '26/10/2023', 'time' => '01:00:12', 'size' => '448 MB', 'status' => 'AN TOÀN', 'status_badge' => 'bg-green-100 text-green-700 border border-green-200'],
    ['name' => 'DAILY_DB_20231025', 'format' => 'Định dạng: SQL GZipped', 'date' => '25/10/2023', 'time' => '01:00:08', 'size' => '445 MB', 'status' => 'ĐÃ LƯU TRỮ', 'status_badge' => 'bg-slate-100 text-slate-600 border border-slate-200'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10">
        <h2 class="font-bold text-slate-800">Sao lưu và Phục hồi dữ liệu</h2>
        <div class="flex items-center gap-4">
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 mb-6">
            <span class="material-icons text-[#254ada]">info</span>
            <div>
                <h4 class="font-bold text-[#254ada] text-sm mb-1">Trạng thái hệ thống an toàn</h4>
                <p class="text-sm text-blue-800">Bản sao lưu gần nhất được tạo vào lúc 01:00 AM hôm nay. Bạn nên thực hiện sao lưu trước khi thực hiện các thay đổi lớn về cấu hình hoặc dữ liệu.</p>
            </div>
        </div>

        <div class="flex gap-4 mb-6">
            <button class="px-5 py-2.5 bg-[#254ada] text-white rounded-lg flex items-center gap-2 font-medium text-sm shadow-sm hover:bg-blue-800 transition">
                <span class="material-icons text-[20px]">cloud_upload</span> Tạo bản sao lưu mới
            </button>
            <button class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg flex items-center gap-2 font-medium text-sm shadow-sm hover:bg-slate-50 transition">
                <span class="material-icons text-[20px]">upload_file</span> Phục hồi từ file
            </button>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Danh sách các bản sao lưu</h3>
                    <div class="flex items-center gap-2 text-sm text-slate-500 cursor-pointer">
                        Sắp xếp: Mới nhất <span class="material-icons text-[18px]">filter_list</span>
                    </div>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-4">Tên bản sao lưu</th>
                            <th class="px-5 py-4">Ngày tạo</th>
                            <th class="px-5 py-4 text-center">Dung lượng</th>
                            <th class="px-5 py-4 text-center">Trạng thái</th>
                            <th class="px-5 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($backups as $bk): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-slate-100 text-slate-400 flex items-center justify-center"><span class="material-icons text-[20px]">sd_storage</span></div>
                                <div>
                                    <div class="font-semibold text-slate-800"><?php echo $bk['name']; ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $bk['format']; ?></div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <div><?php echo $bk['date']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $bk['time']; ?></div>
                            </td>
                            <td class="px-5 py-4 text-center text-slate-600 font-medium"><?php echo $bk['size']; ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2 py-1 <?php echo $bk['status_badge']; ?> text-[10px] font-bold rounded-full uppercase"><?php echo $bk['status']; ?></span>
                            </td>
                            <td class="px-5 py-4 text-right text-slate-400 space-x-1">
                                <button class="hover:text-[#254ada] p-1"><span class="material-icons text-[20px]">download</span></button>
                                <button class="hover:text-orange-500 p-1"><span class="material-icons text-[20px]">restore</span></button>
                                <button class="hover:text-red-600 p-1"><span class="material-icons text-[20px]">delete</span></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="col-span-1 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada]">schedule</span> Lịch sao lưu tự động
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-slate-600">Sao lưu hàng ngày</span>
                            <span class="text-slate-800 font-medium">01:00 AM</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                            <span class="text-slate-600">Sao lưu hàng tuần</span>
                            <span class="text-slate-800 font-medium">Chủ nhật, 03:00 AM</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Giữ lại bản sao lưu</span>
                            <span class="text-slate-800 font-medium">30 ngày gần nhất</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada]">dns</span> Dung lượng lưu trữ
                    </h3>
                    <div class="mb-2 w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-[#254ada] h-2 rounded-full" style="width: 45%"></div>
                    </div>
                    <div class="flex justify-between text-sm mb-5">
                        <span class="text-slate-500">Đã sử dụng: 4.5 GB / 10 GB</span>
                        <span class="font-bold text-[#254ada]">45%</span>
                    </div>
                    <div class="bg-orange-50 border border-orange-100 rounded-lg p-3 text-xs text-orange-800 mb-4">
                        <span class="font-bold">Gợi ý:</span> Xóa các bản sao lưu cũ hơn 30 ngày để giải phóng không gian lưu trữ nếu cần thiết.
                    </div>
                    <button class="w-full py-2 border border-slate-300 rounded-lg text-slate-600 font-medium text-sm hover:bg-slate-50 transition">
                        Dọn dẹp bộ nhớ
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>