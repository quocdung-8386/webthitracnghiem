<?php
/**
 * API: Get Student Progress
 * Returns JSON data for student progress with search, filter, and pagination
 * Uses existing database tables: nguoi_dung, vai_tro, bai_lam, ca_thi
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

// Set UTF-8 for proper Vietnamese handling
ini_set('default_charset', 'UTF-8');

try {
    $conn = Database::getConnection();
    
    // Get parameters from request
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $classFilter = isset($_GET['class']) ? trim($_GET['class']) : '';
    
    // Calculate offset
    $offset = ($page - 1) * $limit;
    
    // Get role ID for "thi_sinh"
    $roleStmt = $conn->prepare("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = 'thi_sinh' LIMIT 1");
    $roleStmt->execute();
    $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$roleRow) {
        // Return empty data if role not found
        echo json_encode([
            'success' => true,
            'data' => [],
            'stats' => [
                ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => '0%', 'icon' => 'trending_up', 'color' => 'blue'],
                ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => '0%', 'icon' => 'check_circle_outline', 'color' => 'green'],
                ['title' => 'ĐIỂM TRUNG BÌNH', 'value' => '0', 'icon' => 'school', 'color' => 'orange'],
                ['title' => 'TỔNG SINH VIÊN', 'value' => '0', 'icon' => 'groups', 'color' => 'purple']
            ],
            'classes' => [],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total_records' => 0,
                'total_pages' => 0
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $thiSinhRoleId = $roleRow['ma_vai_tro'];
    
    // Get total available exam shifts
    $totalExamShifts = 0;
    $examShiftStmt = $conn->query("SELECT COUNT(*) as total FROM ca_thi");
    if ($examShiftStmt) {
        $totalExamShifts = (int)$examShiftStmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    // Default to 15 if no exam shifts exist
    $totalTasks = $totalExamShifts > 0 ? $totalExamShifts : 15;
    
    // Build WHERE clause with prepared statements
    $whereConditions = ["nd.ma_vai_tro = :role_id"];
    $params = [':role_id' => $thiSinhRoleId];
    
    // Search by name, student_code (ten_dang_nhap), or department
    if (!empty($search)) {
        $whereConditions[] = "(nd.ho_ten LIKE :search1 OR nd.ten_dang_nhap LIKE :search2 OR nd.email LIKE :search3)";
        $params[':search1'] = "%{$search}%";
        $params[':search2'] = "%{$search}%";
        $params[':search3'] = "%{$search}%";
    }
    
    // Filter by class - using email or ho_ten to simulate class filter
    if (!empty($classFilter) && $classFilter !== 'all') {
        $whereConditions[] = "nd.email LIKE :class_filter";
        $params[':class_filter'] = "%{$classFilter}%";
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total 
                 FROM nguoi_dung nd 
                 {$whereClause}";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get main data with pagination - join with bai_lam to get progress
    $sql = "SELECT 
                nd.ma_nguoi_dung as student_id,
                nd.ten_dang_nhap as student_code,
                nd.ho_ten as name,
                nd.email,
                COUNT(bl.ma_bai_lam) as completed_tasks,
                :total_tasks as total_tasks,
                COALESCE(AVG(bl.tong_diem), 0) as avg_score
            FROM nguoi_dung nd
            LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
            {$whereClause}
            GROUP BY nd.ma_nguoi_dung, nd.ten_dang_nhap, nd.ho_ten, nd.email
            ORDER BY nd.ma_nguoi_dung DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $conn->prepare($sql);
    $params[':total_tasks'] = $totalTasks;
    $params[':limit'] = $limit;
    $params[':offset'] = $offset;
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get unique "classes" from email domain or simulate from data
    // Using email domain as class simulation
    $classSql = "SELECT DISTINCT SUBSTRING_INDEX(email, '@', -1) as domain 
                 FROM nguoi_dung 
                 WHERE ma_vai_tro = :role_id 
                 ORDER BY domain";
    $classStmt = $conn->prepare($classSql);
    $classStmt->execute([':role_id' => $thiSinhRoleId]);
    $classes = $classStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get statistics
    $statsSql = "SELECT 
                    ROUND(AVG(progress_percent), 1) as avg_progress,
                    ROUND(AVG(avg_score), 1) as avg_score,
                    COUNT(*) as total_students,
                    SUM(CASE WHEN progress_percent >= 70 THEN 1 ELSE 0 END) as completed_count
                 FROM (
                    SELECT 
                        nd.ma_nguoi_dung,
                        COUNT(bl.ma_bai_lam) as completed_tasks,
                        :total_tasks2 as total_tasks,
                        (COUNT(bl.ma_bai_lam) / :total_tasks3 * 100) as progress_percent,
                        AVG(bl.tong_diem) as avg_score
                    FROM nguoi_dung nd
                    LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
                    WHERE nd.ma_vai_tro = :role_id2
                    GROUP BY nd.ma_nguoi_dung
                 ) as sub";
    $statsStmt = $conn->prepare($statsSql);
    $statsStmt->execute([
        ':total_tasks2' => $totalTasks,
        ':total_tasks3' => $totalTasks,
        ':role_id2' => $thiSinhRoleId
    ]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Format data for frontend (matching existing UI structure)
    $progressData = [];
    $avatarColors = [
        'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400',
        'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400',
        'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400',
        'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400'
    ];
    $barColors = [
        'bg-blue-600 dark:bg-blue-500',
        'bg-orange-500',
        'bg-green-500 dark:bg-green-400',
        'bg-purple-500 dark:bg-purple-400'
    ];
    $statusStyles = [
        'VƯỢT TIẾN ĐỘ' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400'],
        'HOÀN THÀNH TỐT' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400'],
        'CHẬM TIẾN ĐỘ' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-400'],
        'ĐÚNG LỘ TRÌNH' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
        'ĐANG HỌC' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400']
    ];
    
    foreach ($results as $index => $row) {
        // Generate avatar initials
        $nameParts = explode(' ', $row['name']);
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= mb_substr($part, 0, 1);
                if (strlen($initials) >= 2) break;
            }
        }
        $initials = strtoupper($initials);
        
        // Calculate percent
        $completed = (int)$row['completed_tasks'];
        $total = (int)$row['total_tasks'];
        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        // Determine status based on percent
        $status = 'ĐANG HỌC';
        if ($percent >= 80) {
            $status = 'VƯỢT TIẾN ĐỘ';
        } elseif ($percent >= 50) {
            $status = 'ĐÚNG LỘ TRÌNH';
        } elseif ($percent > 0 && $percent < 50) {
            $status = 'CHẬM TIẾN ĐỘ';
        }
        
        // Get status style
        $style = isset($statusStyles[$status]) ? $statusStyles[$status] : $statusStyles['ĐANG HỌC'];
        
        // Determine bar color based on percent
        $barColor = $barColors[0]; // default blue
        if ($percent >= 80) {
            $barColor = $barColors[2]; // green
        } elseif ($percent < 50 && $percent > 0) {
            $barColor = $barColors[1]; // orange
        }
        
        // Get department from email domain
        $email = $row['email'];
        $department = '';
        if (strpos($email, '@') !== false) {
            $domain = explode('@', $email)[1];
            $department = strtoupper(explode('.', $domain)[0]);
        }
        
        // Get class from email (before @)
        $class = '';
        if (strpos($email, '@') !== false) {
            $class = explode('@', $email)[0];
            // Take last part as class
            $parts = explode('.', $class);
            $class = end($parts);
        }
        
        $progressData[] = [
            'id' => htmlspecialchars($row['student_code']),
            'name' => htmlspecialchars($row['name']),
            'avatar' => $initials,
            'avatar_bg' => $avatarColors[$index % count($avatarColors)],
            'dept' => $department ?: 'THI_SINH',
            'class' => $class ?: 'Khoa',
            'completed' => $completed,
            'total_tasks' => $total,
            'percent' => $percent,
            'bar_color' => $barColor,
            'score' => number_format($row['avg_score'] ?: 0, 1),
            'status' => $status,
            'status_bg' => $style['bg'],
            'status_text' => $style['text']
        ];
    }
    
    // Format stats for frontend
    $formattedStats = [
        ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => ($stats['avg_progress'] ?: '0') . '%', 'icon' => 'trending_up', 'color' => 'blue'],
        ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => round(($stats['completed_count'] / max($stats['total_students'], 1)) * 100, 1) . '%', 'icon' => 'check_circle_outline', 'color' => 'green'],
        ['title' => 'ĐIỂM TRUNG BÌNH', 'value' => ($stats['avg_score'] ?: '0'), 'icon' => 'school', 'color' => 'orange'],
        ['title' => 'TỔNG SINH VIÊN', 'value' => number_format($stats['total_students'] ?: 0), 'icon' => 'groups', 'color' => 'purple']
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $progressData,
        'stats' => $formattedStats,
        'classes' => $classes,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lấy dữ liệu: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

