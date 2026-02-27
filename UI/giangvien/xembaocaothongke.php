<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo thống kê - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="logo"><h2>🎓 EduQuiz</h2></div>
        <ul class="nav-menu">
            <li><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
            <li><a href="taodethi.php">Quản lý Đề thi</a></li>
            <li class="active"><a href="#">Báo cáo thống kê</a></li>
        </ul>
        <div class="user-profile">
            <div class="avatar">👤</div>
            <div class="info">
                <strong>GV. Nguyễn Văn A</strong>
                <span>Giáo viên</span>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb">Báo cáo thống kê > Tổng quan kết quả thi</div>
                <h1>Thống kê & Kết quả</h1>
            </div>
            <div class="header-actions-btn">
                <button id="btnExportExcel" class="btn-secondary">📥 Xuất file Excel</button>
            </div>
        </header>

        <div class="stat-cards-container">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #ebf4ff; color: #3182ce;">📝</div>
                <div class="stat-info">
                    <span class="stat-title">Tổng lượt thi</span>
                    <strong class="stat-number">128</strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #c6f6d5; color: #276749;">🎯</div>
                <div class="stat-info">
                    <span class="stat-title">Điểm trung bình</span>
                    <strong class="stat-number">7.5</strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #fefcbf; color: #975a16;">📈</div>
                <div class="stat-info">
                    <span class="stat-title">Tỉ lệ Đạt (>= 5đ)</span>
                    <strong class="stat-number">85%</strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #fed7d7; color: #c53030;">⚠️</div>
                <div class="stat-info">
                    <span class="stat-title">Tỉ lệ Trượt (< 5đ)</span>
                    <strong class="stat-number">15%</strong>
                </div>
            </div>
        </div>

        <div class="table-container" style="margin-top: 25px;">
            <div class="toolbar" style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;">
                <div class="search-filter">
                    <input type="text" placeholder="🔍 Tìm kiếm MSSV, Họ tên..." class="search-input">
                    <select class="filter-select">
                        <option>Tất cả Đề thi</option>
                        <option>Thi giữa kỳ Toán Cao Cấp</option>
                        <option>Kiểm tra 15p Vật lý 1</option>
                    </select>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>MSSV</th>
                        <th>HỌ TÊN</th>
                        <th>ĐỀ THI</th>
                        <th>ĐIỂM SỐ</th>
                        <th>THỜI GIAN NỘP</th>
                        <th>KẾT QUẢ</th>
                        <th>CHI TIẾT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-gray">SV2023001</td>
                        <td><strong>Trần Văn Bình</strong></td>
                        <td>Thi giữa kỳ Toán Cao Cấp</td>
                        <td><strong style="color: #276749; font-size: 16px;">9.0</strong> / 10</td>
                        <td class="text-gray">15/05/2023 - 09:45</td>
                        <td><span class="badge badge-easy">ĐẠT</span></td>
                        <td><button class="icon-btn" title="Xem chi tiết bài làm">👁️</button></td>
                    </tr>
                    <tr>
                        <td class="text-gray">SV2023045</td>
                        <td><strong>Lê Thị Hoa</strong></td>
                        <td>Thi giữa kỳ Toán Cao Cấp</td>
                        <td><strong style="color: #c53030; font-size: 16px;">4.5</strong> / 10</td>
                        <td class="text-gray">15/05/2023 - 09:50</td>
                        <td><span class="badge badge-hard">TRƯỢT</span></td>
                        <td><button class="icon-btn" title="Xem chi tiết bài làm">👁️</button></td>
                    </tr>
                    <tr>
                        <td class="text-gray">SV2023088</td>
                        <td><strong>Nguyễn Hữu Trí</strong></td>
                        <td>Kiểm tra 15p Vật lý 1</td>
                        <td><strong style="color: #276749; font-size: 16px;">8.0</strong> / 10</td>
                        <td class="text-gray">16/05/2023 - 14:12</td>
                        <td><span class="badge badge-easy">ĐẠT</span></td>
                        <td><button class="icon-btn" title="Xem chi tiết bài làm">👁️</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div id="toastMessage" class="toast">
    <span class="toast-icon">✅</span>
    <span id="toastText" class="toast-text">Thành công!</span>
</div>

<script src="../../asset/js/giangvien.js"></script>
</body>
</html>