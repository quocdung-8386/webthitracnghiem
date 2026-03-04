<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['vai_tro'])) {
    header("Location: /webthitracnghiem/UI/login.php");
    exit();
}

if ($_SESSION['vai_tro'] !== 'giang_vien') {
    header("Location: /webthitracnghiem/UI/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Hệ thống thi trắc nghiệm</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div>
            <div class="logo">
                <h2 style="display:flex; align-items:center; gap:10px;">
                    📘 <span>EduQuiz</span>
                </h2>
            </div>
            <ul class="nav-menu">
                <li class="active"><a href="index.php">📊 Tổng quan</a></li>
                <li><a href="quanlynganhangcauhoi.php">📝 Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">⚙️ Quản lý Đề thi</a></li>
                <li><a href="#">✍️ Chấm bài tự luận</a></li>
                <li><a href="xembaocaothongke.php">📈 Thống kê & Báo cáo</a></li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div style="margin-bottom: 15px; display:flex; gap:10px; align-items:center;">
                <span style="font-size: 20px;">🌙</span>
                <span style="font-size: 13px; color:#718096;">Chế độ tối</span>
            </div>
            <a href="../logout.php" class="btn-logout-sidebar">
                🚪 <span>Đăng xuất</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header" style="display: block; margin-bottom: 30px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div class="search-filter" style="width: 400px;">
                    <input type="text" placeholder="🔍 Tìm kiếm câu hỏi, đề thi..." class="search-input" style="width:100%; background:#fff;">
                </div>
                <div class="user-profile">
                    <div style="text-align: right; margin-right: 10px;">
                        <strong style="display:block; color:#2d3748;"><?php echo isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Giảng viên'; ?></strong>
                        <span style="font-size: 12px; color:#718096;">Giáo viên Toán học</span>
                    </div>
                    <div class="avatar" style="background: #c3dafe; color: #3182ce;">👨‍🏫</div>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <h1 style="font-size: 24px; color:#1a202c;">Xin chào, Thầy <?php echo isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Giảng viên'; ?>! 👋</h1>
                <p style="color:#718096;">Chào mừng bạn quay trở lại. Hôm nay có một số bài thi cần bạn chấm điểm.</p>
            </div>
        </header>

        <div class="dashboard-grid">
            
            <div class="left-panel">
                <div class="stats-row">
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between;">
                            <div class="dash-icon-box" style="background:#ebf8ff; color:#3182ce;">❓</div>
                            <span class="badge-tag" style="background:#c6f6d5; color:#22543d;">+12% tháng này</span>
                        </div>
                        <div>
                            <div class="dash-number">1,284</div>
                            <div class="dash-label">Số câu hỏi đã soạn</div>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between;">
                            <div class="dash-icon-box" style="background:#faf5ff; color:#805ad5;">📅</div>
                            <span class="badge-tag" style="background:#e9d8fd; color:#553c9a;">Đang diễn ra</span>
                        </div>
                        <div>
                            <div class="dash-number">05</div>
                            <div class="dash-label">Kỳ thi đang diễn ra</div>
                        </div>
                    </div>
                    <div class="dash-card">
                        <div style="display:flex; justify-content:space-between;">
                            <div class="dash-icon-box" style="background:#fffaf0; color:#dd6b20;">📋</div>
                            <span class="badge-tag" style="background:#fed7d7; color:#c53030;">Cần xử lý ngay</span>
                        </div>
                        <div>
                            <div class="dash-number">42</div>
                            <div class="dash-label">Bài thi chờ chấm</div>
                        </div>
                    </div>
                </div>

                <div class="dash-card" style="margin-bottom: 25px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
                        <h3 class="card-title" style="border:none; margin:0;">Kỳ thi gần đây</h3>
                        <a href="#" style="color:#3182ce; text-decoration:none; font-size:13px; font-weight:600;">Xem tất cả</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>TÊN KỲ THI</th>
                                <th>LỚP</th>
                                <th>NGÀY THI</th>
                                <th>TRẠNG THÁI</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Kiểm tra 15p - Giải tích 12</strong></td>
                                <td>12A1</td>
                                <td>20/10/2023</td>
                                <td><span class="badge badge-easy">Hoàn thành</span></td>
                                <td>👁️</td>
                            </tr>
                            <tr>
                                <td><strong>Giữa kỳ I - Toán nâng cao</strong></td>
                                <td>11B2</td>
                                <td>22/10/2023</td>
                                <td><span class="badge badge-medium">Đang chấm</span></td>
                                <td>✏️</td>
                            </tr>
                            <tr>
                                <td><strong>Khảo sát chất lượng đầu năm</strong></td>
                                <td>10C4</td>
                                <td>25/10/2023</td>
                                <td><span class="badge badge-hard" style="background:#ebf8ff; color:#2b6cb0;">Lên lịch</span></td>
                                <td>⚙️</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="dash-card">
                        <h3 class="card-title" style="border:none; margin:0; font-size:15px;">📈 Thống kê điểm số</h3>
                        <div class="chart-bars">
                            <div class="bar" style="height: 30%;"><span>Kém</span></div>
                            <div class="bar active" style="height: 80%;"><span>Trung bình</span></div> <div class="bar" style="height: 50%;"><span>Khá</span></div>
                            <div class="bar" style="height: 40%;"><span>Giỏi</span></div>
                            <div class="bar" style="height: 60%;"><span>Xuất sắc</span></div>
                        </div>
                    </div>
                    <div class="dash-card" style="background: #ebf8ff; border: 1px dashed #3182ce; align-items:center; justify-content:center; text-align:center;">
                        <div style="font-size:40px; color:#3182ce; margin-bottom:10px;">⊕</div>
                        <h3 style="margin-bottom:5px;">Tạo Đề thi mới</h3>
                        <p style="font-size:13px; color:#718096; margin-bottom:15px;">Bắt đầu thiết lập một kỳ thi mới từ ngân hàng câu hỏi của bạn.</p>
                        <a href="taodethi.php" class="btn-primary" style="text-decoration:none;">Bắt đầu ngay</a>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <div class="dash-card">
                    <h3 class="card-title" style="border:none; font-size:16px;">Hoạt động cần xử lý</h3>
                    
                    <div class="activity-item urgent">
                        <span class="act-title">Chấm bài tự luận lớp 12A1</span>
                        <span class="act-desc">Kỳ thi: Giải tích 12 - Tiết 45</span>
                        <a href="#" class="act-link" style="color:#dd6b20;">Hết hạn: 2 giờ tới</a>
                    </div>

                    <div class="activity-item info">
                        <span class="act-title">Cập nhật ma trận đề thi HK1</span>
                        <span class="act-desc">Môn: Toán học cơ bản</span>
                        <a href="#" class="act-link">Ngày mai</a>
                    </div>

                    <div class="activity-item success">
                        <span class="act-title">Duyệt ngân hàng câu hỏi</span>
                        <span class="act-desc">Chương 3: Hình học không gian</span>
                        <a href="#" class="act-link" style="color:#38a169;">Hoàn thành: 60%</a>
                    </div>

                    <button class="btn-secondary" style="width:100%; margin-top:10px;">Xem tất cả thông báo</button>
                </div>

                <div class="dash-card" style="background:#2b6cb0; color:white; border:none; margin-top:25px;">
                    <h3 style="margin-bottom:10px;">💁‍♂️ Hỗ trợ giáo viên</h3>
                    <p style="font-size:13px; opacity:0.9; line-height:1.5; margin-bottom:20px;">Bạn gặp khó khăn trong việc thiết lập trộn đề hoặc nhập câu hỏi từ Excel?</p>
                    <button style="background:rgba(255,255,255,0.2); color:white; border:none; padding:8px 15px; border-radius:6px; cursor:pointer;">Xem hướng dẫn ↗</button>
                </div>
            </div>

        </div>
    </main>
</div>

</body>
</html>