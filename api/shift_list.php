<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

// Lấy tham số phân trang và tìm kiếm
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 10;
$offset = ($page - 1) * $limit;

// Đếm tổng số ca thi (có tìm kiếm)
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
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total'];
$totalPages = ceil($total / $limit);

// Lấy danh sách ca thi
$sql = "
    SELECT 
        ct.ma_ca_thi,
        ct.ma_de_thi,
        ct.thoi_gian_bat_dau,
        ct.thoi_gian_ket_thuc,
        ct.ma_phong,
        dt.tieu_de,
        (SELECT COUNT(*) FROM dang_ky_thi dkt WHERE dkt.ma_ca_thi = ct.ma_ca_thi) as so_luong_dang_ky
    FROM ca_thi ct
    LEFT JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
    WHERE 1=1
";

if (!empty($search)) {
    $sql .= " AND (dt.tieu_de LIKE ? OR ct.ma_phong LIKE ? OR ct.ma_ca_thi LIKE ?)";
}

$sql .= " ORDER BY ct.thoi_gian_bat_dau DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$shifts = [];
$now = date('Y-m-d H:i:s');

while ($row = $result->fetch_assoc()) {
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

echo json_encode([
    'success' => true,
    'data' => $shifts,
    'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'totalPages' => $totalPages
    ]
]);

$stmt->close();
$conn->close();

