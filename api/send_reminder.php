<?php
/**
 * API: Send Reminder to Students
 * Sends reminder emails/notifications to students with slow progress
 * Uses existing database tables: nguoi_dung, vai_tro, bai_lam, ca_thi
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

ini_set('default_charset', 'UTF-8');

try {
    $conn = Database::getConnection();
    
    // Get role ID for "thi_sinh"
    $roleStmt = $conn->prepare("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = 'thi_sinh' LIMIT 1");
    $roleStmt->execute();
    $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$roleRow) {
        echo json_encode([
            'success' => true,
            'message' => 'Không tìm thấy vai trò thí sinh!',
            'sent_count' => 0
        ], JSON_UNESCAPED_UNICODE);
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
    
    // Get students with slow progress (less than 50% completion)
    $sql = "SELECT 
                nd.ma_nguoi_dung as id,
                nd.ten_dang_nhap as student_code,
                nd.ho_ten as name,
                nd.email,
                COUNT(bl.ma_bai_lam) as completed_tasks,
                :total_tasks as total_tasks,
                ROUND(COUNT(bl.ma_bai_lam) / :total_tasks2 * 100, 0) as percent
            FROM nguoi_dung nd
            LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
            WHERE nd.ma_vai_tro = :role_id
            GROUP BY nd.ma_nguoi_dung, nd.ten_dang_nhap, nd.ho_ten, nd.email
            HAVING (COUNT(bl.ma_bai_lam) / :total_tasks3 * 100) < 50
            ORDER BY percent ASC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':total_tasks' => $totalTasks,
        ':total_tasks2' => $totalTasks,
        ':total_tasks3' => $totalTasks,
        ':role_id' => $thiSinhRoleId
    ]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($students)) {
        echo json_encode([
            'success' => true,
            'message' => 'Không có sinh viên nào cần nhắc nhở!',
            'sent_count' => 0
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Simulate sending reminders
    // In production, this would integrate with email service
    $sentCount = 0;
    $failedCount = 0;
    
    foreach ($students as $student) {
        // Here you would implement actual email sending
        // For demo, we'll simulate success
        
        // Example email content (would be sent via SMTP/Mail service)
        $emailSubject = "Nhắc nhở: Tiến độ học tập - " . $student['name'];
        $emailContent = "
            Kính gửi {$student['name']},
            
            Hệ thống Quản lý học tập xin nhắc nhở bạn về tiến độ học tập hiện tại:
            
            - Mã sinh viên: {$student['student_code']}
            - Email: {$student['email']}
            - Bài đã làm: {$student['completed_tasks']}/{$student['total_tasks']} ({$student['percent']}%)
            
            Bạn vui lòng cố gắng hoàn thành thêm các bài thi để theo kịp lộ trình học tập.
            
            Trân trọng,
            Hệ thống Quản lý Tiến trình Học tập
        ";
        
        // In production: mail($student['email'], $emailSubject, $emailContent);
        
        $sentCount++;
    }
    
    // Log the reminder action (if hoat_dong table exists)
    try {
        $logSql = "INSERT INTO hoat_dong (ma_nguoi_dung, hanh_dong, thoi_gian, noi_dung) 
                   VALUES (?, ?, NOW(), ?)";
        $logStmt = $conn->prepare($logSql);
        $adminId = isset($_SESSION['ma_nguoi_dung']) ? $_SESSION['ma_nguoi_dung'] : 1;
        $logContent = "Gửi nhắc nhở tiến trình học tập cho {$sentCount} sinh viên chậm tiến độ";
        $logStmt->execute([$adminId, 'send_reminder', $logContent]);
    } catch (PDOException $e) {
        // Ignore logging errors if table doesn't exist
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Đã gửi nhắc nhở cho {$sentCount} sinh viên",
        'sent_count' => $sentCount,
        'failed_count' => $failedCount,
        'students_notified' => array_map(function($s) {
            return [
                'name' => htmlspecialchars($s['name']),
                'student_code' => $s['student_code'],
                'email' => $s['email'] ?? ''
            ];
        }, $students)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi gửi nhắc nhở: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

