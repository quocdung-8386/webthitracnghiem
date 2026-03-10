<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    $sql = "
    SELECT 
        nd.ma_nguoi_dung as id,
        nd.ho_ten as name,
        nd.email,
        nd.ten_dang_nhap as username,
        nd.trang_thai as status,
        nd.ma_vai_tro as role_id,
        vt.ten_vai_tro as role
    FROM nguoi_dung nd
    JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
    ORDER BY nd.ma_nguoi_dung DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format dữ liệu cho frontend
    $users = [];
    $colors = ['bg-blue-100 text-blue-600', 'bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-purple-100 text-purple-600'];

    foreach ($usersData as $index => $u) {
        $initial = strtoupper(substr($u['name'], 0, 1));
        $colorClass = $colors[$index % count($colors)];

        // Xử lý Role style
        $role_bg = 'bg-blue-50 text-blue-600';
        if ($u['role'] == 'Quản trị viên')
            $role_bg = 'bg-purple-50 text-purple-600';
        if ($u['role'] == 'Giảng viên')
            $role_bg = 'bg-emerald-50 text-emerald-600';

        // Xử lý Status - fix logic here
        $isActive = ($u['status'] == 1);
        $status = $isActive ? 'Đang hoạt động' : 'Bị khóa';
        $status_bg = $isActive ? 'bg-green-50' : 'bg-red-50';
        $status_text = $isActive ? 'text-green-600' : 'text-red-500';
        $dot = $isActive ? 'bg-green-500' : 'bg-red-500';

        $users[] = [
            'id' => $u['id'],
            'name' => $u['name'],
            'email' => $u['email'],
            'username' => $u['username'],
            'role' => $u['role'],
            'role_id' => $u['role_id'],
            'status' => $status,
            'status_value' => $u['status'],
            'initial' => $initial,
            'avatar_bg' => explode(' ', $colorClass)[0],
            'avatar_text' => explode(' ', $colorClass)[1],
            'role_bg' => explode(' ', $role_bg)[0],
            'role_text' => explode(' ', $role_bg)[1],
            'status_bg' => $status_bg,
            'status_text' => $status_text,
            'dot' => $dot
        ];
    }

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy danh sách người dùng: ' . $e->getMessage()
    ]);
}
