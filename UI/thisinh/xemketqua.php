<?php
session_start();

// Bảo vệ route: Chỉ thí sinh mới được vào
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'thisinh') {
    header("Location: ../login.php");
    exit();
}

// Nếu truy cập thẳng mà không qua form nộp bài thì đá về trang chủ
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['current_exam'])) {
    header("Location: timkiemvathamgiathi.php");
    exit();
}

$questions = $_SESSION['current_exam'];
$id_de_thi = $_SESSION['current_exam_id'] ?? 0;
$totalQuestions = count($questions);

// Cấu hình điểm: Hệ 10. Mỗi câu sẽ bằng 10 / tổng số câu.
// Theo ví dụ của bạn (2 câu), mỗi câu sẽ tương ứng 5 điểm.
$diemMoiCau = $totalQuestions > 0 ? (10 / $totalQuestions) : 0;

$correctCount = 0;
$incorrectCount = 0;
$pendingCount = 0; // Số câu tự luận chờ chấm
$score = 0;
$hasTuLuan = false;

// Xử lý thời gian làm bài
$timeTakenSeconds = isset($_POST['time_taken']) ? (int)$_POST['time_taken'] : 0;
$m = floor($timeTakenSeconds / 60);
$s = $timeTakenSeconds % 60;
$timeTakenFormatted = sprintf("%02d:%02d", $m, $s);

$reviewDetails = [];

// CHẤM ĐIỂM
foreach ($questions as $q) {
    $qId = $q['id'];
    $loai = $q['loai'];

    if ($loai == 'trac_nghiem') {
        $correctAnswerIndex = $q['dap_an_dung'];
        $userAnswerIndex = isset($_POST["answer_$qId"]) ? (int)$_POST["answer_$qId"] : -1;

        $isCorrect = ($userAnswerIndex == $correctAnswerIndex);
        
        if ($isCorrect) {
            $correctCount++;
            $score += $diemMoiCau; // Cộng 5 điểm nếu đúng
        } else {
            $incorrectCount++;
        }

        $reviewDetails[] = [
            'loai' => 'trac_nghiem',
            'cau_hoi' => $q['cau_hoi'],
            'dap_an' => $q['dap_an'],
            'dap_an_dung' => $correctAnswerIndex,
            'dap_an_user' => $userAnswerIndex,
            'is_correct' => $isCorrect,
            'loi_giai' => "Giải thích: Đáp án đúng là phần tử đã được đánh dấu xanh. Hãy ôn tập lại lý thuyết phần này nhé!" 
        ];
    } else {
        // XỬ LÝ CÂU TỰ LUẬN
        $hasTuLuan = true;
        $pendingCount++;
        $userAnswerText = isset($_POST["answer_text_$qId"]) ? trim($_POST["answer_text_$qId"]) : "";

        $reviewDetails[] = [
            'loai' => 'tu_luan',
            'cau_hoi' => $q['cau_hoi'],
            'dap_an_user_text' => $userAnswerText,
            'is_correct' => null, // Chờ chấm
            'loi_giai' => 'Câu hỏi tự luận đang chờ Giảng viên chấm điểm. Bạn sẽ được cộng thêm tối đa '.$diemMoiCau.' điểm cho câu này.'
        ];
    }
}

// Xếp loại dựa trên điểm TẠM TÍNH
$score = round($score, 1);
$xepLoai = 'Kém';
$colorRank = '#ef4444'; // Đỏ

if ($score >= 8.5) { $xepLoai = 'Giỏi'; $colorRank = '#2563eb'; } // Xanh dương
elseif ($score >= 7) { $xepLoai = 'Khá'; $colorRank = '#10b981'; } // Xanh lá
elseif ($score >= 5) { $xepLoai = 'T.Bình'; $colorRank = '#f59e0b'; } // Vàng

// Nếu có câu tự luận, chuyển trạng thái thành "Chờ chấm"
if ($hasTuLuan) {
    $xepLoai = 'Chờ chấm';
    $colorRank = '#6b7280'; // Xám
}

// GỌI HEADER
include 'header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="min-h-screen py-8" style="background-color: var(--bg-body, #f9fafb); transition: background-color 0.3s ease;">
    <div id="invoice-box" class="max-w-5xl mx-auto px-4 sm:px-6">
        
        <div class="text-sm mb-6" style="color: var(--text-muted, #6b7280);">
            Trang chủ › Kết quả thi › <strong style="color: var(--text-main, #1f2937);">Chi tiết kết quả</strong>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="md:col-span-2 p-6 rounded-2xl shadow-sm border" style="background-color: var(--bg-surface, #ffffff); border-color: var(--border-color, #e5e7eb);">
                <h2 class="text-lg font-bold mb-1" style="color: var(--text-main, #1f2937);">Kết quả kỳ thi</h2>
                <p class="text-sm mb-6" style="color: var(--text-muted, #6b7280);">Nộp bài lúc <?php echo date('H:i, d/m/Y'); ?></p>
                
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="p-4 rounded-xl text-center flex-1" style="background: rgba(37, 99, 235, 0.1);">
                        <div class="text-xs font-bold uppercase mb-1" style="color: #6b7280;">Điểm số <?php echo $hasTuLuan ? '(Tạm tính)' : ''; ?></div>
                        <div class="text-3xl font-bold" style="color: #2563eb;"><?php echo $score; ?> <span class="text-lg opacity-50">/ 10</span></div>
                    </div>
                    <div class="p-4 rounded-xl text-center flex-1" style="background: rgba(16, 185, 129, 0.1);">
                        <div class="text-xs font-bold uppercase mb-1" style="color: #6b7280;">Câu đúng</div>
                        <div class="text-3xl font-bold" style="color: #10b981;"><?php echo $correctCount; ?> <span class="text-lg opacity-50">/ <?php echo $totalQuestions; ?></span></div>
                    </div>
                    <div class="p-4 rounded-xl text-center flex-1" style="background: rgba(239, 68, 68, 0.1);">
                        <div class="text-xs font-bold uppercase mb-1" style="color: #6b7280;">Câu sai</div>
                        <div class="text-3xl font-bold" style="color: #ef4444;"><?php echo $incorrectCount; ?></div>
                    </div>
                    <div class="p-4 rounded-xl text-center flex-1" style="background: rgba(245, 158, 11, 0.1);">
                        <div class="text-xs font-bold uppercase mb-1" style="color: #6b7280;">Thời gian</div>
                        <div class="text-2xl font-bold mt-1" style="color: #f59e0b;"><?php echo $timeTakenFormatted; ?></div>
                    </div>
                    
                    <div class="w-24 h-24 rounded-full border-4 flex items-center justify-center flex-col ml-4" style="border-color: <?php echo $colorRank; ?>; color: <?php echo $colorRank; ?>;">
                        <span class="font-bold text-lg leading-tight text-center"><?php echo $xepLoai; ?></span>
                    </div>
                </div>
                
                <?php if($hasTuLuan): ?>
                <div class="mt-4 text-sm font-semibold p-3 rounded-lg" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                    ⚠️ Đề thi có <?php echo $pendingCount; ?> câu tự luận đang chờ Giảng viên chấm. Điểm số cuối cùng có thể thay đổi!
                </div>
                <?php endif; ?>
            </div>

            <div id="action-box" class="p-6 rounded-2xl shadow-sm border" style="background-color: var(--bg-surface, #ffffff); border-color: var(--border-color, #e5e7eb);">
                <h3 class="font-bold mb-4" style="color: var(--text-main, #1f2937);">Hành động</h3>
                
                <button onclick="exportPDF()" class="w-full text-white font-bold py-3 rounded-xl mb-3 transition hover:opacity-90 flex items-center justify-center gap-2" style="background-color: var(--primary, #2563eb);">
                    <span class="material-icons">picture_as_pdf</span> Xuất phiếu điểm
                </button>
                
               <a href="phuckhaokhieunai.php?id_de_thi=<?php echo $id_de_thi; ?>" class="w-full flex items-center justify-center gap-2 border font-bold py-3 rounded-xl transition hover:opacity-90" style="background-color: transparent; border-color: var(--border-color); color: var(--text-main);">
    <span class="material-icons">mail_outline</span> Gửi phúc khảo
</a>
                
                <div class="mt-4 p-4 rounded-xl border" style="background: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.2);">
                    <div class="font-bold text-sm mb-1" style="color: #7c3aed;">ℹ Lưu ý</div>
                    <div class="text-xs" style="color: #8b5cf6;">Thời gian khiếu nại/phúc khảo kết thúc sau 48h kể từ khi công bố kết quả chấm thi cuối cùng.</div>
                </div>
            </div>
        </div>

        <div class="mb-4 flex justify-between items-center">
            <h3 class="font-bold" style="color: var(--text-main, #1f2937);">Chi tiết bài làm</h3>
            <div class="text-sm font-semibold">
                <span style="color: #10b981; margin-right: 12px;">● Đúng</span>
                <span style="color: #ef4444; margin-right: 12px;">● Sai</span>
                <span style="color: #f59e0b;">● Chờ chấm</span>
            </div>
        </div>

        <div class="space-y-6">
            <?php foreach ($reviewDetails as $index => $review): ?>
                <div class="flex gap-4">
                    <?php 
                        $circleColor = 'background: rgba(107, 114, 128, 0.1); color: #6b7280;'; // Xám (Chờ chấm)
                        if ($review['loai'] == 'trac_nghiem') {
                            $circleColor = $review['is_correct'] 
                                ? 'background: rgba(16, 185, 129, 0.2); color: #059669;' // Xanh
                                : 'background: rgba(239, 68, 68, 0.2); color: #dc2626;'; // Đỏ
                        }
                    ?>
                    <div class="w-10 h-10 shrink-0 flex items-center justify-center font-bold text-sm rounded-full" style="<?php echo $circleColor; ?>">
                        <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                    </div>
                    
                    <div class="flex-grow">
                        <h4 class="text-lg font-bold mb-4" style="color: var(--text-main, #1f2937);"><?php echo htmlspecialchars($review['cau_hoi']); ?></h4>
                        
                        <?php if ($review['loai'] == 'trac_nghiem'): ?>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <?php foreach ($review['dap_an'] as $ma_dap_an => $ansText): 
                                    $bgColor = 'background: var(--bg-surface); border-color: var(--border-color); color: var(--text-muted);';
                                    $icon = '';
                                    
                                    if ($ma_dap_an == $review['dap_an_dung']) {
                                        $bgColor = 'background: rgba(16, 185, 129, 0.1); border-color: #34d399; color: #065f46; font-weight: bold;';
                                        $icon = '✓';
                                    } elseif ($ma_dap_an == $review['dap_an_user'] && !$review['is_correct']) {
                                        $bgColor = 'background: rgba(239, 68, 68, 0.1); border-color: #f87171; color: #991b1b;';
                                        $icon = '✗';
                                    }
                                ?>
                                    <div class="border rounded-xl p-4 flex justify-between items-center" style="<?php echo $bgColor; ?>">
                                        <span><?php echo htmlspecialchars($ansText); ?></span>
                                        <?php if($icon): ?><span class="font-bold text-lg"><?php echo $icon; ?></span><?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        <?php else: ?>
                            <div class="mb-4 border rounded-xl p-4" style="background: var(--bg-surface); border-color: var(--border-color);">
                                <div class="text-sm font-bold mb-2" style="color: var(--text-muted);">Câu trả lời của bạn:</div>
                                <div style="color: var(--text-main); white-space: pre-wrap;"><?php echo htmlspecialchars($review['dap_an_user_text'] ?: '(Bạn đã để trống câu này)'); ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="border rounded-xl p-4" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2);">
                            <div class="font-bold text-sm mb-1" style="color: #d97706;">💡 LỜI GIẢI / GHI CHÚ</div>
                            <p class="text-sm" style="color: var(--text-main);"><?php echo htmlspecialchars($review['loi_giai']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<script>
    // Hàm xuất PDF tự động
    function exportPDF() {
        // Tạm ẩn cột Hành động để không in nút "Xuất PDF" vào trong file PDF
        const actionBox = document.getElementById('action-box');
        actionBox.style.display = 'none';
        
        // Cấu hình PDF
        var element = document.getElementById('invoice-box');
        var opt = {
            margin:       10,
            filename:     'Phieu_Diem_<?php echo $_SESSION['ho_ten'] ?? 'Thi_Sinh'; ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: document.documentElement.style.getPropertyValue('--bg-body') || '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Bắt đầu tạo PDF
        html2pdf().set(opt).from(element).save().then(() => {
            // Hiển thị lại nút bấm sau khi xuất xong
            actionBox.style.display = 'block';
        });
    }
</script>

<?php include 'footer.php'; ?>