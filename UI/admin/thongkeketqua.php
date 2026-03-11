<?php
/**
 * THỐNG KÊ KẾT QUẢ THI
 * File: UI/admin/thongkeketqua.php
 * 
 * Chức năng: Thống kê tổng quan kết quả thi trắc nghiệm
 * Bao gồm: Tổng lượt thi, điểm TB, tỷ lệ đỗ, câu hỏi khó nhất, biểu đồ theo tháng
 * 
 * @author: Admin
 * @last_modified: 2026
 */

// 1. Cấu hình thông tin trang
$title = "Thống kê tổng quan - Hệ Thống Thi Trực Tuyến";
$active_menu = "stat_result";

// Kết nối database
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

// Biến lưu trữ dữ liệu thống kê
$tong_luot_thi = 0;
$diem_trung_binh = 0;
$ty_le_do = 0;
$cau_hoi_kho_nhat = null;
$monthly_data = [];
$ky_thi_data = [];

// ============================================================
// TRUY VẤN DỮ LIỆU THỐNG KÊ VỚI XỬ LÝ LỖI
// ============================================================

try {
    // 1. Tổng lượt thi - Đếm các bài đã nộp và đã chấm
    $stmt = $conn->query("
        SELECT COUNT(*) 
        FROM bai_lam 
        WHERE trang_thai IN ('da_nop', 'da_cham')
    ");
    $tong_luot_thi = (int) $stmt->fetchColumn();

    // 2. Điểm trung bình - Tính trung bình cộng điểm các bài đã nộp
    $stmt = $conn->query("
        SELECT AVG(tong_diem) 
        FROM bai_lam 
        WHERE trang_thai IN ('da_nop', 'da_cham') 
        AND tong_diem IS NOT NULL
    ");
    $diem_trung_binh = $stmt->fetchColumn();
    $diem_trung_binh = $diem_trung_binh ? round((float) $diem_trung_binh, 2) : 0;

    // 3. Tỷ lệ đỗ (điểm >= 5) - Tính phần trăm thí sinh đạt điểm liệt
    $stmt_total = $conn->query("
        SELECT COUNT(*) 
        FROM bai_lam 
        WHERE trang_thai IN ('da_nop', 'da_cham') 
        AND tong_diem IS NOT NULL
    ");
    $total_exams = (int) $stmt_total->fetchColumn();

    $stmt_pass = $conn->query("
        SELECT COUNT(*) 
        FROM bai_lam 
        WHERE trang_thai IN ('da_nop', 'da_cham') 
        AND tong_diem >= 5
    ");
    $pass_exams = (int) $stmt_pass->fetchColumn();

    // Tránh chia cho 0
    $ty_le_do = $total_exams > 0 ? round(($pass_exams / $total_exams) * 100, 1) : 0;

    // 4. Câu hỏi khó nhất (tỷ lệ sai cao nhất)
    // Query tối ưu: JOIN đúng bảng, xử lý NULL, tránh chia cho 0
    $stmt = $conn->query("
        SELECT 
            ch.ma_cau_hoi,
            ch.noi_dung,
            COUNT(ctbl.ma_chi_tiet) as so_luot_tra_loi,
            SUM(CASE 
                WHEN da_dung.ma_dap_an IS NULL OR ctbl.ma_dap_an_chon IS NULL THEN 0
                WHEN da_dung.ma_dap_an != ctbl.ma_dap_an_chon THEN 1 
                ELSE 0 
            END) as so_cau_sai,
            ROUND(
                CASE 
                    WHEN COUNT(ctbl.ma_chi_tiet) > 0 THEN 
                        SUM(CASE 
                            WHEN da_dung.ma_dap_an IS NULL OR ctbl.ma_dap_an_chon IS NULL THEN 0
                            WHEN da_dung.ma_dap_an != ctbl.ma_dap_an_chon THEN 1 
                            ELSE 0 
                        END) / COUNT(ctbl.ma_chi_tiet) * 100 
                    ELSE 0 
                END
            , 1) as ti_le_sai
        FROM cau_hoi ch
        INNER JOIN dap_an da_dung ON ch.ma_cau_hoi = da_dung.ma_cau_hoi 
            AND da_dung.la_dap_an_dung = 1
        INNER JOIN chi_tiet_bai_lam ctbl ON ch.ma_cau_hoi = ctbl.ma_cau_hoi
        INNER JOIN bai_lam bl ON ctbl.ma_bai_lam = bl.ma_bai_lam
        WHERE bl.trang_thai IN ('da_nop', 'da_cham') 
            AND ctbl.ma_dap_an_chon IS NOT NULL
        GROUP BY ch.ma_cau_hoi, ch.noi_dung
        HAVING COUNT(ctbl.ma_chi_tiet) >= 5
        ORDER BY ti_le_sai DESC
        LIMIT 1
    ");
    $cau_hoi_kho_nhat = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Dữ liệu biểu đồ thí sinh theo tháng (12 tháng gần nhất)
    $stmt = $conn->query("
        SELECT 
            MONTH(thoi_diem_nop) as thang,
            COUNT(*) as so_luong
        FROM bai_lam 
        WHERE trang_thai IN ('da_nop', 'da_cham') 
            AND thoi_diem_nop IS NOT NULL
            AND thoi_diem_nop >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY MONTH(thoi_diem_nop)
        ORDER BY thang
    ");
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Dữ liệu kỳ thi gần đây - Thống kê chi tiết theo từng đề thi
    $stmt = $conn->query("
        SELECT 
            dt.ma_de_thi,
            dt.tieu_de as ten_ky_thi,
            COUNT(bl.ma_bai_lam) as tong_luot_thi,
            ROUND(AVG(CASE WHEN bl.tong_diem IS NOT NULL THEN bl.tong_diem ELSE 0 END), 1) as diem_trung_binh,
            SUM(CASE WHEN bl.tong_diem >= 5 THEN 1 ELSE 0 END) as so_luot_do,
            ROUND(
                CASE 
                    WHEN COUNT(bl.ma_bai_lam) > 0 THEN 
                        SUM(CASE WHEN bl.tong_diem >= 5 THEN 1 ELSE 0 END) / COUNT(bl.ma_bai_lam) * 100 
                    ELSE 0 
                END
            , 1) as ti_le_do
        FROM de_thi dt
        INNER JOIN ca_thi ct ON dt.ma_de_thi = ct.ma_de_thi
        INNER JOIN bai_lam bl ON ct.ma_ca_thi = bl.ma_ca_thi
        WHERE bl.trang_thai IN ('da_nop', 'da_cham')
        GROUP BY dt.ma_de_thi, dt.tieu_de
        ORDER BY ct.thoi_gian_bat_dau DESC
        LIMIT 10
    ");
    $ky_thi_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Ghi log lỗi nhưng không hiển thị chi tiết lỗi cho người dùng
    error_log("Thống kê kết quả thi - Lỗi truy vấn: " . $e->getMessage());
    
    // Hiể $e->getn thị thông báo lỗi thân thiện
    $error_message = "Không thể tải dữ liệu thống kê. Vui lòng thử lại sau.";
}

// ============================================================
// XỬ LÝ DỮ LIỆU BIỂU ĐỒ
// ============================================================

// Tìm max để tính chiều cao biểu đồ
$max_count = 0;
foreach ($monthly_data as $row) {
    if ((int) $row['so_luong'] > $max_count) {
        $max_count = (int) $row['so_luong'];
    }
}

// Chuyển đổi dữ liệu tháng sang format biểu đồ
$chart_data = [];
$month_labels = ['T.1', 'T.2', 'T.3', 'T.4', 'T.5', 'T.6', 'T.7', 'T.8', 'T.9', 'T.10', 'T.11', 'T.12'];
for ($i = 1; $i <= 12; $i++) {
    $found = false;
    foreach ($monthly_data as $row) {
        if ((int) $row['thang'] == $i) {
            $height = $max_count > 0 ? round(($row['so_luong'] / $max_count) * 100) : 0;
            $chart_data[] = [
                'label' => $month_labels[$i-1], 
                'height' => $height . '%', 
                'value' => (int) $row['so_luong']
            ];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $chart_data[] = ['label' => $month_labels[$i-1], 'height' => '0%', 'value' => 0];
    }
}

// Format số hiển thị
$tong_luot_thi_display = number_format($tong_luot_thi);

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="flex items-center gap-3">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Thống kê & Báo cáo <span class="mx-2">›</span> <span
                    class="text-slate-800 dark:text-white font-medium">Xuất báo cáo dữ liệu thi</span>
            </div>
            <span
                class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[10px] font-bold rounded uppercase flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Báo cáo thời gian thực
            </span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" id="searchInput" placeholder="Tìm kiếm dữ liệu..."
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="notifButton" type="button"
                        class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                        <span class="material-icons">notifications</span>
                        <span
                            class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                    </button>
                    <div id="notifDropdown"
                        class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                        <div
                            class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo hệ thống</span>
                            <a href="#"
                                class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                                dấu đã đọc</a>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#"
                                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Báo cáo tháng 10
                                    đã được tổng hợp thành công.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> 1 giờ trước</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button id="darkModeToggle"
                    class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">
        <div class="max-w-7xl mx-auto space-y-6">

            <?php if (isset($error_message)): ?>
            <!-- Hiển thị thông báo lỗi nếu có -->
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-3">
                    <span class="material-icons text-red-500">error_outline</span>
                    <p class="text-sm text-red-600 dark:text-red-400"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 relative overflow-hidden transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center">
                            <span class="material-icons text-[20px]">library_books</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[11px] font-bold rounded-md">Tháng
                            này: +15%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Tổng lượt thi</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2"><?php echo $tong_luot_thi_display; ?></p>
                    <p class="text-[11px] text-slate-400">Dữ liệu tính từ đầu năm 2026</p>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-900/30 text-orange-500 dark:text-orange-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">calculate</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-[11px] font-bold rounded-md uppercase">Hệ
                            10</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Điểm trung bình</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2"><?php echo $diem_trung_binh; ?></p>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-orange-500 dark:bg-orange-400 rounded-full" style="width: <?php echo $diem_trung_binh * 10; ?>%"></div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">check_circle</span>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-[11px] font-bold rounded-md">Tăng
                            2%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Tỷ lệ đỗ</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white mb-2"><?php echo $ty_le_do; ?>%</p>
                    <p class="text-[11px] text-slate-400">Dựa trên 5.0 điểm liệt</p>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 border-l-4 border-l-red-500 dark:border-l-red-500 transition-colors">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400 flex items-center justify-center">
                            <span class="material-icons text-[20px]">warning_amber</span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-1">Câu hỏi khó nhất</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white mb-2"><?php echo $cau_hoi_kho_nhat ? 'ID: #Q-' . $cau_hoi_kho_nhat['ma_cau_hoi'] : 'Chưa có dữ liệu'; ?></p>
                    <p class="text-[12px] text-slate-500 dark:text-slate-400">Tỷ lệ trả lời sai: <span class="font-bold text-red-500 dark:text-red-400"><?php echo $cau_hoi_kho_nhat ? $cau_hoi_kho_nhat['ti_le_sai'] . '%' : 'N/A'; ?></span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-[16px]">Xu hướng thí sinh tham gia
                            </h3>
                            <p class="text-[12px] text-slate-500 dark:text-slate-400">Thống kê theo từng tháng trong năm
                                2026</p>
                        </div>
                        <button
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 rounded text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Năm
                            2026</button>
                    </div>

                    <div class="flex-1 relative mt-4 min-h-[200px]">
                        <div class="absolute inset-0 flex flex-col justify-between text-[10px] text-slate-400 pb-6">
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">10k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">7.5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">2.5k</span>
                                <div class="flex-1 border-t border-slate-100 dark:border-slate-700/50"></div>
                            </div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">0</span>
                                <div class="flex-1 border-t border-slate-200 dark:border-slate-600"></div>
                            </div>
                        </div>

                        <div class="absolute inset-0 flex justify-between items-end pl-10 pr-4 pb-6 pt-2">
                            <?php foreach ($chart_data as $data): ?>
                                <div class="flex flex-col items-center h-full justify-end w-10 relative group">
                                    <div class="w-2.5 bg-blue-100 dark:bg-blue-900/40 relative rounded-t-sm transition-all duration-300 group-hover:w-3"
                                        style="height: <?php echo $data['height']; ?>;">
                                        <div class="absolute bottom-0 left-0 w-full bg-[#254ada] dark:bg-[#4b6bfb] rounded-t-sm transition-all duration-500"
                                            style="height: 100%;"></div>
                                    </div>
                                    <span
                                        class="absolute -bottom-6 text-[11px] font-semibold text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors"><?php echo $data['label']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px] mb-8">Tỷ lệ xếp loại kết quả</h3>

                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="w-48 h-48 rounded-full relative flex items-center justify-center mb-8 shadow-sm transition-transform hover:scale-105 duration-300"
                            style="background: conic-gradient(#3b82f6 0% 25%, #22c55e 25% 65%, #f97316 65% 85%, #ef4444 85% 100%);">
                            <div
                                class="w-36 h-36 bg-white dark:bg-slate-800 rounded-full flex flex-col items-center justify-center shadow-inner transition-colors">
                                <span class="text-4xl font-black text-slate-800 dark:text-white leading-none">85%</span>
                                <span class="text-[9px] font-bold text-slate-400 mt-1">TRÊN TRUNG BÌNH</span>
                            </div>
                        </div>

                        <div class="w-full space-y-3 px-2 text-[13px] font-medium text-slate-600 dark:text-slate-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-blue-500"></span> Giỏi</div>
                                <span class="font-bold text-slate-800 dark:text-white">25%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-green-500"></span> Khá</div>
                                <span class="font-bold text-slate-800 dark:text-white">40%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-orange-500"></span> Trung bình</div>
                                <span class="font-bold text-slate-800 dark:text-white">20%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-red-500"></span> Yếu</div>
                                <span class="font-bold text-slate-800 dark:text-white">15%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px]">Dữ liệu chi tiết theo Kỳ thi gần
                        đây</h3>
                    <button onclick="handleDownload(this)"
                        class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 flex items-center gap-2 transition">
                        <span class="material-icons text-[18px]">download</span> Tải báo cáo
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Tên kỳ thi</th>
                                <th class="px-6 py-4 text-center">Tổng lượt thi</th>
                                <th class="px-6 py-4 text-center">Điểm TB</th>
                                <th class="px-6 py-4 w-[25%]">Tỷ lệ đỗ</th>
                                <th class="px-6 py-4 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                            <?php if (!empty($ky_thi_data)): ?>
                                <?php foreach ($ky_thi_data as $ky_thi): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition data-row">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-[13px] d-name">
                                        <?php echo htmlspecialchars($ky_thi['ten_ky_thi']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-600 dark:text-slate-300">
                                        <?php echo number_format($ky_thi['tong_luot_thi']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-800 dark:text-white">
                                        <?php echo htmlspecialchars($ky_thi['diem_trung_binh']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-green-500 rounded-full" style="width: <?php echo $ky_thi['ti_le_do']; ?>%"></div>
                                            </div>
                                            <span
                                                class="text-[12px] font-bold text-slate-600 dark:text-slate-300"><?php echo $ky_thi['ti_le_do']; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            class="text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition"
                                            title="Xem chi tiết"><span
                                                class="material-icons text-[20px]">visibility</span></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition data-row">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                        <span class="material-icons text-[40px] text-slate-300 dark:text-slate-600">info</span>
                                        <p class="mt-2">Chưa có dữ liệu kỳ thi</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                    <p id="paginationInfo">Hiển thị 1 - <?php echo count($ky_thi_data); ?> trên tổng số <?php echo count($ky_thi_data); ?> bản ghi</p>
                    <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div
        class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span
                class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
    /* =================================================================
       HÀM HIỂN THỊ THÔNG BÁO (TOAST)
       ================================================================= */
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');
        if (!container || !template) return;

        const toastNode = template.content.cloneNode(true);
        const toastEl = toastNode.querySelector('.toast-item');
        const iconEl = toastNode.querySelector('.toast-icon');

        toastNode.querySelector('.toast-title').textContent = title;
        toastNode.querySelector('.toast-message').textContent = message;

        if (type === 'success') {
            toastEl.classList.add('border-green-500');
            iconEl.innerHTML = '<span class="material-icons text-green-500">check_circle</span>';
        } else if (type === 'error') {
            toastEl.classList.add('border-red-500');
            iconEl.innerHTML = '<span class="material-icons text-red-500">error</span>';
        } else if (type === 'warning') {
            toastEl.classList.add('border-orange-500');
            iconEl.innerHTML = '<span class="material-icons text-orange-500">warning</span>';
        } else {
            toastEl.classList.add('border-blue-500');
            iconEl.innerHTML = '<span class="material-icons text-blue-500">info</span>';
        }

        toastNode.querySelector('.toast-close').onclick = () => {
            toastEl.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toastEl.remove(), 300);
        };

        container.appendChild(toastNode);
        setTimeout(() => toastEl.classList.remove('translate-x-full', 'opacity-0'), 10);
        setTimeout(() => { if (container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
    }

    // Xử lý nút tải báo cáo
    function handleDownload(btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xuất file...';
        btn.disabled = true;

        setTimeout(() => {
            showToast('success', 'Hoàn tất', 'Báo cáo đã được tải xuống dưới dạng Excel (.xlsx).');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 1500);
    }

    /* =================================================================
       SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
       ================================================================= */
    document.addEventListener('DOMContentLoaded', function () {

        // 1. Chức năng Dark Mode
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            if (darkModeIcon) darkModeIcon.textContent = 'light_mode';
        }

        darkModeToggle?.addEventListener('click', () => {
            htmlElement.classList.toggle('dark');
            const isDark = htmlElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
        });

        // 2. Chức năng Dropdown Thông báo
        const notifButton = document.getElementById('notifButton');
        const notifDropdown = document.getElementById('notifDropdown');

        if (notifButton && notifDropdown) {
            notifButton.addEventListener('click', function (e) {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function (e) {
                if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.classList.add('hidden');
                }
            });
        }

        // 3. Phân trang thông minh & Tìm kiếm
        const rowsPerPage = 10;
        let currentPage = 1;
        const allRows = Array.from(document.querySelectorAll('.data-row'));
        let filteredRows = [...allRows];

        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            const totalRows = filteredRows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Ẩn/Hiện dòng
            allRows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            // Cập nhật text hiển thị
            let displayStart = totalRows === 0 ? 0 : start + 1;
            let displayEnd = Math.min(end, totalRows);
            let displayTotal = totalRows;

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart} - ${displayEnd}</span> trên tổng số <span class="font-medium text-slate-800 dark:text-white">${displayTotal.toLocaleString()}</span> bản ghi`;
            }

            // Vẽ nút phân trang
            if (paginationControls) {
                paginationControls.innerHTML = '';

                if (totalRows === 0) return;

                // Nút Prev
                const prevBtn = document.createElement('button');
                prevBtn.className = `w-8 h-8 flex items-center justify-center border rounded transition ${currentPage === 1 ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600'}`;
                prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
                prevBtn.disabled = currentPage === 1;
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
                paginationControls.appendChild(prevBtn);

                const createPageBtn = (i) => {
                    const btn = document.createElement('button');
                    if (i === currentPage) {
                        btn.className = 'w-8 h-8 flex items-center justify-center bg-[#254ada] text-white rounded font-medium shadow-sm transition transform scale-105';
                    } else {
                        btn.className = 'w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-transparent hover:bg-slate-50 dark:hover:bg-slate-700 rounded font-medium text-slate-600 dark:text-slate-300 transition';
                    }
                    btn.innerText = i;
                    btn.onclick = () => { currentPage = i; updatePagination(); };
                    return btn;
                };

                const createDots = () => {
                    const span = document.createElement('span');
                    span.className = 'text-slate-400 px-1 tracking-widest text-xs';
                    span.innerText = '...';
                    return span;
                };

                if (totalPages <= 5) {
                    for (let i = 1; i <= totalPages; i++) paginationControls.appendChild(createPageBtn(i));
                } else {
                    paginationControls.appendChild(createPageBtn(1));
                    if (currentPage > 3) paginationControls.appendChild(createDots());

                    let startPage = Math.max(2, currentPage - 1);
                    let endPage = Math.min(totalPages - 1, currentPage + 1);

                    if (currentPage === 1) endPage = 3;
                    if (currentPage === totalPages) startPage = totalPages - 2;

                    for (let i = startPage; i <= endPage; i++) {
                        paginationControls.appendChild(createPageBtn(i));
                    }

                    if (currentPage < totalPages - 2) paginationControls.appendChild(createDots());
                    paginationControls.appendChild(createPageBtn(totalPages));
                }

                // Nút Next
                const nextBtn = document.createElement('button');
                nextBtn.className = `w-8 h-8 flex items-center justify-center border rounded transition ${currentPage === totalPages ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600'}`;
                nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
        }

        function applyFilters() {
            const text = searchInput ? searchInput.value.toLowerCase() : '';
            filteredRows = allRows.filter(row => {
                const nameCell = row.querySelector('.d-name');
                if (!nameCell) return false;
                return nameCell.textContent.toLowerCase().includes(text);
            });
            currentPage = 1;
            updatePagination();
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);

        updatePagination();
    });
</script>

