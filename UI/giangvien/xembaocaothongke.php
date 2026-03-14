<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../app/config/Database.php';

if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'giangvien') {
    header("Location: ../login.php");
    exit();
}

$db = Database::getConnection();
$ma_giao_vien = $_SESSION['ma_nguoi_dung'] ?? $_SESSION['user']['ma_nguoi_dung'];
$ho_ten_gv = $_SESSION['ho_ten'] ?? $_SESSION['user']['ho_ten'] ?? 'Giảng viên';

$name_parts = explode(' ', trim($ho_ten_gv));
$first_letter = strtoupper(substr(end($name_parts), 0, 1));

// 2. XỬ LÝ BỘ LỌC (Lấy mã đề thi từ URL)
$selected_exam = $_GET['ma_de_thi'] ?? 'all';

$where_clause = "dt.ma_giao_vien = ?";
$params = [$ma_giao_vien];

if ($selected_exam !== 'all') {
    $where_clause .= " AND dt.ma_de_thi = ?";
    $params[] = $selected_exam;
}

// 3. TRUY VẤN DỮ LIỆU THỐNG KÊ

// A. Lấy danh sách Đề thi để đưa vào Ô chọn (Dropdown)
$stmt_list_exam = $db->prepare("SELECT ma_de_thi, tieu_de FROM de_thi WHERE ma_giao_vien = ? ORDER BY ngay_tao DESC");
$stmt_list_exam->execute([$ma_giao_vien]);
$list_exams = $stmt_list_exam->fetchAll(PDO::FETCH_ASSOC);

// B. Lấy các con số Tổng quan (Tổng bài, Điểm TB, Đậu, Rớt)
$sql_stats = "SELECT 
                COUNT(b.ma_bai_lam) as total_bai,
                AVG(b.tong_diem) as avg_diem,
                SUM(CASE WHEN b.tong_diem >= 5 THEN 1 ELSE 0 END) as pass_count,
                SUM(CASE WHEN b.tong_diem < 5 THEN 1 ELSE 0 END) as fail_count
              FROM bai_lam b
              JOIN ca_thi c ON b.ma_ca_thi = c.ma_ca_thi
              JOIN de_thi dt ON c.ma_de_thi = dt.ma_de_thi
              WHERE b.trang_thai = 'da_nop' AND b.tong_diem IS NOT NULL AND $where_clause";
$stmt_stats = $db->prepare($sql_stats);
$stmt_stats->execute($params);
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

$total_bai = $stats['total_bai'] ?: 0;
$avg_diem = round($stats['avg_diem'] ?: 0, 1);
$pass_count = $stats['pass_count'] ?: 0;
$fail_count = $stats['fail_count'] ?: 0;
$pass_rate = $total_bai > 0 ? round(($pass_count / $total_bai) * 100) : 0;

// C. Lấy dữ liệu Phổ điểm (Yếu, TB, Khá, Giỏi)
$sql_dist = "SELECT 
                SUM(CASE WHEN b.tong_diem < 5 THEN 1 ELSE 0 END) as kem,
                SUM(CASE WHEN b.tong_diem >= 5 AND b.tong_diem < 7 THEN 1 ELSE 0 END) as trung_binh,
                SUM(CASE WHEN b.tong_diem >= 7 AND b.tong_diem < 9 THEN 1 ELSE 0 END) as kha,
                SUM(CASE WHEN b.tong_diem >= 9 THEN 1 ELSE 0 END) as gioi
             FROM bai_lam b
             JOIN ca_thi c ON b.ma_ca_thi = c.ma_ca_thi
             JOIN de_thi dt ON c.ma_de_thi = dt.ma_de_thi
             WHERE b.trang_thai = 'da_nop' AND b.tong_diem IS NOT NULL AND $where_clause";
$stmt_dist = $db->prepare($sql_dist);
$stmt_dist->execute($params);
$dist = $stmt_dist->fetch(PDO::FETCH_ASSOC);

// D. Lấy Danh sách chi tiết bảng điểm (Sắp xếp từ cao xuống thấp)
$sql_details = "SELECT 
                    nd.ho_ten, 
                    nd.ten_dang_nhap as mssv, 
                    dt.tieu_de, 
                    b.tong_diem, 
                    b.thoi_diem_nop
                FROM bai_lam b
                JOIN nguoi_dung nd ON b.ma_nguoi_dung = nd.ma_nguoi_dung
                JOIN ca_thi c ON b.ma_ca_thi = c.ma_ca_thi
                JOIN de_thi dt ON c.ma_de_thi = dt.ma_de_thi
                WHERE b.trang_thai = 'da_nop' AND b.tong_diem IS NOT NULL AND $where_clause
                ORDER BY b.tong_diem DESC, b.thoi_diem_nop ASC";
$stmt_details = $db->prepare($sql_details);
$stmt_details->execute($params);
$danhSachDiem = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê & Báo cáo</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; flex-direction: column; justify-content: center; }
        .stat-val { font-size: 32px; font-weight: bold; color: #0f172a; margin-bottom: 5px; }
        .stat-lbl { font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .text-blue { color: #2563eb; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-purple { color: #9333ea; }

        .chart-container { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 30px; height: 350px; display: flex; flex-direction: column; justify-content: flex-end; }
        .chart-bars { display: flex; align-items: flex-end; justify-content: space-around; height: 200px; padding-top: 30px; border-bottom: 2px solid #e2e8f0; gap: 20px;}
        .bar-wrapper { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; }
        .bar { width: 60%; max-width: 80px; border-radius: 6px 6px 0 0; position: relative; transition: height 0.5s ease-in-out; }
        .bar-val { position: absolute; top: -25px; width: 100%; text-align: center; font-weight: bold; font-size: 14px; }
        .bar-lbl { margin-top: 15px; font-size: 13px; font-weight: 600; color: #475569; text-align: center; }

        .badge-pass { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #bbf7d0;}
        .badge-fail { background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; border: 1px solid #fecaca;}
        
        .empty-box { text-align: center; padding: 60px 20px; background: #f8fafc; border-radius: 12px; border: 2px dashed #cbd5e1; }

        .sidebar .logo { display: flex; align-items: center; gap: 12px; padding: 20px 20px 10px 20px; margin-bottom: 20px; text-decoration: none;}
        .logo-icon-bg { position: relative; background-color: #2563eb; color: #ffffff; display: flex; justify-content: center; align-items: center; width: 35px; height: 35px; border-radius: 8px; flex-shrink: 0; }
        .logo-graduation-cap { font-size: 16px; z-index: 1; margin-top: -6px; }
        .logo-book-pages { position: absolute; bottom: 6px; width: 22px; height: 10px; background-color: transparent; display: flex; justify-content: center; align-items: flex-end; }
        .logo-book-pages::before, .logo-book-pages::after { content: ""; width: 10px; height: 8px; background-color: #ffffff; border-radius: 2px; transform: rotate(-10deg); margin: 0 -1px; }
        .logo-book-pages::after { transform: rotate(10deg); }
        .sidebar .logo-text { color: #1a202c; font-weight: 800; font-size: 15px; line-height: 1.3; }
    </style>
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div>
            <div class="logo"><div class="logo-icon-bg"><span class="logo-graduation-cap">&#127891;</span><div class="logo-book-pages"></div></div><span class="logo-text">Hệ thống thi<br>trực tuyến</span></div>
            <ul class="nav-menu">
                <li><a href="index.php">Tổng quan</a></li>
                <li><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">Tạo & Thiết lập đề thi</a></li>
                <li><a href="chambaituluan.php">Chấm bài tự luận</a></li>
                <li class="active"><a href="xembaocaothongke.php">Báo cáo & Thống kê</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout-sidebar" style="color: #ef4444; font-weight: bold;">Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb">Quản lý / Thống kê & Báo cáo</div>
                <h1>Phân tích kết quả thi</h1>
            </div>
            <div class="user-profile">
                <div style="text-align: right; margin-right: 15px;">
                    <strong style="display:block; color:#2d3748;"><?php echo htmlspecialchars($ho_ten_gv); ?></strong>
                    <span style="font-size: 12px; color:#718096;">Giảng viên ra đề</span>
                </div>
                <div class="avatar" style="background: #2563eb; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                    <?php echo $first_letter; ?>
                </div>
            </div>
        </header>

        <div style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 600; color: #334155;">Lọc dữ liệu theo Đề thi:</div>
            <form action="" method="GET" style="display: flex; gap: 10px;">
                <select name="ma_de_thi" class="search-input" style="width: 350px; cursor: pointer;">
                    <option value="all" <?php echo $selected_exam == 'all' ? 'selected' : ''; ?>>-- Tất cả đề thi của tôi --</option>
                    <?php foreach ($list_exams as $ex): ?>
                        <option value="<?php echo $ex['ma_de_thi']; ?>" <?php echo $selected_exam == $ex['ma_de_thi'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ex['tieu_de']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary" style="padding: 10px 20px;">Áp dụng</button>
            </form>
        </div>

        <?php if ($total_bai > 0): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-lbl">TỔNG BÀI ĐÃ CHẤM</div>
                    <div class="stat-val text-blue"><?php echo number_format($total_bai); ?> <span style="font-size:16px; font-weight:normal; color:#94a3b8;">bài thi</span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-lbl">ĐIỂM TRUNG BÌNH</div>
                    <div class="stat-val text-purple"><?php echo $avg_diem; ?> <span style="font-size:16px; font-weight:normal; color:#94a3b8;">/ 10</span></div>
                </div>
                <div class="stat-card" style="border-bottom: 4px solid #16a34a;">
                    <div class="stat-lbl">TỶ LỆ QUA MÔN (>=5)</div>
                    <div class="stat-val text-green"><?php echo $pass_rate; ?>% <span style="font-size:16px; font-weight:normal; color:#94a3b8;">(<?php echo $pass_count; ?> SV)</span></div>
                </div>
                <div class="stat-card" style="border-bottom: 4px solid #ef4444;">
                    <div class="stat-lbl">TỶ LỆ TRƯỢT MÔN (<5)</div>
                    <div class="stat-val text-red"><?php echo (100 - $pass_rate); ?>% <span style="font-size:16px; font-weight:normal; color:#94a3b8;">(<?php echo $fail_count; ?> SV)</span></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                
                <div class="chart-container">
                    <h3 style="margin-top:0; font-size:16px; color:#0f172a; margin-bottom: 10px;">Phổ điểm sinh viên</h3>
                    <p style="font-size:13px; color:#64748b; margin-bottom: 20px;">Phân bổ chất lượng bài làm theo 4 mức độ.</p>
                    
                    <?php
                        $max_val = max($dist['kem'], $dist['trung_binh'], $dist['kha'], $dist['gioi']);
                        $max_val = $max_val > 0 ? $max_val : 1;
                        
                        $h_kem = ($dist['kem'] / $max_val) * 100;
                        $h_tb = ($dist['trung_binh'] / $max_val) * 100;
                        $h_kha = ($dist['kha'] / $max_val) * 100;
                        $h_gioi = ($dist['gioi'] / $max_val) * 100;
                    ?>
                    
                    <div class="chart-bars">
                        <div class="bar-wrapper">
                            <div class="bar" style="height: <?php echo $h_kem; ?>%; background: #fca5a5;">
                                <div class="bar-val text-red"><?php echo $dist['kem']; ?></div>
                            </div>
                            <div class="bar-lbl">Yếu<br><span style="font-weight:normal; font-size:11px;">(< 5đ)</span></div>
                        </div>
                        <div class="bar-wrapper">
                            <div class="bar" style="height: <?php echo $h_tb; ?>%; background: #fde047;">
                                <div class="bar-val" style="color:#ca8a04;"><?php echo $dist['trung_binh']; ?></div>
                            </div>
                            <div class="bar-lbl">T.Bình<br><span style="font-weight:normal; font-size:11px;">(5 - 6.9đ)</span></div>
                        </div>
                        <div class="bar-wrapper">
                            <div class="bar" style="height: <?php echo $h_kha; ?>%; background: #93c5fd;">
                                <div class="bar-val text-blue"><?php echo $dist['kha']; ?></div>
                            </div>
                            <div class="bar-lbl">Khá<br><span style="font-weight:normal; font-size:11px;">(7 - 8.9đ)</span></div>
                        </div>
                        <div class="bar-wrapper">
                            <div class="bar" style="height: <?php echo $h_gioi; ?>%; background: #86efac;">
                                <div class="bar-val text-green"><?php echo $dist['gioi']; ?></div>
                            </div>
                            <div class="bar-lbl">Giỏi<br><span style="font-weight:normal; font-size:11px;">(9 - 10đ)</span></div>
                        </div>
                    </div>
                </div>

                <div class="table-container" style="margin: 0; padding-top: 10px;">
                    <div style="padding: 15px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin:0; font-size:16px; color:#0f172a;">Bảng xếp hạng thành tích</h3>
                        <button class="btn-secondary" style="padding: 6px 15px;" onclick="window.print()">🖨️ Xuất PDF</button>
                    </div>
                    <div style="max-height: 290px; overflow-y: auto;">
                        <table class="data-table" style="border: none;">
                            <thead style="background: #f8fafc; position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th style="width: 50px; text-align: center;">TOP</th>
                                    <th>THÍ SINH</th>
                                    <th>ĐỀ THI</th>
                                    <th style="text-align: center;">ĐIỂM SỐ</th>
                                    <th>KẾT QUẢ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach ($danhSachDiem as $diem): ?>
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: <?php echo $rank <= 3 ? '#ea580c' : '#94a3b8'; ?>;">
                                            #<?php echo $rank++; ?>
                                        </td>
                                        <td>
                                            <strong style="color: #1e293b;"><?php echo htmlspecialchars($diem['ho_ten']); ?></strong>
                                            <div style="font-size: 11px; color: #64748b;"><?php echo htmlspecialchars($diem['mssv']); ?></div>
                                        </td>
                                        <td><span style="font-size: 13px; color: #475569;"><?php echo htmlspecialchars($diem['tieu_de']); ?></span></td>
                                        <td style="text-align: center;">
                                            <strong style="font-size: 16px; color: #2563eb;"><?php echo $diem['diem']; ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($diem['diem'] >= 5): ?>
                                                <span class="badge-pass">Qua môn</span>
                                            <?php else: ?>
                                                <span class="badge-fail">Thi lại</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-box">
                <div style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;">📊</div>
                <h3 style="margin-bottom: 10px; color: #334155;">Chưa có dữ liệu thống kê</h3>
                <p style="color: #64748b;">Hiện tại chưa có thí sinh nào hoàn thành bài thi này (hoặc chưa được chấm điểm). Vui lòng quay lại sau!</p>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>