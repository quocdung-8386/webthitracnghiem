<?php
// 1. Cấu hình thông tin trang
$title = "Kết quả & Lời giải - Hệ Thống Thi Trực Tuyến";
$active_menu = "results"; // Biến này dùng để làm sáng menu "Kết quả & Lời giải" trong Sidebar

// Dữ liệu mô phỏng danh sách kết quả thi
$results = [
    [
        'id' => 'SV2023001', 'name' => 'Nguyễn Văn An', 'avatar' => 'NA', 'avatar_bg' => 'bg-blue-100 text-blue-600',
        'exam' => 'Thi giữa kỳ I - Toán cao cấp', 'score' => '8.5', 'total_score' => '10.0', 
        'time_spent' => '45:20', 'time_total' => '60:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023042', 'name' => 'Trần Thị Hoa', 'avatar' => 'TH', 'avatar_bg' => 'bg-orange-100 text-orange-600',
        'exam' => 'Lập trình hướng đối tượng', 'score' => '4.0', 'total_score' => '10.0', 
        'time_spent' => '58:12', 'time_total' => '60:00',
        'status_type' => 'fail'
    ],
    [
        'id' => 'SV2023115', 'name' => 'Lê Hoàng Minh', 'avatar' => 'LM', 'avatar_bg' => 'bg-slate-200 text-slate-600',
        'exam' => 'Thi kết thúc học phần - CSDL', 'score' => '9.2', 'total_score' => '10.0', 
        'time_spent' => '32:45', 'time_total' => '90:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023204', 'name' => 'Phạm Anh Tuấn', 'avatar' => 'PT', 'avatar_bg' => 'bg-purple-100 text-purple-600',
        'exam' => 'Tiếng Anh chuyên ngành', 'score' => '7.8', 'total_score' => '10.0', 
        'time_spent' => '40:00', 'time_total' => '45:00',
        'status_type' => 'pass'
    ],
    [
        'id' => 'SV2023088', 'name' => 'Bùi Ngọc Chi', 'avatar' => 'BC', 'avatar_bg' => 'bg-emerald-100 text-emerald-600',
        'exam' => 'Toán học cao cấp', 'score' => '2.5', 'total_score' => '10.0', 
        'time_spent' => '15:10', 'time_total' => '60:00',
        'status_type' => 'fail'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">KẾT QUẢ & LỜI GIẢI CHI TIẾT</h2>
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
        
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            <div class="p-6 border-b border-slate-100 flex flex-wrap lg:flex-nowrap justify-between items-start gap-4">
                
                <div class="flex-1 space-y-3 max-w-2xl">
                    <div class="flex gap-3">
                        <div class="relative w-1/2">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">library_books</span>
                            <select class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:border-[#1e3bb3] appearance-none cursor-pointer">
                                <option>Tất cả Môn học</option>
                            </select>
                            <span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                        <div class="relative w-1/2">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">assignment</span>
                            <select class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:border-[#1e3bb3] appearance-none cursor-pointer">
                                <option>Tất cả Kỳ thi</option>
                            </select>
                            <span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" placeholder="Tìm theo mã hoặc tên thí sinh..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] transition">
                    </div>
                </div>

                <button class="px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center justify-center gap-2 hover:bg-slate-50 text-sm font-bold shadow-sm transition h-full">
                    <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] text-slate-500 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-5">Mã thí sinh</th>
                            <th class="px-6 py-5">Họ tên</th>
                            <th class="px-6 py-5 w-1/4">Kỳ thi</th>
                            <th class="px-6 py-5">Điểm số</th>
                            <th class="px-6 py-5 text-center">Thời gian<br>làm bài</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($results as $res): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            
                            <td class="px-6 py-4 font-bold text-[#1e3bb3] text-[13px]"><?php echo $res['id']; ?></td>
                            
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full <?php echo $res['avatar_bg']; ?> flex items-center justify-center font-bold text-[12px]">
                                    <?php echo $res['avatar']; ?>
                                </div>
                                <div class="font-bold text-slate-800 text-[13px] leading-tight">
                                    <?php echo str_replace(' ', '<br>', $res['name']); ?>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700 text-[13px] leading-relaxed pr-4">
                                    <?php echo $res['exam']; ?>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 text-[15px]"><?php echo $res['score']; ?></span><span class="text-slate-400 text-[13px]">/<?php echo $res['total_score']; ?></span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="font-medium text-slate-600 text-[13px]"><?php echo $res['time_spent']; ?> <span class="text-slate-300">/</span></div>
                                <div class="text-[12px] text-slate-400"><?php echo $res['time_total']; ?></div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <?php if($res['status_type'] == 'pass'): ?>
                                    <div class="inline-block px-3 py-1 bg-green-50 text-green-600 rounded-full text-center">
                                        <div class="text-[11px] font-bold leading-tight">Đạt</div>
                                        <div class="text-[9px] font-medium opacity-80">(Passed)</div>
                                    </div>
                                <?php else: ?>
                                    <div class="inline-block px-3 py-1 bg-red-50 text-red-500 rounded-full text-center">
                                        <div class="text-[11px] font-bold leading-tight">Trượt</div>
                                        <div class="text-[9px] font-medium opacity-80">(Failed)</div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-[#1e3bb3] rounded-lg text-[12px] font-bold hover:bg-blue-100 transition">
                                    <span class="material-icons text-[16px]">visibility</span> Xem lời giải
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị 1-5 trong số 12,450 kết quả</p>
                <div class="flex items-center gap-2">
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-400 hover:bg-slate-50 transition"><span class="material-icons text-[18px]">chevron_left</span></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded-md font-medium shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">2</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">3</button>
                    <span class="text-slate-400 px-1">...</span>
                    <button class="w-10 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">2490</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-600 hover:bg-slate-50 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include 'components/footer.php'; ?>