<?php
session_start();
require_once __DIR__ . '/../../app/config/Database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'thisinh') {
    header("Location: ../login.php");
    exit();
}

$id_de_thi = $_GET['id_de_thi'] ?? null;

if (!$id_de_thi) {
    die("<div style='padding:50px; text-align:center;'><h2 style='color:red;'>Lỗi: Không tìm thấy mã đề thi!</h2><a href='timkiemvathamgiathi.php'>Quay lại</a></div>");
}

$questions = [];
$thoi_gian_lam = 45; 
$ten_de_thi = "Kỳ thi";

try {
    $conn = Database::getConnection();
    
    // 1. LẤY THÔNG TIN ĐỀ THI
    $stmt_dt = $conn->prepare("SELECT tieu_de, thoi_gian_lam FROM de_thi WHERE ma_de_thi = ?");
    $stmt_dt->execute([$id_de_thi]);
    $deThi = $stmt_dt->fetch(PDO::FETCH_ASSOC);
    if ($deThi) {
        $thoi_gian_lam = (int)$deThi['thoi_gian_lam'];
        $ten_de_thi = $deThi['tieu_de'];
    }

    // 2. LẤY DANH SÁCH CÂU HỎI CỦA ĐỀ THI NÀY
    $sql = "SELECT ch.ma_cau_hoi, ch.noi_dung, ch.loai_cau_hoi 
            FROM cau_hoi ch
            JOIN chi_tiet_de_thi cdt ON ch.ma_cau_hoi = cdt.ma_cau_hoi
            WHERE cdt.ma_de_thi = ?
            ORDER BY ch.loai_cau_hoi ASC"; // Xếp trắc nghiệm lên trước, tự luận xuống dưới
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_de_thi]);
    $raw_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý dữ liệu câu hỏi
    foreach ($raw_questions as $row) {
        $ma_cau_hoi = $row['ma_cau_hoi'];
        $loai = $row['loai_cau_hoi']; // 'trac_nghiem' hoặc 'tu_luan'
        
        $item = [
            'id' => $ma_cau_hoi,
            'cau_hoi' => $row['noi_dung'],
            'loai' => $loai,
            'diem' => 5 // Mỗi câu 5 điểm theo rule của bạn
        ];

        // Nếu là trắc nghiệm thì móc thêm đáp án
        if ($loai == 'trac_nghiem') {
            $stmt_ans = $conn->prepare("SELECT * FROM dap_an WHERE ma_cau_hoi = ? ORDER BY thu_tu ASC");
            $stmt_ans->execute([$ma_cau_hoi]);
            $answers = $stmt_ans->fetchAll(PDO::FETCH_ASSOC);
            
            $dap_an_arr = [];
            $dap_an_dung_id = -1;
            $labels = ['A. ', 'B. ', 'C. ', 'D. '];
            
            foreach ($answers as $i => $ans) {
                $label = isset($labels[$i]) ? $labels[$i] : '';
                $dap_an_arr[$ans['ma_dap_an']] = $label . $ans['noi_dung'];
                if ($ans['la_dap_an_dung'] == 1) {
                    $dap_an_dung_id = $ans['ma_dap_an'];
                }
            }
            $item['dap_an'] = $dap_an_arr;
            $item['dap_an_dung'] = $dap_an_dung_id;
        } 
        
        $questions[] = $item;
    }
    
    $_SESSION['current_exam'] = $questions;
    $_SESSION['current_exam_id'] = $id_de_thi;
    $tongSoCau = count($questions);

    if ($tongSoCau == 0) {
        die("<div style='padding:50px; text-align:center;'><h2 style='color:red;'>Đề thi này chưa có câu hỏi nào!</h2><a href='timkiemvathamgiathi.php'>Quay lại</a></div>");
    }

} catch (PDOException $e) {
    die("Lỗi hệ thống CSDL: " . $e->getMessage());
}

// 3. GỌI HEADER VÀO TRANG
include 'header.php';
?>

<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center;">
    <div style="border: 4px solid #f3f3f3; border-top: 4px solid var(--primary, #3b82f6); border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin-bottom: 15px;"></div>
    <h2 style="font-size: 24px; font-weight: bold; color: var(--text-main, #1f2937);">Đang nộp bài...</h2>
    <p style="color: var(--text-muted, #6b7280); margin-top: 10px;">Hệ thống đang ghi nhận kết quả của bạn, vui lòng đợi trong giây lát.</p>
</div>
<style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>

<div class="main-container">
    <div style="margin-bottom: 15px; font-weight: bold; font-size: 18px; color: var(--text-main);">
        Đang thi: <span style="color: var(--primary);"><?php echo htmlspecialchars($ten_de_thi); ?></span>
    </div>

    <header class="card header-bar">
        <div class="timer-box" style="display: flex; gap: 10px; align-items: center;">
            <div style="font-size: 24px;">⏱️</div>
            <div class="timer-text">
                <span style="font-size: 12px; color: var(--text-muted);">Thời gian còn lại</span><br>
                <strong id="timerDisplay" style="font-size: 18px;">--:--</strong>
            </div>
        </div>

        <div style="flex: 1; margin: 0 40px;">
            <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 8px;">
                <span style="color: var(--text-main);">Tiến độ làm bài: <span id="progressText">0/<?php echo $tongSoCau; ?></span> câu</span>
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
                        <div>
                            <span style="background: var(--bg-body); color: var(--text-main); padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">Câu hỏi <?php echo $index + 1; ?></span>
                            
                            <?php if($q['loai'] == 'trac_nghiem'): ?>
                                <span style="margin-left: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold;">Trắc nghiệm (5 điểm)</span>
                            <?php else: ?>
                                <span style="margin-left: 10px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold;">Tự luận (5 điểm - GV chấm)</span>
                            <?php endif; ?>
                        </div>
                        <button type="button" style="border: none; background: transparent; color: var(--text-muted); cursor: pointer; font-weight: 600;">🚩 Đánh dấu</button>
                    </div>

                    <div style="font-size: 18px; font-weight: 600; margin-bottom: 24px; line-height: 1.5; color: var(--text-main);">
                        <?php echo htmlspecialchars($q['cau_hoi']); ?>
                    </div>

                    <div>
                        <?php if ($q['loai'] == 'trac_nghiem'): ?>
                            <?php foreach ($q['dap_an'] as $ma_dap_an => $ansText): ?>
                            <label class="option-item" onclick="updateProgress(<?php echo $index + 1; ?>)" style="color: var(--text-main);">
                                <input type="radio" name="answer_<?php echo $q['id']; ?>" value="<?php echo $ma_dap_an; ?>" style="margin-right: 12px; transform: scale(1.2);">
                                <span><?php echo htmlspecialchars($ansText); ?></span>
                            </label>
                            <?php endforeach; ?>
                        
                        <?php else: ?>
                            <div style="border: 1px solid var(--border-color); border-radius: 8px; padding: 2px;">
                                <textarea name="answer_text_<?php echo $q['id']; ?>" oninput="updateProgress(<?php echo $index + 1; ?>)" rows="8" placeholder="Nhập câu trả lời tự luận của bạn vào đây..." style="width: 100%; border: none; outline: none; padding: 15px; resize: vertical; background: var(--bg-body); color: var(--text-main); border-radius: 6px; font-family: 'Inter', sans-serif;"></textarea>
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 10px; display: flex; align-items: center; gap: 5px;">
                                <span class="material-icons" style="font-size: 16px;">info</span> Câu hỏi này sẽ được giảng viên chấm điểm sau khi nộp bài.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="display: flex; justify-content: space-between; margin-top: 24px;">
                    <button type="button" id="btnPrev" onclick="changePage(-1)" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-surface); color: var(--text-main); font-weight: bold; cursor: pointer; display: none;">← Câu trước</button>
                    <button type="button" id="btnNext" onclick="changePage(1)" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-surface); color: var(--text-main); font-weight: bold; cursor: pointer; margin-left: auto;">Câu tiếp theo →</button>
                </div>
                
                <div style="margin-top: 20px; display: inline-block; background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                    ☁️ Đã tự động lưu
                </div>
            </div>

            <div class="exam-col-right">
                <div class="card" style="position: sticky; top: 90px;">
                    <div style="font-weight: bold; margin-bottom: 16px; display: flex; justify-content: space-between; color: var(--text-main);">
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
    // 1. Đồng hồ đếm ngược từ Database (Lấy số phút * 60)
    let timeLeft = <?php echo $thoi_gian_lam * 60; ?>; 
    let totalTime = <?php echo $thoi_gian_lam * 60; ?>;
    
    const timerInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            nopBaiSubmit(); 
            return;
        }
        timeLeft--;
        let m = Math.floor(timeLeft / 60);
        let s = timeLeft % 60;
        document.getElementById('timerDisplay').innerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        document.getElementById('timeTaken').value = totalTime - timeLeft; 
    }, 1000);

    // 2. Phân trang
    const totalQuestions = <?php echo $tongSoCau; ?>;
    const perPage = 10;
    let currentPage = 1;
    const totalPages = Math.ceil(totalQuestions / perPage);

    function renderPage() {
        document.querySelectorAll('.question-block').forEach(el => el.style.display = 'none');
        let start = (currentPage - 1) * perPage + 1;
        let end = currentPage * perPage;
        if (end > totalQuestions) end = totalQuestions;

        for (let i = start; i <= end; i++) {
            let qBlock = document.getElementById('q-block-' + i);
            if(qBlock) qBlock.style.display = 'block';
        }

        document.getElementById('btnPrev').style.display = (currentPage === 1) ? 'none' : 'block';
        document.getElementById('btnNext').style.display = (currentPage === totalPages) ? 'none' : 'block';
        
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

    // 3. Tiến độ (Kiểm tra cả Radio Button trắc nghiệm VÀ Textarea tự luận)
    function updateProgress(qNumber) {
        let btn = document.getElementById('palette-btn-' + qNumber);
        if (btn) btn.classList.add('answered');
        
        let answeredCount = 0;
        
        // Vòng lặp kiểm tra toàn bộ câu hỏi (Do PHP sinh ra mảng kiểm tra)
        <?php foreach($questions as $index => $q): ?>
            <?php if($q['loai'] == 'trac_nghiem'): ?>
                if(document.querySelector('input[name="answer_<?php echo $q['id']; ?>"]:checked')) answeredCount++;
            <?php else: ?>
                let ta_<?php echo $q['id']; ?> = document.querySelector('textarea[name="answer_text_<?php echo $q['id']; ?>"]');
                if(ta_<?php echo $q['id']; ?> && ta_<?php echo $q['id']; ?>.value.trim() !== '') answeredCount++;
            <?php endif; ?>
        <?php endforeach; ?>
        
        let percent = Math.round((answeredCount / totalQuestions) * 100);
        document.getElementById('progressText').innerText = answeredCount + '/' + totalQuestions;
        document.getElementById('progressPercent').innerText = percent + '%';
        document.getElementById('progressBar').style.width = percent + '%';
    }

    function nopBaiSubmit() {
        // Đếm lại lần cuối trước khi nộp
        let answeredCount = 0;
        <?php foreach($questions as $index => $q): ?>
            <?php if($q['loai'] == 'trac_nghiem'): ?>
                if(document.querySelector('input[name="answer_<?php echo $q['id']; ?>"]:checked')) answeredCount++;
            <?php else: ?>
                let ta_<?php echo $q['id']; ?> = document.querySelector('textarea[name="answer_text_<?php echo $q['id']; ?>"]');
                if(ta_<?php echo $q['id']; ?> && ta_<?php echo $q['id']; ?>.value.trim() !== '') answeredCount++;
            <?php endif; ?>
        <?php endforeach; ?>

        if(answeredCount < totalQuestions && timeLeft > 0) {
            if(!confirm('Bạn vẫn chưa làm hết câu hỏi (Trắc nghiệm hoặc Tự luận). Bạn có chắc chắn muốn nộp bài?')) return;
        }
        
        clearInterval(timerInterval); 
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        setTimeout(() => {
            document.getElementById('examForm').submit();
        }, 5000);
    }

    renderPage();
</script>

<?php include 'footer.php'; ?>