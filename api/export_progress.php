<?php
/**
 * API: Export Student Progress to Excel
 * Downloads progress data as Excel file using existing database tables
 */

require_once __DIR__ . '/../app/config/Database.php';

ini_set('default_charset', 'UTF-8');

try {
    $conn = Database::getConnection();
    
    // Get filter parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $classFilter = isset($_GET['class']) ? trim($_GET['class']) : '';
    
    // Get role ID for "thi_sinh"
    $roleStmt = $conn->prepare("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = 'thi_sinh' LIMIT 1");
    $roleStmt->execute();
    $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$roleRow) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<script>alert("Không tìm thấy vai trò thí sinh!"); window.history.back();</script>';
        exit;
    }
    
    $thiSinhRoleId = $roleRow['ma_vai_tro'];
    
    // Get total exam shifts
    $examShiftStmt = $conn->query("SELECT COUNT(*) as total FROM ca_thi");
    $totalTasks = 15;
    if ($examShiftStmt) {
        $totalTasks = (int)$examShiftStmt->fetch(PDO::FETCH_ASSOC)['total'];
        if ($totalTasks == 0) $totalTasks = 15;
    }
    
    // Build WHERE clause
    $whereConditions = ["nd.ma_vai_tro = :role_id"];
    $params = [':role_id' => $thiSinhRoleId];
    
    if (!empty($search)) {
        $whereConditions[] = "(nd.ho_ten LIKE :search1 OR nd.ten_dang_nhap LIKE :search2 OR nd.email LIKE :search3)";
        $params[':search1'] = "%{$search}%";
        $params[':search2'] = "%{$search}%";
        $params[':search3'] = "%{$search}%";
    }
    
    if (!empty($classFilter) && $classFilter !== 'all') {
        $whereConditions[] = "nd.email LIKE :class_filter";
        $params[':class_filter'] = "%{$classFilter}%";
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get all data for export
    $sql = "SELECT 
                nd.ten_dang_nhap as 'Mã SV',
                nd.ho_ten as 'Họ tên',
                nd.email as 'Email',
                COUNT(bl.ma_bai_lam) as 'Bài đã làm',
                :total_tasks as 'Tổng bài',
                ROUND(COUNT(bl.ma_bai_lam) / :total_tasks2 * 100, 0) as 'Tiến độ (%)',
                COALESCE(AVG(bl.tong_diem), 0) as 'Điểm TB',
                CASE 
                    WHEN COUNT(bl.ma_bai_lam) / :total_tasks3 >= 0.8 THEN 'VƯỢT TIẾN ĐỘ'
                    WHEN COUNT(bl.ma_bai_lam) / :total_tasks3 >= 0.5 THEN 'ĐÚNG LỘ TRÌNH'
                    WHEN COUNT(bl.ma_bai_lam) > 0 THEN 'CHẬM TIẾN ĐỘ'
                    ELSE 'ĐANG HỌC'
                END as 'Trạng thái'
            FROM nguoi_dung nd
            LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
            {$whereClause}
            GROUP BY nd.ma_nguoi_dung, nd.ten_dang_nhap, nd.ho_ten, nd.email
            ORDER BY nd.ma_nguoi_dung DESC";
    
    $stmt = $conn->prepare($sql);
    $params[':total_tasks'] = $totalTasks;
    $params[':total_tasks2'] = $totalTasks;
    $params[':total_tasks3'] = $totalTasks;
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<script>alert("Không có dữ liệu để xuất!"); window.history.back();</script>';
        exit;
    }
    
    // Generate CSV for Excel
    $filename = 'tien_trinh_hoc_tap_' . date('Y-m-d_His') . '.csv';
    
    // Output CSV headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Add BOM for UTF-8 Excel support
    echo "\xEF\xBB\xBF";
    
    // Output column headers
    $headers = array_keys($results[0]);
    echo '"' . implode('","', $headers) . '"' . "\n";
    
    // Output data rows
    foreach ($results as $row) {
        $escapedRow = array_map(function($value) {
            return str_replace('"', '""', $value);
        }, $row);
        echo '"' . implode('","', $escapedRow) . '"' . "\n";
    }
    
    exit;
    
} catch (PDOException $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<script>alert("Lỗi xuất dữ liệu: ' . addslashes($e->getMessage()) . '"); window.history.back();</script>';
    exit;
}

