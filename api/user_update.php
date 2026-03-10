<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['ma_nguoi_dung']) || empty($data['ho_ten']) || empty($data['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Mã người dùng, họ tên và email là bắt buộc'
        ]);
        exit;
    }

    $checkStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ? AND ma_nguoi_dung != ?");
    $checkStmt->execute([$data['email'], $data['ma_nguoi_dung']]);
    if ($checkStmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email đã tồn tại trong hệ thống'
        ]);
        exit;
    }

    $roleMap = [
        'Quản trị viên' => 1,
        'Giảng viên' => 2,
        'Thí sinh' => 3
    ];
    $roleName = $data['vai_tro'] ?? 'Thí sinh';
    $ma_vai_tro = isset($roleMap[$roleName]) ? $roleMap[$roleName] : 3;

    $updateStmt = $conn->prepare("
        UPDATE nguoi_dung 
        SET ho_ten = ?, email = ?, ma_vai_tro = ?
        WHERE ma_nguoi_dung = ?
    ");
    $updateStmt->execute([
        $data['ho_ten'],
        $data['email'],
        $ma_vai_tro,
        $data['ma_nguoi_dung']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật người dùng thành công'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật người dùng: ' . $e->getMessage()
    ]);
}
