<?php
// 1. Cấu hình thông tin trang
$title = "Bảng Điều Khiển Tổng Quan - Hệ Thống Thi Trực Tuyến";
$active_menu = "dashboard";

// Dữ liệu mô phỏng cho 4 thẻ thống kê
$stats = [
    ['title' => 'Tổng số thí sinh', 'value' => '12,450', 'icon' => 'people', 'color' => 'blue', 'badge' => '+12%', 'badge_color' => 'bg-green-100 text-green-700'],
    ['title' => 'Kỳ thi đang diễn ra', 'value' => '8', 'icon' => 'event_note', 'color' => 'orange', 'badge' => 'Hôm nay', 'badge_color' => 'bg-slate-100 text-slate-600'],
    ['title' => 'Ngân hàng câu hỏi', 'value' => '45,800', 'icon' => 'quiz', 'color' => 'purple', 'badge' => '', 'badge_color' => ''],
    ['title' => 'Bài thi đã hoàn thành', 'value' => '8,230', 'icon' => 'task_alt', 'color' => 'green', 'badge' => '98%', 'badge_color' => 'bg-green-100 text-green-700']
];

// Dữ liệu mô phỏng cho Bảng Kỳ thi đang hoạt động
$active_exams = [
    ['name' => 'Toán Cao Cấp A1', 'desc' => 'Học kỳ 1 - 2023', 'time' => '08:00 - 10:00', 'candidates' => '1,240', 'status' => 'Đang thi', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'],
    ['name' => 'Lập trình Python Cơ bản', 'desc' => 'Khóa hè 2024', 'time' => '13:30 - 15:30', 'candidates' => '540', 'status' => 'Sắp tới', 'status_bg' => 'bg-blue-100', 'status_text' => 'text-blue-700'],
    ['name' => 'Tiếng Anh TOEIC Nội bộ', 'desc' => 'Đợt đánh giá 04', 'time' => 'Cả ngày', 'candidates' => '2,100', 'status' => 'Đang thi', 'status_bg' => 'bg-green-100', 'status_text' => 'text-green-700'],
    ['name' => 'Kiến trúc Máy tính', 'desc' => 'Kiểm tra giữa kỳ', 'time' => '07:30 - 08:30', 'candidates' => '120', 'status' => 'Kết thúc', 'status_bg' => 'bg-slate-100', 'status_text' => 'text-slate-600'],
];

// Dữ liệu mô phỏng cho Nhật ký hệ thống mới nhất
$recent_logs = [
    ['icon' => 'login', 'color' => 'blue', 'title' => 'Đăng nhập thành công', 'desc' => 'Admin <b>Nguyen Van A</b> vừa đăng nhập', 'time' => 'VỪA XONG', 'has_line' => true],
    ['icon' => 'add_task', 'color' => 'orange', 'title' => 'Tạo đề thi mới', 'desc' => 'Giảng viên <b>Tran Thi B</b> đã tạo đề "VLDC_01"', 'time' => '10 PHÚT TRƯỚC', 'has_line' => true],
    ['icon' => 'report_problem', 'color' => 'red', 'title' => 'Cảnh báo gian lận', 'desc' => 'Thí sinh <b>Hoang C</b> mất kết nối camera', 'time' => '25 PHÚT TRƯỚC', 'has_line' => true],
    ['icon' => 'backup', 'color' => 'green', 'title' => 'Sao lưu định kỳ', 'desc' => 'Hệ thống đã tự động sao lưu dữ liệu', 'time' => '1 GIỜ TRƯỚC', 'has_line' => false],
];

// Dữ liệu thao tác nhanh
$quick_actions = [
    ['icon' => 'person_add', 'label' => 'Thêm thí sinh'],
    ['icon' => 'post_add', 'label' => 'Thêm câu hỏi'],
    ['icon' => 'add_circle_outline', 'label' => 'Tạo kỳ thi'],
    ['icon' => 'download', 'label' => 'Xuất kết quả'],
    ['icon' => 'monitor_heart', 'label' => 'Trạng thái SV'],
    ['icon' => 'email', 'label' => 'Gửi thông báo'],
];

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800">Bảng Điều Khiển Tổng Quan</h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:ring-1 focus:ring-[#254ada] focus:outline-none w-64 transition">
            </div>
            <button class="relative text-slate-500 hover:text-[#254ada] transition">
                <span class="material-icons">notifications</span>
                <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
            </button>
            <button class="text-slate-500 hover:text-[#254ada] transition">
                <span class="material-icons">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php foreach($stats as $stat): ?>
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-lg bg-<?php echo $stat['color']; ?>-50 text-<?php echo $stat['color']; ?>-600 flex items-center justify-center">
                        <span class="material-icons"><?php echo $stat['icon']; ?></span>
                    </div>
                    <?php if($stat['badge']): ?>
                    <span class="px-2 py-1 text-[11px] font-bold rounded-md <?php echo $stat['badge_color']; ?> uppercase"><?php echo $stat['badge']; ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium"><?php echo $stat['title']; ?></p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1"><?php echo $stat['value']; ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Kỳ thi đang hoạt động</h3>
                    <a href="#" class="text-sm text-[#1e3bb3] font-medium hover:underline">Xem tất cả</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white text-[11px] text-slate-500 uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-4">Tên kỳ thi</th>
                                <th class="px-5 py-4">Thời gian</th>
                                <th class="px-5 py-4">Thí sinh</th>
                                <th class="px-5 py-4 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($active_exams as $exam): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800"><?php echo $exam['name']; ?></div>
                                    <div class="text-[12px] text-slate-400 mt-0.5"><?php echo $exam['desc']; ?></div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-[13px]"><?php echo $exam['time']; ?></td>
                                <td class="px-5 py-4 text-slate-600 font-medium text-[13px]"><?php echo $exam['candidates']; ?></td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?php echo $exam['status_bg']; ?> <?php echo $exam['status_text']; ?>"><?php echo $exam['status']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col p-6">
                <h3 class="font-bold text-slate-800 mb-6">Nhật ký hệ thống mới nhất</h3>
                <div class="flex-1">
                    <?php foreach($recent_logs as $log): ?>
                    <div class="flex gap-4 mb-6 relative">
                        <?php if($log['has_line']): ?>
                        <div class="absolute top-10 left-[19px] bottom-[-24px] w-[2px] bg-slate-100"></div>
                        <?php endif; ?>
                        
                        <div class="relative z-10 w-10 h-10 rounded-full bg-<?php echo $log['color']; ?>-50 text-<?php echo $log['color']; ?>-500 flex items-center justify-center shrink-0 border-2 border-white">
                            <span class="material-icons text-[20px]"><?php echo $log['icon']; ?></span>
                        </div>
                        
                        <div class="pt-1">
                            <h4 class="text-[14px] font-semibold text-slate-800 leading-none"><?php echo $log['title']; ?></h4>
                            <p class="text-[12px] text-slate-500 mt-1.5"><?php echo $log['desc']; ?></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1.5 uppercase tracking-wide"><?php echo $log['time']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>

        <div>
            <h3 class="font-bold text-slate-800 mb-4">Thao tác nhanh</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach($quick_actions as $action): ?>
                <button class="bg-white p-4 border border-slate-200 rounded-xl hover:border-[#1e3bb3] hover:shadow-md transition group flex flex-col items-center justify-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 group-hover:bg-[#1e3bb3] group-hover:text-white text-slate-500 flex items-center justify-center transition">
                        <span class="material-icons text-[20px]"><?php echo $action['icon']; ?></span>
                    </div>
                    <span class="text-[13px] font-medium text-slate-600 group-hover:text-[#1e3bb3] transition"><?php echo $action['label']; ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</main>

<?php 
// 3. Nhúng Footer
include 'components/footer.php'; 
?>