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

// 2. XỬ LÝ FORM: TẠO ĐỀ THI & LƯU CÂU HỎI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'tao_de_thi') {
    $tieu_de = trim($_POST['tieu_de']);
    $thoi_gian_lam = intval($_POST['thoi_gian_lam']);
    $cau_hoi_duoc_chon = $_POST['cau_hoi'] ?? []; 

    if (empty($tieu_de) || empty($thoi_gian_lam) || empty($cau_hoi_duoc_chon)) {
        $thong_bao = "error|Vui lòng nhập Tên đề thi, Thời gian làm bài và chọn ít nhất 1 câu hỏi!";
    } else {
        try {
            $db->beginTransaction();

            // A. Lưu vào bảng de_thi
            $stmt_de_thi = $db->prepare("INSERT INTO de_thi (tieu_de, thoi_gian_lam, ma_giao_vien) VALUES (?, ?, ?)");
            $stmt_de_thi->execute([$tieu_de, $thoi_gian_lam, $ma_giao_vien]);
            
            // Lấy ID của đề thi vừa được tạo ra
            $ma_de_thi_moi = $db->lastInsertId();

            // B. Lưu các câu hỏi được chọn vào bảng chi_tiet_de_thi
            $stmt_chi_tiet = $db->prepare("INSERT INTO chi_tiet_de_thi (ma_de_thi, ma_cau_hoi) VALUES (?, ?)");
            foreach ($cau_hoi_duoc_chon as $ma_ch) {
                $stmt_chi_tiet->execute([$ma_de_thi_moi, $ma_ch]);
            }

            // XÁC NHẬN LƯU TẤT CẢ
            $db->commit();
            $thong_bao = "success|Tạo đề thi thành công! Đã thêm " . count($cau_hoi_duoc_chon) . " câu hỏi vào đề.";
            
        } catch (PDOException $e) {
            $db->rollBack();
            $thong_bao = "error|Lỗi hệ thống: " . $e->getMessage();
        }
    }
}

// 3. LẤY DỮ LIỆU ĐỂ HIỂN THỊ

// A. Lấy danh sách Đề thi (Có đếm số câu hỏi bên trong)
$sql_danh_sach = "SELECT d.ma_de_thi, d.tieu_de, d.thoi_gian_lam, d.ngay_tao, COUNT(c.ma_cau_hoi) as so_luong_cau_hoi
                  FROM de_thi d
                  LEFT JOIN chi_tiet_de_thi c ON d.ma_de_thi = c.ma_de_thi
                  WHERE d.ma_giao_vien = ?
                  GROUP BY d.ma_de_thi, d.tieu_de, d.thoi_gian_lam, d.ngay_tao
                  ORDER BY d.ngay_tao DESC";
$stmt_ds = $db->prepare($sql_danh_sach);
$stmt_ds->execute([$ma_giao_vien]);
$danhSachDeThi = $stmt_ds->fetchAll(PDO::FETCH_ASSOC);

// B. Lấy danh sách toàn bộ Câu hỏi của GV này để họ tích chọn khi tạo đề
$stmt_ch = $db->prepare("SELECT ma_cau_hoi, noi_dung, muc_do, loai_cau_hoi FROM cau_hoi WHERE ma_giao_vien = ? ORDER BY ngay_tao DESC");
$stmt_ch->execute([$ma_giao_vien]);
$nganHangCauHoi = $stmt_ch->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo & Thiết lập Đề thi</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .action-btn { background: none; border: none; font-size: 13px; font-weight: 600; cursor: pointer; margin-right: 5px; padding: 6px 12px; border-radius: 4px; transition: 0.2s; }
        .btn-view { color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; }
        .btn-view:hover { background: #dbeafe; }
        .btn-delete { color: #e53e3e; background: #fef2f2; border: 1px solid #fecaca; }
        .btn-delete:hover { background: #fee2e2; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-ready { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-draft { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }

        .form-section { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; transition: border 0.2s; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        
        .question-picker { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .question-picker-header { background: #f8fafc; padding: 12px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #0f172a; display: flex; justify-content: space-between;}
        .question-picker-body { max-height: 350px; overflow-y: auto; }
        .question-item { padding: 12px 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 15px; align-items: flex-start; transition: 0.2s; }
        .question-item:hover { background: #f8fafc; }
        .question-item input[type="checkbox"] { width: 18px; height: 18px; margin-top: 2px; cursor: pointer; }

        #view-create { display: none; }

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
                <li class="active"><a href="taodethi.php">Tạo & Thiết lập đề thi</a></li> 
                <li><a href="chambaituluan.php">Chấm bài tự luận</a></li>
                <li><a href="xembaocaothongke.php">Báo cáo & Thống kê</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="btn-logout-sidebar" style="color: #ef4444; font-weight: bold;">Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb" id="bread-text">Quản lý / Đề thi</div>
                <h1 id="page-title">Danh sách Đề thi</h1>
            </div>
            <div class="user-profile">
                <div style="text-align: right; margin-right: 15px;">
                    <strong style="display:block; color:#2d3748;"><?php echo htmlspecialchars($ho_ten_gv); ?></strong>
                    <span style="font-size: 12px; color:#718096;">Giảng viên ra đề</span>
                </div>
                <div class="avatar" style="background: #2563eb; color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px;">
                    <?php $np = explode(' ', trim($ho_ten_gv)); echo strtoupper(substr(end($np), 0, 1)); ?>
                </div>
            </div>
        </header>

        <?php if ($thong_bao != ""): 
            list($type, $msg) = explode('|', $thong_bao);
            $bg = $type == 'success' ? '#dcfce7' : '#fee2e2';
            $color = $type == 'success' ? '#166534' : '#b91c1c';
            $border = $type == 'success' ? '#22c55e' : '#ef4444';
        ?>
            <div style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid <?php echo $border; ?>;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div id="view-list" class="table-container">
            <div class="toolbar">
                <div class="search-filter">
                    <input type="text" placeholder="Tìm kiếm tên đề thi..." class="search-input" style="width: 300px;">
                </div>
                <div class="action-buttons">
                    <button class="btn-primary" onclick="toggleView('create')">+ Tạo đề thi mới</button>
                </div>
            </div>

            <table class="data-table">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="width: 50px; text-align: center;">ID</th>
                        <th>TÊN ĐỀ THI</th>
                        <th style="text-align: center;">THỜI LƯỢNG</th>
                        <th style="text-align: center;">SỐ CÂU HỎI</th>
                        <th>TRẠNG THÁI</th>
                        <th>NGÀY TẠO</th>
                        <th style="text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSachDeThi)): ?>
                        <?php foreach ($danhSachDeThi as $dt): ?>
                            <tr>
                                <td style="text-align: center; color: #64748b; font-weight: 500;">#<?php echo $dt['ma_de_thi']; ?></td>
                                <td><strong style="color: #1e293b; font-size: 15px;"><?php echo htmlspecialchars($dt['tieu_de']); ?></strong></td>
                                <td style="text-align: center;"><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 600; color: #475569; font-size: 13px;"><?php echo $dt['thoi_gian_lam']; ?> phút</span></td>
                                <td style="text-align: center; font-weight: bold; color: #2563eb;"><?php echo $dt['so_luong_cau_hoi']; ?></td>
                                <td>
                                    <?php if ($dt['so_luong_cau_hoi'] > 0): ?>
                                        <span class="status-badge status-ready">Sẵn sàng</span>
                                    <?php else: ?>
                                        <span class="status-badge status-draft">Bản nháp</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #64748b; font-size: 13px;"><?php echo date('d/m/Y', strtotime($dt['ngay_tao'])); ?></td>
                                <td style="text-align: center; min-width: 150px;">
                                    <button class="action-btn btn-view" onclick="alert('Xem chi tiết mã đề: <?php echo $dt['ma_de_thi']; ?>')">Xem</button>
                                    <button class="action-btn btn-delete">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; padding: 50px; color: #64748b;">Bạn chưa tạo đề thi nào trên hệ thống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="view-create">
            <form action="" method="POST">
                <input type="hidden" name="action" value="tao_de_thi">
                
                <div class="form-section">
                    <h2 style="font-size: 18px; color: #0f172a; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">1. Thông tin chung</h2>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Tên kỳ thi / Đề thi <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="tieu_de" class="form-control" placeholder="VD: Kiểm tra giữa kỳ môn Cơ sở dữ liệu" required>
                        </div>
                        <div class="form-group">
                            <label>Thời gian làm bài (Phút) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="thoi_gian_lam" class="form-control" placeholder="VD: 45" required min="5" max="180">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2 style="font-size: 18px; color: #0f172a; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">2. Chọn câu hỏi từ Ngân hàng</h2>
                    
                    <div class="question-picker">
                        <div class="question-picker-header">
                            <span>Nội dung câu hỏi</span>
                            <label style="cursor: pointer; color: #2563eb; display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"> Chọn tất cả
                            </label>
                        </div>
                        <div class="question-picker-body">
                            <?php if (!empty($nganHangCauHoi)): ?>
                                <?php foreach ($nganHangCauHoi as $ch): 
                                    $badge = $ch['muc_do'] == 'de' ? 'badge-easy' : ($ch['muc_do'] == 'kho' ? 'badge-hard' : 'badge-medium');
                                    $mucDoText = $ch['muc_do'] == 'de' ? 'Dễ' : ($ch['muc_do'] == 'kho' ? 'Khó' : 'TB');
                                ?>
                                    <label class="question-item">
                                        <input type="checkbox" name="cau_hoi[]" class="chk-question" value="<?php echo $ch['ma_cau_hoi']; ?>">
                                        <div style="flex: 1;">
                                            <div style="color: #334155; font-size: 14px; margin-bottom: 5px;"><?php echo htmlspecialchars($ch['noi_dung']); ?></div>
                                            <div style="display: flex; gap: 10px;">
                                                <span class="badge <?php echo $badge; ?>" style="font-size: 11px;"><?php echo $mucDoText; ?></span>
                                                <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px;">
                                                    <?php echo $ch['loai_cau_hoi'] == 'trac_nghiem' ? 'Trắc nghiệm' : 'Tự luận'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 30px; text-align: center; color: #64748b;">
                                    Ngân hàng câu hỏi của bạn đang trống. Vui lòng thêm câu hỏi trước!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="margin-top: 15px; font-size: 13px; color: #64748b;">
                        Đã chọn: <strong id="countSelected" style="color: #2563eb;">0</strong> câu hỏi
                    </div>
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" style="padding: 10px 20px;" onclick="toggleView('list')">Hủy bỏ</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 25px;">Lưu Đề thi</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    // JS Ẩn/Hiện giữa Danh sách và Form tạo
    function toggleView(view) {
        const viewList = document.getElementById('view-list');
        const viewCreate = document.getElementById('view-create');
        const title = document.getElementById('page-title');
        const bread = document.getElementById('bread-text');

        if (view === 'create') {
            viewList.style.display = 'none';
            viewCreate.style.display = 'block';
            title.innerText = 'Tạo Đề thi mới';
            bread.innerText = 'Đề thi / Tạo mới';
        } else {
            viewList.style.display = 'block';
            viewCreate.style.display = 'none';
            title.innerText = 'Danh sách Đề thi';
            bread.innerText = 'Quản lý / Đề thi';
        }
    }

    // JS Xử lý Chọn tất cả câu hỏi
    function toggleSelectAll(source) {
        checkboxes = document.querySelectorAll('.chk-question');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
        updateCount();
    }

    // JS Đếm số câu hỏi đã chọn
    document.querySelectorAll('.chk-question').forEach(item => {
        item.addEventListener('change', updateCount);
    });

    function updateCount() {
        const checkedCount = document.querySelectorAll('.chk-question:checked').length;
        document.getElementById('countSelected').innerText = checkedCount;
    }
</script>

</body>
</html>