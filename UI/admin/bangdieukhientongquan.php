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
        'icon' => 'groups',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Giảng viên',
        'value' => number_format($tong_giang_vien),
        'color' => 'green',
        'icon' => 'school',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Đề thi',
        'value' => number_format($tong_de_thi),
        'color' => 'orange',
        'icon' => 'description',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Ca thi đang diễn ra',
        'value' => $ca_thi_dang_dien_ra,
        'color' => 'red',
        'icon' => 'schedule',
        'badge' => $ca_thi_dang_dien_ra > 0 ? 'LIVE' : null,
        'badge_color' => 'bg-red-100 text-red-600'
    ],
    [
        'title' => 'Bài làm đã nộp',
        'value' => number_format($tong_bai_lam),
        'color' => 'purple',
        'icon' => 'assignment_turned_in',
        'badge' => null,
        'badge_color' => null
    ],
    [
        'title' => 'Ngân hàng câu hỏi',
        'value' => number_format($tong_cau_hoi),
        'color' => 'dark',
        'icon' => 'quiz',
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
/* Thao tác nhanh */
$quick_actions = [
    ['icon' => 'person_add', 'label' => 'Thêm thí sinh', 'type' => 'modal', 'target' => 'addStudentModal'],
    ['icon' => 'post_add', 'label' => 'Thêm câu hỏi', 'type' => 'modal', 'target' => 'addQuestionModal'],
    ['icon' => 'add_circle_outline', 'label' => 'Tạo ca thi', 'type' => 'modal', 'target' => 'addExamSessionModal'],
    ['icon' => 'download', 'label' => 'Xuất kết quả', 'type' => 'modal', 'target' => 'exportModal'],
    ['icon' => 'email', 'label' => 'Gửi thông báo', 'type' => 'modal', 'target' => 'notifyModal'],
];

/* Nhúng giao diện */
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Bảng Điều Khiển Tổng Quan</h2>

        <div class="flex items-center gap-5">
            <form action="search_results.php" method="GET" class="relative">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="q" id="quickSearch" placeholder="Tìm kiếm nhanh..." required
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-700 dark:text-white border border-slate-200 dark:border-slate-600 rounded-full text-sm focus:ring-1 focus:ring-[#254ada] focus:outline-none w-64 transition">
            </form>

            <div class="relative">
                <button id="notifButton" type="button"
                    class="relative text-slate-500 hover:text-[#254ada] dark:text-slate-300 dark:hover:text-white transition focus:outline-none">
                    <span class="material-icons">notifications</span>
                    <span
                        class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                </button>

                <div id="notifDropdown"
                    class="hidden absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden">
                    <div
                        class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                        <a href="#" class="text-[11px] text-[#254ada] hover:underline">Đánh dấu đã đọc</a>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <a href="#"
                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-200"><span class="font-semibold">Nguyễn
                                    Văn A</span> vừa nộp bài thi.</p>
                            <span class="text-[11px] text-slate-400">5 phút trước</span>
                        </a>
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-200">Ca thi <span
                                    class="font-semibold">#1024</span> sắp bắt đầu.</p>
                            <span class="text-[11px] text-slate-400">30 phút trước</span>
                        </a>
                    </div>
                    <a href="#"
                        class="block px-4 py-2 text-center text-sm text-[#254ada] font-medium bg-slate-50 dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 transition">Xem
                        tất cả</a>
                </div>
            </div>

            <button id="darkModeToggle"
                class="text-slate-500 hover:text-[#254ada] dark:text-slate-300 dark:hover:text-white transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div
        class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php foreach ($stats as $stat): ?>
                <div
                    class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between hover:shadow-md transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-<?php echo $stat['color']; ?>-50 dark:bg-<?php echo $stat['color']; ?>-900/30 text-<?php echo $stat['color']; ?>-600 dark:text-<?php echo $stat['color']; ?>-400 flex items-center justify-center">
                            <span class="material-icons"><?php echo $stat['icon'] ?? 'bar_chart'; ?></span>
                        </div>

                        <?php if (!empty($stat['badge'])): ?>
                            <span
                                class="px-2 py-1 text-[11px] font-bold rounded-md <?php echo $stat['badge_color']; ?> dark:bg-red-900/30 dark:text-red-400 uppercase">
                                <?php echo $stat['badge']; ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium"><?php echo $stat['title']; ?></p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1"><?php echo $stat['value']; ?>
                        </h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div
                class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white">Kỳ thi đang hoạt động</h3>
                    <a href="#" class="text-sm text-[#1e3bb3] dark:text-[#4b6bfb] font-medium hover:underline">Xem tất
                        cả</a>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-white dark:bg-slate-800 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-5 py-4">Tên kỳ thi</th>
                                <th class="px-5 py-4">Thời gian</th>
                                <th class="px-5 py-4">Thí sinh</th>
                                <th class="px-5 py-4 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach ($active_exams as $exam): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-800 dark:text-white">
                                            <?php echo $exam['name']; ?>
                                        </div>
                                        <div class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5">
                                            <?php echo $exam['desc']; ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-[13px]">
                                        <?php echo $exam['time']; ?>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300 font-medium text-[13px]">
                                        <?php echo $exam['candidates']; ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span
                                            class="px-2.5 py-1 text-[11px] font-semibold rounded-full <?php echo $exam['status_bg']; ?> <?php echo $exam['status_text']; ?> dark:bg-opacity-20"><?php echo $exam['status']; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col p-6 transition-colors">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Nhật ký hệ thống mới nhất</h3>
                <div class="flex-1">
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="flex gap-4 mb-6 relative">
                            <?php if ($log['has_line']): ?>
                                <div class="absolute top-10 left-[19px] bottom-[-24px] w-[2px] bg-slate-100 dark:bg-slate-700">
                                </div>
                            <?php endif; ?>

                            <div
                                class="relative z-10 w-10 h-10 rounded-full bg-<?php echo $log['color']; ?>-50 dark:bg-<?php echo $log['color']; ?>-900/30 text-<?php echo $log['color']; ?>-500 flex items-center justify-center shrink-0 border-2 border-white dark:border-slate-800">
                                <span class="material-icons text-[20px]"><?php echo $log['icon']; ?></span>
                            </div>

                            <div class="pt-1">
                                <h4 class="text-[14px] font-semibold text-slate-800 dark:text-white leading-none">
                                    <?php echo $log['title']; ?>
                                </h4>
                                <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1.5"><?php echo $log['desc']; ?>
                                </p>
                                <p
                                    class="text-[10px] font-bold text-slate-400 dark:text-slate-500 mt-1.5 uppercase tracking-wide">
                                    <?php echo $log['time']; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <div>
            <h3 class="font-bold text-slate-800 dark:text-white mb-4 transition-colors">Thao tác nhanh</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach ($quick_actions as $action): ?>

                    <?php if (isset($action['type']) && $action['type'] === 'modal'): ?>
                        <button onclick="openModal('<?php echo $action['target']; ?>')"
                            class="bg-white dark:bg-slate-800 p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-[#1e3bb3] dark:hover:border-[#4b6bfb] hover:shadow-md transition group flex flex-col items-center justify-center gap-3 w-full cursor-pointer focus:outline-none">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-700 group-hover:bg-[#1e3bb3] dark:group-hover:bg-[#4b6bfb] text-slate-500 dark:text-slate-400 group-hover:text-white flex items-center justify-center transition">
                                <span class="material-icons text-[20px]"><?php echo $action['icon']; ?></span>
                            </div>
                            <span
                                class="text-[13px] font-medium text-slate-600 dark:text-slate-300 group-hover:text-[#1e3bb3] dark:group-hover:text-[#4b6bfb] transition text-center">
                                <?php echo $action['label']; ?>
                            </span>
                        </button>
                    <?php else: ?>
                        <a href="<?php echo $action['url']; ?>"
                            class="bg-white dark:bg-slate-800 p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-[#1e3bb3] dark:hover:border-[#4b6bfb] hover:shadow-md transition group flex flex-col items-center justify-center gap-3 w-full cursor-pointer">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-700 group-hover:bg-[#1e3bb3] dark:group-hover:bg-[#4b6bfb] text-slate-500 dark:text-slate-400 group-hover:text-white flex items-center justify-center transition">
                                <span class="material-icons text-[20px]"><?php echo $action['icon']; ?></span>
                            </div>
                            <span
                                class="text-[13px] font-medium text-slate-600 dark:text-slate-300 group-hover:text-[#1e3bb3] dark:group-hover:text-[#4b6bfb] transition text-center">
                                <?php echo $action['label']; ?>
                            </span>
                        </a>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
        </div>

    </div>
    <div id="exportModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">
            <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">download</span> Xuất kết quả thi
                </h3>
                <button onclick="closeModal('exportModal')" class="text-slate-400 hover:text-red-500 transition"><span
                        class="material-icons">close</span></button>
            </div>
            <div class="p-5">
                <form id="formExport" action="#" method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Chọn Ca Thi /
                            Kỳ Thi</label>
                        <select
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#254ada] focus:outline-none">
                            <option>Ca thi #1024 - Lập trình Web</option>
                            <option>Ca thi #1025 - Cơ sở dữ liệu</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Định dạng
                            file</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300"><input
                                    type="radio" name="format" value="excel" checked class="text-[#254ada]"> Excel
                                (.xlsx)</label>
                            <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300"><input
                                    type="radio" name="format" value="pdf" class="text-[#254ada]"> PDF (.pdf)</label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('exportModal')"
                            class="px-4 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg font-medium transition">Hủy</button>
                        <button type="submit"
                            class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg font-medium transition flex items-center gap-2">
                            <span class="material-icons text-[18px]">file_download</span> Tải xuống
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="notifyModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">
            <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-icons text-orange-500">campaign</span> Gửi thông báo hệ thống
                </h3>
                <button onclick="closeModal('notifyModal')" class="text-slate-400 hover:text-red-500 transition"><span
                        class="material-icons">close</span></button>
            </div>
            <div class="p-5">
                <form id="formNotify" action="#" method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Đối tượng
                            nhận</label>
                        <select
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#254ada] focus:outline-none">
                            <option>Tất cả thí sinh</option>
                            <option>Thí sinh trong ca thi đang diễn ra</option>
                            <option>Tất cả giảng viên</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tiêu đề thông
                            báo</label>
                        <input type="text" placeholder="Nhập tiêu đề..."
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#254ada] focus:outline-none">
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nội
                            dung</label>
                        <textarea rows="4" placeholder="Nhập nội dung thông báo..."
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-[#254ada] focus:outline-none resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal('notifyModal')"
                            class="px-4 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg font-medium transition">Hủy</button>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition flex items-center gap-2">
                            <span class="material-icons text-[18px]">send</span> Gửi ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addStudentModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">

            <div class="flex justify-between items-start p-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center shrink-0">
                        <span class="material-icons">person_add</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white leading-tight">Thêm thí sinh mới
                        </h3>
                        <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5">Nhập thông tin cá nhân của thí
                            sinh vào hệ thống</p>
                    </div>
                </div>
                <button onclick="closeModal('addStudentModal')"
                    class="text-slate-400 hover:text-red-500 transition focus:outline-none">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="p-6">
                <form id="formAddStudent" action="#" method="POST">

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Họ và
                            tên <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="VD: Nguyễn Văn A" required
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mã
                                số thí sinh <span class="text-red-500">*</span></label>
                            <input type="text" placeholder="VD: SV123456" required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                        <div>
                            <label
                                class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                sinh</label>
                            <input type="date"
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Lớp /
                            Đơn vị <span class="text-red-500">*</span></label>
                        <select required
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                            <option value="">Chọn lớp / phòng ban</option>
                            <option value="IT01">Kỹ thuật phần mềm 01</option>
                            <option value="IT02">Kỹ thuật phần mềm 02</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email
                            liên hệ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">email</span>
                            <input type="email" placeholder="example@domain.com" required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 mb-2 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-200 dark:border-slate-600">
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">send</span>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800 dark:text-white">Gửi thông tin đăng
                                    nhập</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Tự động gửi tài khoản & mật
                                    khẩu qua email</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-[#254ada]">
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" onclick="closeModal('addStudentModal')"
                            class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white transition">Hủy
                            bỏ</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                            <span class="material-icons text-[18px]">save</span> Lưu thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addQuestionModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] max-h-[95vh] flex flex-col overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">

            <div class="flex justify-between items-start p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex gap-4 items-center">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-600">
                        <span class="material-icons text-[20px]">post_add</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white leading-tight">Thêm câu hỏi mới</h3>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-0.5">Ngân hàng câu hỏi</p>
                    </div>
                </div>
                <button onclick="closeModal('addQuestionModal')"
                    class="text-slate-400 hover:text-red-500 transition focus:outline-none">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <form id="formAddQuestion" action="#" method="POST" class="flex flex-col flex-1 overflow-hidden">

                <div class="p-5 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Danh
                                mục môn học</label>
                            <select
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                                <option value="toan_cc">Toán Cao Cấp A1</option>
                                <option value="tin_dc">Tin học đại cương</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mức
                                độ khó</label>
                            <select
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                                <option value="easy">Dễ (Easy)</option>
                                <option value="medium">Trung bình (Medium)</option>
                                <option value="hard">Khó (Hard)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nội
                            dung câu hỏi</label>
                        <div
                            class="border border-slate-300 dark:border-slate-600 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-[#254ada] transition">
                            <div
                                class="bg-slate-50 dark:bg-slate-700 border-b border-slate-300 dark:border-slate-600 px-2 py-1.5 flex gap-1 overflow-x-auto">
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded font-bold text-sm">B</button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded italic text-sm">I</button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded underline text-sm">U</button>
                                <div class="w-px bg-slate-300 dark:bg-slate-600 my-1 mx-1"></div>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded"><span
                                        class="material-icons text-[16px]">image</span></button>
                                <button type="button"
                                    class="p-1 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 rounded"><span
                                        class="material-icons text-[16px]">functions</span></button>
                            </div>
                            <textarea rows="3" placeholder="Nhập câu hỏi..."
                                class="w-full bg-white dark:bg-slate-800 text-slate-800 dark:text-white px-3.5 py-2 text-sm outline-none resize-y"></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Đáp án
                            (Chọn nút tròn nếu là đáp án đúng)</label>
                        <div class="space-y-2.5">
                            <?php
                            $options = ['A', 'B', 'C', 'D'];
                            foreach ($options as $index => $opt):
                                ?>
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_answer" value="<?php echo $opt; ?>" <?php echo $opt === 'B' ? 'checked' : ''; ?>
                                        class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                    <div
                                        class="flex-1 flex items-center border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 rounded-lg overflow-hidden focus-within:border-[#254ada] focus-within:ring-1 focus-within:ring-[#254ada] transition <?php echo $opt === 'B' ? 'border-[#254ada] ring-1 ring-[#254ada] bg-blue-50/50 dark:bg-blue-900/20' : ''; ?>">
                                        <div
                                            class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-600 font-bold text-[13px] text-slate-500 dark:text-slate-400 shrink-0 <?php echo $opt === 'B' ? 'text-[#254ada]' : ''; ?>">
                                            <?php echo $opt; ?>
                                        </div>
                                        <input type="text" placeholder="Nhập đáp án <?php echo $opt; ?>..."
                                            class="flex-1 bg-transparent px-3 py-1.5 text-slate-800 dark:text-white outline-none text-sm">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giải
                            thích (Tùy chọn)</label>
                        <textarea rows="2" placeholder="Giải thích đáp án..."
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition resize-y"></textarea>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 shrink-0">
                    <button type="button" onclick="closeModal('addQuestionModal')"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                        bỏ</button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                        <span class="material-icons text-[18px]">save</span> Lưu câu hỏi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="addExamSessionModal"
        class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div id="examModalBox"
            class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] max-h-[95vh] flex flex-col overflow-hidden transform transition-all duration-300 border border-slate-200 dark:border-slate-700">

            <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div>
                    <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2 leading-tight">
                        <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px]">add_circle</span>
                        Tạo ca thi mới
                    </h3>
                </div>
                <button onclick="closeModal('addExamSessionModal')"
                    class="text-slate-400 hover:text-red-500 transition focus:outline-none">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div
                class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 shrink-0 flex items-center justify-center">
                <div class="flex flex-col items-center relative z-10">
                    <div id="step1Icon"
                        class="w-7 h-7 rounded-full bg-[#254ada] text-white flex items-center justify-center font-bold text-xs shadow-md ring-4 ring-blue-50 dark:ring-blue-900/30 transition-all">
                        1</div>
                </div>
                <div id="line1to2" class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-2 transition-all"></div>
                <div class="flex flex-col items-center relative z-10">
                    <div id="step2Icon"
                        class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-xs transition-all">
                        2</div>
                </div>
                <div id="line2to3" class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-2 transition-all"></div>
                <div class="flex flex-col items-center relative z-10">
                    <div id="step3Icon"
                        class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-xs transition-all">
                        3</div>
                </div>
            </div>

            <form id="formAddExamSession" action="#" method="POST" class="flex flex-col flex-1 overflow-hidden">

                <div id="step1Content" class="p-5 overflow-y-auto custom-scrollbar flex-1 block">
                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tên ca
                            thi <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="VD: Kiểm tra giữa kỳ môn Giải tích" required
                            class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label
                                class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                thi <span class="text-red-500">*</span></label>
                            <input type="date" required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ
                                bắt đầu <span class="text-red-500">*</span></label>
                            <input type="time" required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <label
                                class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Thời
                                lượng (phút) <span class="text-red-500">*</span></label>
                            <input type="number" value="60" min="1" required
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mật
                                khẩu (Tùy chọn)</label>
                            <input type="password" placeholder="Bỏ trống nếu không cần"
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                    </div>
                </div>

                <div id="step2Content"
                    class="p-5 overflow-y-auto custom-scrollbar flex-1 hidden bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="flex gap-2 mb-4">
                        <div class="relative flex-1">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                            <input type="text" placeholder="Tìm tên, mã đề..."
                                class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                        </div>
                        <select
                            class="border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-[#254ada] outline-none max-w-[120px]">
                            <option>Tất cả môn</option>
                        </select>
                    </div>

                    <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-3">
                        Danh sách đề thi khả dụng (24)</p>
                    <div class="space-y-3">
                        <label
                            class="flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:border-[#254ada] transition-all has-[:checked]:border-[#254ada] has-[:checked]:ring-1 has-[:checked]:ring-[#254ada]">
                            <div class="mt-0.5"><input type="radio" name="selected_exam" value="1" checked
                                    class="w-4 h-4 text-[#254ada]"></div>
                            <div class="flex-1">
                                <h4 class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight">Đề kiểm
                                    tra giữa kỳ 1 - Toán 12</h4>
                                <p class="text-[11px] text-slate-500 mt-1">Mã đề: TOAN12-GK1-001</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="step3Content"
                    class="p-6 overflow-y-auto custom-scrollbar flex-1 hidden bg-slate-50/50 dark:bg-slate-900/50">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full min-h-[400px]">

                        <div
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex flex-col h-full">
                            <h4
                                class="text-[14px] font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                                <span
                                    class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[18px]">domain</span>
                                Chọn theo Lớp / Phòng ban
                            </h4>

                            <div class="relative mb-4">
                                <span
                                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                                <input type="text" placeholder="Tìm kiếm lớp..."
                                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                            </div>

                            <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-1">

                                <div>
                                    <label
                                        class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer transition">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" checked
                                                class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                            <span class="text-[13px] font-semibold text-slate-800 dark:text-white">Khoa
                                                Công nghệ thông tin</span>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-[11px] font-bold text-slate-500">450</span>
                                    </label>

                                    <div
                                        class="pl-8 mt-1 space-y-1 border-l border-slate-200 dark:border-slate-700 ml-4">
                                        <label
                                            class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer transition">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" checked
                                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                                <span class="text-[13px] text-slate-600 dark:text-slate-300">Lớp
                                                    K65-CNTT1</span>
                                            </div>
                                            <span class="text-[11px] text-slate-400">85 SV</span>
                                        </label>
                                        <label
                                            class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer transition">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox"
                                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                                <span class="text-[13px] text-slate-600 dark:text-slate-300">Lớp
                                                    K65-CNTT2</span>
                                            </div>
                                            <span class="text-[11px] text-slate-400">92 SV</span>
                                        </label>
                                    </div>
                                </div>

                                <label
                                    class="flex items-center justify-between p-2 mt-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer transition">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox"
                                            class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                        <span class="text-[13px] font-semibold text-slate-800 dark:text-white">Khoa Cơ
                                            khí</span>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-[11px] font-bold text-slate-500">320</span>
                                </label>

                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex flex-col h-full">
                            <div class="flex justify-between items-center mb-4">
                                <h4
                                    class="text-[14px] font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                    <span
                                        class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[18px]">person_add</span>
                                    Chọn Thí sinh lẻ
                                </h4>
                                <button type="button"
                                    class="text-[12px] font-semibold text-[#254ada] hover:underline">Chọn tất
                                    cả</button>
                            </div>

                            <div class="relative mb-4">
                                <span
                                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                                <input type="text" placeholder="Tìm tên, MSSV, Email..."
                                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
                            </div>

                            <div class="flex-1 flex flex-col overflow-hidden">
                                <div
                                    class="flex text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide pb-2 border-b border-slate-100 dark:border-slate-700 mb-2 px-2">
                                    <div class="w-8"></div>
                                    <div class="flex-1">Họ tên & MSSV</div>
                                    <div class="w-24 text-right">Lớp</div>
                                </div>

                                <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 space-y-1">
                                    <label
                                        class="flex items-center p-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg cursor-pointer transition">
                                        <div class="w-8"><input type="checkbox"
                                                class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-[13px] font-semibold text-slate-800 dark:text-white leading-tight">
                                                Nguyễn Văn An</p>
                                            <p class="text-[11px] text-slate-500">SV2023001</p>
                                        </div>
                                        <div class="w-24 text-right text-[12px] text-slate-600 dark:text-slate-400">
                                            K65-CNTT1</div>
                                    </label>
                                    <label
                                        class="flex items-center p-2 bg-blue-50/50 dark:bg-blue-900/20 rounded-lg cursor-pointer transition">
                                        <div class="w-8"><input type="checkbox" checked
                                                class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-[13px] font-semibold text-slate-800 dark:text-white leading-tight">
                                                Trần Thị Bích</p>
                                            <p class="text-[11px] text-slate-500">SV2023002</p>
                                        </div>
                                        <div class="w-24 text-right text-[12px] text-slate-600 dark:text-slate-400">
                                            K65-QTKD1</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-6 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-xl p-4 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800/50 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center shrink-0">
                                <span class="material-icons">groups</span>
                            </div>
                            <div>
                                <h4 class="text-[14px] font-bold text-slate-800 dark:text-white">Tổng cộng thí sinh đã
                                    chọn</h4>
                                <p class="text-[12px] text-[#254ada] dark:text-[#4b6bfb] mt-0.5">Dựa trên các lớp và thí
                                    sinh lẻ đã tích chọn</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-black text-[#254ada] dark:text-[#4b6bfb]">86</span>
                            <span class="text-[13px] font-medium text-slate-500 dark:text-slate-400 ml-1">thí
                                sinh</span>
                        </div>
                    </div>

                </div>

                <div
                    class="flex items-center justify-between p-5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 shrink-0">
                    <button type="button" onclick="closeModal('addExamSessionModal')"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition">Hủy
                        bỏ</button>

                    <div id="footerStep1" class="flex gap-3">
                        <button type="button" onclick="goToStep(2)"
                            class="px-5 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                            Tiếp theo <span class="material-icons text-[18px]">arrow_forward</span>
                        </button>
                    </div>

                    <div id="footerStep2" class="hidden flex gap-3">
                        <button type="button" onclick="goToStep(1)"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition flex items-center gap-1.5">
                            <span class="material-icons text-[18px]">arrow_back</span> Quay lại
                        </button>
                        <button type="button" onclick="goToStep(3)"
                            class="px-5 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                            Tiếp theo <span class="material-icons text-[18px]">arrow_forward</span>
                        </button>
                    </div>

                    <div id="footerStep3" class="hidden flex gap-3">
                        <button type="button" onclick="goToStep(2)"
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition flex items-center gap-1.5">
                            <span class="material-icons text-[18px]">arrow_back</span> Quay lại
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                            <span class="material-icons text-[18px]">check_circle</span> Hoàn tất & Tạo kỳ thi
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>

<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
            <span class="material-icons text-[16px]">close</span>
        </button>
    </div>
</template>

<?php
// 3. Nhúng Footer
include 'components/footer.php';
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* =========================================
           1. CHỨC NĂNG DARK MODE (GIAO DIỆN TỐI)
           ========================================= */
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;

        // Kiểm tra trạng thái đã lưu trong LocalStorage
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            darkModeIcon.textContent = 'light_mode'; // Đổi icon thành mặt trời
        }

        // Xử lý sự kiện click
        darkModeToggle.addEventListener('click', function () {
            if (htmlElement.classList.contains('dark')) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                darkModeIcon.textContent = 'dark_mode';
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                darkModeIcon.textContent = 'light_mode';
            }
        });

        /* =========================================
           2. CHỨC NĂNG DROPDOWN THÔNG BÁO
           ========================================= */
        const notifButton = document.getElementById('notifButton');
        const notifDropdown = document.getElementById('notifDropdown');

        // Bật/tắt dropdown khi click vào nút chuông
        notifButton.addEventListener('click', function (e) {
            e.stopPropagation(); // Ngăn sự kiện click lan ra ngoài
            notifDropdown.classList.toggle('hidden');
        });

        // Ẩn dropdown khi click ra ngoài vùng thông báo
        document.addEventListener('click', function (e) {
            if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });

        /* =========================================
           3. TÌM KIẾM NHANH (Tùy chọn AJAX / Gợi ý)
           ========================================= */
        // Hiện tại form đã dùng method GET để chuyển hướng sang trang search_results.php?q=...
        // Nếu bạn muốn tìm kiếm realtime (lọc dữ liệu tại chỗ), bạn có thể viết thêm logic ở đây.
        const searchInput = document.getElementById('quickSearch');
        searchInput.addEventListener('keyup', function (e) {
            // Ví dụ: Lọc các thẻ trên dashboard khi gõ (nếu cần)
            // const filter = e.target.value.toLowerCase();
            // console.log("Đang tìm kiếm: ", filter);
        });

    });

    // Optional: Đóng modal khi click ra ngoài vùng đen (backdrop)
    window.onclick = function (event) {
        const exportModal = document.getElementById('exportModal');
        const notifyModal = document.getElementById('notifyModal');
        const addStudentModal = document.getElementById('addStudentModal'); // Thêm dòng này

        if (event.target == exportModal) closeModal('exportModal');
        if (event.target == notifyModal) closeModal('notifyModal');
        if (event.target == addStudentModal) closeModal('addStudentModal'); // Thêm dòng này
    }

    // Đóng modal khi click ra ngoài vùng đen (backdrop)
    window.onclick = function (event) {
        const exportModal = document.getElementById('exportModal');
        const notifyModal = document.getElementById('notifyModal');
        const addStudentModal = document.getElementById('addStudentModal');
        const addQuestionModal = document.getElementById('addQuestionModal'); // Thêm dòng này

        if (event.target == exportModal) closeModal('exportModal');
        if (event.target == notifyModal) closeModal('notifyModal');
        if (event.target == addStudentModal) closeModal('addStudentModal');
        if (event.target == addQuestionModal) closeModal('addQuestionModal'); // Thêm dòng này
    }

    // Đóng modal khi click ra ngoài vùng đen (backdrop)
    // Hàm xử lý chuyển bước trong Modal "Tạo ca thi mới"
    function goToStep(step) {
        const modalBox = document.getElementById('examModalBox'); // Để thay đổi kích thước

        const stepContents = [
            document.getElementById('step1Content'),
            document.getElementById('step2Content'),
            document.getElementById('step3Content')
        ];

        const footers = [
            document.getElementById('footerStep1'),
            document.getElementById('footerStep2'),
            document.getElementById('footerStep3')
        ];

        const stepIcons = [
            document.getElementById('step1Icon'),
            document.getElementById('step2Icon'),
            document.getElementById('step3Icon')
        ];

        const lines = [
            document.getElementById('line1to2'),
            document.getElementById('line2to3')
        ];

        // Thay đổi kích thước Modal tùy theo bước
        if (step === 3) {
            modalBox.classList.remove('max-w-[500px]');
            modalBox.classList.add('max-w-[900px]'); // Mở rộng Modal cho 2 cột
        } else {
            modalBox.classList.remove('max-w-[900px]');
            modalBox.classList.add('max-w-[500px]'); // Thu nhỏ lại cho Bước 1 & 2
        }

        // Ẩn hiện nội dung và footer
        for (let i = 0; i < 3; i++) {
            if (i + 1 === step) {
                stepContents[i].classList.remove('hidden');
                stepContents[i].classList.add('block');
                footers[i].classList.remove('hidden');
                footers[i].classList.add('flex');
            } else {
                stepContents[i].classList.remove('block');
                stepContents[i].classList.add('hidden');
                footers[i].classList.remove('flex');
                footers[i].classList.add('hidden');
            }
        }

        // Cập nhật giao diện Stepper (Tiến trình)
        for (let i = 0; i < 3; i++) {
            const icon = stepIcons[i];
            if (i + 1 < step) {
                // Bước đã qua: Màu xanh, dấu check
                icon.innerHTML = '<span class="material-icons text-[14px]">check</span>';
                icon.className = 'w-7 h-7 rounded-full bg-[#254ada] text-white flex items-center justify-center font-bold text-xs shadow-md ring-4 ring-blue-50 dark:ring-blue-900/30 transition-all';
            } else if (i + 1 === step) {
                // Bước hiện tại: Màu xanh, hiển thị số
                icon.innerHTML = (i + 1).toString();
                icon.className = 'w-7 h-7 rounded-full bg-[#254ada] text-white flex items-center justify-center font-bold text-xs shadow-md ring-4 ring-blue-50 dark:ring-blue-900/30 transition-all';
            } else {
                // Bước chưa tới: Màu xám, hiển thị số
                icon.innerHTML = (i + 1).toString();
                icon.className = 'w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-xs transition-all';
            }
        }

        // Cập nhật đường kẻ nối
        if (step >= 2) {
            lines[0].className = 'w-16 h-[2px] bg-[#254ada] dark:bg-[#4b6bfb] mx-2 transition-all';
        } else {
            lines[0].className = 'w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-2 transition-all';
        }

        if (step >= 3) {
            lines[1].className = 'w-16 h-[2px] bg-[#254ada] dark:bg-[#4b6bfb] mx-2 transition-all';
        } else {
            lines[1].className = 'w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-2 transition-all';
        }
    }

    // Sửa lại hàm openModal một chút để reset về Bước 1 mỗi khi mở Modal Tạo ca thi
    function openModal(modalID) {
        const modal = document.getElementById(modalID);
        if (modal) {
            modal.classList.remove('hidden');
            if (modalID === 'addExamSessionModal') {
                goToStep(1); // Luôn mở lại ở Bước 1
            }
        }
    }

    // Hàm đóng Modal
    function closeModal(modalID) {
        const modal = document.getElementById(modalID);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Optional: Đóng modal khi click ra ngoài vùng đen (backdrop)
    window.onclick = function (event) {
        const exportModal = document.getElementById('exportModal');
        const notifyModal = document.getElementById('notifyModal');
        if (event.target == exportModal) closeModal('exportModal');
        if (event.target == notifyModal) closeModal('notifyModal');
    }

    // Hàm hiển thị thông báo (Toast)
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');

        // Copy HTML từ template
        const toastNode = template.content.cloneNode(true);
        const toastEl = toastNode.querySelector('.toast-item');
        const iconEl = toastNode.querySelector('.toast-icon');

        // Cài đặt nội dung
        toastNode.querySelector('.toast-title').textContent = title;
        toastNode.querySelector('.toast-message').textContent = message;

        // Tùy chỉnh màu sắc và Icon theo loại (Thành công / Thất bại)
        if (type === 'success') {
            toastEl.classList.add('border-green-500');
            iconEl.innerHTML = '<span class="material-icons text-green-500">check_circle</span>';
        } else if (type === 'error') {
            toastEl.classList.add('border-red-500');
            iconEl.innerHTML = '<span class="material-icons text-red-500">error</span>';
        }

        // Xử lý nút đóng
        const closeBtn = toastNode.querySelector('.toast-close');
        closeBtn.onclick = function () {
            hideToast(toastEl);
        };

        // Thêm vào container
        container.appendChild(toastNode);

        // Hiệu ứng trượt vào (Animation)
        setTimeout(() => {
            toastEl.classList.remove('translate-x-full', 'opacity-0');
        }, 10);

        // Tự động đóng sau 4 giây
        setTimeout(() => {
            if (container.contains(toastEl)) hideToast(toastEl);
        }, 4000);
    }

    // Hàm ẩn thông báo mượt mà
    function hideToast(toastEl) {
        toastEl.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (toastEl.parentNode) toastEl.parentNode.removeChild(toastEl);
        }, 300); // Đợi animation CSS xong mới xóa phần tử
    }

    // Bắt sự kiện gửi form "Tạo ca thi mới"
    document.getElementById('formAddExamSession').addEventListener('submit', function (e) {
        e.preventDefault(); // Chặn hành vi load lại trang mặc định

        // Lấy nút submit để tạo hiệu ứng đang tải (Loading)
        const submitBtn = document.querySelector('#footerStep3 button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xử lý...';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

        // MÔ PHỎNG GỌI API (Đợi 1.5 giây)
        setTimeout(() => {

            // --- GIẢ LẬP KẾT QUẢ TỪ SERVER ---
            // Đổi isSuccess thành false để xem thông báo lỗi nhé!
            const isSuccess = true;

            if (isSuccess) {
                // 1. Đóng Modal
                closeModal('addExamSessionModal');

                // 2. Hiện thông báo Thành công
                showToast('success', 'Tạo kỳ thi thành công!', 'Ca thi đã được lưu vào hệ thống và sẵn sàng.');

                // 3. Reset form và đưa về bước 1 cho lần mở sau
                this.reset();
                goToStep(1);

            } else {
                // Hiện thông báo Lỗi (Ví dụ: Trùng mã ca thi, lỗi máy chủ)
                showToast('error', 'Tạo kỳ thi thất bại!', 'Đã xảy ra lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.');
            }

            // Trả lại trạng thái nút ban đầu
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');

        }, 1500);
    });

</script>