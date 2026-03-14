<?php
session_start();
require_once __DIR__ . '/../../app/config/Database.php';

// Bảo vệ route: Chỉ thí sinh mới được vào
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'thisinh') {
    header("Location: ../login.php");
    exit();
}

$ma_nguoi_dung = $_SESSION['ma_nguoi_dung'];
$id_de_thi_selected = $_GET['id_de_thi'] ?? 0;
$success_msg = "";
$error_msg = "";

try {
    $conn = Database::getConnection();

    // 1. XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "GỬI PHÚC KHẢO" (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ma_de_thi = $_POST['ma_de_thi'];
        $ma_cau_hoi = $_POST['ma_cau_hoi'];
        $noi_dung = trim($_POST['noi_dung']);
        
        // Xử lý Upload ảnh minh chứng
        $minh_chung = null;
        if (isset($_FILES['minh_chung']) && $_FILES['minh_chung']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/';
            // Tạo thư mục nếu chưa có
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $file_ext = pathinfo($_FILES['minh_chung']['name'], PATHINFO_EXTENSION);
            // Đổi tên file để không bị trùng (Ví dụ: phuckhao_3_168000000.jpg)
            $file_name = 'phuckhao_' . $ma_nguoi_dung . '_' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['minh_chung']['tmp_name'], $target_file)) {
                $minh_chung = $file_name;
            }
        }

        if (empty($noi_dung)) {
            $error_msg = "Vui lòng nhập nội dung khiếu nại/phúc khảo!";
        } else {
            // Lưu vào Database
            $stmt_insert = $conn->prepare("INSERT INTO khieu_nai (ma_nguoi_dung, ma_de_thi, ma_cau_hoi, noi_dung, minh_chung) VALUES (?, ?, ?, ?, ?)");
            if ($stmt_insert->execute([$ma_nguoi_dung, $ma_de_thi, $ma_cau_hoi, $noi_dung, $minh_chung])) {
                $success_msg = "Đã gửi yêu cầu phúc khảo thành công! Vui lòng theo dõi trạng thái bên dưới.";
                // Xóa GET param để tránh gửi trùng
                $id_de_thi_selected = $ma_de_thi; 
            } else {
                $error_msg = "Có lỗi xảy ra, không thể gửi yêu cầu.";
            }
        }
    }

    // 2. LẤY DANH SÁCH ĐỀ THI MÀ THÍ SINH ĐÃ LÀM (Để hiển thị ra Dropdown)
    // Tạm thời lấy toàn bộ đề thi để Demo (Thực tế sẽ JOIN với bảng ket_qua_thi)
    $stmt_dt = $conn->query("SELECT ma_de_thi, tieu_de FROM de_thi ORDER BY ngay_tao DESC");
    $danhSachDeThi = $stmt_dt->fetchAll(PDO::FETCH_ASSOC);

    // 3. LẤY DANH SÁCH CÂU HỎI THUỘC VỀ ĐỀ THI ĐANG ĐƯỢC CHỌN
    $danhSachCauHoi = [];
    if ($id_de_thi_selected > 0) {
        $stmt_ch = $conn->prepare("SELECT ch.ma_cau_hoi, ch.noi_dung 
                                   FROM cau_hoi ch 
                                   JOIN chi_tiet_de_thi cdt ON ch.ma_cau_hoi = cdt.ma_cau_hoi 
                                   WHERE cdt.ma_de_thi = ?");
        $stmt_ch->execute([$id_de_thi_selected]);
        $danhSachCauHoi = $stmt_ch->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. LẤY LỊCH SỬ PHÚC KHẢO CỦA THÍ SINH NÀY
    $stmt_ls = $conn->prepare("SELECT kn.*, dt.tieu_de as ten_de_thi 
                               FROM khieu_nai kn 
                               LEFT JOIN de_thi dt ON kn.ma_de_thi = dt.ma_de_thi 
                               WHERE kn.ma_nguoi_dung = ? 
                               ORDER BY kn.ngay_tao DESC");
    $stmt_ls->execute([$ma_nguoi_dung]);
    $lichSuPhucKhao = $stmt_ls->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Lỗi hệ thống CSDL: " . $e->getMessage());
}

// Gọi Header
include 'header.php';
?>

<div class="min-h-screen py-8" style="background-color: var(--bg-body, #f9fafb); transition: background-color 0.3s ease;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-2" style="color: var(--text-main, #1f2937);">Phúc khảo & Khiếu nại</h1>
            <p style="color: var(--text-muted, #6b7280);">Gửi yêu cầu xem xét lại kết quả chấm điểm hoặc báo cáo lỗi câu hỏi trong đề thi.</p>
        </div>

        <?php if($success_msg): ?>
            <div class="mb-6 p-4 rounded-xl border flex items-center gap-3" style="background: rgba(16, 185, 129, 0.1); border-color: #34d399; color: #065f46;">
                <span class="material-icons">check_circle</span>
                <span class="font-bold"><?php echo $success_msg; ?></span>
            </div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="mb-6 p-4 rounded-xl border flex items-center gap-3" style="background: rgba(239, 68, 68, 0.1); border-color: #f87171; color: #991b1b;">
                <span class="material-icons">error</span>
                <span class="font-bold"><?php echo $error_msg; ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="rounded-2xl shadow-sm border p-6 sticky top-24" style="background-color: var(--bg-surface, #ffffff); border-color: var(--border-color, #e5e7eb);">
                    <h2 class="text-lg font-bold mb-6 flex items-center gap-2" style="color: var(--text-main, #1f2937);">
                        <span class="material-icons" style="color: var(--primary, #3b82f6);">edit_document</span> Tạo yêu cầu mới
                    </h2>
                    
                    <form action="phuckhaokhieunai.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                        
                        <div>
                            <label class="block text-sm font-bold mb-2" style="color: var(--text-muted);">1. Chọn bài thi cần phúc khảo <span class="text-red-500">*</span></label>
                            <select name="ma_de_thi" required onchange="window.location.href='phuckhaokhieunai.php?id_de_thi='+this.value" class="w-full border py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                                <option value="">-- Chọn bài thi --</option>
                                <?php foreach($danhSachDeThi as $dt): ?>
                                    <option value="<?php echo $dt['ma_de_thi']; ?>" <?php echo ($id_de_thi_selected == $dt['ma_de_thi']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dt['tieu_de']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2" style="color: var(--text-muted);">2. Chọn câu hỏi bị lỗi <span class="text-red-500">*</span></label>
                            <select name="ma_cau_hoi" required class="w-full border py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);">
                                <?php if(empty($danhSachCauHoi)): ?>
                                    <option value="">Vui lòng chọn bài thi ở trên trước</option>
                                <?php else: ?>
                                    <option value="">-- Chọn câu hỏi cần xem xét --</option>
                                    <?php foreach($danhSachCauHoi as $ch): ?>
                                        <option value="<?php echo $ch['ma_cau_hoi']; ?>">
                                            Câu hỏi: <?php echo mb_strimwidth(htmlspecialchars($ch['noi_dung']), 0, 50, "..."); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2" style="color: var(--text-muted);">3. Nội dung miêu tả <span class="text-red-500">*</span></label>
                            <textarea name="noi_dung" required rows="4" placeholder="Vui lòng miêu tả rõ lý do bạn muốn phúc khảo (VD: Đáp án A mới là đúng theo giáo trình trang...)" class="w-full border py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" style="background-color: var(--bg-body); color: var(--text-main); border-color: var(--border-color);"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2" style="color: var(--text-muted);">4. Ảnh minh chứng (Tùy chọn)</label>
                            
                            <div class="relative border-2 border-dashed rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer" style="border-color: var(--border-color);" onclick="document.getElementById('file-upload').click()">
                                <input type="file" name="minh_chung" id="file-upload" accept="image/*" class="hidden" onchange="previewImage(event)">
                                <div id="upload-prompt">
                                    <span class="material-icons text-4xl mb-2" style="color: var(--text-muted);">cloud_upload</span>
                                    <p class="text-sm font-semibold" style="color: var(--primary);">Bấm để chọn ảnh</p>
                                    <p class="text-xs mt-1" style="color: var(--text-muted);">PNG, JPG, JPEG (Tối đa 5MB)</p>
                                </div>
                                <img id="image-preview" src="#" alt="Preview" class="hidden mx-auto max-h-40 rounded-lg shadow-sm">
                            </div>
                        </div>

                        <button type="submit" class="w-full text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 transition hover:opacity-90" style="background-color: var(--primary, #2563eb);">
                            <span class="material-icons" style="font-size: 18px;">send</span> Gửi yêu cầu
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-2xl shadow-sm border p-6" style="background-color: var(--bg-surface, #ffffff); border-color: var(--border-color, #e5e7eb);">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold flex items-center gap-2" style="color: var(--text-main, #1f2937);">
                            <span class="material-icons" style="color: var(--primary, #3b82f6);">history</span> Lịch sử yêu cầu của bạn
                        </h2>
                        <span class="text-sm font-bold px-3 py-1 rounded-full" style="background: var(--bg-body); color: var(--text-muted);">Tổng: <?php echo count($lichSuPhucKhao); ?></span>
                    </div>

                    <?php if (empty($lichSuPhucKhao)): ?>
                        <div class="text-center py-12 border-2 border-dashed rounded-xl" style="border-color: var(--border-color);">
                            <span class="material-icons text-6xl mb-4" style="color: var(--text-muted);">inbox</span>
                            <h3 class="font-bold mb-2" style="color: var(--text-main);">Chưa có yêu cầu nào!</h3>
                            <p class="text-sm" style="color: var(--text-muted);">Bạn chưa từng gửi yêu cầu phúc khảo nào trong hệ thống.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach($lichSuPhucKhao as $ls): 
                                // Xử lý màu sắc Badge Trạng thái
                                $statusText = 'Đang chờ';
                                $statusStyle = 'background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid #fcd34d;'; // Vàng
                                $iconStatus = 'pending_actions';

                                if ($ls['trang_thai'] == 'da_duyet') {
                                    $statusText = 'Đã duyệt';
                                    $statusStyle = 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid #6ee7b7;'; // Xanh
                                    $iconStatus = 'check_circle';
                                } elseif ($ls['trang_thai'] == 'tu_choi') {
                                    $statusText = 'Từ chối';
                                    $statusStyle = 'background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid #fca5a5;'; // Đỏ
                                    $iconStatus = 'cancel';
                                }
                            ?>
                            <div class="border rounded-xl p-5 hover:shadow-md transition" style="background-color: var(--bg-body); border-color: var(--border-color);">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-4">
                                    <div>
                                        <div class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--text-muted);">
                                            Kỳ thi: <span style="color: var(--primary);"><?php echo htmlspecialchars($ls['ten_de_thi']); ?></span>
                                        </div>
                                        <div class="text-sm" style="color: var(--text-main);">
                                            <span class="font-bold">Mã câu hỏi lỗi:</span> #<?php echo str_pad($ls['ma_cau_hoi'], 4, '0', STR_PAD_LEFT); ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap" style="<?php echo $statusStyle; ?>">
                                        <span class="material-icons" style="font-size: 14px;"><?php echo $iconStatus; ?></span>
                                        <?php echo $statusText; ?>
                                    </div>
                                </div>
                                
                                <div class="p-3 rounded-lg mb-4 text-sm" style="background: var(--bg-surface); color: var(--text-main); border: 1px solid var(--border-color);">
                                    <span class="font-bold" style="color: var(--text-muted);">Nội dung khiếu nại:</span><br>
                                    <?php echo nl2br(htmlspecialchars($ls['noi_dung'])); ?>
                                </div>

                                <div class="flex justify-between items-end">
                                    <div class="text-xs" style="color: var(--text-muted);">
                                        <span class="material-icons align-middle" style="font-size: 14px;">schedule</span> 
                                        Gửi lúc: <?php echo date('H:i d/m/Y', strtotime($ls['ngay_tao'])); ?>
                                    </div>
                                    
                                    <?php if($ls['minh_chung']): ?>
                                        <a href="../../uploads/<?php echo $ls['minh_chung']; ?>" target="_blank" class="text-xs font-bold hover:underline flex items-center gap-1" style="color: var(--primary);">
                                            <span class="material-icons" style="font-size: 16px;">image</span> Xem minh chứng
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Script để hiển thị trước hình ảnh khi người dùng chọn file
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('image-preview');
        var prompt = document.getElementById('upload-prompt');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                prompt.classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'footer.php'; ?>