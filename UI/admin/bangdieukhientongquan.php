<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

/* Bật debug (ổn định rồi thì tắt) */
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* Kiểm tra đăng nhập & đúng vai trò admin */
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: /webthitracnghiem/UI/login.php");
    exit();
}

/* 1. Cấu hình trang */
$title = "Bảng Điều Khiển Tổng Quan - Hệ Thống Thi Trực Tuyến";
$active_menu = "dashboard";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* ============================= */
/* 1. Tổng số thí sinh */
/* ============================= */
$stmt = $conn->query("
    SELECT COUNT(*) 
    FROM nguoi_dung nd
    JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
    WHERE vt.ten_vai_tro = 'thi_sinh'
");
$tong_thi_sinh = $stmt->fetchColumn();

/* ============================= */
/* 2. Tổng số giảng viên */
/* ============================= */
$stmt = $conn->query("
    SELECT COUNT(*) 
    FROM nguoi_dung nd
    JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
    WHERE vt.ten_vai_tro = 'giang_vien'
");
$tong_giang_vien = $stmt->fetchColumn();

/* ============================= */
/* 3. Tổng số đề thi */
/* ============================= */
$stmt = $conn->query("SELECT COUNT(*) FROM de_thi");
$tong_de_thi = $stmt->fetchColumn();

/* ============================= */
/* 4. Ca thi đang diễn ra */
/* ============================= */
$stmt = $conn->query("
    SELECT COUNT(*) 
    FROM ca_thi
    WHERE NOW() BETWEEN thoi_gian_bat_dau AND thoi_gian_ket_thuc
");
$ca_thi_dang_dien_ra = $stmt->fetchColumn();

/* ============================= */
/* 5. Tổng bài làm đã nộp */
/* ============================= */
$stmt = $conn->query("
    SELECT COUNT(*) 
    FROM bai_lam
    WHERE trang_thai IN ('da_nop','da_cham')
");
$tong_bai_lam = $stmt->fetchColumn();

/* ============================= */
/* 6. Tổng câu hỏi */
/* ============================= */
$stmt = $conn->query("SELECT COUNT(*) FROM cau_hoi");
$tong_cau_hoi = $stmt->fetchColumn();

$stats = [
    [
        'title' => 'Thí sinh',
        'value' => number_format($tong_thi_sinh),
        'color' => 'blue',
        'icon'  => 'groups',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Giảng viên',
        'value' => number_format($tong_giang_vien),
        'color' => 'green',
        'icon'  => 'school',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Đề thi',
        'value' => number_format($tong_de_thi),
        'color' => 'orange',
        'icon'  => 'description',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Ca thi đang diễn ra',
        'value' => $ca_thi_dang_dien_ra,
        'color' => 'red',
        'icon'  => 'schedule',
        'badge' => $ca_thi_dang_dien_ra > 0 ? 'LIVE' : null,
        'badge_color' => 'bg-red-100 text-red-600'
    ],
    [
        'title' => 'Bài làm đã nộp',
        'value' => number_format($tong_bai_lam),
        'color' => 'purple',
        'icon'  => 'assignment_turned_in',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Ngân hàng câu hỏi',
        'value' => number_format($tong_cau_hoi),
        'color' => 'dark',
        'icon'  => 'quiz',
        'badge' => null,
        'badge_color' => null
    ]
];

/* ============================= */
/*  BẢNG CA THI GẦN NHẤT */
/* ============================= */

$stmt = $conn->query("
    SELECT 
        ct.ma_ca_thi,
        dt.tieu_de,
        ct.thoi_gian_bat_dau,
        ct.thoi_gian_ket_thuc,
        (
            SELECT COUNT(*) 
            FROM dang_ky_thi dk 
            WHERE dk.ma_ca_thi = ct.ma_ca_thi
        ) AS so_luong_thi_sinh
    FROM ca_thi ct
    JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
    ORDER BY ct.thoi_gian_bat_dau DESC
    LIMIT 5
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$active_exams = [];

foreach ($rows as $row) {

    $now = time();
    $start = strtotime($row['thoi_gian_bat_dau']);
    $end = strtotime($row['thoi_gian_ket_thuc']);

    $status = 'Đã kết thúc';
    $status_bg = 'bg-slate-100';
    $status_text = 'text-slate-600';

    if ($now >= $start && $now <= $end) {
        $status = 'Đang thi';
        $status_bg = 'bg-green-100';
        $status_text = 'text-green-700';
    } elseif ($now < $start) {
        $status = 'Sắp tới';
        $status_bg = 'bg-blue-100';
        $status_text = 'text-blue-700';
    }

    $active_exams[] = [
        'name' => htmlspecialchars($row['tieu_de']),
        'desc' => 'Ca thi #' . $row['ma_ca_thi'],
        'time' => date('H:i d/m/Y', $start) . ' - ' . date('H:i d/m/Y', $end),
        'candidates' => $row['so_luong_thi_sinh'],
        'status' => $status,
        'status_bg' => $status_bg,
        'status_text' => $status_text,
    ];
}

/* Nhật ký demo */
$recent_logs = [
    ['icon' => 'login', 'color' => 'blue', 'title' => 'Đăng nhập thành công', 'desc' => 'Admin vừa đăng nhập', 'time' => 'VỪA XONG', 'has_line' => true],
    ['icon' => 'add_task', 'color' => 'orange', 'title' => 'Tạo đề thi mới', 'desc' => 'Giảng viên đã tạo đề mới', 'time' => '10 PHÚT TRƯỚC', 'has_line' => true],
    ['icon' => 'backup', 'color' => 'green', 'title' => 'Sao lưu định kỳ', 'desc' => 'Hệ thống đã sao lưu dữ liệu', 'time' => '1 GIỜ TRƯỚC', 'has_line' => false],
];

/* Thao tác nhanh */
$quick_actions = [
    ['icon' => 'person_add', 'label' => 'Thêm thí sinh'],
    ['icon' => 'post_add', 'label' => 'Thêm câu hỏi'],
    ['icon' => 'add_circle_outline', 'label' => 'Tạo ca thi'],
    ['icon' => 'download', 'label' => 'Xuất kết quả'],
    ['icon' => 'email', 'label' => 'Gửi thông báo'],
];

/* Nhúng giao diện */
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
            <span class="material-icons"><?php echo $stat['icon'] ?? 'bar_chart'; ?></span>
        </div>

        <?php if(!empty($stat['badge'])): ?>
        <span class="px-2 py-1 text-[11px] font-bold rounded-md <?php echo $stat['badge_color']; ?> uppercase">
            <?php echo $stat['badge']; ?>
        </span>
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