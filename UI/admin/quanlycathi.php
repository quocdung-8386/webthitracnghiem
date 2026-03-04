<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý Ca thi - Hệ Thống Thi Trực Tuyến";
$active_menu = "shift_exam"; // Làm sáng menu "Quản lý ca thi" trong Sidebar

// Dữ liệu mô phỏng cho Bảng Ca thi
$shifts = [
    [
        'name' => 'Ca sáng - Đợt 1', 'id' => 'SH-001', 
        'time' => '07:30 - 09:30', 'date' => '20/12/2023', 
        'location_icon' => 'business', 'location' => 'Phòng Lab 402', 
        'students_assigned' => 50, 'students_total' => 50, 
        'avatars' => ['SV', 'SV', '+48'], 'avatar_bg' => 'bg-blue-100 text-blue-600',
        'status' => 'ĐANG THI', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700', 
        'is_ended' => false
    ],
    [
        'name' => 'Ca sáng - Đợt 2', 'id' => 'SH-002', 
        'time' => '10:00 - 12:00', 'date' => '20/12/2023', 
        'location_icon' => 'business', 'location' => 'Phòng Lab 402', 
        'students_assigned' => 0, 'students_total' => 50, 
        'avatars' => ['?'], 'avatar_bg' => 'bg-slate-100 text-slate-400',
        'status' => 'SẮP TỚI', 'status_bg' => 'bg-blue-50', 'status_text' => 'text-blue-600', 
        'is_ended' => false
    ],
    [
        'name' => 'Ca chiều (Online)', 'id' => 'SH-003', 
        'time' => '13:30 - 15:30', 'date' => '20/12/2023', 
        'location_icon' => 'link', 'location' => 'exam.edu.vn/online/...', 
        'students_assigned' => 200, 'students_total' => 200, 
        'avatars' => ['SV', '+199'], 'avatar_bg' => 'bg-orange-100 text-orange-600',
        'status' => 'SẮP TỚI', 'status_bg' => 'bg-blue-50', 'status_text' => 'text-blue-600', 
        'is_ended' => false
    ],
    [
        'name' => 'Ca tối (Dự phòng)', 'id' => 'SH-004', 
        'time' => '18:00 - 20:00', 'date' => '19/12/2023', 
        'location_icon' => 'business', 'location' => 'Phòng B.301', 
        'students_assigned' => 120, 'students_total' => 120, 
        'avatars' => [], 'avatar_bg' => '', // Không hiển thị avatar cho ca đã kết thúc
        'status' => 'ĐÃ KẾT THÚC', 'status_bg' => 'bg-slate-100', 'status_text' => 'text-slate-600', 
        'is_ended' => true
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800">Quản lý Ca thi và Phân bổ thí sinh</h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm ca thi..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        
        <div class="mb-6">
            <div class="text-[13px] text-slate-500 mb-2">
                Kỳ thi & Đề thi <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Toán Cao Cấp A1 - Học kỳ 1 2023</span>
            </div>
            <div class="flex justify-between items-end">
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-icons text-[#1e3bb3] text-[28px]">calendar_month</span> Danh sách các ca thi
                </h2>
                <div class="flex gap-3">
                    <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">person_add_alt_1</span> Gán thí sinh hàng loạt
                    </button>
                    <button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">add</span> Thêm ca thi
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-[#1e3bb3] flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">event_note</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wide mb-0.5">Tổng số ca thi</p>
                    <p class="text-3xl font-black text-slate-800">08</p>
                </div>
            </div>
            
            <div class="bg-green-50/50 border border-green-100 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">how_to_reg</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-green-600 uppercase tracking-wide mb-0.5">Thí sinh đã gán</p>
                    <p class="text-3xl font-black text-slate-800">1,215 <span class="text-lg text-slate-400 font-semibold">/ 1,240</span></p>
                </div>
            </div>

            <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">warning</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-orange-600 uppercase tracking-wide mb-0.5">Thí sinh chưa gán</p>
                    <p class="text-3xl font-black text-slate-800">25</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] text-slate-500 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-5 w-14 text-center"><span class="material-icons text-slate-300 text-[20px]">check_box_outline_blank</span></th>
                            <th class="px-6 py-5">Tên ca thi</th>
                            <th class="px-6 py-5">Thời gian</th>
                            <th class="px-6 py-5">Địa điểm / Link</th>
                            <th class="px-6 py-5 text-center">Thí sinh</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($shifts as $shift): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-center">
                                <span class="material-icons text-slate-300 text-[20px] cursor-pointer hover:text-[#1e3bb3]">check_box_outline_blank</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-[14px]"><?php echo $shift['name']; ?></div>
                                <div class="text-[12px] text-slate-400 mt-0.5">ID: <?php echo $shift['id']; ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700"><?php echo $shift['time']; ?></div>
                                <div class="text-[12px] text-slate-400 mt-0.5"><?php echo $shift['date']; ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-[18px] text-slate-400"><?php echo $shift['location_icon']; ?></span>
                                    <?php echo $shift['location']; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if(count($shift['avatars']) > 0): ?>
                                    <div class="flex justify-center -space-x-2 mb-1">
                                        <?php foreach($shift['avatars'] as $av): ?>
                                            <div class="w-6 h-6 rounded-full <?php echo $shift['avatar_bg']; ?> border-2 border-white flex items-center justify-center text-[8px] font-bold z-10"><?php echo $av; ?></div>
                                        <?php endform; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="text-[12px] <?php echo ($shift['students_assigned'] == 0) ? 'text-orange-500 font-bold' : 'text-[#1e3bb3] font-semibold'; ?>">
                                    <?php echo $shift['students_assigned']; ?>/<?php echo $shift['students_total']; ?> thí sinh
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full <?php echo $shift['status_bg']; ?> <?php echo $shift['status_text']; ?> uppercase inline-block leading-tight text-center">
                                    <?php echo str_replace(' ', '<br>', $shift['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 text-slate-400">
                                <?php if(!$shift['is_ended']): ?>
                                    <button class="hover:text-[#1e3bb3] p-1.5 transition rounded-md hover:bg-blue-50" title="Thêm thí sinh"><span class="material-icons text-[18px]">person_add</span></button>
                                <?php else: ?>
                                    <button class="hover:text-[#1e3bb3] p-1.5 transition rounded-md hover:bg-blue-50" title="Xem thống kê điểm"><span class="material-icons text-[18px]">insert_chart_outlined</span></button>
                                <?php endif; ?>
                                <button class="hover:text-slate-700 p-1.5 transition rounded-md hover:bg-slate-100" title="Chỉnh sửa"><span class="material-icons text-[18px]">edit</span></button>
                                <button class="hover:text-red-500 p-1.5 transition rounded-md hover:bg-red-50" title="Xóa"><span class="material-icons text-[18px]">delete</span></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị 1-4 trên tổng số 8 ca thi</p>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 rounded-md text-slate-600 transition font-medium">Trước</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-400">1</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 rounded-md text-slate-600">2</button>
                    <button class="px-3 py-1.5 border border-slate-200 bg-white hover:bg-slate-50 rounded-md text-slate-600 transition font-medium">Sau</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50/30 border border-blue-100 rounded-xl p-6">
                <h3 class="font-bold text-[#1e3bb3] flex items-center gap-2 mb-4">
                    <span class="material-icons text-[20px]">info</span> Hướng dẫn phân bổ
                </h3>
                <ul class="space-y-3 text-[13px] text-slate-600 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="material-icons text-[#1e3bb3] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Sử dụng <b>Gán thí sinh hàng loạt</b> để tự động chia thí sinh vào các ca thi theo bảng chữ cái hoặc ngẫu nhiên.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="material-icons text-[#1e3bb3] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Mỗi ca thi có thể cấu hình giới hạn số lượng thí sinh hoặc gán theo phòng học cụ thể.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-6 flex flex-col items-center justify-center text-center shadow-sm cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition group">
                <div class="w-12 h-12 rounded-full bg-slate-800 text-white flex items-center justify-center mb-3 group-hover:-translate-y-1 transition-transform">
                    <span class="material-icons text-[24px]">upload_file</span>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Nhập danh sách từ Excel</h3>
                <p class="text-[13px] text-slate-500">Tải lên file danh sách phân bổ ca thi theo định dạng mẫu của hệ thống.</p>
            </div>
        </div>

    </div>
</main>

<?php include 'components/footer.php'; ?>