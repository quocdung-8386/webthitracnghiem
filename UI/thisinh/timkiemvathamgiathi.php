<?php
$thongTinThiSinh = [
    'ten' => 'Nguyễn Văn An',
    'id' => '#12345',
    'avatar' => 'https://i.pravatar.cc/150?img=11'
];

$danhSachKyThi = [
    ['ten_ky_thi' => 'Kiểm tra giữa kỳ môn Giải tích 1', 'mon_hoc' => 'Toán học', 'thoi_gian' => '90 phút', 'so_cau' => '45 câu hỏi', 'bat_dau' => '08:00 - 15/10/2023', 'trang_thai' => 'dang_mo', 'id' => 101],
    ['ten_ky_thi' => 'Lập trình Java Cơ bản', 'mon_hoc' => 'CNTT', 'thoi_gian' => '60 phút', 'so_cau' => '30 câu hỏi', 'bat_dau' => '14:00 - 16/10/2023', 'trang_thai' => 'dang_mo', 'id' => 102],
    ['ten_ky_thi' => 'Tiếng Anh - Chứng chỉ B1', 'mon_hoc' => 'Ngoại ngữ', 'thoi_gian' => '120 phút', 'so_cau' => '100 câu hỏi', 'bat_dau' => '07:30 - 20/10/2023', 'trang_thai' => 'sap_dien_ra', 'id' => 103],
    ['ten_ky_thi' => 'Vật lý Đại cương 2', 'mon_hoc' => 'Vật lý', 'thoi_gian' => '45 phút', 'so_cau' => '40 câu hỏi', 'bat_dau' => '09:00 - 18/10/2023', 'trang_thai' => 'dang_mo', 'id' => 104]
];

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm & Tham gia kỳ thi</title>
    <link rel="stylesheet" href="../../asset/css/thisinh.css">
</head>
<body>

    <nav class="navbar">
        <a href="timkiemvathamgiathi.php" class="nav-brand">
            <span class="nav-brand-icon">🎓</span> EduQuiz
        </a>
        
        <ul class="nav-links">
            <li class="<?php echo ($current_page == 'timkiemvathamgiathi.php') ? 'active' : ''; ?>">
                <a href="timkiemvathamgiathi.php">Trang chủ</a>
            </li>
            <li class="<?php echo ($current_page == 'lambaithi.php') ? 'active' : ''; ?>">
                <a href="lambaithi.php">Kỳ thi của tôi</a>
            </li>
            <li class="<?php echo ($current_page == 'xemketqua.php') ? 'active' : ''; ?>">
                <a href="xemketqua.php">Kết quả</a>
            </li>
            <li class="<?php echo ($current_page == 'phuckhaokhieunai.php') ? 'active' : ''; ?>">
                <a href="phuckhaokhieunai.php">Khiếu nại</a>
            </li>
        </ul>
        
        <div class="nav-user">
            <span id="btnToggleTheme" style="font-size: 20px; cursor:pointer; color: var(--text-muted); margin-right: 15px;">🌙</span>
            <div class="user-info">
                <span class="user-name"><?php echo $thongTinThiSinh['ten']; ?></span>
                <span class="user-role">Thí sinh <?php echo $thongTinThiSinh['id']; ?></span>
            </div>
            <img src="<?php echo $thongTinThiSinh['avatar']; ?>" alt="Avatar" class="avatar" style="object-fit: cover;">
        </div>
    </nav>

    <main class="main-container">
        <header class="page-header" style="margin-bottom: 30px;">
            <h1 style="font-size: 28px; margin-bottom: 8px;">Tìm kiếm & Tham gia kỳ thi</h1>
            <p style="color: var(--text-muted);">Khám phá các kỳ thi trực tuyến mới nhất. Tham gia ngay để đánh giá năng lực của bạn.</p>
        </header>

        <form method="GET" action="timkiemvathamgiathi.php" class="filter-bar card" style="display: flex; gap: 16px; padding: 20px; border-radius: 12px; margin-bottom: 40px; align-items: center;">
            <div class="search-box" style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 16px; flex: 1; background: var(--bg-body);">
                <span style="color: var(--text-muted);">🔍</span>
                <input type="text" name="keyword" placeholder="Tìm tên kỳ thi..." style="border: none; background: transparent; padding: 12px 8px; width: 100%; outline: none;">
            </div>
            
            <select name="mon_hoc" style="padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-body); outline: none; min-width: 180px;">
                <option value="">Tất cả môn học</option>
                <option value="cntt">Công nghệ thông tin</option>
                <option value="toan">Toán học</option>
                <option value="nn">Ngoại ngữ</option>
            </select>
            
            <select name="trang_thai" style="padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-body); outline: none; min-width: 150px;">
                <option value="">Trạng thái</option>
                <option value="dang_mo">Đang mở</option>
                <option value="sap_dien_ra">Sắp diễn ra</option>
            </select>
            
            <button type="submit" style="background: var(--primary); color: white; border: none; padding: 0 24px; border-radius: 8px; font-weight: 600; cursor: pointer; height: 42px; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 14px;">=</span> Lọc kết quả
            </button>
        </form>

        <section class="exam-list">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">📅 Danh sách kỳ thi hiện có</div>
                <div style="font-size: 14px; color: var(--text-muted);">Hiển thị 24 kết quả</div>
            </div>

            <div class="exam-cards-grid">
                <?php foreach($danhSachKyThi as $kythi): ?>
                    <?php 
                        $isOpen = $kythi['trang_thai'] === 'dang_mo';
                        $borderColor = $isOpen ? 'var(--primary)' : '#ca8a04';
                        $badgeBg = $isOpen ? '#d1fae5' : '#fef08a';
                        $badgeColor = $isOpen ? '#059669' : '#ca8a04';
                        $badgeText = $isOpen ? 'ĐANG MỞ' : 'SẮP DIỄN RA';
                        $btnBg = $isOpen ? 'var(--primary-light)' : 'var(--bg-body)';
                        $btnColor = $isOpen ? 'var(--primary)' : 'var(--text-muted)';
                        $btnText = $isOpen ? 'Chi tiết ➔' : 'Chưa bắt đầu';
                    ?>
                    
                    <div class="card exam-card" style="border-top-color: <?php echo $borderColor; ?>; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <span class="badge" style="background: <?php echo $badgeBg; ?>; color: <?php echo $badgeColor; ?>;"><?php echo $badgeText; ?></span>
                            <span style="color: var(--border-color); cursor: pointer;">🔖</span>
                        </div>
                        
                        <div style="font-size: 16px; font-weight: 700; margin-bottom: 16px; line-height: 1.4; flex-grow: 1;">
                            <?php echo $kythi['ten_ky_thi']; ?>
                        </div>
                        
                        <div style="font-size: 13px; color: var(--text-muted); display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
                            <div>📚 <?php echo $kythi['mon_hoc']; ?></div>
                            <div>⏱ <?php echo $kythi['thoi_gian']; ?> • <?php echo $kythi['so_cau']; ?></div>
                            <div>📅 Bắt đầu: <?php echo $kythi['bat_dau']; ?></div>
                        </div>

                        <?php if($isOpen): ?>
                            <a href="lambaithi.php?id=<?php echo $kythi['id']; ?>" style="display: block; width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; text-align: center; background: <?php echo $btnBg; ?>; color: <?php echo $btnColor; ?>; text-decoration: none;">
                                <?php echo $btnText; ?>
                            </a>
                        <?php else: ?>
                            <div style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; text-align: center; background: <?php echo $btnBg; ?>; color: <?php echo $btnColor; ?>; cursor: not-allowed;">
                                <?php echo $btnText; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

         <div class="pagination">
               
                <div class="page-controls">
                    <button>|‹</button> <button>‹</button>
                    <button class="active">1</button> <button>2</button> <button>3</button> <button>4</button>
                    <button>›</button> <button>›|</button>
                </div>
            </div>
    </main>

    <footer class="footer-large">
        <div class="footer-grid">
            <div>
                <div style="font-size: 20px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 8px;">
                    <span class="nav-brand-icon">🎓</span> EduQuiz
                </div>
                <p class="footer-desc">Nền tảng thi trắc nghiệm trực tuyến chuyên nghiệp, công bằng và hiệu quả dành cho sinh viên và giảng viên.</p>
            </div>
            <div class="footer-col">
                <h4>Liên kết</h4>
                <div class="footer-links">
                    <a href="#">Hướng dẫn sử dụng</a>
                    <a href="#">Quy chế phòng thi</a>
                    <a href="#">Hỗ trợ kỹ thuật</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Liên hệ</h4>
                <div class="footer-links">
                    <span>✉ support@eduquiz.vn</span>
                    <span>📞 1900 6789</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2023 EduQuiz System. All rights reserved.</span>
            <div style="display: flex; gap: 16px; font-size: 18px;">
                <span>🌐</span>
            </div>
        </div>
    </footer>

    <script src="../../asset/js/thisinh.js"></script>
</body>
</html>