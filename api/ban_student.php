<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$ma_bai_lam = isset($_POST['ma_bai_lam']) ? (int)$_POST['ma_bai_lam'] : 0;
$ly_do = isset($_POST['ly_do']) ? trim($_POST['ly_do']) : '';

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

$stmt = $conn->prepare("UPDATE bai_lam SET trang_thai = 'banned', thoi_gian_nop = NOW() WHERE ma_bai_lam = ?");

try {
    $stmt->execute([$ma_bai_lam]);
    
    if (!empty($ly_do)) {
        $lydoStmt = $conn->prepare("INSERT INTO vi_pham_thi (ma_bai_lam, loai_vi_pham, thoi_gian) VALUES (?, ?, NOW())");
        $lydoStmt->execute([$ma_bai_lam, 'Đình chỉ: ' . $ly_do]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Thí sinh đã bị đình chỉ']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

