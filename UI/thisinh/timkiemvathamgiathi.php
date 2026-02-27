<?php
// --- MÔ PHỎNG DỮ LIỆU TỪ CONTROLLER ---
$thongTinThiSinh = [
    'ten' => 'Nguyễn Văn An',
    'id' => '#12345',
    'avatar' => 'https://i.pravatar.cc/150?img=11' // Dùng avatar giả lập
];

// Danh sách các kỳ thi lấy từ Database
$danhSachKyThi = [
    [
        'ten_ky_thi' => 'Kiểm tra giữa kỳ môn Giải tích 1',
        'mon_hoc' => 'Toán học',
        'thoi_gian' => '90 phút',
        'so_cau' => '45 câu hỏi',
        'bat_dau' => '08:00 - 15/10/2023',
        'trang_thai' => 'dang_mo', // dang_mo | sap_dien_ra
        'id' => 101
    ],
    [
        'ten_ky_thi' => 'Lập trình Java Cơ bản',
        'mon_hoc' => 'CNTT',
        'thoi_gian' => '60 phút',
        'so_cau' => '30 câu hỏi',
        'bat_dau' => '14:00 - 16/10/2023',
        'trang_thai' => 'dang_mo',
        'id' => 102
    ],
    [
        'ten_ky_thi' => 'Tiếng Anh - Chứng chỉ B1',
        'mon_hoc' => 'Ngoại ngữ',
        'thoi_gian' => '120 phút',
        'so_cau' => '100 câu hỏi',
        'bat_dau' => '07:30 - 20/10/2023',
        'trang_thai' => 'sap_dien_ra',
        'id' => 103
    ],
    [
        'ten_ky_thi' => 'Vật lý Đại cương 2',
        'mon_hoc' => 'Vật lý',
        'thoi_gian' => '45 phút',
        'so_cau' => '40 câu hỏi',
        'bat_dau' => '09:00 - 18/10/2023',
        'trang_thai' => 'dang_mo',
        'id' => 104
    ]
];
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
      <div class="logo">
                <h2>🎓 EduQuiz</h2>
            </div>
        <ul class="nav-links">
            <li class="active"><a href="#">Trang chủ</a></li>
            <li><a href="#">Kỳ thi của tôi</a></li>
            <li><a href="#">Kết quả</a></li>
            <li><a href="#">Khiếu nại</a></li>
        </ul>
        <div class="nav-user">
            <span style="font-size: 20px; cursor:pointer; color: var(--text-muted);">🌙</span>
            <div class="user-info">
                <span class="user-name"><?php echo $thongTinThiSinh['ten']; ?></span>
                <span class="user-role">Thí sinh <?php echo $thongTinThiSinh['id']; ?></span>
            </div>
            <img src="<?php echo $thongTinThiSinh['avatar']; ?>" alt="Avatar" class="avatar">
        </div>
    </nav>

    <main class="main-container">
        <header class="page-header">
            <h1>Tìm kiếm & Tham gia kỳ thi</h1>
            <p>Khám phá các kỳ thi trực tuyến mới nhất. Tham gia ngay để đánh giá năng lực của bạn.</p>
        </header>

        <section class="filter-section">
            <div class="search-box">
                <span style="color: var(--text-muted);">🔍</span>
                <input type="text" placeholder="Tìm tên kỳ thi...">
            </div>
            <select class="filter-select">
                <option value="">Tất cả môn học</option>
                <option value="cntt">Công nghệ thông tin</option>
                <option value="toan">Toán học</option>
                <option value="nn">Ngoại ngữ</option>
            </select>
            <select class="filter-select">
                <option value="">Trạng thái</option>
                <option value="open">Đang mở</option>
                <option value="upcoming">Sắp diễn ra</option>
            </select>
            <button class="btn-filter">
                <span style="font-size: 14px;">=</span> Lọc kết quả
            </button>
        </section>

        <section class="exam-list">
            <div class="list-header">
                <div class="list-title">📅 Danh sách kỳ thi hiện có</div>
                <div class="list-count">Hiển thị 24 kết quả</div>
            </div>

            <div class="exam-grid">
                <?php foreach($danhSachKyThi as $kythi): ?>
                    <?php 
                        // Cài đặt class và text dựa vào trạng thái
                        $isOpen = $kythi['trang_thai'] === 'dang_mo';
                        $cardClass = $isOpen ? 'open' : 'upcoming';
                        $badgeText = $isOpen ? 'ĐANG MỞ' : 'SẮP DIỄN RA';
                        $btnClass = $isOpen ? 'join' : 'disabled';
                        $btnText = $isOpen ? 'Chi tiết ➔' : 'Chưa bắt đầu';
                    ?>
                    
                    <div class="exam-card <?php echo $cardClass; ?>">
                        <div class="card-header">
                            <span class="badge <?php echo $cardClass; ?>"><?php echo $badgeText; ?></span>
                            <span class="icon-bookmark">🔖</span>
                        </div>
                        
                        <div class="exam-title"><?php echo $kythi['ten_ky_thi']; ?></div>
                        
                        <div class="exam-info">
                            <div>📚 <?php echo $kythi['mon_hoc']; ?></div>
                            <div>⏱ <?php echo $kythi['thoi_gian']; ?> • <?php echo $kythi['so_cau']; ?></div>
                            <div>📅 Bắt đầu: <?php echo $kythi['bat_dau']; ?></div>
                        </div>

                        <?php if($isOpen): ?>
                            <a href="lambaithi.php?id=<?php echo $kythi['id']; ?>" class="btn-action <?php echo $btnClass; ?>"><?php echo $btnText; ?></a>
                        <?php else: ?>
                            <button class="btn-action <?php echo $btnClass; ?>" disabled><?php echo $btnText; ?></button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="pagination">
            <div class="page-item">‹</div>
            <div class="page-item active">1</div>
            <div class="page-item">2</div>
            <div class="page-item">3</div>
            <div class="page-item" style="border: none; background: transparent;">...</div>
            <div class="page-item">8</div>
            <div class="page-item">›</div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-brand"><span class="nav-brand-icon">?</span> EduTest</div>
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
                    <span>✉ support@edutest.vn</span>
                    <span>📞 1900 6789</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© 2023 EduTest System. All rights reserved.</span>
            <div style="display: flex; gap: 16px; font-size: 18px;">
                <span>fb</span>
                <span>🌐</span>
            </div>
        </div>
    </footer>

<script src="../../asset/js/thisinh.js"></script>
</body>
</html>