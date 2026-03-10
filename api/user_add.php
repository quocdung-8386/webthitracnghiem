<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['ho_ten']) || empty($data['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Họ tên và email là bắt buộc'
        ]);
        exit;
    }

    $checkStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ?");
    $checkStmt->execute([$data['email']]);
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
    
    $stmt = $conn->query("SELECT MAX(ma_nguoi_dung) as max_id FROM nguoi_dung");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxId = $result['max_id'] ?? 'ND00000';
    $num = intval(str_replace('ND', '', $maxId)) + 1;
    $ma_nguoi_dung = 'ND' . str_pad($num, 5, '0', STR_PAD_LEFT);

    $ten_dang_nhap = $data['ten_dang_nhap'] ?? explode('@', $data['email'])[0];
    $mat_khau = password_hash('123456', PASSWORD_BCRYPT);

    $insertStmt = $conn->prepare("
        INSERT INTO nguoi_dung (ma_nguoi_dung, ho_ten, email, ten_dang_nhap, mat_khau, ma_vai_tro, trang_thai)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ");
    $insertStmt->execute([
        $ma_nguoi_dung,
        $data['ho_ten'],
        $data['email'],
        $ten_dang_nhap,
        $mat_khau,
        $ma_vai_tro
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Thêm người dùng thành công',
        'user_id' => $ma_nguoi_dung
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm người dùng: ' . $e->getMessage()
    ]);
}
