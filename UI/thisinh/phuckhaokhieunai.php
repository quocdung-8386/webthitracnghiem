<?php
// --- MÔ PHỎNG DỮ LIỆU TỪ CONTROLLER ---
$thongTinThiSinh = [
    'ten' => 'Nguyễn Văn A',
    'avatar' => 'https://i.pravatar.cc/150?img=11' 
];

// Danh sách các bài thi có thể phúc khảo
$baiThiHopLe = [
    ['id' => 1, 'ten' => 'Kỹ thuật lập trình'],
    ['id' => 2, 'ten' => 'Toán cao cấp A1'],
    ['id' => 3, 'ten' => 'Mạng máy tính']
];

// Lịch sử yêu cầu
$lichSuYeuCau = [
    [
        'ma_don' => '#RF-8291',
        'bai_thi' => 'Kỹ thuật lập trình',
        'pham_vi' => 'Tất cả câu hỏi',
        'ngay_gui' => '16/10/2023',
        'trang_thai' => 'pending', // pending | approved | rejected
        'txt_trang_thai' => 'Đang chờ'
    ],
    [
        'ma_don' => '#RF-7430',
        'bai_thi' => 'Toán cao cấp A1',
        'pham_vi' => 'Câu hỏi 15',
        'ngay_gui' => '10/10/2023',
        'trang_thai' => 'approved',
        'txt_trang_thai' => 'Đã duyệt'
    ],
    [
        'ma_don' => '#RF-6122',
        'bai_thi' => 'Anh văn chuyên ngành',
        'pham_vi' => 'Tất cả câu hỏi',
        'ngay_gui' => '01/10/2023',
        'trang_thai' => 'rejected',
        'txt_trang_thai' => 'Từ chối'
    ],
    [
        'ma_don' => '#RF-5510',
        'bai_thi' => 'Mạng máy tính',
        'pham_vi' => 'Câu hỏi 2',
        'ngay_gui' => '28/09/2023',
        'trang_thai' => 'approved',
        'txt_trang_thai' => 'Đã duyệt'
    ]
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi phúc khảo & Khiếu nại</title>
    <link rel="stylesheet" href="../../asset/css/thisinh.css">
</head>
<body>

    <nav class="navbar">
       <div class="logo">
                <h2>🎓 EduQuiz</h2>
            </div>
        <ul class="nav-links">
            <li><a href="#">Trang chủ</a></li>
            <li><a href="#">Kỳ thi của tôi</a></li>
            <li class="active"><a href="#">Phúc khảo</a></li>
        </ul>
        <div class="nav-user">
            <span style="font-size: 20px; cursor:pointer; color: var(--text-muted);">🌙</span>
            <div class="user-info">
                <span class="user-name"><?php echo $thongTinThiSinh['ten']; ?></span>
            </div>
            <img src="<?php echo $thongTinThiSinh['avatar']; ?>" alt="Avatar" class="avatar">
        </div>
    </nav>

    <main class="main-container">
        <header class="page-header">
            <h1>Gửi phúc khảo & Khiếu nại</h1>
            <p>Gửi yêu cầu xem xét lại kết quả bài thi hoặc báo cáo sự cố.</p>
        </header>

        <div class="content-grid">
            <div class="card form-section">
                <h3 class="card-title">📝 Tạo yêu cầu mới</h3>
                
                <form id="formPhucKhao">
                    <div class="form-group">
                        <label for="baiThi">Chọn bài thi cần phúc khảo</label>
                        <select id="baiThi" class="form-control" required>
                            <option value="">-- Chọn bài thi --</option>
                            <?php foreach($baiThiHopLe as $bt): ?>
                                <option value="<?php echo $bt['id']; ?>"><?php echo $bt['ten']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cauHoi">Chọn câu hỏi cụ thể (không bắt buộc)</label>
                        <select id="cauHoi" class="form-control">
                            <option value="all">Tất cả câu hỏi</option>
                            <option value="1">Câu 1</option>
                            <option value="2">Câu 2</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="noiDung">Nội dung khiếu nại chi tiết</label>
                        <textarea id="noiDung" class="form-control" placeholder="Vui lòng mô tả chi tiết lý do bạn muốn phúc khảo..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Đính kèm minh chứng (Hình ảnh/PDF)</label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="fileInput" hidden accept=".png, .jpg, .jpeg, .pdf">
                            <div class="upload-icon">☁️</div>
                            <div class="upload-text"><span>Tải tệp lên</span> hoặc kéo thả vào đây</div>
                            <div class="upload-hint">PNG, JPG, PDF tối đa 10MB</div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        ➤ Gửi yêu cầu
                    </button>
                </form>
            </div>

            <div class="history-section">
                <div class="card" style="margin-bottom: 24px;">
                    <div class="history-header">
                        <h3 class="card-title" style="margin: 0;">🕒 Lịch sử yêu cầu</h3>
                        <div class="history-tabs">
                            <button class="tab-btn active">Tất cả</button>
                            <button class="tab-btn">Đang chờ</button>
                            <button class="tab-btn">Đã xong</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>MÃ ĐƠN</th>
                                    <th>BÀI THI / CÂU HỎI</th>
                                    <th>NGÀY GỬI</th>
                                    <th>TRẠNG THÁI</th>
                                    <th>THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($lichSuYeuCau as $yc): ?>
                                    <tr>
                                        <td style="color: var(--text-muted);"><?php echo $yc['ma_don']; ?></td>
                                        <td>
                                            <span class="td-exam"><?php echo $yc['bai_thi']; ?></span>
                                            <span class="td-scope"><?php echo $yc['pham_vi']; ?></span>
                                        </td>
                                        <td><?php echo $yc['ngay_gui']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $yc['trang_thai']; ?>">
                                                <?php echo $yc['txt_trang_thai']; ?>
                                            </span>
                                        </td>
                                        <td><button class="btn-detail">Chi tiết</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-footer">
                        <span>Hiển thị 4 trên 12 yêu cầu</span>
                        <div class="page-controls">
                            <button class="page-btn">‹</button>
                            <button class="page-btn">›</button>
                        </div>
                    </div>
                </div>

                <div class="alert-info">
                    <div class="alert-icon">i</div>
                    <div class="alert-content">
                        <h4>Thông tin lưu ý</h4>
                        <p>Thời gian phúc khảo tối đa là 7 ngày kể từ ngày công bố điểm thi. Kết quả sẽ được gửi thông báo qua email và hiển thị tại danh sách trên trong vòng 3-5 ngày làm việc.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="simple-footer">
        <div class="simple-footer-brand">
            <span class="nav-brand-icon" style="padding: 2px 6px; border-radius: 4px; font-size: 12px;">?</span> ExamPortal
        </div>
        <div>© 2023 Hệ thống thi trực tuyến. Tất cả các quyền được bảo lưu.</div>
    </footer>

<script src="../../asset/js/thisinh.js"></script>
</body>
</html>