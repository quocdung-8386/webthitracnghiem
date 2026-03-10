<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý Ca thi - Hệ Thống Thi Trực Tuyến";
$active_menu = "shift_exam";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

date_default_timezone_set('Asia/Ho_Chi_Minh');
$now = date('Y-m-d H:i:s');

$shifts = [];

// Lấy tham số tìm kiếm và phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10;
$offset = ($page - 1) * $limit;

/* ================================
        ĐẾM TỔNG SỐ CA THI
================================ */

$countSql = "
SELECT COUNT(*) as total
FROM ca_thi ct
LEFT JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
WHERE 1=1
";

$params = [];
$types = "";

if (!empty($search)) {
    $countSql .= " AND (dt.tieu_de LIKE ? OR ct.ma_phong LIKE ? OR ct.ma_ca_thi LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
    $types = "sss";
}

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);

$totalRow = $countStmt->fetch(PDO::FETCH_ASSOC);
$total = $totalRow['total'];
$totalPages = ceil($total / $limit);

/* ================================
        LẤY DANH SÁCH CA THI
================================ */

$sql = "
SELECT 
    ct.ma_ca_thi,
    ct.ma_de_thi,
    ct.thoi_gian_bat_dau,
    ct.thoi_gian_ket_thuc,
    ct.ma_phong,
    dt.tieu_de,
    (
        SELECT COUNT(*) 
        FROM dang_ky_thi dkt 
        WHERE dkt.ma_ca_thi = ct.ma_ca_thi
    ) as so_luong_dang_ky
FROM ca_thi ct
LEFT JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
WHERE 1=1
";

$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (dt.tieu_de LIKE ? OR ct.ma_phong LIKE ? OR ct.ma_ca_thi LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
    $types = "sss";
}

$sql .= " ORDER BY ct.thoi_gian_bat_dau DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {

    $start = date('H:i', strtotime($row['thoi_gian_bat_dau']));
    $end = date('H:i', strtotime($row['thoi_gian_ket_thuc']));
    $date = date('d/m/Y', strtotime($row['thoi_gian_bat_dau']));

    if ($now < $row['thoi_gian_bat_dau']) {

        $status = 'SẮP TỚI';
        $status_bg = 'bg-blue-50 dark:bg-blue-900/30';
        $status_text = 'text-blue-600 dark:text-blue-400';
        $is_ended = false;

    } elseif ($now >= $row['thoi_gian_bat_dau'] && $now <= $row['thoi_gian_ket_thuc']) {

        $status = 'ĐANG THI';
        $status_bg = 'bg-green-100 dark:bg-green-900/30';
        $status_text = 'text-green-700 dark:text-green-400';
        $is_ended = false;

    } else {

        $status = 'ĐÃ KẾT THÚC';
        $status_bg = 'bg-slate-100 dark:bg-slate-700';
        $status_text = 'text-slate-600 dark:text-slate-400';
        $is_ended = true;

    }

    $shifts[] = [
        'name' => htmlspecialchars($row['tieu_de'] ?: 'Ca thi'),
        'id' => 'SH-' . str_pad($row['ma_ca_thi'], 3, '0', STR_PAD_LEFT),
        'ma_ca_thi' => $row['ma_ca_thi'],
        'ma_de_thi' => $row['ma_de_thi'],
        'time' => $start . ' - ' . $end,
        'date' => $date,
        'thoi_gian_bat_dau' => $row['thoi_gian_bat_dau'],
        'thoi_gian_ket_thuc' => $row['thoi_gian_ket_thuc'],
        'location_icon' => 'business',
        'location' => htmlspecialchars($row['ma_phong']),
        'students_assigned' => (int)$row['so_luong_dang_ky'],
        'students_total' => 0,
        'avatars' => [],
        'avatar_bg' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300',
        'status' => $status,
        'status_bg' => $status_bg,
        'status_text' => $status_text,
        'is_ended' => $is_ended
    ];
}

/* ================================
        LẤY DANH SÁCH ĐỀ THI
================================ */

$examSql = "SELECT ma_de_thi, tieu_de FROM de_thi ORDER BY ma_de_thi DESC";
$examResult = $conn->query($examSql);

$exams = [];

while ($row = $examResult->fetch(PDO::FETCH_ASSOC)) {

    $exams[] = [
        'ma_de_thi' => $row['ma_de_thi'],
        'tieu_de' => htmlspecialchars($row['tieu_de'], ENT_QUOTES, 'UTF-8')
    ];

}

/* ================================
            THỐNG KÊ
================================ */

$totalShifts = $total;
$totalAssigned = 0;
$totalUnassigned = 25;

/* ================================
        HEADER + SIDEBAR
================================ */

include 'components/header.php';
include 'components/sidebar.php';
?>
<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Kỳ thi & Đề thi<span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản lý Ca thi và Phân bổ thí sinh</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <form method="GET" action="" class="inline">
                    <input type="text" id="searchInput" name="search" placeholder="Tìm kiếm ca thi..."
                        value="<?php echo htmlspecialchars($search); ?>"
                        class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
                </form>
            </div>

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
                            class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu
                            đã đọc</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#"
                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                    class="font-semibold text-slate-800 dark:text-white">Ca sáng - Đợt 1</span> đã bắt
                                đầu tính thời gian làm bài.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                    class="material-icons text-[12px]">schedule</span> 5 phút trước</span>
                        </a>
                    </div>
                    <a href="#"
                        class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">Xem
                        tất cả</a>
                </div>
            </div>

            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div class="mb-6">
            <div class="text-[13px] text-slate-500 dark:text-slate-400 mb-2">
                Kỳ thi & Đề thi <span class="mx-2">›</span> <span
                    class="text-slate-800 dark:text-white font-medium">Toán Cao Cấp A1 - Học kỳ 1 2023</span>
            </div>
            <div class="flex justify-between items-end">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[28px]">calendar_month</span>
                    Danh sách các ca thi
                </h2>
                <div class="flex gap-3">
                    <button onclick="openModal('assignStudentsModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">person_add_alt_1</span> Gán thí sinh hàng loạt
                    </button>
                    <button onclick="openModal('addShiftModal')"
                        class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">add</span> Thêm ca thi
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div
                class="bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800/50 text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">event_note</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-0.5">
                        Tổng số ca thi</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $totalShifts; ?></p>
                </div>
            </div>

            <div
                class="bg-green-50/50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/50 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">how_to_reg</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wide mb-0.5">
                        Thí sinh đã gán</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white"><?php echo number_format($totalAssigned); ?> <span
                            class="text-lg text-slate-400 dark:text-slate-500 font-semibold">/ <?php echo number_format($totalAssigned + $totalUnassigned); ?></span></p>
                </div>
            </div>

            <div
                class="bg-orange-50/50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-800/50 text-orange-500 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">warning</span>
                </div>
                <div>
                    <p
                        class="text-[11px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-0.5">
                        Thí sinh chưa gán</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $totalUnassigned; ?></p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6 transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="shiftsTable">
                    <thead
                        class="bg-white dark:bg-slate-800 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700 transition-colors">
                        <tr>
                            <th class="px-6 py-5 w-14 text-center">
                                <input type="checkbox" id="selectAllBtn"
                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                            </th>
                            <th class="px-6 py-5">Tên ca thi</th>
                            <th class="px-6 py-5">Thời gian</th>
                            <th class="px-6 py-5">Địa điểm / Link</th>
                            <th class="px-6 py-5 text-center">Thí sinh</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="shiftsTableBody">
                        <?php foreach ($shifts as $shift): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition shift-row" data-ma-ca-thi="<?php echo $shift['ma_ca_thi']; ?>">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px] shift-name">
                                        <?php echo $shift['name']; ?>
                                    </div>
                                    <div class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5 shift-id">ID:
                                        <?php echo $shift['id']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700 dark:text-slate-300">
                                        <?php echo $shift['time']; ?>
                                    </div>
                                    <div class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        <?php echo $shift['date']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium shift-location">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="material-icons text-[18px] text-slate-400 dark:text-slate-500"><?php echo $shift['location_icon']; ?></span>
                                        <?php echo $shift['location']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if (count($shift['avatars']) > 0): ?>
                                        <div class="flex justify-center -space-x-2 mb-1">
                                            <?php foreach ($shift['avatars'] as $av): ?>
                                                <div
                                                    class="w-6 h-6 rounded-full <?php echo $shift['avatar_bg']; ?> border-2 border-white dark:border-slate-800 flex items-center justify-center text-[8px] font-bold z-10">
                                                    <?php echo $av; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div
                                        class="text-[12px] <?php echo ($shift['students_assigned'] == 0) ? 'text-orange-500 dark:text-orange-400 font-bold' : 'text-[#1e3bb3] dark:text-[#4b6bfb] font-semibold'; ?>">
                                        <?php echo $shift['students_assigned']; ?>/<?php echo $shift['students_total']; ?>
                                        thí sinh
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full <?php echo $shift['status_bg']; ?> <?php echo $shift['status_text']; ?> uppercase inline-block leading-tight text-center shift-status"
                                        data-start="<?php echo $shift['thoi_gian_bat_dau']; ?>"
                                        data-end="<?php echo $shift['thoi_gian_ket_thuc']; ?>">
                                        <?php echo str_replace(' ', '<br>', $shift['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                                    <?php if (!$shift['is_ended']): ?>
                                        <button
                                            onclick="showToast('info', 'Thêm thí sinh', 'Mở bảng thêm thí sinh cho <?php echo htmlspecialchars($shift['name']); ?>')"
                                            class="hover:text-[#1e3bb3] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"
                                            title="Thêm thí sinh"><span
                                                class="material-icons text-[18px]">person_add</span></button>
                                    <?php else: ?>
                                        <button
                                            onclick="showToast('success', 'Thống kê điểm', 'Mở báo cáo điểm cho <?php echo htmlspecialchars($shift['name']); ?>')"
                                            class="hover:text-[#1e3bb3] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"
                                            title="Xem thống kê điểm"><span
                                                class="material-icons text-[18px]">insert_chart_outlined</span></button>
                                    <?php endif; ?>
                                    <button onclick="openEditModal(<?php echo $shift['ma_ca_thi']; ?>, '<?php echo htmlspecialchars($shift['name']); ?>', <?php echo $shift['ma_de_thi']; ?>, '<?php echo $shift['date']; ?>', '<?php echo substr($shift['thoi_gian_bat_dau'], 11, 5); ?>', '<?php echo substr($shift['thoi_gian_ket_thuc'], 11, 5); ?>', '<?php echo htmlspecialchars($shift['location']); ?>')"
                                        class="hover:text-slate-700 dark:hover:text-white p-1.5 transition rounded-md hover:bg-slate-100 dark:hover:bg-slate-700"
                                        title="Chỉnh sửa"><span class="material-icons text-[18px]">edit</span></button>
                                    <button onclick="deleteShift(<?php echo $shift['ma_ca_thi']; ?>, '<?php echo htmlspecialchars($shift['name']); ?>')"
                                        class="hover:text-red-500 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700"
                                        title="Xóa"><span class="material-icons text-[18px]">delete</span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <?php echo ($offset + 1); ?>-<?php echo min($offset + $limit, $total); ?> trên tổng số <?php echo $total; ?> ca thi</p>
                <div id="paginationControls" class="flex items-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition font-medium">Trước</a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500 font-medium">Trước</span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="w-8 h-8 flex items-center justify-center bg-[#254ada] text-white rounded-md font-medium shadow-sm"><?php echo $i; ?></span>
                        <?php elseif ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-md text-slate-600 dark:text-slate-300 transition"><?php echo $i; ?></a>
                        <?php elseif ($i == $page - 2 || $i == $page + 2): ?>
                            <span class="text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition font-medium">Sau</a>
                    <?php else: ?>
                        <span class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500 font-medium">Sau</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 rounded-xl p-6 transition-colors">
                <h3 class="font-bold text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center gap-2 mb-4">
                    <span class="material-icons text-[20px]">info</span> Hướng dẫn phân bổ
                </h3>
                <ul class="space-y-3 text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span
                            class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Sử dụng <b>Gán thí sinh hàng loạt</b> để tự động chia thí sinh vào các ca thi theo bảng
                            chữ cái hoặc ngẫu nhiên.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span
                            class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Mỗi ca thi có thể cấu hình giới hạn số lượng thí sinh hoặc gán theo phòng học cụ
                            thể.</span>
                    </li>
                </ul>
            </div>

            <div onclick="showToast('info', 'Tải file', 'Mở popup upload file Excel danh sách thí sinh')"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6 flex flex-col items-center justify-center text-center shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-500 transition group">
                <div
                    class="w-12 h-12 rounded-full bg-slate-800 dark:bg-slate-700 text-white flex items-center justify-center mb-3 group-hover:-translate-y-1 transition-transform shadow-md">
                    <span class="material-icons text-[24px]">upload_file</span>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1">Nhập danh sách từ Excel</h3>
                <p class="text-[13px] text-slate-500 dark:text-slate-400">Tải lên file danh sách phân bổ ca thi theo
                    định dạng mẫu của hệ thống.</p>
            </div>
        </div>

    </div>
</main>

<div id="addShiftModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">add_task</span> Thêm ca thi mới
            </h3>
            <button type="button" onclick="closeModal('addShiftModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form onsubmit="event.preventDefault(); submitAddShift();" class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Đề thi
                    <span class="text-red-500">*</span></label>
                <select id="add_ma_de_thi" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                    <option value="">-- Chọn đề thi --</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?php echo $exam['ma_de_thi']; ?>"><?php echo $exam['tieu_de']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ngày thi
                        <span class="text-red-500">*</span></label>
                    <input type="date" id="add_date" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ bắt đầu
                        <span class="text-red-500">*</span></label>
                    <input type="time" id="add_start_time" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ kết thúc
                        <span class="text-red-500">*</span></label>
                    <input type="time" id="add_end_time" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Địa điểm / Phòng máy <span class="text-red-500">*</span></label>
                    <input type="text" id="add_ma_phong" placeholder="VD: Phòng Lab 405" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-5 mt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('addShiftModal')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                    bỏ</button>
                <button type="submit" id="btnSubmitShift"
                    class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2">Tạo
                    ca thi</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Shift Modal -->
<div id="editShiftModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">edit</span> Chỉnh sửa ca thi
            </h3>
            <button type="button" onclick="closeModal('editShiftModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form onsubmit="event.preventDefault(); submitEditShift();" class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <input type="hidden" id="edit_ma_ca_thi" value="">
            
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Đề thi
                    <span class="text-red-500">*</span></label>
                <select id="edit_ma_de_thi" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                    <option value="">-- Chọn đề thi --</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?php echo $exam['ma_de_thi']; ?>"><?php echo $exam['tieu_de']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ngày thi
                        <span class="text-red-500">*</span></label>
                    <input type="date" id="edit_date" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ bắt đầu
                        <span class="text-red-500">*</span></label>
                    <input type="time" id="edit_start_time" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ kết thúc
                        <span class="text-red-500">*</span></label>
                    <input type="time" id="edit_end_time" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Địa điểm / Phòng máy <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_ma_phong" placeholder="VD: Phòng Lab 405" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-5 mt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('editShiftModal')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                    bỏ</button>
                <button type="submit" id="btnEditShift"
                    class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2">Lưu
                    thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div id="assignStudentsModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">

        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">person_add_alt_1</span> Gán thí sinh tự
                động
            </h3>
            <button type="button" onclick="closeModal('assignStudentsModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>

        <form onsubmit="event.preventDefault(); submitAssignStudents();"
            class="flex-1 overflow-y-auto custom-scrollbar p-6">

            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-4 flex items-center justify-between mb-6 transition-colors">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800/50 text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center justify-center">
                        <span class="material-icons">groups</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Thí sinh chờ phân bổ</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-0.5">Thuộc danh sách thi: Toán Cao
                            Cấp A1</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-[#254ada] dark:text-[#4b6bfb]">25</span>
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">thí sinh</span>
                </div>
            </div>

            <h4 class="text-[13px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-3">1. Phương
                thức phân bổ</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <label
                    class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-[#254ada] dark:hover:border-[#4b6bfb] hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors has-[:checked]:border-[#254ada] has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:border-[#4b6bfb]">
                    <div class="mt-0.5">
                        <input type="radio" name="allocation_method" value="random" checked
                            class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Chia ngẫu nhiên</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Hệ thống sẽ bốc thăm và chia đều
                            thí sinh vào các ca thi đã chọn.</p>
                    </div>
                </label>

                <label
                    class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-[#254ada] dark:hover:border-[#4b6bfb] hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors has-[:checked]:border-[#254ada] has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:border-[#4b6bfb]">
                    <div class="mt-0.5">
                        <input type="radio" name="allocation_method" value="alpha"
                            class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Theo bảng chữ cái
                            (A-Z)</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Ưu tiên điền đầy ca thi theo thứ
                            tự chữ cái tên thí sinh.</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-between items-end mb-3">
                <h4 class="text-[13px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">2. Chọn ca
                    thi áp dụng</h4>
                <label
                    class="flex items-center gap-1.5 cursor-pointer text-[12px] font-medium text-[#254ada] dark:text-[#4b6bfb] hover:underline">
                    <input type="checkbox" checked
                        class="w-3.5 h-3.5 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                    Chọn tất cả ca chưa đầy
                </label>
            </div>

            <div class="space-y-2.5 mb-6 max-h-[160px] overflow-y-auto custom-scrollbar pr-2">
                <label
                    class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 rounded-lg cursor-not-allowed opacity-60">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" disabled class="w-4 h-4 text-slate-300 border-slate-200 rounded">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Ca sáng - Đợt 1</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">07:30 - 09:30 | Phòng Lab 402</p>
                        </div>
                    </div>
                    <span class="text-[12px] font-bold text-red-500">Đã đầy (50/50)</span>
                </label>

                <label
                    class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-600 hover:border-[#254ada] rounded-lg cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" checked
                            class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Ca sáng - Đợt 2</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">10:00 - 12:00 | Phòng Lab
                                402</p>
                        </div>
                    </div>
                    <span class="text-[12px] font-bold text-green-600 dark:text-green-400">Trống (0/50)</span>
                </label>
            </div>
        </form>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script>
// ================================
// AJAX Functions for Shift Management
// ================================

// Submit Add Shift
function submitAddShift() {
    const ma_de_thi = document.getElementById('add_ma_de_thi').value;
    const date = document.getElementById('add_date').value;
    const start_time = document.getElementById('add_start_time').value;
    const end_time = document.getElementById('add_end_time').value;
    const ma_phong = document.getElementById('add_ma_phong').value;
    
    if (!ma_de_thi || !date || !start_time || !end_time || !ma_phong) {
        showToast('error', 'Lỗi', 'Vui lòng điền đầy đủ thông tin');
        return;
    }
    
    const btn = document.getElementById('btnSubmitShift');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons animate-spin">sync</span> Đang xử lý...';
    
    const formData = new FormData();
    formData.append('ma_de_thi', ma_de_thi);
    formData.append('date', date);
    formData.append('start_time', start_time);
    formData.append('end_time', end_time);
    formData.append('ma_phong', ma_phong);
    
    fetch('api/add_shift.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Thành công', 'Thêm ca thi thành công');
            closeModal('addShiftModal');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('error', 'Lỗi', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        showToast('error', 'Lỗi', 'Không thể kết nối server');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Tạo ca thi';
    });
}

// Open Edit Modal
function openEditModal(ma_ca_thi, name, ma_de_thi, date, start_time, end_time, ma_phong) {
    document.getElementById('edit_ma_ca_thi').value = ma_ca_thi;
    document.getElementById('edit_ma_de_thi').value = ma_de_thi;
    document.getElementById('edit_date').value = date.split('/').reverse().join('-');
    document.getElementById('edit_start_time').value = start_time;
    document.getElementById('edit_end_time').value = end_time;
    document.getElementById('edit_ma_phong').value = ma_phong;
    openModal('editShiftModal');
}

// Submit Edit Shift
function submitEditShift() {
    const ma_ca_thi = document.getElementById('edit_ma_ca_thi').value;
    const ma_de_thi = document.getElementById('edit_ma_de_thi').value;
    const date = document.getElementById('edit_date').value;
    const start_time = document.getElementById('edit_start_time').value;
    const end_time = document.getElementById('edit_end_time').value;
    const ma_phong = document.getElementById('edit_ma_phong').value;
    
    if (!ma_ca_thi || !ma_de_thi || !date || !start_time || !end_time || !ma_phong) {
        showToast('error', 'Lỗi', 'Vui lòng điền đầy đủ thông tin');
        return;
    }
    
    const btn = document.getElementById('btnEditShift');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-icons animate-spin">sync</span> Đang xử lý...';
    
    const formData = new FormData();
    formData.append('ma_ca_thi', ma_ca_thi);
    formData.append('ma_de_thi', ma_de_thi);
    formData.append('date', date);
    formData.append('start_time', start_time);
    formData.append('end_time', end_time);
    formData.append('ma_phong', ma_phong);
    
    fetch('api/update_shift.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Thành công', 'Cập nhật ca thi thành công');
            closeModal('editShiftModal');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('error', 'Lỗi', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        showToast('error', 'Lỗi', 'Không thể kết nối server');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Lưu thay đổi';
    });
}

// Delete Shift
function deleteShift(ma_ca_thi, name) {
    if (!confirm('Bạn có chắc muốn xóa ca thi "' + name + '"?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('ma_ca_thi', ma_ca_thi);
    
    fetch('api/delete_shift.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Thành công', 'Xóa ca thi thành công');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('error', 'Lỗi', data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        showToast('error', 'Lỗi', 'Không thể kết nối server');
    });
}

// ================================
// Realtime Status Update (every 30 seconds)
// ================================
function updateShiftStatuses() {
    const statusElements = document.querySelectorAll('.shift-status');
    const now = new Date();
    
    statusElements.forEach(element => {
        const start = new Date(element.getAttribute('data-start'));
        const end = new Date(element.getAttribute('data-end'));
        
        let status, status_bg, status_text;
        
        if (now < start) {
            status = 'SẮP TỚI';
            status_bg = 'bg-blue-50 dark:bg-blue-900/30';
            status_text = 'text-blue-600 dark:text-blue-400';
        } else if (now >= start && now <= end) {
            status = 'ĐANG THI';
            status_bg = 'bg-green-100 dark:bg-green-900/30';
            status_text = 'text-green-700 dark:text-green-400';
        } else {
            status = 'ĐÃ KẾT THÚC';
            status_bg = 'bg-slate-100 dark:bg-slate-700';
            status_text = 'text-slate-600 dark:text-slate-400';
        }
        
        element.className = `px-2.5 py-1 text-[10px] font-bold rounded-full ${status_bg} ${status_text} uppercase inline-block leading-tight text-center shift-status`;
        element.innerHTML = status.replace(' ', '<br>');
    });
}

// Update status every 30 seconds
setInterval(updateShiftStatuses, 30000);

// Search functionality with debounce
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
}
</script>
