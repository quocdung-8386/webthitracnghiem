<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KIỂM TRA BẢO MẬT (Chuẩn hóa theo logic mới)
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'giangvien') {
    header("Location: ../login.php");
    exit();
}

require_once '../../app/config/Database.php';

$danhSachCauHoi = [];

try {
    $db = Database::getConnection();
    
    // Lấy ID giảng viên từ Session
    $ma_giao_vien = $_SESSION['ma_nguoi_dung'];

    // 2. TRUY VẤN LẤY DANH SÁCH CÂU HỎI CỦA GIẢNG VIÊN NÀY
    // Dùng LEFT JOIN để lấy được tên danh mục từ bảng danh_muc
    $sql = "SELECT c.*, d.ten_danh_muc 
            FROM cau_hoi c
            LEFT JOIN danh_muc d ON c.ma_danh_muc = d.ma_danh_muc
            WHERE c.ma_giao_vien = ?
            ORDER BY c.ngay_tao DESC";
            
    $stmt = $db->prepare($sql);
    $stmt->execute([$ma_giao_vien]);
    $danhSachCauHoi = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi hệ thống CSDL: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân hàng câu hỏi - Hệ thống thi trắc nghiệm</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
    <style>
        .action-text-btn {
            background: none;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .btn-edit {
            color: #2563eb;
            background: #eff6ff;
        }

        .btn-delete {
            color: #e53e3e;
            background: #fef2f2;
        }

        .action-text-btn:hover {
            opacity: 0.8;
        }
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
                    <span class="logo-text">Hệ thống thi trắc nghiệm</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">📊 Tổng quan</a></li>
                    <li class="active"><a href="quanlynganhangcauhoi.php">📝 Ngân hàng câu hỏi</a></li>
                    <li><a href="taodethi.php">⚙️ Quản lý Đề thi</a></li>
                    <li><a href="#">✍️ Chấm bài tự luận</a></li>
                    <li><a href="xembaocaothongke.php">📈 Thống kê & Báo cáo</a></li>
                </ul>
            </div>
            <div class="sidebar-footer">
                <a href="../logout.php" class="btn-logout-sidebar">🚪 Đăng xuất</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <div>
                    <div class="breadcrumb">Quản lý / Ngân hàng câu hỏi</div>
                    <h1>Danh sách câu hỏi</h1>
                </div>
                <div class="user-profile">
                    <div style="text-align: right; margin-right: 10px;">
                        <strong style="display:block; color:#2d3748;"><?php echo $_SESSION['ho_ten'] ?? 'Giảng viên'; ?></strong>
                        <span style="font-size: 12px; color:#718096;">Giáo viên Toán học</span>
                    </div>
                    <div class="avatar" style="background: #c3dafe; color: #3182ce;">👨‍🏫</div>
                </div>
            </header>

            <div class="table-container">
                <div class="toolbar">
                    <div class="search-filter">
                        <input type="text" placeholder="Tìm kiếm nội dung câu hỏi..." class="search-input">
                        <select class="filter-select">
                            <option>Tất cả danh mục</option>
                        </select>
                    </div>
                    <div class="action-buttons">
                        <button class="btn-secondary">Nhập Excel</button>
                        <button id="btnOpenModal" class="btn-primary">Thêm câu hỏi mới</button>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox"></th>
                            <th>NỘI DUNG CÂU HỎI</th>
                            <th>DANH MỤC</th>
                            <th>MỨC ĐỘ</th>
                            <th>NGÀY TẠO</th>
                            <th>THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($danhSachCauHoi)): ?>
                            <?php foreach ($danhSachCauHoi as $cauHoi): ?>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cauHoi['noi_dung']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge-subject">
                                            <?php echo htmlspecialchars($cauHoi['ten_danh_muc'] ?? 'Chưa phân loại'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        // Xử lý giao diện cho Mức độ (de, trung_binh, kho)
                                        $mucDo = $cauHoi['muc_do'];
                                        $badgeClass = 'badge-medium';
                                        $mucDoText = 'Trung bình';

                                        if ($mucDo === 'de') {
                                            $badgeClass = 'badge-easy';
                                            $mucDoText = 'Dễ';
                                        } elseif ($mucDo === 'kho') {
                                            $badgeClass = 'badge-hard';
                                            $mucDoText = 'Khó';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $mucDoText; ?></span>
                                    </td>
                                    <td class="text-gray">
                                        <?php echo date('d/m/Y', strtotime($cauHoi['ngay_tao'])); ?>
                                    </td>
                                    <td class="actions">
                                        <button class="action-text-btn btn-edit">Sửa</button>
                                        <button class="action-text-btn btn-delete">Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #718096;">
                                    Chưa có câu hỏi nào trong ngân hàng. Bấm "Thêm câu hỏi mới" để bắt đầu soạn đề.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>