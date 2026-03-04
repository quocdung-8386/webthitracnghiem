<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý tiến trình - Hệ Thống Thi Trực Tuyến";
$active_menu = "progress"; // Làm sáng menu "Quản lý tiến trình" trong Sidebar

// Dữ liệu mô phỏng cho thẻ thống kê (Stats)
$stats = [
    ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => '68.5%', 'icon' => 'trending_up', 'color' => 'blue'],
    ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => '74.2%', 'icon' => 'check_circle_outline', 'color' => 'green'],
    ['title' => 'THỜI GIAN TB', 'value' => '42 phút', 'icon' => 'history', 'color' => 'orange'],
    ['title' => 'ĐANG HỌC', 'value' => '1,204', 'icon' => 'groups', 'color' => 'purple'],
];

// Dữ liệu mô phỏng danh sách tiến trình của thí sinh
$progress_data = [
    [
        'id' => 'SV2023001', 'name' => 'Nguyễn Văn An', 'avatar' => 'NA', 'avatar_bg' => 'bg-blue-100 text-blue-600',
        'dept' => 'CNTT', 'class' => 'K20A', 'completed' => 12, 'total_tasks' => 15,
        'percent' => 80, 'bar_color' => 'bg-blue-600', 'score' => '8.5', 
        'status' => 'VƯỢT TIẾN ĐỘ', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'
    ],
    [
        'id' => 'SV2023042', 'name' => 'Trần Thị Hoa', 'avatar' => 'TH', 'avatar_bg' => 'bg-orange-100 text-orange-600',
        'dept' => 'CNTT', 'class' => 'K20A', 'completed' => 6, 'total_tasks' => 15,
        'percent' => 40, 'bar_color' => 'bg-orange-500', 'score' => '6.2', 
        'status' => 'CHẬM TIẾN ĐỘ', 'status_bg' => 'bg-orange-100', 'status_text' => 'text-orange-700'
    ],
    [
        'id' => 'SV2023115', 'name' => 'Lê Hoàng Minh', 'avatar' => 'LM', 'avatar_bg' => 'bg-slate-200 text-slate-600',
        'dept' => 'Kinh tế', 'class' => 'K21', 'completed' => 14, 'total_tasks' => 15,
        'percent' => 93, 'bar_color' => 'bg-green-500', 'score' => '9.1', 
        'status' => 'HOÀN THÀNH TỐT', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'
    ],
    [
        'id' => 'SV2023204', 'name' => 'Phạm Anh Tuấn', 'avatar' => 'PT', 'avatar_bg' => 'bg-purple-100 text-purple-600',
        'dept' => 'Ngôn ngữ Anh', 'class' => 'K19', 'completed' => 10, 'total_tasks' => 15,
        'percent' => 66, 'bar_color' => 'bg-blue-600', 'score' => '7.4', 
        'status' => 'ĐÚNG LỘ TRÌNH', 'status_bg' => 'bg-blue-100', 'status_text' => 'text-blue-700'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">QUẢN LÝ TIẾN TRÌNH HỌC TẬP</h2>
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
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <?php foreach($stats as $stat): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex items-center gap-5">
                <div class="w-14 h-14 rounded-full bg-<?php echo $stat['color']; ?>-50 text-<?php echo $stat['color']; ?>-600 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[28px]"><?php echo $stat['icon']; ?></span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1"><?php echo $stat['title']; ?></p>
                    <p class="text-3xl font-black text-slate-800"><?php echo $stat['value']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
            
            <div class="p-5 border-b border-slate-100 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <div class="relative min-w-[200px]">
                        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_alt</span>
                        <select class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:border-[#1e3bb3] appearance-none cursor-pointer">
                            <option>Tất cả Lớp/Đơn vị</option>
                            <option>CNTT K20A</option>
                            <option>Kinh tế K21</option>
                        </select>
                        <span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                    
                    <div class="relative flex-1 lg:min-w-[300px]">
                        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" placeholder="Tìm tên thí sinh..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] transition">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                    </button>
                    <button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">mail</span> Nhắc nhở thí sinh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] text-slate-500 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-5">Thí sinh</th>
                            <th class="px-6 py-5">Lớp</th>
                            <th class="px-6 py-5 text-center">Bài<br>luyện tập</th>
                            <th class="px-6 py-5 w-[25%]">Tiến độ</th>
                            <th class="px-6 py-5 text-center">Điểm<br>trung bình</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Chi<br>tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($progress_data as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full <?php echo $row['avatar_bg']; ?> flex items-center justify-center font-bold text-[13px] shrink-0 border border-slate-100">
                                    <?php echo $row['avatar']; ?>
                                </div>
                                <div class="font-bold text-slate-800 text-[14px] leading-tight">
                                    <?php echo str_replace(' ', '<br>', $row['name']); ?>
                                    <div class="text-[11px] text-slate-400 font-normal mt-1">ID: <?php echo $row['id']; ?></div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700 text-[13px]"><?php echo $row['dept']; ?></div>
                                <div class="text-[12px] text-slate-500"><?php echo $row['class']; ?></div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="text-[11px] text-slate-500 mb-0.5">Đã hoàn thành</div>
                                <div class="font-bold text-slate-800 text-[14px]"><?php echo $row['completed']; ?>/<?php echo $row['total_tasks']; ?></div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full <?php echo $row['bar_color']; ?>" style="width: <?php echo $row['percent']; ?>%"></div>
                                    </div>
                                    <span class="font-bold text-slate-700 text-[13px] w-8 text-right"><?php echo $row['percent']; ?>%</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-slate-800 text-[14px]"><?php echo $row['score']; ?></span><span class="text-slate-400 text-[12px]"> / 10</span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight <?php echo $row['status_bg']; ?> <?php echo $row['status_text']; ?>">
                                    <?php echo str_replace(' ', '<br>', $row['status']); ?>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <button class="w-8 h-8 rounded-full border border-slate-200 text-slate-400 hover:text-[#1e3bb3] hover:border-[#1e3bb3] hover:bg-blue-50 transition flex items-center justify-center mx-auto" title="Xem chi tiết">
                                    <span class="material-icons text-[18px]">visibility</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị 1-4 trong số 1,204 thí sinh</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-400 hover:bg-slate-50 transition"><span class="material-icons text-[18px]">chevron_left</span></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded-md font-medium shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">2</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">3</button>
                    <span class="text-slate-400 px-1">...</span>
                    <button class="w-10 h-8 flex items-center justify-center bg-white border border-slate-200 hover:bg-slate-50 rounded-md text-slate-600 transition">120</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-md text-slate-600 hover:bg-slate-50 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include 'components/footer.php'; ?>