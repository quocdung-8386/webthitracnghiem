<?php
// --- MÔ PHỎNG DỮ LIỆU TỪ CONTROLLER TRUYỀN SANG ---
$thoiGianConLai = "34:12";
$tongSoCau = 40;
$cauHienTai = 12;
$cauDaLam = 12;
$phanTramTienDo = ($cauDaLam / $tongSoCau) * 100;

// Trạng thái: 1 = Đã trả lời, 2 = Đang chọn, 3 = Đánh dấu, 0 = Chưa trả lời
$trangThaiCauHoi = array_fill(1, 40, 0); 
for($i=1; $i<=11; $i++) { $trangThaiCauHoi[$i] = 1; }
$trangThaiCauHoi[4] = 3; 
$trangThaiCauHoi[12] = 2; 

// 1. GỌI HEADER VÀO TRANG
include 'header.php';
?>

<div class="main-container">
    <header class="card header-bar">
        <div class="timer-box" style="display: flex; gap: 10px; align-items: center;">
            <div style="font-size: 24px;">⏱️</div>
            <div class="timer-text">
                <span style="font-size: 12px; color: var(--text-muted);">Thời gian còn lại</span><br>
                <strong><?php echo $thoiGianConLai; ?></strong>
            </div>
        </div>

        <div style="flex: 1; margin: 0 40px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                <span>Tiến độ làm bài: <?php echo $cauDaLam; ?>/<?php echo $tongSoCau; ?> câu</span>
                <span style="color: var(--primary);"><?php echo round($phanTramTienDo); ?>%</span>
            </div>
            <div style="height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; background: var(--primary); width: <?php echo $phanTramTienDo; ?>%;"></div>
            </div>
        </div>

        <div>
            <button style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Nộp bài ➤
            </button>
        </div>
    </header>

    <div class="exam-layout" style="margin-top: 20px;">
        <div class="exam-col-left">
            <div class="card">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <span style="background: var(--bg-body); padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">Câu hỏi <?php echo $cauHienTai; ?></span>
                    <button style="border: none; background: transparent; color: var(--text-muted); cursor: pointer; font-weight: 600;">🚩 Đánh dấu</button>
                </div>

                <div style="font-size: 18px; font-weight: 600; margin-bottom: 24px;">
                    Trong quá trình phát triển phần mềm, giai đoạn nào sau đây thường tốn nhiều thời gian và chi phí nhất trong vòng đời bảo trì?
                </div>

                <div>
                    <label class="option-item">
                        <input type="radio" name="answer_12" value="A" style="margin-right: 12px; transform: scale(1.2);">
                        <span>A. Phân tích yêu cầu và thiết kế hệ thống</span>
                    </label>
                    <label class="option-item selected">
                        <input type="radio" name="answer_12" value="B" checked style="margin-right: 12px; transform: scale(1.2);">
                        <span>B. Kiểm thử và gỡ lỗi (Debugging)</span>
                    </label>
                    <label class="option-item">
                        <input type="radio" name="answer_12" value="C" style="margin-right: 12px; transform: scale(1.2);">
                        <span>C. Viết mã nguồn (Coding)</span>
                    </label>
                    <label class="option-item">
                        <input type="radio" name="answer_12" value="D" style="margin-right: 12px; transform: scale(1.2);">
                        <span>D. Triển khai và hướng dẫn sử dụng</span>
                    </label>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                <button style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: white; font-weight: bold; cursor: pointer;">← Câu trước</button>
                <button style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: white; font-weight: bold; cursor: pointer;">Câu tiếp theo →</button>
            </div>
            
            <div style="margin-top: 20px; display: inline-block; background: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                ☁️ Đã tự động lưu 10 giây trước
            </div>
        </div>

        <div class="exam-col-right">
            <div class="card">
                <div style="font-weight: bold; margin-bottom: 16px; display: flex; justify-content: space-between;">
                    Danh sách câu hỏi <span style="font-size: 12px; color: var(--text-muted); font-weight: normal;">Tổng: 40</span>
                </div>
                
                <div class="q-grid">
                    <?php 
                    for($i = 1; $i <= $tongSoCau; $i++): 
                        $class = '';
                        if($trangThaiCauHoi[$i] == 1) $class = 'answered';
                        elseif($trangThaiCauHoi[$i] == 2) $class = 'current';
                    ?>
                        <div class="q-btn <?php echo $class; ?>" <?php if($class=='current') echo 'style="border: 2px solid var(--primary); color: var(--primary);"'; ?>>
                            <?php echo $i; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// 2. GỌI FOOTER VÀO TRANG
include 'footer.php';
?>