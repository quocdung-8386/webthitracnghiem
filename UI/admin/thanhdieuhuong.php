<?php
// 1. Cấu hình thông tin trang
$title = "Trung tâm Điều phối Phiên thi - Hệ Thống Thi Trực Tuyến";
$active_menu = "nav_bar"; // Làm sáng menu "Thanh điều hướng" theo đúng ảnh chụp

// Dữ liệu mô phỏng cho 6 phòng thi
$rooms = [
    [
        'name' => 'Phòng thi 01', 'subject' => 'LẬP TRÌNH JAVA',
        'students' => '42/45', 'time' => '15:30',
        'status' => 'CẢNH BÁO', 'status_icon' => 'warning', 'type' => 'warning',
        'alert_icon' => 'person_off', 'alert_text' => '02 Thí sinh mất kết nối > 5 phút'
    ],
    [
        'name' => 'Phòng thi 02', 'subject' => 'CƠ SỞ DỮ LIỆU',
        'students' => '30/30', 'time' => '45:12',
        'status' => 'ĐANG DIỄN RA', 'status_icon' => '', 'type' => 'normal',
        'alert_icon' => 'check_circle', 'alert_text' => 'Không có cảnh báo hoạt động'
    ],
    [
        'name' => 'Phòng thi 03', 'subject' => 'KINH TẾ VĨ MÔ',
        'students' => '118/120', 'time' => '03:45',
        'status' => 'SẮP KẾT THÚC', 'status_icon' => '', 'type' => 'ending',
        'alert_icon' => 'assignment_turned_in', 'alert_text' => '112 thí sinh đã nộp bài'
    ],
    [
        'name' => 'Phòng thi 04', 'subject' => 'TOÁN RỜI RẠC',
        'students' => '25/25', 'time' => '82:20',
        'status' => 'ĐANG DIỄN RA', 'status_icon' => '', 'type' => 'normal',
        'alert_icon' => 'info', 'alert_text' => 'Đã bắt đầu được 7 phút'
    ],
    [
        'name' => 'Phòng thi 05', 'subject' => 'MẠNG MÁY TÍNH',
        'students' => '55/56', 'time' => '22:05',
        'status' => 'VI PHẠM', 'status_icon' => 'error', 'type' => 'danger',
        'alert_icon' => 'visibility_off', 'alert_text' => '01 Thí sinh thoát trình duyệt'
    ],
    [
        'name' => 'Phòng thi 06', 'subject' => 'TRIẾT HỌC',
        'students' => '18/20', 'time' => '110:00',
        'status' => 'ĐANG DIỄN RA', 'status_icon' => '', 'type' => 'normal',
        'alert_icon' => 'lock', 'alert_text' => 'Mã phòng: TRIET24A'
    ]
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">GIÁM SÁT THI TRỰC TUYẾN</h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm phòng thi..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 mb-1">Trung tâm Điều phối Phiên thi</h2>
                <p class="text-sm text-slate-500">Hiện có 6 phòng thi đang diễn ra đồng thời</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-full text-sm font-semibold border border-green-100">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div> Hệ thống ổn định
                </div>
                <button class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-full flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[18px]">refresh</span> Làm mới
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-6">
            <?php foreach($rooms as $room): ?>
            <?php
                // Cài đặt style linh hoạt dựa trên Type của phòng
                $card_class = "bg-white border-slate-200 hover:border-slate-300";
                $badge_class = "bg-green-50 text-green-600 border-green-100";
                $time_color = "text-slate-800";
                $alert_color = "text-slate-500";
                
                if($room['type'] == 'warning') {
                    $card_class = "bg-red-50/30 border-red-200 shadow-sm relative";
                    $badge_class = "bg-red-50 text-red-500 border-red-100";
                    $time_color = "text-red-500";
                    $alert_color = "text-red-500 font-medium";
                } elseif($room['type'] == 'danger') {
                    $card_class = "bg-red-50/50 border-red-300 shadow-sm relative";
                    $badge_class = "bg-red-500 text-white border-red-600 shadow-sm";
                    $time_color = "text-slate-800";
                    $alert_color = "text-red-600 font-bold";
                } elseif($room['type'] == 'ending') {
                    $badge_class = "bg-orange-50 text-orange-600 border-orange-100";
                    $time_color = "text-orange-500";
                }
            ?>

            <div class="rounded-2xl border <?php echo $card_class; ?> p-6 flex flex-col transition-all duration-200">
                
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg"><?php echo $room['name']; ?></h3>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-1 border-b border-slate-200 pb-2 w-fit">MÔN: <?php echo $room['subject']; ?></p>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded flex items-center gap-1 border <?php echo $badge_class; ?>">
                        <?php if($room['status_icon']): ?>
                            <span class="material-icons text-[14px]"><?php echo $room['status_icon']; ?></span>
                        <?php endif; ?>
                        <?php echo $room['status']; ?>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white border border-slate-100 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">THÍ SINH</span>
                        <span class="text-2xl font-black text-slate-800"><?php echo $room['students']; ?></span>
                    </div>
                    <div class="bg-white border border-slate-100 rounded-xl p-4 flex flex-col items-center justify-center shadow-sm">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">THỜI GIAN CÒN</span>
                        <span class="text-2xl font-black <?php echo $time_color; ?>"><?php echo $room['time']; ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[13px] mb-6 mt-auto <?php echo $alert_color; ?>">
                    <span class="material-icons text-[18px]"><?php echo $room['alert_icon']; ?></span>
                    <?php echo $room['alert_text']; ?>
                </div>

                <div class="flex gap-3">
                    <button class="flex-1 py-2.5 bg-[#1e3bb3] text-white font-semibold rounded-xl text-[14px] hover:bg-blue-800 shadow-md shadow-blue-900/20 transition-all">
                        Vào giám sát
                    </button>
                    <button class="w-12 h-[42px] bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-slate-200 transition">
                        <span class="material-icons text-[20px]">settings</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</main>

<?php include 'components/footer.php'; ?>