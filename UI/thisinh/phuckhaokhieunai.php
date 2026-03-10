<?php
// --- 1. MÔ PHỎNG DỮ LIỆU TỪ CONTROLLER ---
$baiThiHopLe = [
    ['id' => 1, 'ten' => 'Kỹ thuật lập trình'],
    ['id' => 2, 'ten' => 'Toán cao cấp A1'],
    ['id' => 3, 'ten' => 'Mạng máy tính']
];

$lichSuYeuCau = [
    [
        'ma_don' => '#RF-8291', 'bai_thi' => 'Kỹ thuật lập trình', 'pham_vi' => 'Tất cả câu hỏi', 
        'ngay_gui' => '16/10/2023', 'trang_thai' => 'pending', 'txt_trang_thai' => 'Đang chờ',
        'bg_color' => '#fef3c7', 'text_color' => '#d97706' // Màu vàng (Warning)
    ],
    [
        'ma_don' => '#RF-7430', 'bai_thi' => 'Toán cao cấp A1', 'pham_vi' => 'Câu hỏi 15', 
        'ngay_gui' => '10/10/2023', 'trang_thai' => 'approved', 'txt_trang_thai' => 'Đã duyệt',
        'bg_color' => '#d1fae5', 'text_color' => '#059669' // Màu xanh (Success)
    ],
    [
        'ma_don' => '#RF-6122', 'bai_thi' => 'Anh văn chuyên ngành', 'pham_vi' => 'Tất cả câu hỏi', 
        'ngay_gui' => '01/10/2023', 'trang_thai' => 'rejected', 'txt_trang_thai' => 'Từ chối',
        'bg_color' => '#fee2e2', 'text_color' => '#dc2626' // Màu đỏ (Danger)
    ],
    [
        'ma_don' => '#RF-5510', 'bai_thi' => 'Mạng máy tính', 'pham_vi' => 'Câu hỏi 2', 
        'ngay_gui' => '28/09/2023', 'trang_thai' => 'approved', 'txt_trang_thai' => 'Đã duyệt',
        'bg_color' => '#d1fae5', 'text_color' => '#059669'
    ]
];

// --- 2. GỌI HEADER VÀO TRANG ---
include 'header.php';
?>

<main class="main-container">
    <header class="page-header" style="margin-bottom: 32px;">
        <h1 style="font-size: 28px; margin-bottom: 8px;">Gửi phúc khảo & Khiếu nại</h1>
        <p style="color: var(--text-muted);">Gửi yêu cầu xem xét lại kết quả bài thi hoặc báo cáo sự cố.</p>
    </header>

    <div class="pk-grid">
        <div class="card">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">📝 Tạo yêu cầu mới</h3>
            
            <form id="formPhucKhao">
                <div style="margin-bottom: 20px;">
                    <label for="baiThi" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Chọn bài thi cần phúc khảo</label>
                    <select id="baiThi" class="form-control" required>
                        <option value="">-- Chọn bài thi --</option>
                        <?php foreach($baiThiHopLe as $bt): ?>
                            <option value="<?php echo $bt['id']; ?>"><?php echo $bt['ten']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="cauHoi" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Chọn câu hỏi cụ thể (không bắt buộc)</label>
                    <select id="cauHoi" class="form-control">
                        <option value="all">Tất cả câu hỏi</option>
                        <option value="1">Câu 1</option>
                        <option value="2">Câu 2</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="noiDung" style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Nội dung khiếu nại chi tiết</label>
                    <textarea id="noiDung" class="form-control" placeholder="Vui lòng mô tả chi tiết lý do bạn muốn phúc khảo..." style="resize: vertical; min-height: 100px;" required></textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Đính kèm minh chứng (Hình ảnh/PDF)</label>
                    <div class="upload-area" id="uploadArea">
                        <input type="file" id="fileInput" hidden accept=".png, .jpg, .jpeg, .pdf">
                        <div style="font-size: 24px; color: var(--text-muted); margin-bottom: 8px;">☁️</div>
                        <div class="upload-text" style="font-size: 14px; margin-bottom: 4px;"><span>Tải tệp lên</span> hoặc kéo thả vào đây</div>
                        <div style="font-size: 12px; color: var(--text-muted);">PNG, JPG, PDF tối đa 10MB</div>
                    </div>
                </div>

                <button type="submit" class="btn-action btn-primary" style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                    ➤ Gửi yêu cầu
                </button>
            </form>
        </div>

        <div>
            <div class="card" style="margin-bottom: 24px; overflow-x: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 16px; margin: 0;">🕒 Lịch sử yêu cầu</h3>
                    <div style="display: flex; gap: 8px;">
                        <button style="padding: 6px 12px; border-radius: 20px; font-size: 13px; border: none; background: var(--bg-body); color: var(--text-main); font-weight: 600; cursor: pointer;">Tất cả</button>
                        <button style="padding: 6px 12px; border-radius: 20px; font-size: 13px; border: none; background: transparent; color: var(--text-muted); font-weight: 500; cursor: pointer;">Đang chờ</button>
                    </div>
                </div>

                <table class="table-history">
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
                                    <span style="font-weight: 600; color: var(--text-main); display: block; margin-bottom: 4px;"><?php echo $yc['bai_thi']; ?></span>
                                    <span style="font-size: 12px; color: var(--text-muted);"><?php echo $yc['pham_vi']; ?></span>
                                </td>
                                <td><?php echo $yc['ngay_gui']; ?></td>
                                <td>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: <?php echo $yc['bg_color']; ?>; color: <?php echo $yc['text_color']; ?>;">
                                        <span style="display: block; width: 6px; height: 6px; border-radius: 50%; background: <?php echo $yc['text_color']; ?>;"></span>
                                        <?php echo $yc['txt_trang_thai']; ?>
                                    </span>
                                </td>
                                <td><button style="color: var(--primary); background: transparent; border: none; font-weight: 600; cursor: pointer; font-size: 14px;">Chi tiết</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 13px; color: var(--text-muted);">
                    <span>Hiển thị 4 trên 12 yêu cầu</span>
                    <div class="page-controls" style="display: flex;">
                        <button style="padding: 4px 8px;">‹</button>
                        <button style="padding: 4px 8px;">›</button>
                    </div>
                </div>
            </div>

            <div class="alert-box purple">
                <div class="alert-icon">i</div>
                <div class="alert-text">
                    <strong style="display: block; font-size: 14px; margin-bottom: 4px;">Thông tin lưu ý</strong>
                    <p style="margin: 0;">Thời gian phúc khảo tối đa là 7 ngày kể từ ngày công bố điểm thi. Kết quả sẽ được gửi thông báo qua email và hiển thị tại danh sách trên trong vòng 3-5 ngày làm việc.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// --- 4. GỌI FOOTER VÀO TRANG ---
include 'footer.php';
?>