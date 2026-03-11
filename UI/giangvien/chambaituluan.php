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
$thong_bao = "";

// 2. XỬ LÝ LƯU ĐIỂM (Cập nhật thẳng vào DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'cham_diem') {
    $ma_chi_tiet = $_POST['ma_chi_tiet'];
    $diem = floatval($_POST['diem']);
    
    try {
        // Cập nhật điểm cho câu tự luận đó trong bảng chi_tiet_bai_lam
        $stmt_update = $db->prepare("UPDATE chi_tiet_bai_lam SET diem = ? WHERE ma_chi_tiet = ?");
        $stmt_update->execute([$diem, $ma_chi_tiet]);
        $thong_bao = "success|Lưu điểm thành công cho bài thi này!";
    } catch (PDOException $e) {
        $thong_bao = "error|Lỗi khi lưu điểm: " . $e->getMessage();
    }
}

// 3. TRUY VẤN LẤY BÀI TỰ LUẬN CHỜ CHẤM
try {
    // KẾT NỐI 6 BẢNG ĐỂ LẤY FULL THÔNG TIN (Sinh viên, Bài làm, Câu hỏi, Đề thi)
    $sql = "SELECT 
                ct.ma_chi_tiet,
                nd.ho_ten AS ten_sinh_vien,
                nd.ten_dang_nhap AS ma_sinh_vien,
                dt.tieu_de AS ten_de_thi,
                b.thoi_gian_nop,
                ch.noi_dung AS cau_hoi,
                ct.cau_tra_loi AS bai_lam,
                ct.diem
            FROM chi_tiet_bai_lam ct
            JOIN bai_lam b ON ct.ma_bai_lam = b.ma_bai_lam
            JOIN nguoi_dung nd ON b.ma_nguoi_dung = nd.ma_nguoi_dung
            JOIN cau_hoi ch ON ct.ma_cau_hoi = ch.ma_cau_hoi
            JOIN ca_thi c ON b.ma_ca_thi = c.ma_ca_thi
            JOIN de_thi dt ON c.ma_de_thi = dt.ma_de_thi
            WHERE ch.loai_cau_hoi = 'tu_luan' 
              AND dt.ma_giao_vien = ?
            ORDER BY b.thoi_gian_nop DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$ma_giao_vien]);
    $danhSachChoCham = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $thong_bao = "error|Lỗi truy vấn dữ liệu. Hãy kiểm tra lại tên cột (VD: cau_tra_loi, thoi_gian_nop) trong Database! Chi tiết: " . $e->getMessage();
    $danhSachChoCham = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chấm bài tự luận - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .btn-grade { background-color: #ebf8ff; color: #3182ce; border: 1px solid #bee3f8; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .btn-grade:hover { background-color: #bee3f8; }
        .btn-view { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .btn-view:hover { background-color: #e2e8f0; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 100; }
        .grading-modal { background: #fff; width: 1000px; max-width: 95%; height: 85vh; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .modal-header { padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .modal-body { display: flex; flex: 1; overflow: hidden; }
        .essay-content { flex: 2; padding: 25px; overflow-y: auto; border-right: 1px solid #e2e8f0; }
        .grading-panel { flex: 1; padding: 25px; background: #f8fafc; overflow-y: auto; }
        
        .question-box { background: #eff6ff; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 20px; color: #1e3a8a; font-weight: 500;}
        .answer-box { font-size: 15px; line-height: 1.6; color: #334155; white-space: pre-wrap; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; min-height: 200px;}
        
        .score-input { width: 100px; font-size: 24px; font-weight: bold; text-align: center; color: #2563eb; padding: 10px; border: 2px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s; }
        .score-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
        .close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b; }
        
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
            <div class="logo">
                <div class="logo-icon-bg"><span class="logo-graduation-cap">&#127891;</span><div class="logo-book-pages"></div></div>
                <span class="logo-text">Hệ thống thi<br>trực tuyến</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Tổng quan</a></li>
                <li><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">Tạo & Thiết lập đề thi</a></li>
                <li class="active"><a href="chambaituluan.php">Chấm bài tự luận</a></li>
                <li><a href="xembaocaothongke.php">Thống kê & Báo cáo</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout-sidebar" style="color: #ef4444; font-weight: bold;">Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb">Quản lý / Chấm bài tự luận</div>
                <h1>Danh sách bài thi chờ chấm</h1>
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
        </header>

        <div class="table-container">
            <?php if ($thong_bao != ""): list($type, $msg) = explode('|', $thong_bao); ?>
                <div style="background: <?php echo $type == 'success' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $type == 'success' ? '#166534' : '#b91c1c'; ?>; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid <?php echo $type == 'success' ? '#22c55e' : '#ef4444'; ?>;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="toolbar">
                <div class="search-filter">
                    <input type="text" placeholder="Tìm theo tên thí sinh..." class="search-input" style="width: 250px;">
                    <select class="filter-select">
                        <option value="">Trạng thái</option>
                        <option value="chua_cham">Chưa chấm</option>
                        <option value="da_cham">Đã chấm</option>
                    </select>
                </div>
            </div>

            <table class="data-table">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th>THÍ SINH</th>
                        <th>ĐỀ THI</th>
                        <th>THỜI GIAN NỘP</th>
                        <th>TRẠNG THÁI</th>
                        <th style="text-align: center;">ĐIỂM</th>
                        <th style="text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSachChoCham)): ?>
                        <?php foreach ($danhSachChoCham as $bai): ?>
                            <tr>
                                <td>
                                    <strong style="color: #1e293b;"><?php echo htmlspecialchars($bai['ten_sinh_vien']); ?></strong>
                                    <div style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($bai['ma_sinh_vien']); ?></div>
                                </td>
                                <td><span class="badge-subject"><?php echo htmlspecialchars($bai['ten_de_thi']); ?></span></td>
                                <td style="color: #64748b; font-size: 13px;">
                                    <?php echo date('d/m/Y H:i', strtotime($bai['thoi_gian_nop'])); ?>
                                </td>
                                <td>
                                    <?php if ($bai['diem'] === null || $bai['diem'] === ''): ?>
                                        <span class="badge" style="background: #fef3c7; color: #b45309;">Chờ chấm</span>
                                    <?php else: ?>
                                        <span class="badge badge-easy">Đã chấm</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <strong style="color: #2563eb; font-size: 16px;">
                                        <?php echo ($bai['diem'] !== null && $bai['diem'] !== '') ? $bai['diem'] . '/10' : '-'; ?>
                                    </strong>
                                </td>
                                <td style="text-align: center;">
                                    <button class="btn-grade" 
                                            data-id="<?php echo $bai['ma_chi_tiet']; ?>"
                                            data-name="<?php echo htmlspecialchars($bai['ten_sinh_vien']); ?>"
                                            data-exam="<?php echo htmlspecialchars($bai['ten_de_thi']); ?>"
                                            data-mssv="<?php echo htmlspecialchars($bai['ma_sinh_vien']); ?>"
                                            data-question="<?php echo htmlspecialchars($bai['cau_hoi']); ?>"
                                            data-answer="<?php echo htmlspecialchars($bai['bai_lam'] ?? '(Thí sinh không nhập câu trả lời)'); ?>"
                                            data-score="<?php echo $bai['diem']; ?>"
                                            onclick="openGradingModal(this)">
                                        <?php echo ($bai['diem'] !== null && $bai['diem'] !== '') ? 'Sửa điểm' : 'Chấm điểm'; ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-box">
                                    <div style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;">📝</div>
                                    <h3 style="margin-bottom: 10px; color: #334155;">Hiện không có bài tự luận nào cần chấm</h3>
                                    <p style="color: #64748b;">Khi thí sinh nộp bài thi tự luận, danh sách sẽ xuất hiện tại đây.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div class="modal-overlay" id="gradingModal">
    <div class="grading-modal">
        <div class="modal-header">
            <div>
                <h2 style="font-size: 18px; margin: 0; color: #0f172a;">Chấm bài: <span id="mdl_studentName" style="color: #2563eb;"></span></h2>
                <div style="font-size: 13px; color: #64748b; margin-top: 4px;">Đề thi: <span id="mdl_examName"></span> | Mã SV: <span id="mdl_mssv"></span></div>
            </div>
            <button class="close-btn" onclick="closeGradingModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <div class="essay-content">
                <div class="question-box">
                    <strong>Nội dung câu hỏi:</strong><br><br>
                    <span id="mdl_question"></span>
                </div>
                
                <h4 style="margin-bottom: 15px; color: #475569;">Bài làm của sinh viên:</h4>
                <div class="answer-box" id="mdl_answer"></div>
            </div>
            
            <div class="grading-panel">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Đánh giá & Cho điểm</h3>
                
                <form action="" method="POST">
                    <input type="hidden" name="action" value="cham_diem">
                    <input type="hidden" name="ma_chi_tiet" id="mdl_id">

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Điểm số (Thang điểm 10):</label>
                        <div style="display: flex; align-items: baseline; gap: 10px;">
                            <input type="number" step="0.5" min="0" max="10" name="diem" id="mdl_score" class="score-input" required placeholder="0.0">
                            <span style="font-size: 18px; color: #94a3b8; font-weight: bold;">/ 10</span>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px; opacity: 0.5; pointer-events: none;" title="Tính năng đang cập nhật">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Nhận xét của giảng viên:</label>
                        <textarea class="form-control" style="height: 120px;" placeholder="Tính năng nhận xét đang được nâng cấp..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Lưu điểm ngay</button>
                    <button type="button" class="btn-secondary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px;" onclick="closeGradingModal()">Đóng</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('gradingModal');

    // JS hứng dữ liệu từ nút bấm và bắn vào Kính lúp (Modal)
    function openGradingModal(btn) {
        document.getElementById('mdl_studentName').innerText = btn.getAttribute('data-name');
        document.getElementById('mdl_examName').innerText = btn.getAttribute('data-exam');
        document.getElementById('mdl_mssv').innerText = btn.getAttribute('data-mssv');
        document.getElementById('mdl_question').innerText = btn.getAttribute('data-question');
        document.getElementById('mdl_answer').innerText = btn.getAttribute('data-answer');
        
        let currentScore = btn.getAttribute('data-score');
        document.getElementById('mdl_score').value = (currentScore !== null && currentScore !== '') ? currentScore : '';
        document.getElementById('mdl_id').value = btn.getAttribute('data-id');
        
        modal.style.display = 'flex';
    }

    function closeGradingModal() {
        modal.style.display = 'none';
    }

    // Click ra vùng tối để đóng
    window.onclick = function(event) {
        if (event.target == modal) {
            closeGradingModal();
        }
    }
</script>

</body>
</html> 