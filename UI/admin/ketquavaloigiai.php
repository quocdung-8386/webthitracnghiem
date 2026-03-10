<?php
// 1. Cấu hình thông tin trang
$title = "Kết quả & Lời giải - Hệ Thống Thi Trực Tuyến";
$active_menu = "results"; // Biến này dùng để làm sáng menu "Kết quả & Lời giải" trong Sidebar

// Kết nối database
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

// Xử lý filter và search từ GET parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$ma_mon_hoc = isset($_GET['ma_mon']) ? $_GET['ma_mon'] : '';
$ma_ky_thi = isset($_GET['ma_ky_thi']) ? $_GET['ma_ky_thi'] : '';

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3; // Số dòng mỗi trang (đúng với JavaScript client-side)
$offset = ($page - 1) * $limit;

// Lấy danh sách môn học (từ de_thi)
$subjectsStmt = $conn->query("
    SELECT DISTINCT dt.ma_de_thi, dt.tieu_de 
    FROM de_thi dt
    ORDER BY dt.tieu_de ASC
");
$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách kỳ thi (từ ca_thi + de_thi)
$examsStmt = $conn->query("
    SELECT ct.ma_ca_thi, dt.tieu_de, ct.thoi_gian_bat_dau
    FROM ca_thi ct
    JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
    ORDER BY ct.thoi_gian_bat_dau DESC
    LIMIT 50
");
$examSessions = $examsStmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm tổng số kết quả (với điều kiện lọc)
// JOIN đúng các bảng: bai_lam, nguoi_dung, vai_tro, ca_thi, de_thi
// Chỉ lấy dữ liệu của role: ten_vai_tro = 'thi_sinh'
// Chỉ hiển thị các bài có trang_thai: 'da_nop', 'da_cham'
$countSql = "
    SELECT COUNT(*) 
    FROM bai_lam bl
    INNER JOIN nguoi_dung nd ON bl.ma_nguoi_dung = nd.ma_nguoi_dung
    INNER JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
    INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
    INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
    WHERE vt.ten_vai_tro = 'thi_sinh'
    AND bl.trang_thai IN ('da_nop', 'da_cham')
";

$params = [];
if (!empty($search)) {
    $countSql .= " AND (nd.ho_ten LIKE ? OR nd.ten_dang_nhap LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($ma_mon_hoc)) {
    $countSql .= " AND dt.ma_de_thi = ?";
    $params[] = $ma_mon_hoc;
}
if (!empty($ma_ky_thi)) {
    $countSql .= " AND ct.ma_ca_thi = ?";
    $params[] = $ma_ky_thi;
}

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Lấy dữ liệu kết quả thi với JOIN đúng các bảng
// Sửa tên cột: diem_so → tong_diem, thoi_gian_thi → thoi_gian_lam
// Sửa tên cột: thoi_gian_bat_dau → thoi_diem_bat_dau, thoi_gian_nop → thoi_diem_nop
$sql = "
    SELECT 
        bl.ma_bai_lam,
        nd.ma_nguoi_dung,
        nd.ten_dang_nhap,
        nd.ho_ten,
        dt.ma_de_thi,
        dt.tieu_de as ten_de_thi,
        dt.thoi_gian_lam,
        ct.ma_ca_thi,
        bl.tong_diem,
        bl.thoi_diem_bat_dau,
        bl.thoi_diem_nop,
        bl.trang_thai
    FROM bai_lam bl
    INNER JOIN nguoi_dung nd ON bl.ma_nguoi_dung = nd.ma_nguoi_dung
    INNER JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
    INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
    INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
    WHERE vt.ten_vai_tro = 'thi_sinh'
    AND bl.trang_thai IN ('da_nop', 'da_cham')
";

if (!empty($search)) {
    $sql .= " AND (nd.ho_ten LIKE ? OR nd.ten_dang_nhap LIKE ?)";
}
if (!empty($ma_mon_hoc)) {
    $sql .= " AND dt.ma_de_thi = ?";
}
if (!empty($ma_ky_thi)) {
    $sql .= " AND ct.ma_ca_thi = ?";
}

$sql .= " ORDER BY bl.thoi_diem_nop DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$resultsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý dữ liệu để hiển thị
$results = [];
$passScore = 5.0; // Điểm để đạt (pass)

foreach ($resultsData as $row) {
    // Tính thời gian làm bài: thoi_diem_nop - thoi_diem_bat_dau
    // Hiển thị dạng: mm:ss
    $startTime = strtotime($row['thoi_diem_bat_dau']);
    $endTime = strtotime($row['thoi_diem_nop']);
    $timeSpentSeconds = $endTime - $startTime;
    $timeSpent = floor($timeSpentSeconds / 60) . ':' . str_pad($timeSpentSeconds % 60, 2, '0', STR_PAD_LEFT);
    
    // Thời gian thi tối đa (phút)
    $timeTotal = $row['thoi_gian_lam'] ? $row['thoi_gian_lam'] : 60;
    
    // Xác định trạng thái pass/fail
    // >= 5 → Đạt, < 5 → Trượt
    $score = (float)$row['tong_diem'];
    $statusType = ($score >= $passScore) ? 'pass' : 'fail';
    
    // Tạo avatar từ họ tên: Nguyễn Văn An → NA
    $nameParts = explode(' ', $row['ho_ten']);
    $avatar = count($nameParts) >= 2 
        ? strtoupper(substr($nameParts[0], 0, 1) . end($nameParts)) 
        : strtoupper(substr($row['ho_ten'], 0, 2));
    
    // Màu avatar ngẫu nhiên nhưng cố định theo tên
    $avatarColors = [
        ['bg-blue-100 text-blue-600', 'dark:bg-blue-900/50 dark:text-blue-400'],
        ['bg-orange-100 text-orange-600', 'dark:bg-orange-900/50 dark:text-orange-400'],
        ['bg-emerald-100 text-emerald-600', 'dark:bg-emerald-900/50 dark:text-emerald-400'],
        ['bg-purple-100 text-purple-600', 'dark:bg-purple-900/50 dark:text-purple-400'],
        ['bg-rose-100 text-rose-600', 'dark:bg-rose-900/50 dark:text-rose-400'],
        ['bg-cyan-100 text-cyan-600', 'dark:bg-cyan-900/50 dark:text-cyan-400'],
    ];
    $colorIndex = array_sum(array_map('ord', str_split($row['ho_ten']))) % count($avatarColors);
    $avatarBg = $avatarColors[$colorIndex][0];
    $avatarBgDark = $avatarColors[$colorIndex][1];
    
    $results[] = [
        'id' => $row['ma_nguoi_dung'],
        'ten_dang_nhap' => $row['ten_dang_nhap'],
        'name' => $row['ho_ten'],
        'avatar' => $avatar,
        'avatar_bg' => $avatarBg,
        'avatar_bg_dark' => $avatarBgDark,
        'exam' => $row['ten_de_thi'],
        'score' => number_format($score, 1),
        'total_score' => '10.0',
        'time_spent' => $timeSpent,
        'time_total' => $timeTotal . ':00',
        'status_type' => $statusType,
        'ma_bai_lam' => $row['ma_bai_lam'],
        'thoi_diem_bat_dau' => $row['thoi_diem_bat_dau'],
        'thoi_diem_nop' => $row['thoi_diem_nop']
    ];
}

// Xử lý Export CSV
// Sử dụng prepared statement để chống SQL injection
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="ket_qua_thi_' . date('Ymd_His') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header: mã thí sinh, tên đăng nhập, họ tên, tên đề thi, thời gian làm bài, điểm, thời gian bắt đầu, thời gian nộp, trạng thái
    fputcsv($output, ['Mã thí sinh', 'Tên đăng nhập', 'Họ tên', 'Tên đề thi', 'Thời gian làm bài', 'Điểm', 'Thời gian bắt đầu', 'Thời gian nộp', 'Trạng thái']);
    
    // Get all data without pagination for export
    // Sử dụng prepared statement
    $exportSql = "
        SELECT 
            nd.ma_nguoi_dung,
            nd.ten_dang_nhap,
            nd.ho_ten,
            dt.tieu_de,
            bl.tong_diem,
            bl.thoi_diem_bat_dau,
            bl.thoi_diem_nop,
            bl.trang_thai
        FROM bai_lam bl
        INNER JOIN nguoi_dung nd ON bl.ma_nguoi_dung = nd.ma_nguoi_dung
        INNER JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
        INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
        INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
        WHERE vt.ten_vai_tro = 'thi_sinh'
        AND bl.trang_thai IN ('da_nop', 'da_cham')
    ";
    
    $exportParams = [];
    
    if (!empty($search)) {
        $exportSql .= " AND (nd.ho_ten LIKE ? OR nd.ten_dang_nhap LIKE ?)";
        $exportParams[] = "%$search%";
        $exportParams[] = "%$search%";
    }
    if (!empty($ma_mon_hoc)) {
        $exportSql .= " AND dt.ma_de_thi = ?";
        $exportParams[] = $ma_mon_hoc;
    }
    if (!empty($ma_ky_thi)) {
        $exportSql .= " AND ct.ma_ca_thi = ?";
        $exportParams[] = $ma_ky_thi;
    }
    
    $exportSql .= " ORDER BY bl.thoi_diem_nop DESC";
    
    $exportStmt = $conn->prepare($exportSql);
    $exportStmt->execute($exportParams);
    $exportData = $exportStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($exportData as $row) {
        // Tính thời gian làm bài: thoi_diem_nop - thoi_diem_bat_dau
        $timeSpentSec = strtotime($row['thoi_diem_nop']) - strtotime($row['thoi_diem_bat_dau']);
        $timeSpent = floor($timeSpentSec / 60) . ' phút ' . ($timeSpentSec % 60) . ' giây';
        
        // Xác định trạng thái: >= 5 → Đạt, < 5 → Trượt
        $status = ($row['tong_diem'] >= $passScore) ? 'Đạt' : 'Trượt';
        
        // Format thời gian
        $thoi_gian_bat_dau = date('d/m/Y H:i:s', strtotime($row['thoi_diem_bat_dau']));
        $thoi_gian_nop = date('d/m/Y H:i:s', strtotime($row['thoi_diem_nop']));
        
        fputcsv($output, [
            $row['ma_nguoi_dung'],
            $row['ten_dang_nhap'],
            $row['ho_ten'],
            $row['tieu_de'],
            $timeSpent,
            number_format($row['tong_diem'], 1),
            $thoi_gian_bat_dau,
            $thoi_gian_nop,
            $status
        ]);
    }
    
    fclose($output);
    exit;
}

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Kết quả và lời giải chi tiết</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..."
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="notifButton" type="button"
                        class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                        <span class="material-icons">notifications</span>
                        <span
                            class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                    </button>
                    <div id="notifDropdown"
                        class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                        <div
                            class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                            <a href="#"
                                class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                                dấu đã đọc</a>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                        class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa chấm
                                    xong 150 bài thi môn Toán.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 5 phút trước</span>
                            </a>
                        </div>
                        <a href="#"
                            class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Xem
                            tất cả</a>
                    </div>
                </div>
                <button id="darkModeToggle"
                    class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">

            <div
                class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-wrap lg:flex-nowrap justify-between items-start gap-4">
                <div class="flex-1 space-y-3 max-w-2xl">
                    <div class="flex gap-3">
                        <div class="relative w-1/2">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">library_books</span>
                            <select id="subjectFilter"
                                class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition"
                                onchange="applyFilters()">
                                <option value="">Tất cả Môn học</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['ma_de_thi']; ?>" <?php echo ($ma_mon_hoc == $subject['ma_de_thi']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subject['tieu_de']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                        <div class="relative w-1/2">
                            <span
                                class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">assignment</span>
                            <select id="examFilter"
                                class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition"
                                onchange="applyFilters()">
                                <option value="">Tất cả Kỳ thi</option>
                                <?php foreach ($examSessions as $exam): ?>
                                    <option value="<?php echo $exam['ma_ca_thi']; ?>" <?php echo ($ma_ky_thi == $exam['ma_ca_thi']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($exam['tieu_de']); ?> (<?php echo date('d/m/Y', strtotime($exam['thoi_gian_bat_dau'])); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span
                                class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <div class="relative">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" id="searchInput" placeholder="Tìm theo mã hoặc tên thí sinh..."
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    </div>
                </div>

                <button onclick="handleExportReport(this)"
                    class="px-6 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-600 text-sm font-bold shadow-sm transition h-full">
                    <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="resultsTable">
                    <thead
                        class="bg-white dark:bg-slate-800 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700 transition-colors">
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
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                        <?php if (count($results) > 0): ?>
                            <?php foreach ($results as $res): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition result-row">

                                    <td class="px-6 py-4 font-bold text-[#1e3bb3] dark:text-[#4b6bfb] text-[13px] r-id">
                                        <?php echo htmlspecialchars($res['id']); ?></td>

                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full <?php echo $res['avatar_bg']; ?> <?php echo $res['avatar_bg_dark']; ?> flex items-center justify-center font-bold text-[12px] border border-white dark:border-slate-700">
                                            <?php echo $res['avatar']; ?>
                                        </div>
                                        <div class="font-bold text-slate-800 dark:text-white text-[13px] leading-tight r-name">
                                            <?php echo str_replace(' ', '<br>', htmlspecialchars($res['name'])); ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div
                                            class="font-medium text-slate-700 dark:text-slate-300 text-[13px] leading-relaxed pr-4">
                                            <?php echo htmlspecialchars($res['exam']); ?>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="font-bold text-slate-800 dark:text-white text-[15px]"><?php echo $res['score']; ?></span><span
                                            class="text-slate-400 dark:text-slate-500 text-[13px]">/<?php echo $res['total_score']; ?></span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="font-medium text-slate-600 dark:text-slate-300 text-[13px]">
                                            <?php echo $res['time_spent']; ?> <span
                                                class="text-slate-300 dark:text-slate-600">/</span></div>
                                        <div class="text-[12px] text-slate-400 dark:text-slate-500">
                                            <?php echo $res['time_total']; ?></div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <?php if ($res['status_type'] == 'pass'): ?>
                                            <div
                                                class="inline-block px-3 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-center border border-green-100 dark:border-green-800/50">
                                                <div class="text-[11px] font-bold leading-tight">Đạt</div>
                                                <div class="text-[9px] font-medium opacity-80">(Passed)</div>
                                            </div>
                                        <?php else: ?>
                                            <div
                                                class="inline-block px-3 py-1 bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 rounded-full text-center border border-red-100 dark:border-red-800/50">
                                                <div class="text-[11px] font-bold leading-tight">Trượt</div>
                                                <div class="text-[9px] font-medium opacity-80">(Failed)</div>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <button
                                            onclick="showToast('info', 'Chi tiết bài thi', 'Đang mở lời giải chi tiết cho thí sinh <?php echo htmlspecialchars($res['name']); ?>...')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-[#1e3bb3] dark:text-[#4b6bfb] rounded-lg text-[12px] font-bold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                                            <span class="material-icons text-[16px]">visibility</span> Xem lời giải
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <span class="material-icons text-4xl mb-2">search_off</span>
                                    <p>Không tìm thấy kết quả thi nào</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <?php echo min($offset + 1, $totalRecords); ?>-<?php echo min($offset + $limit, $totalRecords); ?> trong số <?php echo $totalRecords; ?> kết quả</p>
                <div id="paginationControls" class="flex items-center gap-2 mt-3 md:mt-0">
                </div>
            </div>
        </div>

    </div>
</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div
        class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span
                class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
    /* =================================================================
       HÀM HIỂN THỊ THÔNG BÁO (TOAST)
       ================================================================= */
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');
        if (!container || !template) return;

        const toastNode = template.content.cloneNode(true);
        const toastEl = toastNode.querySelector('.toast-item');
        const iconEl = toastNode.querySelector('.toast-icon');

        toastNode.querySelector('.toast-title').textContent = title;
        toastNode.querySelector('.toast-message').textContent = message;

        if (type === 'success') {
            toastEl.classList.add('border-green-500');
            iconEl.innerHTML = '<span class="material-icons text-green-500">check_circle</span>';
        } else if (type === 'error') {
            toastEl.classList.add('border-red-500');
            iconEl.innerHTML = '<span class="material-icons text-red-500">error</span>';
        } else if (type === 'warning') {
            toastEl.classList.add('border-orange-500');
            iconEl.innerHTML = '<span class="material-icons text-orange-500">warning</span>';
        } else {
            toastEl.classList.add('border-blue-500');
            iconEl.innerHTML = '<span class="material-icons text-blue-500">info</span>';
        }

        toastNode.querySelector('.toast-close').onclick = () => {
            toastEl.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toastEl.remove(), 300);
        };

        container.appendChild(toastNode);
        setTimeout(() => toastEl.classList.remove('translate-x-full', 'opacity-0'), 10);
        setTimeout(() => { if (container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
    }

    /* =================================================================
       HÀM XỬ LÝ XUẤT BÁO CÁO (GIẢ LẬP LOADING)
       ================================================================= */
    function handleExportReport(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[20px]">autorenew</span> Đang trích xuất...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        // Lấy các tham số filter hiện tại
        const subjectFilter = document.getElementById('subjectFilter');
        const examFilter = document.getElementById('examFilter');
        const searchInput = document.getElementById('searchInput');
        
        let exportUrl = '?export=csv';
        
        if (subjectFilter && subjectFilter.value) {
            exportUrl += '&ma_mon=' + encodeURIComponent(subjectFilter.value);
        }
        if (examFilter && examFilter.value) {
            exportUrl += '&ma_ky_thi=' + encodeURIComponent(examFilter.value);
        }
        if (searchInput && searchInput.value) {
            exportUrl += '&search=' + encodeURIComponent(searchInput.value);
        }

        setTimeout(() => {
            // Chuyển hướng đến URL xuất CSV
            window.location.href = exportUrl;
            
            showToast('success', 'Thành công', 'Báo cáo điểm thi đã được tải xuống thiết bị của bạn.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
        }, 500);
    }

    /* =================================================================
       HÀM ÁP DỤNG BỘ LỌC
       ================================================================= */
    function applyFilters() {
        const subjectFilter = document.getElementById('subjectFilter');
        const examFilter = document.getElementById('examFilter');
        const searchInput = document.getElementById('searchInput');
        
        let url = '?';
        const params = [];
        
        if (subjectFilter && subjectFilter.value) {
            params.push('ma_mon=' + encodeURIComponent(subjectFilter.value));
        }
        if (examFilter && examFilter.value) {
            params.push('ma_ky_thi=' + encodeURIComponent(examFilter.value));
        }
        if (searchInput && searchInput.value) {
            params.push('search=' + encodeURIComponent(searchInput.value));
        }
        
        if (params.length > 0) {
            url += params.join('&');
        } else {
            url = window.location.pathname;
        }
        
        window.location.href = url;
    }

    /* =================================================================
       SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
       ================================================================= */
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Chức năng Dark Mode
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            if (darkModeIcon) darkModeIcon.textContent = 'light_mode';
        }

        darkModeToggle?.addEventListener('click', () => {
            htmlElement.classList.toggle('dark');
            const isDark = htmlElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
        });

        // 2. Chức năng Dropdown Thông báo
        const notifButton = document.getElementById('notifButton');
        const notifDropdown = document.getElementById('notifDropdown');

        if (notifButton && notifDropdown) {
            notifButton.addEventListener('click', function (e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        // 3. Logic Tìm kiếm & Phân trang Real-time
        const rowsPerPage = 3; // Hiển thị 3 dòng/trang
        let currentPage = 1;
        let filteredRows = [];

        const allRows = Array.from(document.querySelectorAll('.result-row'));
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            const displayStart = totalRows === 0 ? 0 : start + 1;
            const displayEnd = Math.min(end, totalRows);

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}-${displayEnd}</span> trong số <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> kết quả`;
            }

            if (paginationControls) {
                paginationControls.innerHTML = '';

                // Prev Button
                const prevBtn = document.createElement('button');
                prevBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
                prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
                paginationControls.appendChild(prevBtn);

                // Page Numbers
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    if (i === currentPage) {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md bg-[#254ada] text-white font-bold text-xs shadow-sm';
                    } else {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium text-xs';
                    }
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => { currentPage = i; updatePagination(); };
                    paginationControls.appendChild(pageBtn);
                }

                // Next Button
                const nextBtn = document.createElement('button');
                nextBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
                nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
        }

        // Bắt sự kiện Lọc qua thanh tìm kiếm
        searchInput?.addEventListener('input', function () {
            const text = this.value.toLowerCase();

            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.r-name').textContent.toLowerCase();
                const id = row.querySelector('.r-id').textContent.toLowerCase();
                return name.includes(text) || id.includes(text);
            });

            currentPage = 1;
            updatePagination();
        });

        // Chạy lần đầu
        filteredRows = [...allRows];
        updatePagination();
    });
</script>

