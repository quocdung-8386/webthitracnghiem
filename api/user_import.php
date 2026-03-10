<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

try {
    $conn = Database::getConnection();
    
    // Kiểm tra phương thức request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không hợp lệ'
        ]);
        exit;
    }

    // Kiểm tra file upload
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng chọn file để import'
        ]);
        exit;
    }

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Chỉ chấp nhận xlsx, xls, csv
    if (!in_array($fileExt, ['xlsx', 'xls', 'csv'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Chỉ hỗ trợ file .xlsx, .xls, .csv'
        ]);
        exit;
    }

    $importedCount = 0;
    $errorCount = 0;
    $errors = [];

    // Xử lý file CSV đơn giản
    if ($fileExt === 'csv') {
        $handle = fopen($file['tmp_name'], 'r');
        $headerSkipped = false;
        
        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Bỏ qua dòng header
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }

            if (count($row) < 2) continue;

            $ho_ten = trim($row[0] ?? '');
            $email = trim($row[1] ?? '');
            $vai_tro = isset($row[2]) ? trim($row[2]) : 'Thí sinh';

            if (empty($ho_ten) || empty($email)) {
                $errorCount++;
                continue;
            }

            // Kiểm tra email trùng
            $checkStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $errorCount++;
                $errors[] = "Email $email đã tồn tại";
                continue;
            }

            // Lấy mã vai trò
            $roleStmt = $conn->prepare("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = ?");
            $roleStmt->execute([$vai_tro]);
            $role = $roleStmt->fetch(PDO::FETCH_ASSOC);
            $ma_vai_tro = $role ? $role['ma_vai_tro'] : 3;

            // Tạo mã người dùng
            $stmt = $conn->query("SELECT MAX(ma_nguoi_dung) as max_id FROM nguoi_dung");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_id = ($result['max_id'] ?? 0) + 1;
            $ma_nguoi_dung = 'ND' . str_pad($new_id, 5, '0', STR_PAD_LEFT);

            // Tạo tên đăng nhập
            $ten_dang_nhap = explode('@', $email)[0];
            $mat_khau = password_hash('123456', PASSWORD_BCRYPT);

            // Insert
            $insertStmt = $conn->prepare("
                INSERT INTO nguoi_dung (ma_nguoi_dung, ho_ten, email, ten_dang_nhap, mat_khau, ma_vai_tro, trang_thai)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            
            try {
                $insertStmt->execute([
                    $ma_nguoi_dung,
                    $ho_ten,
                    $email,
                    $ten_dang_nhap,
                    $mat_khau,
                    $ma_vai_tro
                ]);
                $importedCount++;
            } catch (Exception $e) {
                $errorCount++;
            }
        }
        fclose($handle);
    } else {
        // Xử lý Excel (xlsx, xls) - cần thư viện PHPExcel
        // Tạm thời thông báo yêu cầu file CSV
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng sử dụng file CSV. Để hỗ trợ Excel (.xlsx, .xls), cần cài đặt thư viện PhpSpreadsheet.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => "Import thành công: $importedCount người dùng. Thất bại: $errorCount",
        'imported' => $importedCount,
        'errors' => array_slice($errors, 0, 5) // Giới hạn 5 lỗi đầu tiên
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi import: ' . $e->getMessage()
    ]);
}

