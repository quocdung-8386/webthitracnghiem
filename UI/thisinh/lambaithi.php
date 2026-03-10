<?php
session_start();

// --- TẠO NGÂN HÀNG 40 CÂU HỎI MÔN CÔNG NGHỆ PHẦN MỀM ---
$questions = [
    [
        'id' => 1,
        'cau_hoi' => 'Trong kiến trúc Model-View-Controller (MVC), thành phần nào chịu trách nhiệm xử lý logic nghiệp vụ và tương tác dữ liệu?',
        'dap_an' => ['A. View', 'B. Model', 'C. Controller', 'D. Router'],
        'dap_an_dung' => 1 // Index 1 = B
    ],
    [
        'id' => 2,
        'cau_hoi' => 'Trong quá trình phát triển phần mềm, giai đoạn nào sau đây thường tốn nhiều thời gian và chi phí nhất trong vòng đời bảo trì?',
        'dap_an' => ['A. Phân tích yêu cầu và thiết kế hệ thống', 'B. Kiểm thử và gỡ lỗi (Debugging)', 'C. Viết mã nguồn (Coding)', 'D. Triển khai và hướng dẫn sử dụng'],
        'dap_an_dung' => 1 // Index 1 = B
    ],
    [
        'id' => 3,
        'cau_hoi' => 'Mô hình phát triển phần mềm nào nhấn mạnh vào sự lặp đi lặp lại và phản hồi liên tục từ khách hàng?',
        'dap_an' => ['A. Waterfall (Thác nước)', 'B. V-Model', 'C. Agile', 'D. Spiral (Xoắn ốc)'],
        'dap_an_dung' => 2 // Index 2 = C
    ]
];

// Sinh thêm 37 câu hỏi giả để đủ 40 câu
for ($i = 4; $i <= 40; $i++) {
    $questions[] = [
        'id' => $i,
        'cau_hoi' => "Đây là nội dung câu hỏi số $i thuộc bộ môn Công nghệ phần mềm. Hãy chọn đáp án chính xác nhất?",
        'dap_an' => ['A. Đáp án A', 'B. Đáp án B', 'C. Đáp án C', 'D. Đáp án D'],
        'dap_an_dung' => rand(0, 3)
    ];
}

// Lưu dữ liệu vào session để trang kết quả lấy ra chấm điểm
$_SESSION['current_exam'] = $questions;
$tongSoCau = count($questions);

// 1. GỌI HEADER VÀO TRANG
include 'header.php';
?>

<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center;">
    <div style="border: 4px solid #f3f3f3; border-top: 4px solid var(--primary); border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
    <h2 style="font-size: 24px; font-weight: bold; color: var(--text-main);">Đang nộp bài...</h2>
    <p style="color: var(--text-muted); margin-top: 10px;">Hệ thống đang ghi nhận kết quả của bạn, vui lòng đợi trong giây lát.</p>
</div>
<style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>

<div class="main-container">
    <header class="card header-bar">
        <div class="timer-box" style="display: flex; gap: 10px; align-items: center;">
            <div style="font-size: 24px;">⏱️</div>
            <div class="timer-text">
                <span style="font-size: 12px; color: var(--text-muted);">Thời gian còn lại</span><br>
                <strong id="timerDisplay" style="font-size: 18px;">45:00</strong>
            </div>
        </div>

        <div style="flex: 1; margin: 0 40px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                <span>Tiến độ làm bài: <span id="progressText">0/<?php echo $tongSoCau; ?></span> câu</span>
                <span id="progressPercent" style="color: var(--primary);">0%</span>
            </div>
            <div style="height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                <div id="progressBar" style="height: 100%; background: var(--primary); width: 0%; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <div>
            <button type="button" onclick="nopBaiSubmit()" style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                Nộp bài ➤
            </button>
        </div>
    </header>

    <form id="examForm" action="xemketqua.php" method="POST">
        <input type="hidden" name="time_taken" id="timeTaken" value="0">

        <div class="exam-layout" style="margin-top: 20px;">
            <div class="exam-col-left">
                
                <?php foreach ($questions as $index => $q): ?>
                <div class="card question-block" id="q-block-<?php echo $index + 1; ?>" style="display: none; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <span style="background: var(--bg-body); padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">Câu hỏi <?php echo $index + 1; ?></span>
                        <button type="button" style="border: none; background: transparent; color: var(--text-muted); cursor: pointer; font-weight: 600;">🚩 Đánh dấu</button>
                    </div>

                    <div style="font-size: 18px; font-weight: 600; margin-bottom: 24px; line-height: 1.5;">
                        <?php echo htmlspecialchars($q['cau_hoi']); ?>
                    </div>

                    <div>
                        <?php foreach ($q['dap_an'] as $ansIndex => $ansText): ?>
                        <label class="option-item" onclick="updateProgress(<?php echo $index + 1; ?>)">
                            <input type="radio" name="answer_<?php echo $q['id']; ?>" value="<?php echo $ansIndex; ?>" style="margin-right: 12px; transform: scale(1.2);">
                            <span><?php echo htmlspecialchars($ansText); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                    <button type="button" id="btnPrev" onclick="changePage(-1)" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: white; font-weight: bold; cursor: pointer; display: none;">← Câu trước</button>
                    <button type="button" id="btnNext" onclick="changePage(1)" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: white; font-weight: bold; cursor: pointer; margin-left: auto;">Câu tiếp theo →</button>
                </div>
                
                <div style="margin-top: 20px; display: inline-block; background: #d1fae5; color: #059669; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                    ☁️ Đã tự động lưu 10 giây trước
                </div>
            </div>

            <div class="exam-col-right">
                <div class="card" style="position: sticky; top: 90px;">
                    <div style="font-weight: bold; margin-bottom: 16px; display: flex; justify-content: space-between;">
                        Danh sách câu hỏi <span style="font-size: 12px; color: var(--text-muted); font-weight: normal;">Tổng: <?php echo $tongSoCau; ?></span>
                    </div>
                    
                    <div class="q-grid">
                        <?php for($i = 1; $i <= $tongSoCau; $i++): ?>
                            <div id="palette-btn-<?php echo $i; ?>" class="q-btn" style="cursor: pointer;" onclick="jumpToPage(<?php echo ceil($i / 10); ?>)">
                                <?php echo $i; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Logic Đếm ngược 45 phút
    let timeLeft = 45 * 60; // 45 phút (tính bằng giây)
    let totalTime = 45 * 60;
    
    const timerInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            nopBaiSubmit(); // Hết giờ tự động nộp bài
            return;
        }
        timeLeft--;
        let m = Math.floor(timeLeft / 60);
        let s = timeLeft % 60;
        document.getElementById('timerDisplay').innerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        document.getElementById('timeTaken').value = totalTime - timeLeft; // Lưu lại số giây đã làm để chấm điểm
    }, 1000);

    // 2. Logic Phân trang (10 câu / trang)
    const totalQuestions = <?php echo $tongSoCau; ?>;
    const perPage = 10;
    let currentPage = 1;
    const totalPages = Math.ceil(totalQuestions / perPage);

    function renderPage() {
        // Ẩn toàn bộ câu hỏi
        document.querySelectorAll('.question-block').forEach(el => el.style.display = 'none');
        
        // Tính toán hiển thị từ câu X đến câu Y
        let start = (currentPage - 1) * perPage + 1;
        let end = currentPage * perPage;
        if (end > totalQuestions) end = totalQuestions;

        // Hiện các câu thuộc trang hiện tại
        for (let i = start; i <= end; i++) {
            let qBlock = document.getElementById('q-block-' + i);
            if(qBlock) qBlock.style.display = 'block';
        }

        // Cập nhật ẩn/hiện nút điều hướng
        document.getElementById('btnPrev').style.display = (currentPage === 1) ? 'none' : 'block';
        document.getElementById('btnNext').style.display = (currentPage === totalPages) ? 'none' : 'block';
        
        // Highlight trang hiện tại trên Palette (nếu muốn)
        document.querySelectorAll('.q-btn').forEach(btn => btn.style.border = '');
        let currentBtn = document.getElementById('palette-btn-' + start);
        if(currentBtn && !currentBtn.classList.contains('answered')) {
            currentBtn.style.border = '2px solid var(--primary)';
        }
    }

    function changePage(step) {
        currentPage += step;
        renderPage();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function jumpToPage(page) {
        currentPage = page;
        renderPage();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // 3. Logic Cập nhật Tiến độ & Đổi màu Palette
    let answeredCount = 0;
    function updateProgress(qNumber) {
        // Đổi màu ô palette thành 'answered' (Class CSS có sẵn trong file của bạn)
        let btn = document.getElementById('palette-btn-' + qNumber);
        if (btn) btn.classList.add('answered');
        
        // Tính số câu đã tick radio
        answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
        
        // Cập nhật text và thanh tiến độ
        let percent = Math.round((answeredCount / totalQuestions) * 100);
        document.getElementById('progressText').innerText = answeredCount + '/' + totalQuestions;
        document.getElementById('progressPercent').innerText = percent + '%';
        document.getElementById('progressBar').style.width = percent + '%';
    }

    // 4. Logic Nộp bài (Delay 5s)
    function nopBaiSubmit() {
        if(answeredCount < totalQuestions && timeLeft > 0) {
            if(!confirm('Bạn vẫn chưa làm hết câu hỏi. Bạn có chắc chắn muốn nộp bài?')) return;
        }
        
        clearInterval(timerInterval); // Dừng đồng hồ
        
        // Hiện màn hình loading overlay (bằng thẻ flex)
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Đợi 5 giây rồi submit Form
        setTimeout(() => {
            document.getElementById('examForm').submit();
        }, 5000);
    }

    // Khởi tạo chạy hàm render trang đầu tiên khi vừa mở web
    renderPage();
</script>

<?php
// 2. GỌI FOOTER VÀO TRANG
include 'footer.php';
?>