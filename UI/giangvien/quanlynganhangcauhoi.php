<?php
session_start();
// Kiểm tra bảo mật
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'giangvien') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân hàng câu hỏi - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div>
            <div class="logo">
                <h2 style="display:flex; align-items:center; gap:10px;">📘 <span>EduQuiz</span></h2>
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
            <div style="margin-bottom: 15px; display:flex; gap:10px; align-items:center;">
                <span style="font-size: 20px;">🌙</span>
                <span style="font-size: 13px; color:#718096;">Chế độ tối</span>
            </div>
            <a href="../logout.php" class="btn-logout-sidebar">🚪 <span>Đăng xuất</span></a>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <div class="breadcrumb">Quản lý > Ngân hàng câu hỏi</div>
                <h1>Danh sách câu hỏi</h1>
            </div>
            <div class="user-profile">
                <div style="text-align: right; margin-right: 10px;">
                    <strong style="display:block; color:#2d3748;"><?php echo isset($_SESSION['ho_ten']) ? $_SESSION['ho_ten'] : 'Giảng viên'; ?></strong>
                    <span style="font-size: 12px; color:#718096;">Giáo viên</span>
                </div>
                <div class="avatar" style="background: #c3dafe; color: #3182ce;">👨‍🏫</div>
            </div>
        </header>

        <div class="table-container">
            <div class="toolbar">
                <div class="search-filter">
                    <input type="text" placeholder="🔍 Tìm kiếm nội dung câu hỏi..." class="search-input">
                    <select class="filter-select">
                        <option>Tất cả môn học</option>
                        <option>Toán học</option>
                        <option>Vật lý</option>
                    </select>
                    <select class="filter-select">
                        <option>Mức độ</option>
                        <option>Dễ</option>
                        <option>Trung bình</option>
                        <option>Khó</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button class="btn-secondary">📥 Nhập Excel</button>
                    <button id="btnOpenModal" class="btn-primary">+ Thêm câu hỏi mới</button>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox"></th>
                        <th>NỘI DUNG CÂU HỎI</th>
                        <th>MÔN HỌC</th>
                        <th>MỨC ĐỘ</th>
                        <th>NGÀY TẠO</th>
                        <th>THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <strong>Hàm số nào sau đây đồng biến trên R?</strong><br>
                            <span class="text-gray" style="font-size: 12px;">A. y = x^3... | B. y = x^4...</span>
                        </td>
                        <td><span class="badge-subject">Toán học</span></td>
                        <td><span class="badge badge-easy">Dễ</span></td>
                        <td class="text-gray">20/10/2023</td>
                        <td class="actions">
                            <button class="icon-btn" title="Sửa">✏️</button>
                            <button class="icon-btn" title="Xóa">🗑️</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <strong>Định luật II Newton được phát biểu như thế nào?</strong>
                        </td>
                        <td><span class="badge-subject">Vật lý</span></td>
                        <td><span class="badge badge-medium">Trung bình</span></td>
                        <td class="text-gray">19/10/2023</td>
                        <td class="actions">
                            <button class="icon-btn" title="Sửa">✏️</button>
                            <button class="icon-btn" title="Xóa">🗑️</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button class="btn-secondary" style="padding: 5px 10px;">&lt;</button>
                <button class="btn-primary" style="padding: 5px 10px;">1</button>
                <button class="btn-secondary" style="padding: 5px 10px;">2</button>
                <button class="btn-secondary" style="padding: 5px 10px;">&gt;</button>
            </div>
        </div>
    </main>
</div>

<div id="addQuestionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Thêm câu hỏi mới</h2>
            <span class="close-btn">&times;</span>
        </div>
        <div class="modal-body">
            <form id="formAddQuestion">
                <div class="form-row">
                    <div class="form-group">
                        <label>Môn học:</label>
                        <select><option>Toán học</option><option>Vật lý</option></select>
                    </div>
                    <div class="form-group">
                        <label>Mức độ:</label>
                        <select><option>Dễ</option><option>Trung bình</option><option>Khó</option></select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nội dung câu hỏi:</label>
                    <textarea rows="3" placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>
                <div class="form-group">
                    <label>Các phương án (Chọn đáp án đúng):</label>
                    <div class="answer-item"><input type="radio" name="correct"><input type="text" placeholder="Đáp án A"></div>
                    <div class="answer-item"><input type="radio" name="correct"><input type="text" placeholder="Đáp án B"></div>
                    <div class="answer-item"><input type="radio" name="correct"><input type="text" placeholder="Đáp án C"></div>
                    <div class="answer-item"><input type="radio" name="correct"><input type="text" placeholder="Đáp án D"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button id="btnCancel" class="btn-secondary">Hủy bỏ</button>
            <button class="btn-primary">Lưu câu hỏi</button>
        </div>
    </div>
</div>

<div id="toastMessage" class="toast">
    <span class="toast-icon">✅</span>
    <span id="toastText" class="toast-text">Thành công!</span>
</div>

<script src="../../asset/js/giangvien.js"></script>
</body>
</html>