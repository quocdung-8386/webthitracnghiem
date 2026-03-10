<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$ma_ca_thi = isset($_POST['ma_ca_thi']) ? (int)$_POST['ma_ca_thi'] : 0;
$ma_de_thi = isset($_POST['ma_de_thi']) ? (int)$_POST['ma_de_thi'] : 0;
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
$end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
$ma_phong = isset($_POST['ma_phong']) ? trim($_POST['ma_phong']) : '';

// Validate required fields
if (empty($ma_ca_thi) || empty($ma_de_thi) || empty($date) || empty($start_time) || empty($end_time) || empty($ma_phong)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng ngày không hợp lệ']);
    exit;
}

// Validate time format
if (!preg_match('/^\d{2}:\d{2}$/', $start_time) || !preg_match('/^\d{2}:\d{2}$/', $end_time)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng giờ không hợp lệ']);
    exit;
}

// Combine date and time
$thoi_gian_bat_dau = $date . ' ' . $start_time . ':00';
$thoi_gian_ket_thuc = $date . ' ' . $end_time . ':00';

// Validate time range
if (strtotime($thoi_gian_ket_thuc) <= strtotime($thoi_gian_bat_dau)) {
    echo json_encode(['success' => false, 'message' => 'Giờ kết thúc phải lớn hơn giờ bắt đầu']);
    exit;
}

// Escape output
$ma_phong = htmlspecialchars($ma_phong, ENT_QUOTES, 'UTF-8');

// Check if shift exists
$checkStmt = $conn->prepare("SELECT ma_ca_thi FROM ca_thi WHERE ma_ca_thi = ?");
$checkStmt->execute([$ma_ca_thi]);
$checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

if (!$checkResult) {
    echo json_encode(['success' => false, 'message' => 'Ca thi không tồn tại']);
    exit;
}

// Check if exam exists
$checkExamStmt = $conn->prepare("SELECT ma_de_thi FROM de_thi WHERE ma_de_thi = ?");
$checkExamStmt->execute([$ma_de_thi]);
$checkExamResult = $checkExamStmt->fetch(PDO::FETCH_ASSOC);

if (!$checkExamResult) {
    echo json_encode(['success' => false, 'message' => 'Đề thi không tồn tại']);
    exit;
}

// Update shift
$stmt = $conn->prepare("UPDATE ca_thi SET ma_de_thi = ?, thoi_gian_bat_dau = ?, thoi_gian_ket_thuc = ?, ma_phong = ? WHERE ma_ca_thi = ?");

try {
    $stmt->execute([$ma_de_thi, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $ma_phong, $ma_ca_thi]);
    echo json_encode(['success' => true, 'message' => 'Cập nhật ca thi thành công']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật ca thi: ' . $e->getMessage()]);
}

