<?php
/**
 * API Lấy lịch sử xuất báo cáo
 * File: api/get_report_history.php
 * Method: GET
 * Parameters: 
 *   - search: Từ khóa tìm kiếm theo tên file
 *   - page: Số trang hiện tại (mặc định: 1)
 *   - limit: Số bản ghi/trang (mặc định: 10)
 * 
 * Response JSON:
 * {
 *   "success": true,
 *   "data": [...],
 *   "pagination": {
 *     "current_page": 1,
 *     "total_pages": 5,
 *     "total_records": 50,
 *     "from": 1,
 *     "to": 10
 *   }
 * }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Chỉ chấp nhận GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false, 
        'message' => 'Chỉ chấp nhận phương thức GET'
    ]);
    exit;
}

require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    // Lấy parameters từ query string
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    // Validate pagination parameters
    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 10;
    if ($limit > 50) $limit = 50; // Giới hạn max 50 bản ghi/trang
    
    $offset = ($page - 1) * $limit;
    
    // Chuẩn bị câu lệnh đếm tổng số bản ghi
    $whereClause = "";
    $params = [];
    
    if (!empty($search)) {
        $whereClause = " WHERE ten_file LIKE :search";
        $params[':search'] = "%{$search}%";
    }
    
    // Đếm tổng số bản ghi
    $countSql = "SELECT COUNT(*) as total FROM bao_cao_xuat" . $whereClause;
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;
    
    // Query lấy dữ liệu phân trang
    // Theo yêu cầu: ma_bao_cao, ten_file, loai_bao_cao, ngay_tao, dinh_dang, dung_luong, duong_dan_file
    $sql = "SELECT 
                ma_bao_cao,
                ten_file,
                loai_bao_cao,
                ngay_tao,
                dinh_dang,
                dung_luong,
                duong_dan_file
            FROM bao_cao_xuat"
            . $whereClause . 
            " ORDER BY ngay_tao DESC 
            LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    if (!empty($search)) {
        $stmt->bindValue(':search', "%{$search}%", PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dữ liệu để phù hợp với frontend
    $formatStyles = [
        'XLSX' => ['bg' => 'bg-green-50 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400'],
        'PDF' => ['bg' => 'bg-red-50 dark:bg-red-900/30', 'text' => 'text-red-500 dark:text-red-400'],
        'CSV' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400']
    ];
    
    $formattedReports = [];
    foreach ($reports as $report) {
        $format = strtoupper($report['dinh_dang']);
        $style = $formatStyles[$format] ?? $formatStyles['XLSX'];
        
        $formattedReports[] = [
            'id' => $report['ma_bao_cao'],
            'name' => $report['ten_file'],
            'type' => $report['loai_bao_cao'],
            'date' => date('d/m/Y H:i', strtotime($report['ngay_tao'])),
            'format' => $format,
            'format_bg' => $style['bg'],
            'format_text' => $style['text'],
            'size' => $report['dung_luong'],
            'path' => $report['duong_dan_file']
        ];
    }
    
    // Trả về JSON theo format yêu cầu
    echo json_encode([
        'success' => true,
        'data' => $formattedReports,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'from' => $totalRecords > 0 ? $offset + 1 : 0,
            'to' => min($offset + $limit, $totalRecords)
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi database: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

