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

    // Lấy trạng thái hiện tại
    $statusStmt = $conn->prepare("SELECT trang_thai FROM nguoi_dung WHERE ma_nguoi_dung = ?");
    $statusStmt->execute([$data['ma_nguoi_dung']]);
    $currentStatus = $statusStmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentStatus) {
        echo json_encode([
            'success' => false,
            'message' => 'Không tìm thấy người dùng'
        ]);
        exit;
    }

    // Chuyển đổi trạng thái: 0 -> 1 (mở khóa), 1 -> 0 (khóa)
    $newStatus = ($currentStatus['trang_thai'] == 1) ? 0 : 1;

    // Cập nhật trạng thái
    $updateStmt = $conn->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE ma_nguoi_dung = ?");
    $updateStmt->execute([$newStatus, $data['ma_nguoi_dung']]);

    $message = ($newStatus == 1) ? 'Mở khóa tài khoản thành công' : 'Khóa tài khoản thành công';

    echo json_encode([
        'success' => true,
        'message' => $message,
        'new_status' => $newStatus
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thay đổi trạng thái: ' . $e->getMessage()
    ]);
}

