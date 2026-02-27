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
    <title>Thiết lập đề thi - EduQuiz</title>
    <link rel="stylesheet" href="../../asset/css/giangvien.css">
</head>

<body>

    <div class="app-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>🎓 EduQuiz</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="quanlynganhangcauhoi.php">Ngân hàng câu hỏi</a></li>
                <li class="active"><a href="#">Quản lý Đề thi</a></li>
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
            <header class="page-header header-exam">
                <div>
                    <div class="breadcrumb">Quản lý đề thi > Tạo và Thiết lập đề thi</div>
                    <h1>Thiết lập Đề thi mới</h1>
                </div>
                <div class="header-actions-btn">
                    <button class="btn-secondary">👁️ Xem trước</button>
                    <button class="btn-primary">📤 Xuất bản / Kích hoạt</button>
                </div>
            </header>

            <div class="exam-grid-layout">

                <div class="exam-left-col">
                    <div class="card">
                        <h3 class="card-title">ℹ️ Thông tin chung</h3>
                        <div class="form-group">
                            <label>TÊN ĐỀ THI</label>
                            <input type="text" placeholder="Nhập tên đề thi...">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>THỜI GIAN (PHÚT)</label>
                                <input type="number" value="60">
                            </div>
                            <div class="form-group">
                                <label>SỐ LƯỢNG CÂU</label>
                                <input type="number" value="0" readonly class="bg-gray">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>MÔ TẢ</label>
                            <textarea rows="3" placeholder="Ghi chú thêm cho thí sinh..."></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="card-title">⚙️ Cấu hình tham số</h3>
                        <div class="config-item">
                            <span>Trộn câu hỏi</span>
                            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                        </div>
                        <div class="config-item">
                            <span>Trộn đáp án</span>
                            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                        </div>
                        <div class="config-item">
                            <span>Xem lại kết quả</span>
                            <label class="switch"><input type="checkbox"><span class="slider"></span></label>
                        </div>
                    </div>
                </div>

                <div class="exam-right-col">
                    <div class="card">
                        <div class="tabs">
                            <div class="tab active">👤 Chọn thủ công</div>
                            <div class="tab">✨ Sinh đề tự động (Ma trận)</div>
                        </div>

                        <div class="search-add-bar">
                            <input type="text" placeholder="🔍 Tìm kiếm câu hỏi..." class="search-input full-width">
                            <button id="btnOpenSelectModal" class="btn-text-primary">⊕ Thêm từ ngân hàng</button>
                        </div>

                        <div class="empty-state">
                            <span class="icon-list">📄</span>
                            <p>Chưa có câu hỏi nào được chọn.</p>
                            <small>Hãy chọn câu hỏi từ ngân hàng để thêm vào đề thi.</small>
                        </div>

                        <div class="selected-questions">
                            <h4 style="margin-bottom: 15px; display: flex; justify-content: space-between;">
                                Cấu trúc đề thi <span style="font-size: 12px; color: #718096; font-weight: normal;">Tự
                                    động lưu...</span>
                            </h4>

                            <div class="question-box">
                                <div class="q-number">1</div>
                                <div class="q-content">
                                    <strong>Nội dung câu hỏi ví dụ 1...</strong>
                                    <span class="badge badge-easy"
                                        style="display: inline-block; margin-top: 5px;">DỄ</span>
                                </div>
                            </div>

                            <div class="question-box">
                                <div class="q-number">2</div>
                                <div class="q-content">
                                    <strong>Nội dung câu hỏi ví dụ 2...</strong>
                                    <span class="badge badge-medium"
                                        style="display: inline-block; margin-top: 5px;">TRUNG BÌNH</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../asset/js/giangvien.js"></script>
    <div id="selectQuestionModal" class="modal">
        <div class="modal-content" style="width: 800px;">
            <div class="modal-header">
                <h2>Chọn câu hỏi từ Ngân hàng</h2>
                <span class="close-select-btn close-btn">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <div class="search-filter" style="margin-bottom: 15px;">
                    <input type="text" placeholder="🔍 Tìm kiếm nhanh câu hỏi..." class="search-input"
                        style="width: 100%;">
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="checkAll"></th>
                            <th>NỘI DUNG CÂU HỎI</th>
                            <th>MỨC ĐỘ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="check-item"></td>
                            <td><strong>Tính pH của dung dịch chứa 0.01M HCl.</strong></td>
                            <td><span class="badge badge-hard">Khó</span></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="check-item"></td>
                            <td><strong>Cho tam giác ABC vuông tại A, có AB = 3cm, AC = 4cm...</strong></td>
                            <td><span class="badge badge-easy">Dễ</span></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="check-item"></td>
                            <td><strong>Đạo hàm của hàm số y = sin(x) là gì?</strong></td>
                            <td><span class="badge badge-medium">Trung bình</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <span style="flex:1; color:#718096; display:flex; align-items:center;">
                    Đã chọn: <strong id="selectedCount"
                        style="color:#2563eb; margin: 0 5px; font-size: 16px;">0</strong> câu
                </span>
                <button id="btnCancelSelect" class="btn-secondary">Hủy bỏ</button>
                <button id="btnConfirmSelect" class="btn-primary">Thêm vào đề thi</button>
            </div>
        </div>
    </div>

    <div id="toastMessage" class="toast">
        <span class="toast-icon">✅</span>
        <span id="toastText" class="toast-text">Thành công!</span>
    </div>
</body>

</html>