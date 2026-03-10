<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate dữ liệu bắt buộc
    if (empty($data['ma_nguoi_dung'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Mã người dùng không được để trống'
        ]);
        exit;
    }

    // Xóa người dùng
    $deleteStmt = $conn->prepare("DELETE FROM nguoi_dung WHERE ma_nguoi_dung = ?");
    $deleteStmt->execute([$data['ma_nguoi_dung']]);

    if ($deleteStmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy người dùng để xóa'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi xóa người dùng: ' . $e->getMessage()
    ]);
}

