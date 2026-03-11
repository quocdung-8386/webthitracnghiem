<?php
/**
 * API Xuất báo cáo dữ liệu thi
 * File: api/export_report.php
 * Method: POST
 * Parameters (JSON body):
 *   - loai_bao_cao: Loại báo cáo (theo_lop, theo_mon, tong_hop, ca_nhan)
 *   - tu_ngay: Ngày bắt đầu (YYYY-MM-DD)
 *   - den_ngay: Ngày kết thúc (YYYY-MM-DD)
 *   - dinh_dang: Định dạng file (XLSX, PDF, CSV)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST']);
    exit;
}

require_once __DIR__ . '/../app/config/Database.php';

// Thư viện PhpSpreadsheet
$vendorAutoload = null;
$possiblePaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php'
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $vendorAutoload = $path;
        break;
    }
}

try {
    // Lấy dữ liệu từ request body (JSON)
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Dữ liệu JSON không hợp lệ');
    }
    
    $loai_bao_cao = $input['loai_bao_cao'] ?? 'tong_hop';
    $tu_ngay = $input['tu_ngay'] ?? date('Y-m-d', strtotime('-30 days'));
    $den_ngay = $input['den_ngay'] ?? date('Y-m-d');
    $dinh_dang = strtoupper($input['dinh_dang'] ?? 'XLSX');
    
    // Validate định dạng file
    if (!in_array($dinh_dang, ['XLSX', 'PDF', 'CSV'])) {
        throw new Exception('Định dạng file không hợp lệ. Chỉ chấp nhận XLSX, PDF, CSV');
    }
    
    // Validate ngày
    if (!strtotime($tu_ngay) || !strtotime($den_ngay)) {
        throw new Exception('Ngày không hợp lệ');
    }
    
    $conn = Database::getConnection();
    
    // ========================================
    // Query dữ liệu từ database
    // Bảng: bai_lam, nguoi_dung, ca_thi, de_thi
    // ========================================
    $sql = "SELECT 
                bl.ma_bai_lam,
                nd.ho_ten as ten_thi_sinh,
                nd.email,
                nd.ten_dang_nhap,
                dt.tieu_de as ten_de_thi,
                ct.thoi_gian_bat_dau,
                ct.thoi_gian_ket_thuc,
                bl.thoi_diem_bat_dau,
                bl.thoi_diem_nop,
                bl.tong_diem,
                bl.trang_thai,
                TIMESTAMPDIFF(MINUTE, bl.thoi_diem_bat_dau, bl.thoi_diem_nop) as thoi_gian_lam
            FROM bai_lam bl
            INNER JOIN nguoi_dung nd ON bl.ma_nguoi_dung = nd.ma_nguoi_dung
            INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
            INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
            WHERE DATE(bl.thoi_diem_nop) BETWEEN :tu_ngay AND :den_ngay
            AND bl.trang_thai = 'da_nop'
            ORDER BY bl.thoi_diem_nop DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':tu_ngay', $tu_ngay);
    $stmt->bindValue(':den_ngay', $den_ngay);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($data)) {
        throw new Exception('Không có dữ liệu trong khoảng thời gian từ ' . $tu_ngay . ' đến ' . $den_ngay);
    }
    
    // Tạo tên file
    $loai_bao_cao_text = [
        'theo_lop' => 'Bao_cao_theo_lop',
        'theo_mon' => 'Bao_cao_theo_mon',
        'tong_hop' => 'Bao_cao_tong_hop',
        'ca_nhan' => 'Bao_cao_ca_nhan'
    ];
    
    $prefix = $loai_bao_cao_text[$loai_bao_cao] ?? 'Bao_cao';
    $filename = $prefix . '_' . date('Ymd_His');
    $extension = strtolower($dinh_dang);
    
    // Thư mục lưu file
    $uploadDir = __DIR__ . '/../storage/reports/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Không thể tạo thư mục lưu trữ');
        }
    }
    
    $filePath = $uploadDir . $filename . '.' . $extension;
    $fileUrl = '/storage/reports/' . $filename . '.' . $extension;
    
    // Xuất file theo định dạng
    if ($dinh_dang === 'XLSX') {
        if ($vendorAutoload === null) {
            throw new Exception('Thư viện PhpSpreadsheet chưa được cài đặt');
        }
        require_once $vendorAutoload;
        $result = exportToXLSX($data, $filePath, $tu_ngay, $den_ngay);
    } elseif ($dinh_dang === 'CSV') {
        $result = exportToCSV($data, $filePath, $tu_ngay, $den_ngay);
    } elseif ($dinh_dang === 'PDF') {
        $result = exportToPDF($data, $filePath, $tu_ngay, $den_ngay);
    }
    
    // Kiểm tra file đã được tạo
    if (!file_exists($filePath)) {
        throw new Exception('Không thể tạo file báo cáo');
    }
    
    // Tính dung lượng file
    $fileSize = filesize($filePath);
    $fileSizeStr = formatBytes($fileSize);
    
    // Lưu vào database bảng bao_cao_xuat
    $insertSql = "INSERT INTO bao_cao_xuat 
                  (ten_file, loai_bao_cao, dinh_dang, dung_luong, duong_dan_file) 
                  VALUES (:ten_file, :loai_bao_cao, :dinh_dang, :dung_luong, :duong_dan_file)";
    
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->execute([
        ':ten_file' => $filename . '.' . $extension,
        ':loai_bao_cao' => getLoaiBaoCaoText($loai_bao_cao),
        ':dinh_dang' => $dinh_dang,
        ':dung_luong' => $fileSizeStr,
        ':duong_dan_file' => $fileUrl
    ]);
    
    $ma_bao_cao = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Xuất báo cáo thành công',
        'data' => [
            'id' => $ma_bao_cao,
            'filename' => $filename . '.' . $extension,
            'path' => $fileUrl,
            'size' => $fileSizeStr,
            'format' => $dinh_dang,
            'record_count' => count($data)
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Xuất file Excel (XLSX) sử dụng PhpSpreadsheet
 */
function exportToXLSX($data, $filePath, $tu_ngay, $den_ngay) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Tiêu đề báo cáo
    $sheet->mergeCells('A1:J1');
    $sheet->setCellValue('A1', 'BÁO CÁO KẾT QUẢ THI TRẮC NGHIỆM');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    // Thông tin thời gian
    $sheet->mergeCells('A2:J2');
    $sheet->setCellValue('A2', 'Từ ngày: ' . date('d/m/Y', strtotime($tu_ngay)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($den_ngay)));
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    // Header columns
    $headers = ['STT', 'Mã bài làm', 'Họ tên', 'Email', 'Tên đề thi', 'Thời gian bắt đầu', 'Thời gian nộp', 'Thời gian làm (phút)', 'Điểm số', 'Trạng thái'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '4', $header);
        $col++;
    }
    
    // Style header
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '254ada']],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A4:J4')->applyFromArray($headerStyle);
    
    // Dữ liệu
    $row = 5;
    $stt = 1;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $stt++);
        $sheet->setCellValue('B' . $row, $item['ma_bai_lam']);
        $sheet->setCellValue('C' . $row, $item['ten_thi_sinh']);
        $sheet->setCellValue('D' . $row, $item['email']);
        $sheet->setCellValue('E' . $row, $item['ten_de_thi']);
        $sheet->setCellValue('F' . $row, date('d/m/Y H:i', strtotime($item['thoi_diem_bat_dau'])));
        $sheet->setCellValue('G' . $row, date('d/m/Y H:i', strtotime($item['thoi_diem_nop'])));
        $sheet->setCellValue('H' . $row, $item['thoi_gian_lam'] ?? 0);
        $sheet->setCellValue('I' . $row, $item['tong_diem'] ?? 0);
        $sheet->setCellValue('J' . $row, getStatusText($item['trang_thai']));
        
        // Style hàng
        $sheet->getStyle('A' . $row . ':J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $row++;
    }
    
    // Auto-size columns
    foreach (range('A', 'J') as $colLetter) {
        $sheet->getColumnDimension($colLetter)->setAutoSize(true);
    }
    
    // Tổng kết
    $sheet->mergeCells('A' . $row . ':H' . $row);
    $sheet->setCellValue('A' . $row, 'Tổng số bài thi: ' . count($data));
    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
    
    // Save file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($filePath);
    
    return true;
}

/**
 * Xuất file CSV
 */
function exportToCSV($data, $filePath, $tu_ngay, $den_ngay) {
    $fp = fopen($filePath, 'w');
    
    if (!$fp) {
        throw new Exception('Không thể tạo file CSV');
    }
    
    // UTF-8 BOM for Excel
    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Tiêu đề
    fputcsv($fp, ['BÁO CÁO KẾT QUẢ THI TRẮC NGHIỆM']);
    fputcsv($fp, ['Từ ngày: ' . date('d/m/Y', strtotime($tu_ngay)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($den_ngay))]);
    fputcsv($fp, []); // Empty row
    
    // Headers
    $headers = ['STT', 'Mã bài làm', 'Họ tên', 'Email', 'Tên đề thi', 'Thời gian bắt đầu', 'Thời gian nộp', 'Thời gian làm (phút)', 'Điểm số', 'Trạng thái'];
    fputcsv($fp, $headers);
    
    // Data
    $stt = 1;
    foreach ($data as $item) {
        fputcsv($fp, [
            $stt++,
            $item['ma_bai_lam'],
            $item['ten_thi_sinh'],
            $item['email'],
            $item['ten_de_thi'],
            date('d/m/Y H:i', strtotime($item['thoi_diem_bat_dau'])),
            date('d/m/Y H:i', strtotime($item['thoi_diem_nop'])),
            $item['thoi_gian_lam'] ?? 0,
            $item['tong_diem'] ?? 0,
            getStatusText($item['trang_thai'])
        ]);
    }
    
    fclose($fp);
    return true;
}

/**
 * Xuất file PDF (Sử dụng TCPDF nếu có, fallback văn bản nếu không)
 */
function exportToPDF($data, $filePath, $tu_ngay, $den_ngay) {
    // Thử load TCPDF
    $tcpdfPaths = [
        __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php',
        __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php'
    ];
    
    $tcpdfLoaded = false;
    foreach ($tcpdfPaths as $tcpdfPath) {
        if (file_exists($tcpdfPath)) {
            require_once($tcpdfPath);
            $tcpdfLoaded = true;
            break;
        }
    }
    
    if ($tcpdfLoaded) {
        $pdf = new \TCPDF();
        $pdf->SetCreator('Hệ Thống Thi Trực Tuyến');
        $pdf->AddPage();
        
        // Tiêu đề
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'BÁO CÁO KẾT QUẢ THI TRẮC NGHIỆM', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Từ ngày: ' . date('d/m/Y', strtotime($tu_ngay)) . ' - Đến ngày: ' . date('d/m/Y', strtotime($den_ngay)), 0, 1, 'C');
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(37, 74, 218);
        $pdf->SetTextColor(255, 255, 255);
        
        $headers = ['STT', 'Họ tên', 'Email', 'Đề thi', 'Điểm', 'Trạng thái'];
        $widths = [15, 40, 50, 50, 20, 25];
        
        foreach ($headers as $i => $header) {
            $pdf->Cell($widths[$i], 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Data
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        
        $stt = 1;
        foreach ($data as $item) {
            $pdf->Cell($widths[0], 6, $stt++, 1);
            $pdf->Cell($widths[1], 6, mb_substr($item['ten_thi_sinh'], 0, 20), 1);
            $pdf->Cell($widths[2], 6, mb_substr($item['email'], 0, 25), 1);
            $pdf->Cell($widths[3], 6, mb_substr($item['ten_de_thi'], 0, 25), 1);
            $pdf->Cell($widths[4], 6, $item['tong_diem'] ?? 0, 1, 0, 'C');
            $pdf->Cell($widths[5], 6, getStatusText($item['trang_thai']), 1, 0, 'C');
            $pdf->Ln();
            
            if ($stt > 30) {
                $pdf->Cell(0, 6, '... (tiếp theo trang khác)', 0, 1, 'C');
                break;
            }
        }
        
        $pdf->Output($filePath, 'F');
    } else {
        // Fallback: Tạo file text nếu không có TCPDF
        $content = "BÁO CÁO KẾT QUẢ THI TRẮC NGHIỆM\n";
        $content .= "Từ ngày: " . date('d/m/Y', strtotime($tu_ngay)) . " - Đến ngày: " . date('d/m/Y', strtotime($den_ngay)) . "\n";
        $content .= "=====================================\n\n";
        
        foreach ($data as $item) {
            $content .= "Mã: {$item['ma_bai_lam']} | {$item['ten_thi_sinh']} | {$item['ten_de_thi']} | Điểm: {$item['tong_diem']}\n";
        }
        
        $content .= "\n=====================================\n";
        $content .= "Tổng số: " . count($data) . " bài thi\n";
        
        if (file_put_contents($filePath, $content) === false) {
            throw new Exception('Không thể tạo file PDF');
        }
    }
    return true;
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Get loại báo cáo text
 */
function getLoaiBaoCaoText($loai) {
    $texts = [
        'theo_lop' => 'Báo cáo theo lớp',
        'theo_mon' => 'Báo cáo theo môn',
        'tong_hop' => 'Báo cáo tổng hợp',
        'ca_nhan' => 'Báo cáo cá nhân'
    ];
    return $texts[$loai] ?? 'Báo cáo tổng hợp';
}

/**
 * Get trạng thái text
 */
function getStatusText($status) {
    $texts = [
        'dang_lam' => 'Đang làm',
        'da_nop' => 'Đã nộp',
        'da_cham' => 'Đã chấm'
    ];
    return $texts[$status] ?? $status;
}

