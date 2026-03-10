<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$ma_bai_lam = isset($_POST['ma_bai_lam']) ? (int)$_POST['ma_bai_lam'] : 0;
$loai_vi_pham = isset($_POST['loai_vi_pham']) ? trim($_POST['loai_vi_pham']) : 'Cảnh báo từ giám sát viên';

if (empty($ma_bai_lam)) {
    echo json_encode(['success' => false, 'message' => 'Mã bài làm không hợp lệ']);
    exit;
}

$checkStmt = $conn->prepare("SELECT ma_bai_lam, trang_thai FROM bai_lam WHERE ma_bai_lam = ?");
$checkStmt->execute([$ma_bai_lam]);
$checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

if (!$checkResult) {
    echo json_encode(['success' => false, 'message' => 'Bài làm không tồn tại']);
    exit;
}

if ($checkResult['trang_thai'] !== 'dang_lam') {
    echo json_encode(['success' => false, 'message' => 'Thí sinh không đang trong phiên thi']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO vi_pham_thi (ma_bai_lam, loai_vi_pham, thoi_gian) VALUES (?, ?, NOW())");

try {
    $stmt->execute([$ma_bai_lam, $loai_vi_pham]);
    echo json_encode(['success' => true, 'message' => 'Cảnh báo đã được gửi']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

