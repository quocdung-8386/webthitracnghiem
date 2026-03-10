<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../app/config/Database.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'giangvien') {
    header("Location: ../login.php");
    exit();
}

$db = Database::getConnection();

$ma_giao_vien = $_SESSION['ma_nguoi_dung'] ?? $_SESSION['user']['ma_nguoi_dung'];
$ho_ten_gv = $_SESSION['ho_ten'] ?? $_SESSION['user']['ho_ten'] ?? 'Giảng viên';

    $stmt1 = $db->prepare("SELECT COUNT(*) as total FROM cau_hoi WHERE ma_giao_vien = ?");
    $stmt1->execute([$ma_giao_vien]);
    $tongCauHoi = $stmt1->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt2 = $db->prepare("SELECT COUNT(*) as total FROM de_thi WHERE ma_giao_vien = ?");
    $stmt2->execute([$ma_giao_vien]);
    $tongDeThi = $stmt2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Đếm số bài làm đã nộp (chờ chấm)
$stmt3 = $db->prepare("
    SELECT COUNT(b.ma_bai_lam) as total 
    FROM bai_lam b
    JOIN ca_thi c ON b.ma_ca_thi = c.ma_ca_thi
    JOIN de_thi d ON c.ma_de_thi = d.ma_de_thi
    WHERE d.ma_giao_vien = ? AND b.trang_thai = 'da_nop'
");
$stmt3->execute([$ma_giao_vien]);
$baiChoCham = $stmt3->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// B. Lấy 5 kỳ thi tạo gần đây nhất
$stmt4 = $db->prepare("SELECT tieu_de, thoi_gian_lam, ngay_tao FROM de_thi WHERE ma_giao_vien = ? ORDER BY ngay_tao DESC LIMIT 5");
$stmt4->execute([$ma_giao_vien]);
$deThiGanDay = $stmt4->fetchAll(PDO::FETCH_ASSOC);

// C. XỬ LÝ BIỂU ĐỒ: Phân tích mức độ câu hỏi của Giảng viên này
$stmt_chart = $db->prepare("SELECT muc_do, COUNT(*) as count FROM cau_hoi WHERE ma_giao_vien = ? GROUP BY muc_do");
$stmt_chart->execute([$ma_giao_vien]);
$chartDataRaw = $stmt_chart->fetchAll(PDO::FETCH_KEY_PAIR); // Trả về mảng ['de' => X, 'trung_binh' => Y, 'kho' => Z]

$totalChart = array_sum($chartDataRaw) ?: 1; // Tránh chia cho 0 nếu chưa có câu hỏi nào
$pctDe = isset($chartDataRaw['de']) ? round(($chartDataRaw['de'] / $totalChart) * 100) : 0;
$pctTB = isset($chartDataRaw['trung_binh']) ? round(($chartDataRaw['trung_binh'] / $totalChart) * 100) : 0;
$pctKho = isset($chartDataRaw['kho']) ? round(($chartDataRaw['kho'] / $totalChart) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan Dashboard - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .dash-icon-box { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px; font-weight: bold; font-size: 18px; }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-purple { background: #faf5ff; color: #a855f7; }
        .icon-red { background: #fef2f2; color: #ef4444; }
        
        .badge-tag { font-size: 12px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
        .tag-green { background: #f0fdf4; color: #16a34a; }
        .tag-gray { background: #f1f5f9; color: #64748b; }
        .tag-red { background: #fef2f2; color: #dc2626; }

        .empty-state { text-align: center; padding: 30px; color: #94a3b8; font-style: italic; background: #f8fafc; border-radius: 8px; border: 1px dashed #cbd5e1; }

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
            <div class="logo">
                <div class="logo-icon-bg">
                    <span class="logo-graduation-cap">&#127891;</span>
                    <div class="logo-book-pages"></div>
                </div>
                <span class="logo-text">Hệ thống thi<br>trực tuyến</span>
            </div>
            <ul class="nav-menu">
                <li class="active"><a href="index.php">Tổng quan</a></li>
                <li><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">Tạo & thiết lập đề thi</a></li>
                <li><a href="chambaituluan.php">Chấm bài tự luận</a></li>
                <li><a href="xembaocaothongke.php">Thống kê & Báo cáo</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout-sidebar" style="color: #ef4444; font-weight: bold;">Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header" style="display: block; margin-bottom: 30px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div class="search-filter" style="width: 400px; border: none;">
                    <h1 style="font-size: 22px; color:#0f172a; margin: 0;">Xin chào, Thầy/Cô <?php echo htmlspecialchars($ho_ten_gv); ?>!</h1>
                </div>
                <div class="user-profile">
                    <div style="text-align: right; margin-right: 15px;">
                        <strong style="display:block; color:#2d3748;"><?php echo htmlspecialchars($ho_ten_gv); ?></strong>
                        <span style="font-size: 12px; color:#718096;">Giảng viên ra đề</span>
                    </div>
                    <div class="avatar" style="background: #2563eb; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                        <?php echo strtoupper(substr(trim(end(explode(' ', $ho_ten_gv))), 0, 1)); ?>
                    </div>
                </div>
            </div>
            <div style="margin-top: 5px;">
                <p style="color:#64748b; font-size: 14px;">Chào mừng bạn quay trở lại. Đây là bảng tóm tắt hoạt động giảng dạy của bạn.</p>
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="left-panel">
                <div class="stats-row">
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 15px;">
                            <div class="dash-icon-box icon-blue">Q</div>
                            <span class="badge-tag tag-gray">Tổng kho</span>
                        </div>
                        <div>
                            <div class="dash-number" style="font-size: 28px; color: #0f172a;"><?php echo number_format($tongCauHoi); ?></div>
                            <div class="dash-label" style="color: #64748b; font-weight: 500;">Câu hỏi đã soạn</div>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 15px;">
                            <div class="dash-icon-box icon-purple">E</div>
                            <span class="badge-tag tag-gray">Hệ thống</span>
                        </div>
                        <div>
                            <div class="dash-number" style="font-size: 28px; color: #0f172a;"><?php echo number_format($tongDeThi); ?></div>
                            <div class="dash-label" style="color: #64748b; font-weight: 500;">Kỳ thi đã tạo</div>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 15px;">
                            <div class="dash-icon-box icon-red">!</div>
                            <?php if ($baiChoCham > 0): ?>
                                <span class="badge-tag tag-red">Cần xử lý</span>
                            <?php else: ?>
                                <span class="badge-tag tag-green">Hoàn tất</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="dash-number" style="font-size: 28px; color: #0f172a;"><?php echo number_format($baiChoCham); ?></div>
                            <div class="dash-label" style="color: #64748b; font-weight: 500;">Bài thi chờ chấm</div>
                        </div>
                    </div>
                </div>

                <div class="dash-card" style="margin-bottom: 25px; padding: 0; overflow: hidden;">
                    <div style="display:flex; justify-content:space-between; align-items: center; padding: 20px 20px 15px 20px; border-bottom:1px solid #f1f5f9;">
                        <h3 class="card-title" style="border:none; margin:0; font-size: 16px; color: #0f172a;">Kỳ thi tạo gần đây</h3>
                        <a href="taodethi.php" style="color:#2563eb; text-decoration:none; font-size:13px; font-weight:600;">Xem tất cả</a>
                    </div>
                    <table class="data-table" style="margin: 0; border: none;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th style="padding-left: 20px;">TÊN KỲ THI</th>
                                <th>THỜI LƯỢNG</th>
                                <th>NGÀY TẠO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($deThiGanDay)): ?>
                                <?php foreach ($deThiGanDay as $dt): ?>
                                    <tr>
                                        <td style="padding-left: 20px; font-weight: 600; color: #334155;"><?php echo htmlspecialchars($dt['tieu_de']); ?></td>
                                        <td style="color: #64748b;"><?php echo $dt['thoi_gian_lam']; ?> phút</td>
                                        <td style="color: #64748b; font-size: 13px;"><?php echo date('d/m/Y', strtotime($dt['ngay_tao'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3"><div class="empty-state">Bạn chưa tạo kỳ thi nào trên hệ thống.</div></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="dash-card">
                        <h3 class="card-title" style="border:none; margin:0 0 15px 0; font-size:15px; color: #0f172a;">Phân bổ mức độ câu hỏi</h3>
                        <?php if ($tongCauHoi > 0): ?>
                            <div class="chart-bars" style="height: 150px; display: flex; align-items: flex-end; gap: 15px; padding-top: 20px;">
                                <div class="bar" style="flex: 1; background: #dbeafe; border-radius: 4px 4px 0 0; position: relative; height: <?php echo $pctDe; ?>%;">
                                    <span style="position: absolute; top: -20px; width: 100%; text-align: center; font-size: 12px; font-weight: bold; color: #2563eb;"><?php echo $pctDe; ?>%</span>
                                    <span style="position: absolute; bottom: -25px; width: 100%; text-align: center; font-size: 12px; color: #64748b;">Dễ</span>
                                </div>
                                <div class="bar active" style="flex: 1; background: #3b82f6; border-radius: 4px 4px 0 0; position: relative; height: <?php echo $pctTB; ?>%;">
                                    <span style="position: absolute; top: -20px; width: 100%; text-align: center; font-size: 12px; font-weight: bold; color: #1e40af;"><?php echo $pctTB; ?>%</span>
                                    <span style="position: absolute; bottom: -25px; width: 100%; text-align: center; font-size: 12px; color: #64748b;">Trung bình</span>
                                </div> 
                                <div class="bar" style="flex: 1; background: #fecaca; border-radius: 4px 4px 0 0; position: relative; height: <?php echo $pctKho; ?>%;">
                                    <span style="position: absolute; top: -20px; width: 100%; text-align: center; font-size: 12px; font-weight: bold; color: #dc2626;"><?php echo $pctKho; ?>%</span>
                                    <span style="position: absolute; bottom: -25px; width: 100%; text-align: center; font-size: 12px; color: #64748b;">Khó</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="empty-state" style="margin-top: 10px;">Chưa có dữ liệu để phân tích.</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dash-card" style="background: #f8fafc; border: 1px dashed #cbd5e1; align-items:center; justify-content:center; text-align:center; display: flex; flex-direction: column;">
                        <div style="font-size:32px; color:#2563eb; margin-bottom:15px; background: #dbeafe; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">+</div>
                        <h3 style="margin-bottom:8px; color: #0f172a; font-size: 16px;">Tạo Đề thi mới</h3>
                        <p style="font-size:13px; color:#64748b; margin-bottom:20px; padding: 0 10px;">Thiết lập một kỳ thi mới từ ngân hàng câu hỏi của bạn.</p>
                        <a href="taodethi.php" class="btn-primary" style="text-decoration:none; padding: 8px 20px;">Bắt đầu thiết lập</a>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="dash-card">
                    <h3 class="card-title" style="border:none; font-size:16px; color: #0f172a; margin-bottom: 20px;">Hoạt động cần xử lý</h3>
                    
                    <?php if ($baiChoCham > 0): ?>
                        <div class="activity-item urgent" style="border-left: 3px solid #ef4444; padding-left: 12px; margin-bottom: 15px;">
                            <span class="act-title" style="display: block; font-weight: 600; color: #334155; margin-bottom: 4px;">Chấm bài thi tự luận</span>
                            <span class="act-desc" style="display: block; font-size: 13px; color: #64748b; margin-bottom: 6px;">Bạn có <strong style="color:#ef4444;"><?php echo $baiChoCham; ?></strong> bài thi đang chờ chấm điểm.</span>
                            <a href="chambaituluan.php" class="act-link" style="color:#2563eb; font-size: 13px; font-weight: 600; text-decoration: none;">Đi tới trang chấm bài →</a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">Bạn đã hoàn thành mọi công việc. Hiện không có bài thi nào cần chấm.</div>
                    <?php endif; ?>
                    
                    <?php if ($tongCauHoi == 0): ?>
                        <div class="activity-item info" style="border-left: 3px solid #3b82f6; padding-left: 12px; margin-bottom: 15px; margin-top: 15px;">
                            <span class="act-title" style="display: block; font-weight: 600; color: #334155; margin-bottom: 4px;">Ngân hàng câu hỏi trống</span>
                            <span class="act-desc" style="display: block; font-size: 13px; color: #64748b; margin-bottom: 6px;">Bạn cần thêm câu hỏi trước khi có thể tạo đề thi.</span>
                            <a href="quanlynganhangcauhoi.php" class="act-link" style="color:#2563eb; font-size: 13px; font-weight: 600; text-decoration: none;">Thêm câu hỏi ngay →</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dash-card" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color:white; border:none; margin-top:20px; border-radius: 12px;">
                    <h3 style="margin-bottom:12px; font-size: 16px;">Trung tâm hỗ trợ</h3>
                    <p style="font-size:13px; opacity:0.9; line-height:1.6; margin-bottom:20px;">Xem tài liệu hướng dẫn cách thiết lập trộn đề hoặc nhập câu hỏi hàng loạt từ file Excel.</p>
                    <button style="background:rgba(255,255,255,0.15); color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer; font-weight: 600; font-size: 13px; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">Xem hướng dẫn chi tiết</button>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>