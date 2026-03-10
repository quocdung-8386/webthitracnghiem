<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Thống kê tổng quan
$statsSql = "
SELECT 
    (SELECT COUNT(*) FROM bai_lam WHERE trang_thai = 'dang_lam') as total_examining,
    (SELECT COUNT(*) FROM vi_pham_thi WHERE thoi_gian >= DATE_SUB(NOW(), INTERVAL 1 HOUR)) as total_violations
";
$statsStmt = $conn->query($statsSql);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Query lấy danh sách thí sinh đang thi
// SỬA THEO DATABASE SCHEMA:
// - bai_lam.thoi_diem_bat_dau (không phải thoi_gian_bat_dau)
// - so_cau_da_lam tính từ COUNT(chi_tiet_bai_lam) WHERE ma_dap_an_chon IS NOT NULL
// - tong_so_cau tính từ COUNT(chi_tiet_de_thi) JOIN ca_thi -> de_thi
// - ten_dang_nhap thay cho mssv (database không có cột mssv)
$sql = "
SELECT 
    nd.ma_nguoi_dung,
    nd.ho_ten,
    nd.ten_dang_nhap,
    bl.ma_bai_lam,
    bl.thoi_diem_bat_dau,
    bl.trang_thai,
    ct.ma_ca_thi,
    dt.ma_de_thi,
    dt.tieu_de as ten_de_thi,
    (
        SELECT COUNT(*) 
        FROM chi_tiet_bai_lam ctbl 
        WHERE ctbl.ma_bai_lam = bl.ma_bai_lam 
        AND ctbl.ma_dap_an_chon IS NOT NULL
    ) as so_cau_da_lam,
    (
        SELECT COUNT(*) 
        FROM chi_tiet_de_thi ctdt 
        WHERE ctdt.ma_de_thi = dt.ma_de_thi
    ) as tong_so_cau,
    (
        SELECT COUNT(*) 
        FROM vi_pham_thi vpt 
        WHERE vpt.ma_bai_lam = bl.ma_bai_lam
    ) as so_vi_pham,
    (
        SELECT GROUP_CONCAT(vpt.loai_vi_pham SEPARATOR '|') 
        FROM vi_pham_thi vpt 
        WHERE vpt.ma_bai_lam = bl.ma_bai_lam
    ) as ds_vi_pham
FROM nguoi_dung nd
INNER JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
WHERE bl.trang_thai = 'dang_lam'
";

$params = [];
if (!empty($search)) {
    $sql .= " AND (nd.ho_ten LIKE ? OR nd.ten_dang_nhap LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
}

$sql .= " ORDER BY bl.thoi_diem_bat_dau DESC LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$students = [];

foreach ($result as $row) {
    $progress = $row['so_cau_da_lam'] . '/' . $row['tong_so_cau'];
    $percent = $row['tong_so_cau'] > 0 ? round(($row['so_cau_da_lam'] / $row['tong_so_cau']) * 100) : 0;
    
    // Tạo avatar từ tên (không có cột avatar trong database)
    $parts = explode(' ', $row['ho_ten']);
    $avatar = count($parts) >= 2 ? substr($parts[0], 0, 1) . substr($parts[1], 0, 1) : substr($row['ho_ten'], 0, 2);
    
    $so_vi_pham = (int)$row['so_vi_pham'];
    $is_online = true;
    $status_type = 'normal';
    $status_msg = 'Chưa có vi phạm nào';
    
    if ($so_vi_pham >= 5) {
        $status_type = 'disconnected';
        $status_msg = 'MẤT KẾT NỐI';
        $is_online = false;
    } elseif ($so_vi_pham >= 3) {
        $status_type = 'danger';
        $violations = explode('|', $row['ds_vi_pham'] ?? '');
        $status_msg = 'CẢNH BÁO VI PHẠM - ' . htmlspecialchars($violations[0] ?? 'Vi phạm');
    } elseif ($so_vi_pham >= 1) {
        $status_type = 'warning';
        $status_msg = 'CÓ ' . $so_vi_pham . ' CẢNH BÁO - ' . htmlspecialchars($row['ds_vi_pham'] ?? 'Vi phạm');
    }
    
    if ($status_filter === 'violation' && $status_type === 'normal') {
        continue;
    }
    if ($status_filter === 'disconnected' && $status_type !== 'disconnected') {
        continue;
    }
    
    $students[] = [
        'ma_nguoi_dung' => $row['ma_nguoi_dung'],
        'ma_bai_lam' => $row['ma_bai_lam'],
        'name' => htmlspecialchars($row['ho_ten']),
        'mssv' => htmlspecialchars($row['ten_dang_nhap']), // ten_dang_nhap thay cho mssv
        'thoi_diem_bat_dau' => $row['thoi_diem_bat_dau'],
        'avatar' => $avatar,
        'progress' => $progress,
        'percent' => $percent,
        'status_type' => $status_type,
        'status_msg' => $status_msg,
        'online' => $is_online
    ];
}

echo json_encode([
    'success' => true,
    'stats' => [
        'total_examining' => (int)$stats['total_examining'],
        'total_violations' => (int)$stats['total_violations']
    ],
    'students' => $students
]);

