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

// Validate required fields
if (empty($ma_ca_thi)) {
    echo json_encode(['success' => false, 'message' => 'Mã ca thi không hợp lệ']);
    exit;
}

// Check if shift exists
$checkStmt = $conn->prepare("SELECT ma_ca_thi FROM ca_thi WHERE ma_ca_thi = ?");
$checkStmt->execute([$ma_ca_thi]);
$checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

if (!$checkResult) {
    echo json_encode(['success' => false, 'message' => 'Ca thi không tồn tại']);
    exit;
}

// Check if there are registered students
$checkRegStmt = $conn->prepare("SELECT COUNT(*) as count FROM dang_ky_thi WHERE ma_ca_thi = ?");
$checkRegStmt->execute([$ma_ca_thi]);
$regRow = $checkRegStmt->fetch(PDO::FETCH_ASSOC);

if ($regRow['count'] > 0) {
    echo json_encode(['success' => false, 'message' => 'Không thể xóa ca thi đã có thí sinh đăng ký']);
    exit;
}

// Delete shift
$stmt = $conn->prepare("DELETE FROM ca_thi WHERE ma_ca_thi = ?");

try {
    $stmt->execute([$ma_ca_thi]);
    echo json_encode(['success' => true, 'message' => 'Xóa ca thi thành công']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa ca thi: ' . $e->getMessage()]);
}

