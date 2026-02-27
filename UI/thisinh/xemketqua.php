<?php
// --- MÔ PHỎNG DỮ LIỆU TỪ CONTROLLER ---
$thongTinThiSinh = ['ten' => 'Nguyễn Văn A', 'id' => '#TS10293'];
$ketQua = [
    'ten_ky_thi' => 'Kiến trúc phần mềm - Final',
    'thoi_gian_nop' => '10:30, 24/10/2023',
    'tong_diem' => '8.5',
    'cau_dung' => 34,
    'tong_cau' => 40,
    'cau_sai' => 6,
    'thoi_gian_lam' => '45:12',
    'xep_loai' => 'Giỏi'
];

$chiTietBaiLam = [
    [
        'id' => 1,
        'cau_hoi' => 'Trong kiến trúc Model-View-Controller (MVC), thành phần nào chịu trách nhiệm xử lý logic nghiệp vụ và tương tác dữ liệu?',
        'dap_an' => [
            'A' => 'View',
            'B' => 'Model',
            'C' => 'Controller',
            'D' => 'Router'
        ],
        'da_chon' => 'B',
        'dap_an_dung' => 'B',
        'loi_giai' => 'Model là thành phần chứa các đối tượng dữ liệu và logic nghiệp vụ của ứng dụng. Nó trực tiếp quản lý dữ liệu, logic và các quy tắc của ứng dụng.'
    ],
    [
        'id' => 2,
        'cau_hoi' => 'Đâu là một đặc điểm chính của Microservices Architecture?',
        'dap_an' => [
            'A' => 'Một cơ sở dữ liệu dùng chung duy nhất',
            'B' => 'Các dịch vụ độc lập và giao tiếp qua API',
            'C' => 'Triển khai tất cả tính năng trong một khối (Monolith)',
            'D' => 'Chỉ sử dụng một ngôn ngữ lập trình cho toàn hệ thống'
        ],
        'da_chon' => 'A',
        'dap_an_dung' => 'B',
        'loi_giai' => 'Trong Microservices, mỗi dịch vụ là một thực thể độc lập, có thể phát triển, triển khai và mở rộng riêng biệt. Chúng giao tiếp với nhau qua các giao thức nhẹ như HTTP/REST hoặc Message Bus. Đáp án A là đặc điểm của Monolith.'
    ],
    [
        'id' => 3,
        'cau_hoi' => 'Design Pattern nào được sử dụng để tạo ra một đối tượng duy nhất trong suốt vòng đời ứng dụng?',
        'dap_an' => [
            'A' => 'Factory Pattern',
            'B' => 'Observer Pattern',
            'C' => 'Singleton Pattern',
            'D' => 'Strategy Pattern'
        ],
        'da_chon' => 'C',
        'dap_an_dung' => 'C',
        'loi_giai' => ''
    ]
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả kỳ thi</title>
    <link rel="stylesheet" href="../../asset/css/thisinh.css">
</head>
<body class="page-ketqua">

    <nav class="navbar">
        <div class="logo">
                <h2>🎓 EduQuiz</h2>
            </div>
        <ul class="nav-links">
            <li><a href="timkiemvathamgiathi.php">Trang chủ</a></li>
            <li><a href="#">Kỳ thi của tôi</a></li>
            <li class="active"><a href="xemketqua.php">Kết quả</a></li>
            <li><a href="phuckhaokhieunai.php">Hỗ trợ</a></li>
        </ul>
        <div class="nav-user">
            <div class="user-info">
                <span class="user-name"><?php echo $thongTinThiSinh['ten']; ?></span>
                <span class="user-role">Thí sinh <?php echo $thongTinThiSinh['id']; ?></span>
            </div>
            <div class="avatar"></div>
        </div>
    </nav>

    <main class="main-container">
        <div class="breadcrumb">
            Trang chủ › Kết quả thi › <strong><?php echo $ketQua['ten_ky_thi']; ?></strong>
        </div>

        <div class="top-grid">
            <div class="card result-overview">
                <div class="overview-header">
                    <h2>Kết quả kỳ thi</h2>
                    <p>Kỳ thi kết thúc lúc <?php echo $ketQua['thoi_gian_nop']; ?></p>
                </div>
                
                <div class="stats-container">
                    <div class="stats-boxes">
                        <div class="stat-box blue">
                            <span>TỔNG ĐIỂM</span>
                            <strong><?php echo $ketQua['tong_diem']; ?> <small>/ 10</small></strong>
                        </div>
                        <div class="stat-box green">
                            <span>CÂU ĐÚNG</span>
                            <strong><?php echo $ketQua['cau_dung']; ?> <small>/ <?php echo $ketQua['tong_cau']; ?></small></strong>
                        </div>
                        <div class="stat-box red">
                            <span>CÂU SAI</span>
                            <strong><?php echo $ketQua['cau_sai']; ?></strong>
                        </div>
                        <div class="stat-box yellow">
                            <span>THỜI GIAN</span>
                            <strong><?php echo $ketQua['thoi_gian_lam']; ?></strong>
                        </div>
                    </div>
                    <div class="grade-circle">
                        <strong><?php echo $ketQua['xep_loai']; ?></strong>
                        <span>Xếp loại</span>
                    </div>
                </div>
            </div>

            <div class="side-actions">
                <div class="card action-card">
                    <h3>Hành động</h3>
                    <button class="btn-action btn-primary" id="btnExportPDF">📥 Xuất phiếu điểm (PDF)</button>
                    <a href="phuckhaokhieunai.php" class="btn-action btn-outline">📝 Gửi phúc khảo</a>
                </div>
                <div class="alert-box purple">
                    <div class="alert-icon">i</div>
                    <div class="alert-text">
                        <strong>Lưu ý</strong>
                        <p>Thời gian khiếu nại kết thúc sau 48h kể từ khi công bố kết quả. Vui lòng kiểm tra kỹ bài làm trước khi gửi phúc khảo.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="details-section">
            <div class="details-header">
                <h3>Chi tiết bài làm</h3>
                <div class="legend">
                    <span class="dot green"></span> Đúng
                    <span class="dot red"></span> Sai
                </div>
            </div>

            <div class="question-list">
                <?php foreach($chiTietBaiLam as $index => $cau): 
                    $isCorrect = ($cau['da_chon'] == $cau['dap_an_dung']);
                    $statusClass = $isCorrect ? 'correct' : 'wrong';
                    $qNum = str_pad($cau['id'], 2, "0", STR_PAD_LEFT);
                ?>
                <div class="q-item">
                    <div class="q-number <?php echo $statusClass; ?>"><?php echo $qNum; ?></div>
                    <div class="q-content">
                        <div class="q-text"><?php echo $cau['cau_hoi']; ?></div>
                        <div class="q-options">
                            <?php foreach($cau['dap_an'] as $key => $val): 
                                $optClass = '';
                                if ($key == $cau['da_chon'] && !$isCorrect) $optClass = 'selected-wrong';
                                if ($key == $cau['dap_an_dung']) $optClass = 'selected-correct';
                            ?>
                                <div class="opt-box <?php echo $optClass; ?>">
                                    <?php echo $key; ?>. <?php echo $val; ?>
                                    <?php if($optClass == 'selected-correct'): ?><span class="icon">✔</span><?php endif; ?>
                                    <?php if($optClass == 'selected-wrong'): ?><span class="icon">✘</span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if(!empty($cau['loi_giai'])): ?>
                        <div class="q-explanation">
                            <strong>💡 LỜI GIẢI</strong>
                            <p><?php echo $cau['loi_giai']; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="pagination">
                <span>Hiển thị 1-10 trên 40 câu hỏi</span>
                <div class="page-controls">
                    <button>|‹</button> <button>‹</button>
                    <button class="active">1</button> <button>2</button> <button>3</button> <button>4</button>
                    <button>›</button> <button>›|</button>
                </div>
            </div>
        </div>

        <div class="bottom-banner">
            <div class="banner-text">
                <span class="icon">💬</span>
                <div>
                    <strong>Bạn không hài lòng với kết quả?</strong>
                    <p>Vui lòng liên hệ giám thị hoặc gửi đơn phúc khảo chính thức.</p>
                </div>
            </div>
            <button class="btn-darkmode" id="btnToggleTheme">🌙 Chế độ tối/sáng</button>
        </div>
    </main>

    <footer class="main-footer">
        <p>© 2023 Hệ thống thi trực tuyến ExamPortal. Bảo lưu mọi quyền.</p>
        <div class="footer-links">
            <a href="#">Điều khoản dịch vụ</a> | <a href="#">Chính sách bảo mật</a>
        </div>
    </footer>

<script src="../../asset/js/thisinh.js"></script>
</body>
</html>