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

// 2. XỬ LÝ THÊM / SỬA / XÓA CÂU HỎI & ĐÁP ÁN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add' || $action === 'edit') {
        $noi_dung = trim($_POST['noi_dung']);
        $loai_cau_hoi = $_POST['loai_cau_hoi'];
        $muc_do = $_POST['muc_do'];
        $ma_danh_muc = !empty($_POST['ma_danh_muc']) ? $_POST['ma_danh_muc'] : null;
        
        // Nhận mảng 4 đáp án và vị trí đáp án đúng
        $dap_an_arr = $_POST['dap_an'] ?? []; 
        $dap_an_dung_index = $_POST['dap_an_dung'] ?? 0;

        if (empty($noi_dung)) {
            $thong_bao = "error|Nội dung câu hỏi không được để trống!";
        } else {
            try {
                $db->beginTransaction();
                if ($action === 'add') {
                    // Bước 1: Thêm câu hỏi
                    $stmt = $db->prepare("INSERT INTO cau_hoi (noi_dung, loai_cau_hoi, muc_do, ma_danh_muc, ma_giao_vien) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$noi_dung, $loai_cau_hoi, $muc_do, $ma_danh_muc, $ma_giao_vien]);
                    $ma_cau_hoi_moi = $db->lastInsertId();

                    // Bước 2: Thêm 4 đáp án (Nếu là trắc nghiệm)
                    if ($loai_cau_hoi === 'trac_nghiem') {
                        $stmt_da = $db->prepare("INSERT INTO dap_an (ma_cau_hoi, noi_dung, la_dap_an_dung) VALUES (?, ?, ?)");
                        foreach ($dap_an_arr as $index => $nd_da) {
                            $is_correct = ($index == $dap_an_dung_index) ? 1 : 0;
                            $stmt_da->execute([$ma_cau_hoi_moi, trim($nd_da), $is_correct]);
                        }
                    }
                    $thong_bao = "success|Đã thêm câu hỏi và đáp án vào Ngân hàng!";
                } 
                else {
                    $ma_cau_hoi = $_POST['ma_cau_hoi'];
                    
                    // Bước 1: Cập nhật nội dung câu hỏi
                    $stmt = $db->prepare("UPDATE cau_hoi SET noi_dung = ?, loai_cau_hoi = ?, muc_do = ?, ma_danh_muc = ? WHERE ma_cau_hoi = ? AND ma_giao_vien = ?");
                    $stmt->execute([$noi_dung, $loai_cau_hoi, $muc_do, $ma_danh_muc, $ma_cau_hoi, $ma_giao_vien]);

                    // Bước 2: Xử lý đáp án
                    if ($loai_cau_hoi === 'trac_nghiem') {
                        // Để đơn giản và an toàn khi sửa: Xóa sạch đáp án cũ và nạp lại đáp án mới
                        $db->prepare("DELETE FROM dap_an WHERE ma_cau_hoi = ?")->execute([$ma_cau_hoi]);
                        
                        $stmt_da = $db->prepare("INSERT INTO dap_an (ma_cau_hoi, noi_dung, la_dap_an_dung) VALUES (?, ?, ?)");
                        foreach ($dap_an_arr as $index => $nd_da) {
                            $is_correct = ($index == $dap_an_dung_index) ? 1 : 0;
                            $stmt_da->execute([$ma_cau_hoi, trim($nd_da), $is_correct]);
                        }
                    } else {
                        // Nếu đổi từ trắc nghiệm sang tự luận, dọn dẹp rác đáp án cũ
                        $db->prepare("DELETE FROM dap_an WHERE ma_cau_hoi = ?")->execute([$ma_cau_hoi]);
                    }
                    $thong_bao = "success|Cập nhật câu hỏi và đáp án thành công!";
                }
                $db->commit();
            } catch (PDOException $e) {
                $db->rollBack();
                $thong_bao = "error|Lỗi hệ thống khi lưu: " . $e->getMessage();
            }
        }
    } 
    elseif ($action === 'delete') {
        $ma_cau_hoi = $_POST['ma_cau_hoi'];
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM dap_an WHERE ma_cau_hoi = ?")->execute([$ma_cau_hoi]);
            $stmt = $db->prepare("DELETE FROM cau_hoi WHERE ma_cau_hoi = ? AND ma_giao_vien = ?");
            $stmt->execute([$ma_cau_hoi, $ma_giao_vien]);
            $db->commit();
            $thong_bao = "success|Đã xóa câu hỏi khỏi Ngân hàng!";
        } catch (PDOException $e) {
            $db->rollBack();
            if ($e->getCode() == 23000 || strpos($e->getMessage(), '1451') !== false) {
                $thong_bao = "error|Không thể xóa! Câu hỏi này đang được sử dụng trong Đề thi.";
            } else {
                $thong_bao = "error|Lỗi xóa dữ liệu: " . $e->getMessage();
            }
        }
    }
}

// 3. LẤY DỮ LIỆU ĐỂ HIỂN THỊ VÀ JAVASCRIPT
$stmt_dm = $db->query("SELECT ma_danh_muc, ten_danh_muc FROM danh_muc");
$danhMucList = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);

$sql_cau_hoi = "SELECT c.*, d.ten_danh_muc FROM cau_hoi c LEFT JOIN danh_muc d ON c.ma_danh_muc = d.ma_danh_muc WHERE c.ma_giao_vien = ? ORDER BY c.ngay_tao DESC";
$stmt_ch = $db->prepare($sql_cau_hoi);
$stmt_ch->execute([$ma_giao_vien]);
$danhSachCauHoi = $stmt_ch->fetchAll(PDO::FETCH_ASSOC);

$stmt_all_da = $db->prepare("SELECT ma_cau_hoi, noi_dung, la_dap_an_dung FROM dap_an WHERE ma_cau_hoi IN (SELECT ma_cau_hoi FROM cau_hoi WHERE ma_giao_vien = ?)");
$stmt_all_da->execute([$ma_giao_vien]);
$allAnswers = $stmt_all_da->fetchAll(PDO::FETCH_ASSOC);

$answersDict = [];
foreach ($allAnswers as $da) {
    $answersDict[$da['ma_cau_hoi']][] = $da;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân hàng câu hỏi</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .action-btn { background: none; border: none; font-size: 13px; font-weight: 600; cursor: pointer; margin-right: 5px; padding: 6px 12px; border-radius: 4px; transition: 0.2s; }
        .btn-edit { color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; }
        .btn-edit:hover { background: #d1fae5; }
        .btn-delete { color: #e53e3e; background: #fef2f2; border: 1px solid #fecaca; }
        .btn-delete:hover { background: #fee2e2; }

        .form-section { background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }

        .radio-group { display: flex; gap: 20px; }
        .radio-label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; color: #475569; }

        #answer-section { display: none; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 15px; }
        .ans-row { display: flex; gap: 15px; margin-bottom: 12px; align-items: center; }
        .ans-radio { width: 20px; height: 20px; cursor: pointer; accent-color: #16a34a; }
        .ans-label { font-weight: bold; color: #1e293b; width: 25px; font-size: 16px;}

        #view-form { display: none; }
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
                <li class="active"><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">Tạo & Thiết lập đề thi</a></li>
                <li><a href="chambaituluan.php">Chấm bài tự luận</a></li>
                <li><a href="xembaocaothongke.php">Báo cáo & Thống kê</a></li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <a href="../../logout.php" class="btn-logout-sidebar" style="color: #ef4444; font-weight: bold;">Đăng xuất</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb" id="bread-text">Quản lý / Ngân hàng câu hỏi</div>
                <h1 id="page-title">Danh sách câu hỏi</h1>
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

        <?php if ($thong_bao != ""): list($type, $msg) = explode('|', $thong_bao); ?>
            <div style="background: <?php echo $type == 'success' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $type == 'success' ? '#166534' : '#b91c1c'; ?>; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid <?php echo $type == 'success' ? '#22c55e' : '#ef4444'; ?>;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div id="view-list" class="table-container">
            <div class="toolbar">
                <div class="search-filter">
                    <input type="text" placeholder="Tìm kiếm nội dung..." class="search-input" style="width: 300px;">
                </div>
                <div class="action-buttons">
                    <button class="btn-primary" onclick="openAddForm()">+ Thêm câu hỏi mới</button>
                </div>
            </div>

            <?php if (!empty($danhSachCauHoi)): ?>
                <table class="data-table">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="width: 50px; text-align: center;">ID</th>
                            <th style="width: 45%;">NỘI DUNG CÂU HỎI</th>
                            <th>LOẠI</th>
                            <th>MỨC ĐỘ</th>
                            <th>DANH MỤC</th>
                            <th style="text-align: center;">THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachCauHoi as $ch): 
                            $badgeLvl = $ch['muc_do'] == 'de' ? 'badge-easy' : ($ch['muc_do'] == 'kho' ? 'badge-hard' : 'badge-medium');
                            $txtLvl = $ch['muc_do'] == 'de' ? 'Dễ' : ($ch['muc_do'] == 'kho' ? 'Khó' : 'TB');
                        ?>
                            <tr>
                                <td style="text-align: center; color: #94a3b8;">#<?php echo $ch['ma_cau_hoi']; ?></td>
                                <td><div style="color: #1e293b; font-size: 14px; line-height: 1.5; font-weight: 500;"><?php echo htmlspecialchars($ch['noi_dung']); ?></div></td>
                                <td><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #475569; font-weight: 600;"><?php echo $ch['loai_cau_hoi'] == 'trac_nghiem' ? 'Trắc nghiệm' : 'Tự luận'; ?></span></td>
                                <td><span class="badge <?php echo $badgeLvl; ?>"><?php echo $txtLvl; ?></span></td>
                                <td><span class="badge-subject"><?php echo htmlspecialchars($ch['ten_danh_muc'] ?? 'Chưa phân loại'); ?></span></td>
                                <td style="text-align: center; min-width: 140px;">
                                    <button class="action-btn btn-edit" onclick="openEditForm('<?php echo $ch['ma_cau_hoi']; ?>', `<?php echo htmlspecialchars(addslashes($ch['noi_dung'])); ?>`, '<?php echo $ch['loai_cau_hoi']; ?>', '<?php echo $ch['muc_do']; ?>', '<?php echo $ch['ma_danh_muc']; ?>')">Sửa</button>
                                    <button class="action-btn btn-delete" onclick="deleteQuestion('<?php echo $ch['ma_cau_hoi']; ?>')">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-box">
                    <div style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;">📚</div>
                    <h3 style="margin-bottom: 10px; color: #334155;">Ngân hàng trống</h3>
                    <button class="btn-primary" onclick="openAddForm()">Thêm câu hỏi đầu tiên</button>
                </div>
            <?php endif; ?>
        </div>

        <div id="view-form">
            <form action="" method="POST" id="frmCauHoi">
                <input type="hidden" name="action" id="frmAction" value="add">
                <input type="hidden" name="ma_cau_hoi" id="frmId" value="">
                
                <div class="form-section">
                    <h2 id="form-title" style="font-size: 18px; color: #0f172a; margin-top: 0; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">Tạo câu hỏi mới</h2>
                    
                    <div class="form-group">
                        <label>Nội dung câu hỏi <span style="color:#ef4444;">*</span></label>
                        <textarea name="noi_dung" id="frmNoiDung" class="form-control" placeholder="Nhập câu hỏi tại đây..." required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Loại câu hỏi <span style="color:#ef4444;">*</span></label>
                            <div class="radio-group" style="padding: 12px 0;">
                                <label class="radio-label"><input type="radio" name="loai_cau_hoi" value="trac_nghiem" id="type_tn" onclick="toggleAnswerSection()" checked> Trắc nghiệm</label>
                                <label class="radio-label"><input type="radio" name="loai_cau_hoi" value="tu_luan" id="type_tl" onclick="toggleAnswerSection()"> Tự luận</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mức độ <span style="color:#ef4444;">*</span></label>
                            <select name="muc_do" id="frmMucDo" class="form-control" required>
                                <option value="de">Dễ</option>
                                <option value="trung_binh" selected>Trung bình</option>
                                <option value="kho">Khó</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Danh mục (Tùy chọn)</label>
                            <select name="ma_danh_muc" id="frmDanhMuc" class="form-control">
                                <option value="">-- Chưa phân loại --</option>
                                <?php foreach($danhMucList as $dm): ?>
                                    <option value="<?php echo $dm['ma_danh_muc']; ?>"><?php echo htmlspecialchars($dm['ten_danh_muc']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div id="answer-section">
                        <h3 style="margin-top: 0; font-size: 15px; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; justify-content: space-between;">
                            <span>Nhập 4 lựa chọn & Tích chọn Đáp án đúng <span style="color:#ef4444;">*</span></span>
                            <span style="font-size: 12px; font-weight: normal; color: #16a34a; background: #dcfce7; padding: 4px 10px; border-radius: 20px;">Màu xanh là đáp án đúng</span>
                        </h3>
                        
                        <div class="ans-row">
                            <input type="radio" name="dap_an_dung" value="0" id="da_dung_0" class="ans-radio" checked>
                            <span class="ans-label">A.</span>
                            <input type="text" name="dap_an[]" id="da_nd_0" class="form-control ans-input" placeholder="Nhập lựa chọn A...">
                        </div>
                        <div class="ans-row">
                            <input type="radio" name="dap_an_dung" value="1" id="da_dung_1" class="ans-radio">
                            <span class="ans-label">B.</span>
                            <input type="text" name="dap_an[]" id="da_nd_1" class="form-control ans-input" placeholder="Nhập lựa chọn B...">
                        </div>
                        <div class="ans-row">
                            <input type="radio" name="dap_an_dung" value="2" id="da_dung_2" class="ans-radio">
                            <span class="ans-label">C.</span>
                            <input type="text" name="dap_an[]" id="da_nd_2" class="form-control ans-input" placeholder="Nhập lựa chọn C...">
                        </div>
                        <div class="ans-row">
                            <input type="radio" name="dap_an_dung" value="3" id="da_dung_3" class="ans-radio">
                            <span class="ans-label">D.</span>
                            <input type="text" name="dap_an[]" id="da_nd_3" class="form-control ans-input" placeholder="Nhập lựa chọn D...">
                        </div>
                    </div>

                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" style="padding: 10px 20px;" onclick="closeForm()">Hủy bỏ</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 25px;" id="btnSubmit">Lưu câu hỏi</button>
                </div>
            </form>
        </div>

        <form id="frmDelete" action="" method="POST" style="display:none;">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="ma_cau_hoi" id="deleteId">
        </form>
    </main>
</div>

<script>
    // Dữ liệu đáp án truyền từ PHP sang JS
    const answersData = <?php echo json_encode($answersDict); ?>;

    const viewList = document.getElementById('view-list');
    const viewForm = document.getElementById('view-form');
    const ansSection = document.getElementById('answer-section');
    const ansInputs = document.querySelectorAll('.ans-input');

    // Ẩn hiện phần nhập đáp án A,B,C,D
    function toggleAnswerSection() {
        if (document.getElementById('type_tn').checked) {
            ansSection.style.display = 'block';
            ansInputs.forEach(input => input.setAttribute('required', 'true'));
        } else {
            ansSection.style.display = 'none';
            ansInputs.forEach(input => input.removeAttribute('required'));
        }
    }

    // Mở form Thêm mới
    function openAddForm() {
        viewList.style.display = 'none'; viewForm.style.display = 'block';
        document.getElementById('form-title').innerText = 'Tạo câu hỏi mới';
        document.getElementById('frmAction').value = 'add';
        document.getElementById('frmId').value = '';
        document.getElementById('frmNoiDung').value = '';
        document.getElementById('type_tn').checked = true;
        document.getElementById('frmMucDo').value = 'trung_binh';
        document.getElementById('frmDanhMuc').value = '';
        
        // Clear 4 đáp án
        ansInputs.forEach(input => input.value = '');
        document.getElementById('da_dung_0').checked = true;

        toggleAnswerSection();
    }

    // Mở form Sửa (Lấy lại đúng 4 đáp án đã lưu)
    function openEditForm(id, content, type, level, cat) {
        viewList.style.display = 'none'; viewForm.style.display = 'block';
        document.getElementById('form-title').innerText = 'Chỉnh sửa câu hỏi';
        document.getElementById('frmAction').value = 'edit';
        document.getElementById('frmId').value = id;
        document.getElementById('frmNoiDung').value = content;
        
        if(type === 'tu_luan') document.getElementById('type_tl').checked = true;
        else document.getElementById('type_tn').checked = true;
        
        document.getElementById('frmMucDo').value = level;
        document.getElementById('frmDanhMuc').value = cat || '';

        // Đổ dữ liệu đáp án vào 4 ô A, B, C, D
        if(type === 'trac_nghiem' && answersData[id]) {
            let daps = answersData[id];
            for (let i = 0; i < 4; i++) {
                if (daps[i]) {
                    document.getElementById('da_nd_' + i).value = daps[i].noi_dung;
                    if (daps[i].la_dap_an_dung == 1) {
                        document.getElementById('da_dung_' + i).checked = true;
                    }
                } else {
                    document.getElementById('da_nd_' + i).value = '';
                }
            }
        }
        
        toggleAnswerSection();
    }

    function closeForm() {
        viewList.style.display = 'block'; viewForm.style.display = 'none';
    }

    function deleteQuestion(id) {
        if (confirm('Bạn có chắc chắn muốn xóa câu hỏi và đáp án này không?')) {
            document.getElementById('deleteId').value = id;
            document.getElementById('frmDelete').submit();
        }
    }
</script>

</body>
</html>