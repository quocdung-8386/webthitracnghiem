<?php
session_start();

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
    <title>Quản lý Ngân hàng Câu hỏi - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
</head>

<body>

    <div class="app-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>🎓 EduQuiz</h2>
            </div>
            <ul class="nav-menu">
                <li class="active"><a href="#">Ngân hàng câu hỏi</a></li>
                <li><a href="taodethi.php">Quản lý Đề thi</a></li>
                <li><a href="xembaocaothongke.php">Báo cáo thống kê</a></li>
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
                <h1>Quản lý Ngân hàng câu hỏi</h1>
                <div class="header-actions"><span>🌙</span><span>🔔</span></div>
            </header>

            <div class="toolbar">
                <div class="search-filter">
                    <input type="text" placeholder="🔍 Tìm kiếm câu hỏi..." class="search-input">
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
                    <button class="btn-secondary">📄 Nhập từ file</button>
                    <button id="btnOpenModal" class="btn-primary">+ Thêm câu hỏi mới</button>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NỘI DUNG CÂU HỎI</th>
                            <th>MÔN HỌC</th>
                            <th>MỨC ĐỘ</th>
                            <th>THAO TÁC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-gray">Q-1024</td>
                            <td><strong>Trong các hàm số sau, hàm số nào là hàm số chẵn?</strong></td>
                            <td><span class="badge-subject">Toán học</span></td>
                            <td><span class="badge badge-easy">Dễ</span></td>
                            <td class="actions">
                                <button class="icon-btn">✏️</button>
                                <button class="icon-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-gray">Q-1025</td>
                            <td><strong>Nêu định luật II Newton và viết biểu thức.</strong></td>
                            <td><span class="badge-subject">Vật lý</span></td>
                            <td><span class="badge badge-medium">Trung bình</span></td>
                            <td class="actions">
                                <button class="icon-btn">✏️</button>
                                <button class="icon-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="addQuestionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm câu hỏi trắc nghiệm mới</h2>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <form id="formAddQuestion">
                    <div class="form-group">
                        <label>Nội dung câu hỏi:</label>
                        <textarea rows="3" placeholder="Nhập nội dung câu hỏi vào đây..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Môn học (Danh mục):</label>
                            <select>
                                <option>Toán học</option>
                                <option>Vật lý</option>
                                <option>Hóa học</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Mức độ:</label>
                            <select>
                                <option>Dễ</option>
                                <option>Trung bình</option>
                                <option>Khó</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Các đáp án (Chọn vào nút tròn để đặt làm đáp án đúng):</label>
                        <div class="answer-list">
                            <div class="answer-item">
                                <input type="radio" name="correct_answer" checked>
                                <input type="text" placeholder="Đáp án A">
                            </div>
                            <div class="answer-item">
                                <input type="radio" name="correct_answer">
                                <input type="text" placeholder="Đáp án B">
                            </div>
                            <div class="answer-item">
                                <input type="radio" name="correct_answer">
                                <input type="text" placeholder="Đáp án C">
                            </div>
                            <div class="answer-item">
                                <input type="radio" name="correct_answer">
                                <input type="text" placeholder="Đáp án D">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btnCancel" class="btn-secondary">Hủy bỏ</button>
                <button class="btn-primary">Lưu câu hỏi</button>
            </div>
        </div>
    </div>

    <script src="../../asset/js/giangvien.js"></script>
    <div id="toastMessage" class="toast">
        <span class="toast-icon">✅</span>
        <span class="toast-text">Đã thêm câu hỏi thành công!</span>
    </div>
</body>

</html>