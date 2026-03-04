<?php
// 1. Cấu hình thông tin trang
$title = "Xuất báo cáo - Hệ Thống Thi Trực Tuyến";
$active_menu = "export_report"; // Biến active menu ở thanh sidebar

// Dữ liệu mô phỏng Lịch sử xuất báo cáo
$export_history = [
    [
        'name' => 'Bao_cao_Hoc_ky_1_Khoi_12_2023.xlsx', 'type' => 'Báo cáo theo lớp', 
        'date' => '12/05/2024 08:30', 'format' => 'XLSX', 
        'format_bg' => 'bg-green-50', 'format_text' => 'text-green-600', 'size' => '2.4 MB'
    ],
    [
        'name' => 'Ket_qua_Thi_Toan_Cao_Cap_A1.pdf', 'type' => 'Báo cáo theo môn', 
        'date' => '11/05/2024 15:45', 'format' => 'PDF', 
        'format_bg' => 'bg-red-50', 'format_text' => 'text-red-500', 'size' => '4.8 MB'
    ],
    [
        'name' => 'Danh_sach_Sinh_vien_Nganh_IT.csv', 'type' => 'Báo cáo cá nhân', 
        'date' => '10/05/2024 10:20', 'format' => 'CSV', 
        'format_bg' => 'bg-blue-50', 'format_text' => 'text-blue-600', 'size' => '850 KB'
    ],
    [
        'name' => 'Tong_hop_Ky_thi_Gia_dinh_2024.xlsx', 'type' => 'Báo cáo tổng hợp', 
        'date' => '09/05/2024 14:15', 'format' => 'XLSX', 
        'format_bg' => 'bg-green-50', 'format_text' => 'text-green-600', 'size' => '1.2 MB'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 tracking-wide">Trung tâm Xuất báo cáo dữ liệu thi</h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-6xl space-y-6">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-6 border-b border-slate-100 pb-4">
                    <span class="material-icons text-slate-400 text-[20px]">feed</span> Cấu hình xuất báo cáo mới
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-2">Loại báo cáo</label>
                        <div class="relative">
                            <select class="w-full pl-4 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:border-[#1e3bb3] appearance-none cursor-pointer">
                                <option>Báo cáo theo lớp học</option>
                                <option>Báo cáo theo môn học</option>
                                <option>Báo cáo tổng hợp kỳ thi</option>
                            </select>
                            <span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-2">Khoảng thời gian</label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" placeholder="dd/mm/yyyy" class="w-full pl-3 pr-8 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                <span class="material-icons absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">calendar_today</span>
                            </div>
                            <span class="text-slate-400 text-sm">đến</span>
                            <div class="relative flex-1">
                                <input type="text" placeholder="dd/mm/yyyy" class="w-full pl-3 pr-8 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                <span class="material-icons absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">calendar_today</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-2">Định dạng file</label>
                        <div class="flex items-center gap-2">
                            <button class="flex-1 py-2.5 border border-[#1e3bb3] bg-blue-50 text-[#1e3bb3] rounded-lg text-sm font-semibold transition">Excel</button>
                            <button class="flex-1 py-2.5 border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 rounded-lg text-sm font-medium transition">PDF</button>
                            <button class="flex-1 py-2.5 border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 rounded-lg text-sm font-medium transition">CSV</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4 pt-4 border-t border-slate-100">
                    <button class="text-sm font-medium text-slate-500 hover:text-slate-700 transition">Đặt lại</button>
                    <button class="px-6 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-semibold shadow-sm transition">
                        <span class="material-icons text-[18px]">download</span> Tạo báo cáo
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-[16px] flex items-center gap-2">
                        <span class="material-icons text-slate-400 text-[20px]">history</span> Lịch sử xuất báo cáo gần đây
                    </h3>
                    <button class="text-[13px] text-[#1e3bb3] font-semibold hover:underline flex items-center gap-1">
                        <span class="material-icons text-[16px]">refresh</span> Làm mới danh sách
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 w-1/3">Tên báo cáo</th>
                                <th class="px-6 py-4">Loại</th>
                                <th class="px-6 py-4">Ngày tạo</th>
                                <th class="px-6 py-4 text-center">Định dạng</th>
                                <th class="px-6 py-4 text-center">Dung lượng</th>
                                <th class="px-6 py-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($export_history as $row): ?>
                            <tr class="hover:bg-slate-50/50 transition group">
                                <td class="px-6 py-4 font-medium text-slate-700 text-[13px] truncate" title="<?php echo $row['name']; ?>">
                                    <?php echo $row['name']; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-[13px]"><?php echo $row['type']; ?></td>
                                <td class="px-6 py-4 text-slate-500 text-[13px] font-mono"><?php echo $row['date']; ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 <?php echo $row['format_bg']; ?> <?php echo $row['format_text']; ?> text-[10px] font-bold rounded uppercase"><?php echo $row['format']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-600 text-[13px] font-medium"><?php echo $row['size']; ?></td>
                                <td class="px-6 py-4 text-center text-[#1e3bb3]">
                                    <button class="p-1.5 rounded-md hover:bg-blue-50 transition" title="Tải xuống">
                                        <span class="material-icons text-[20px]">file_download</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                    <p>Hiển thị 4 trong tổng số 28 báo cáo đã xuất</p>
                    <div class="flex items-center gap-1.5">
                        <button class="w-8 h-8 flex items-center justify-center bg-slate-50 border border-slate-200 rounded-md text-slate-300 cursor-not-allowed"><span class="material-icons text-[18px]">chevron_left</span></button>
                        <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-600 hover:bg-slate-50 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-xl flex gap-4 items-start">
                    <span class="material-icons text-[#1e3bb3] bg-blue-100 p-2 rounded-lg">security</span>
                    <div>
                        <h4 class="font-bold text-[#1e3bb3] text-[14px] mb-1">Lưu ý bảo mật</h4>
                        <p class="text-[12px] text-blue-900/80 leading-relaxed">Các báo cáo chứa dữ liệu định danh thí sinh. Vui lòng chỉ chia sẻ file cho nhân sự có thẩm quyền.</p>
                    </div>
                </div>
                
                <div class="bg-orange-50/50 border border-orange-100 p-5 rounded-xl flex gap-4 items-start">
                    <span class="material-icons text-orange-500 bg-orange-100 p-2 rounded-lg">auto_delete</span>
                    <div>
                        <h4 class="font-bold text-orange-600 text-[14px] mb-1">Tự động dọn dẹp</h4>
                        <p class="text-[12px] text-orange-900/80 leading-relaxed">Hệ thống sẽ tự động xóa các file báo cáo đã được xuất quá 30 ngày để tối ưu không gian lưu trữ.</p>
                    </div>
                </div>

                <div class="bg-slate-50/80 border border-slate-200 p-5 rounded-xl flex gap-4 items-start">
                    <span class="material-icons text-slate-500 bg-slate-200 p-2 rounded-lg">support_agent</span>
                    <div>
                        <h4 class="font-bold text-slate-700 text-[14px] mb-1">Hỗ trợ kỹ thuật</h4>
                        <p class="text-[12px] text-slate-600 leading-relaxed">Nếu không tìm thấy dữ liệu mong muốn, vui lòng liên hệ quản trị viên kỹ thuật để được hỗ trợ.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>