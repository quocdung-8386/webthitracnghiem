<?php
$title = "Nhật Ký Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "logs"; 

$logs = [
    ['time' => '14:25:30', 'date' => '12/10/2023', 'initial' => 'NV', 'name' => 'Nguyen Van A', 'role' => 'Quản trị viên', 'action' => 'ĐĂNG NHẬP', 'badge' => 'bg-green-100 text-green-700', 'desc' => 'Đăng nhập thành công vào hệ thống quản trị', 'ip' => '192.168.1.15'],
    ['time' => '14:10:15', 'date' => '12/10/2023', 'initial' => 'TT', 'name' => 'Tran Thi B', 'role' => 'Giáo viên', 'action' => 'THÊM MỚI', 'badge' => 'bg-blue-100 text-blue-700', 'desc' => 'Tạo đề thi mới: "VLDC_01"', 'ip' => '172.16.0.42'],
    ['time' => '13:55:02', 'date' => '12/10/2023', 'initial' => 'NV', 'name' => 'Nguyen Van A', 'role' => 'Quản trị viên', 'action' => 'CHỈNH SỬA', 'badge' => 'bg-orange-100 text-orange-700', 'desc' => 'Cập nhật quyền hạn người dùng "student_02"', 'ip' => '192.168.1.15'],
    ['time' => '13:40:44', 'date' => '12/10/2023', 'initial' => 'LH', 'name' => 'Le Hoang', 'role' => 'Quản trị viên', 'action' => 'XÓA', 'badge' => 'bg-red-100 text-red-700', 'desc' => 'Xóa vĩnh viễn 12 câu hỏi trùng lặp trong Ngân hàng', 'ip' => '113.161.4.1'],
    ['time' => '13:30:10', 'date' => '12/10/2023', 'initial' => 'HS', 'name' => 'Hệ thống', 'role' => 'System Bot', 'action' => 'SAO LƯU', 'badge' => 'bg-slate-200 text-slate-700', 'desc' => 'Hoàn thành sao lưu định kỳ 13:00', 'ip' => 'localhost'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10">
        <div class="flex items-center gap-2">
            <span class="material-icons text-slate-400">history</span>
            <span class="font-bold text-slate-800">Nhật ký hoạt động hệ thống</span>
        </div>
        <div class="flex items-center gap-4">
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6 grid grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">TÌM KIẾM</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" placeholder="Tìm theo nội dung..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">KHOẢNG THỜI GIAN</label>
                <select class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#254ada]">
                    <option>Hôm nay</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">NGƯỜI THỰC HIỆN</label>
                <select class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#254ada]">
                    <option>Tất cả vai trò</option>
                </select>
            </div>
            <div>
                <button class="w-full py-2 bg-[#254ada] text-white rounded-lg flex items-center justify-center gap-2 font-medium text-sm shadow-sm hover:bg-blue-800 transition">
                    <span class="material-icons text-[18px]">filter_alt</span> Áp dụng bộ lọc
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Chi tiết lịch sử thao tác</h3>
                <div class="flex gap-2">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50"><span class="material-icons text-[20px]">download</span></button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50"><span class="material-icons text-[20px]">refresh</span></button>
                </div>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4">Người thực hiện</th>
                        <th class="px-6 py-4">Hành động</th>
                        <th class="px-6 py-4">Nội dung chi tiết</th>
                        <th class="px-6 py-4 text-right">Địa chỉ IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach($logs as $log): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800"><?php echo $log['time']; ?></div>
                            <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $log['date']; ?></div>
                        </td>
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-[#254ada] flex items-center justify-center font-bold text-xs"><?php echo $log['initial']; ?></div>
                            <div>
                                <div class="font-semibold text-slate-800"><?php echo $log['name']; ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $log['role']; ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 <?php echo $log['badge']; ?> text-[10px] font-bold rounded uppercase"><?php echo $log['action']; ?></span>
                        </td>
                        <td class="px-6 py-4 text-slate-600"><?php echo $log['desc']; ?></td>
                        <td class="px-6 py-4 text-right text-slate-500 font-mono text-xs"><?php echo $log['ip']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="p-4 border-t border-slate-200 flex justify-between items-center text-sm text-slate-500">
                <p>Hiển thị 1 - 5 của 1,250 bản ghi</p>
                </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>