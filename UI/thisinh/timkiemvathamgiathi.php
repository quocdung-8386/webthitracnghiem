<?php
session_start();
require_once __DIR__ . '/../../app/config/Database.php';

// Bảo vệ route: Chỉ thí sinh mới được vào
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'thisinh') {
    header("Location: ../login.php");
    exit();
}

try {
    $conn = Database::getConnection();

    // 1. LẤY DANH SÁCH MÔN HỌC ĐỂ ĐỔ VÀO DROPDOWN
    $stmt_dm = $conn->query("SELECT * FROM danh_muc");
    $danhMucs = $stmt_dm->fetchAll(PDO::FETCH_ASSOC);

    // 2. NHẬN THAM SỐ LỌC TỪ URL (GET)
    $search = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? '');
    $status = trim($_GET['status'] ?? '');

    // 3. XÂY DỰNG CÂU LỆNH SQL ĐỘNG THEO BỘ LỌC
    $whereConditions = [];
    $params = [];

    // Lọc theo Tên kỳ thi
    if ($search !== '') {
        $whereConditions[] = "d.tieu_de LIKE ?";
        $params[] = "%$search%";
    }

    // Lọc theo Môn học (Danh mục)
    if ($category !== '') {
        $whereConditions[] = "dm.ma_danh_muc = ?";
        $params[] = $category;
    }

    // Nối các điều kiện lại với nhau
    $whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";

    $sql = "SELECT d.ma_de_thi, d.tieu_de, d.thoi_gian_lam, d.ngay_tao,
                   COUNT(cdt.ma_cau_hoi) as so_cau_hoi,
                   MAX(dm.ten_danh_muc) as ten_mon_hoc
            FROM de_thi d
            LEFT JOIN chi_tiet_de_thi cdt ON d.ma_de_thi = cdt.ma_de_thi
            LEFT JOIN cau_hoi ch ON cdt.ma_cau_hoi = ch.ma_cau_hoi
            LEFT JOIN danh_muc dm ON ch.ma_danh_muc = dm.ma_danh_muc
            $whereClause
            GROUP BY d.ma_de_thi, d.tieu_de, d.thoi_gian_lam, d.ngay_tao
            ORDER BY d.ngay_tao DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $danhSachDeThi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Xử lý bộ lọc Trạng thái
    if ($status !== '') {
        $danhSachDeThi = array_filter($danhSachDeThi, function($exam) use ($status) {
            if ($status === 'dang_mo') return true;
            if ($status === 'sap_dien_ra' || $status === 'da_dong') return false; 
            return true;
        });
    }

} catch (PDOException $e) {
    die("Lỗi hệ thống CSDL: " . $e->getMessage());
}

// 4. GỌI HEADER
include 'header.php';
?>

<div class="min-h-screen py-8" style="background-color: var(--bg-body, #f9fafb); transition: background-color 0.3s ease;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-2" style="color: var(--text-main, #1f2937); transition: color 0.3s ease;">Tìm kiếm & Tham gia kỳ thi</h1>
            <p style="color: var(--text-muted, #6b7280); transition: color 0.3s ease;">Khám phá các kỳ thi trực tuyến mới nhất. Tham gia ngay để đánh giá năng lực của bạn.</p>
        </div>

        <form method="GET" action="timkiemvathamgiathi.php" class="p-4 rounded-2xl shadow-sm border mb-8 flex flex-col md:flex-row gap-4 items-center" style="background-color: var(--bg-body, #ffffff); border-color: var(--border-color, #e5e7eb); transition: all 0.3s ease;">
            
            <div class="relative flex-grow w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="material-icons" style="font-size: 20px; color: var(--text-muted, #9ca3af);">search</span>
                </span>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm tên kỳ thi..." class="w-full border py-3 pl-10 pr-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--bg-body, #f9fafb); color: var(--text-main, #1f2937); border-color: var(--border-color, #e5e7eb);">
            </div>

            <select name="category" class="w-full md:w-48 border py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--bg-body, #f9fafb); color: var(--text-main, #1f2937); border-color: var(--border-color, #e5e7eb);">
                <option value="">Tất cả môn học</option>
                <?php foreach($danhMucs as $dm): ?>
                    <option value="<?php echo $dm['ma_danh_muc']; ?>" <?php echo ($category == $dm['ma_danh_muc']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dm['ten_danh_muc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="w-full md:w-40 border py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: var(--bg-body, #f9fafb); color: var(--text-main, #1f2937); border-color: var(--border-color, #e5e7eb);">
                <option value="">Trạng thái</option>
                <option value="dang_mo" <?php echo ($status == 'dang_mo') ? 'selected' : ''; ?>>Đang mở</option>
                <option value="sap_dien_ra" <?php echo ($status == 'sap_dien_ra') ? 'selected' : ''; ?>>Sắp diễn ra</option>
                <option value="da_dong" <?php echo ($status == 'da_dong') ? 'selected' : ''; ?>>Đã đóng</option>
            </select>

            <button type="submit" class="w-full md:w-auto text-white font-bold py-3 px-6 rounded-xl flex items-center justify-center gap-2 whitespace-nowrap" style="background-color: var(--primary, #2563eb);">
                <span class="material-icons" style="font-size: 18px;">filter_list</span> Lọc kết quả
            </button>
            
            <?php if($search || $category || $status): ?>
                <a href="timkiemvathamgiathi.php" style="color: var(--text-muted, #9ca3af);">
                    <span class="material-icons">close</span>
                </a>
            <?php endif; ?>
        </form>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold flex items-center gap-2" style="color: var(--text-main, #1f2937);">
                <span class="material-icons" style="color: var(--primary, #3b82f6);">event_note</span> Danh sách kỳ thi hiện có
            </h2>
            <span class="text-sm" style="color: var(--text-muted, #6b7280);">Hiển thị <?php echo count($danhSachDeThi); ?> kết quả</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
            
            <?php if (!empty($danhSachDeThi)): ?>
                <?php foreach ($danhSachDeThi as $deThi): ?>
                <div class="rounded-2xl shadow-sm border p-6 flex flex-col h-full border-t-4" style="background-color: var(--bg-body, #ffffff); border-color: var(--border-color, #e5e7eb); border-top-color: var(--primary, #3b82f6); transition: all 0.3s ease;">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-xs font-bold px-3 py-1 rounded-full" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">ĐANG MỞ</span>
                        <span class="material-icons" style="font-size: 18px; color: #f87171;">local_activity</span>
                    </div>
                    
                    <h3 class="text-lg font-bold mb-4 line-clamp-2" style="min-height: 56px; color: var(--text-main, #1f2937);">
                        <?php echo htmlspecialchars($deThi['tieu_de']); ?>
                    </h3>
                    
                    <div class="space-y-2 mb-6 flex-grow">
                        <div class="flex items-center text-sm gap-2" style="color: var(--text-muted, #4b5563);">
                            <span class="material-icons" style="font-size: 16px; color: #10b981;">menu_book</span>
                            <span><?php echo htmlspecialchars($deThi['ten_mon_hoc'] ?? 'Chưa phân loại môn'); ?></span>
                        </div>
                        <div class="flex items-center text-sm gap-2" style="color: var(--text-muted, #4b5563);">
                            <span class="material-icons" style="font-size: 16px; color: var(--text-muted, #9ca3af);">schedule</span>
                            <span><?php echo $deThi['thoi_gian_lam']; ?> phút • <?php echo $deThi['so_cau_hoi']; ?> câu hỏi</span>
                        </div>
                        <div class="flex items-center text-sm gap-2" style="color: var(--text-muted, #4b5563);">
                            <span class="material-icons" style="font-size: 16px; color: #60a5fa;">calendar_today</span>
                            <span>Tạo ngày: <?php echo date('d/m/Y', strtotime($deThi['ngay_tao'])); ?></span>
                        </div>
                    </div>
                    
                    <a href="lambaithi.php?id_de_thi=<?php echo $deThi['ma_de_thi']; ?>" class="block w-full text-center font-bold py-3 rounded-xl mt-auto" style="background: rgba(37, 99, 235, 0.1); color: var(--primary, #2563eb); transition: all 0.2s;">
                        Chi tiết ➔
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full p-10 rounded-2xl border text-center" style="background-color: var(--bg-body, #ffffff); border-color: var(--border-color, #e5e7eb);">
                    <span class="material-icons text-6xl mb-4" style="color: var(--text-muted, #d1d5db);">search_off</span>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-main, #374151);">Không tìm thấy kỳ thi nào!</h3>
                    <p style="color: var(--text-muted, #6b7280);">Không có kỳ thi nào khớp với bộ lọc của bạn. Hãy thử thay đổi từ khóa hoặc môn học.</p>
                </div>
            <?php endif; ?>

        </div>
        
    </div>
</div>

<?php include 'footer.php'; ?>