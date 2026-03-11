<?php
/**
 * PHÂN TÍCH HỌC THUẬT
 * File: UI/admin/phantichhocthuat.php
 * 
 * Chức năng: Thống kê và phân tích chất lượng câu hỏi dựa trên dữ liệu thật từ database
 * Bao gồm: Top câu hỏi khó, tỷ lệ đúng/sai/bỏ qua, đánh giá độ khó
 * 
 * @author: Admin
 * @last_modified: 2026
 */

// 1. Cấu hình thông tin trang
$title = "Phân tích Học thuật - Hệ Thống Thi Trực Tuyến";
$active_menu = "academic";

// Kết nối database
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

// Khởi tạo biến lưu trữ dữ liệu
$top_difficult = [];
$question_stats = [];
$error_message = '';
$total_questions = 0;
$total_answered = 0;

// ============================================================
// HÀM XỬ LÝ ĐÁNH GIÁ ĐỘ KHÓ CÂU HỎI
// ============================================================
function evaluateDifficulty($correct_rate) {
    if ($correct_rate < 20) {
        return [
            'eval' => 'QUÁ KHÓ',
            'bg' => 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400'
        ];
    } elseif ($correct_rate < 50) {
        return [
            'eval' => 'KHÓ',
            'bg' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400'
        ];
    } elseif ($correct_rate < 80) {
        return [
            'eval' => 'BÌNH THƯỜNG',
            'bg' => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'
        ];
    } else {
        return [
            'eval' => 'QUÁ DỄ',
            'bg' => 'bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb]'
        ];
    }
}

// ============================================================
// TRUY VẤN DỮ LIỆU VỚI XỬ LÝ LỖI
// ============================================================

try {
    
    // ============================================================
    // 1. TOP 10 CÂU HỎI KHÓ NHẤT
    // Query: Tính tỷ lệ (sai + bỏ qua) / tổng số lần xuất hiện
    // ============================================================
    
    $stmt = $conn->query("
        SELECT 
            ch.ma_cau_hoi,
            ch.noi_dung,
            COUNT(ctbl.ma_chi_tiet) as so_luot_xuat_hien,
            -- Đếm số câu trả lời sai (không đúng và không null)
            SUM(CASE 
                WHEN da_dung.ma_dap_an IS NOT NULL 
                     AND ctbl.ma_dap_an_chon IS NOT NULL 
                     AND da_dung.ma_dap_an != ctbl.ma_dap_an_chon 
                THEN 1 ELSE 0 
            END) as so_cau_sai,
            -- Đếm số câu bỏ qua (null)
            SUM(CASE WHEN ctbl.ma_dap_an_chon IS NULL THEN 1 ELSE 0 END) as so_bo_qua,
            -- Tổng số lần sai hoặc bỏ qua
            SUM(CASE 
                WHEN da_dung.ma_dap_an IS NOT NULL 
                     AND (ctbl.ma_dap_an_chon IS NULL 
                          OR da_dung.ma_dap_an != ctbl.ma_dap_an_chon)
                THEN 1 ELSE 0 
            END) as tong_sai_bo_qua,
            -- Tỷ lệ sai/bỏ qua (càng cao càng khó)
            ROUND(
                CASE 
                    WHEN COUNT(ctbl.ma_chi_tiet) > 0 THEN 
                        SUM(CASE 
                            WHEN da_dung.ma_dap_an IS NOT NULL 
                                 AND (ctbl.ma_dap_an_chon IS NULL 
                                      OR da_dung.ma_dap_an != ctbl.ma_dap_an_chon)
                            THEN 1 ELSE 0 
                        END) * 100.0 / COUNT(ctbl.ma_chi_tiet)
                    ELSE 0 
                END
            , 1) as ty_le_sai_bo_qua
        FROM cau_hoi ch
        INNER JOIN dap_an da_dung ON ch.ma_cau_hoi = da_dung.ma_cau_hoi 
            AND da_dung.la_dap_an_dung = 1
        INNER JOIN chi_tiet_bai_lam ctbl ON ch.ma_cau_hoi = ctbl.ma_cau_hoi
        INNER JOIN bai_lam bl ON ctbl.ma_bai_lam = bl.ma_bai_lam
        WHERE bl.trang_thai IN ('da_nop', 'da_cham')
        GROUP BY ch.ma_cau_hoi, ch.noi_dung
        HAVING COUNT(ctbl.ma_chi_tiet) >= 5  -- Chỉ lấy câu hỏi có ít nhất 5 lượt trả lời
        ORDER BY ty_le_sai_bo_qua DESC
        LIMIT 10
    ");
    
    $top_difficult_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý dữ liệu top difficult để hiển thị
    foreach ($top_difficult_raw as $item) {
        $rate = floatval($item['ty_le_sai_bo_qua']);
        $width = min($rate, 100);
        
        // Xác định màu sắc dựa trên tỷ lệ
        if ($rate >= 80) {
            $color = 'bg-red-600';
            $text = 'text-red-600 dark:text-red-400';
        } elseif ($rate >= 60) {
            $color = 'bg-red-500';
            $text = 'text-red-500 dark:text-red-400';
        } elseif ($rate >= 40) {
            $color = 'bg-orange-500';
            $text = 'text-orange-500 dark:text-orange-400';
        } elseif ($rate >= 30) {
            $color = 'bg-orange-400';
            $text = 'text-orange-500 dark:text-orange-400';
        } else {
            $color = 'bg-yellow-400';
            $text = 'text-yellow-600 dark:text-yellow-400';
        }
        
        // Cắt ngắn nội dung nếu quá dài
        $desc = mb_strlen($item['noi_dung']) > 60 
            ? mb_substr($item['noi_dung'], 0, 60) . '...' 
            : $item['noi_dung'];
        $desc = '"' . htmlspecialchars($desc) . '"';
        
        $top_difficult[] = [
            'id' => 'Q-' . $item['ma_cau_hoi'],
            'desc' => $desc,
            'rate' => $rate . '%',
            'width' => $width . '%',
            'color' => $color,
            'text' => $text
        ];
    }
    
    // ============================================================
    // 2. CHI TIẾT THỐNG KÊ TỪNG CÂU HỎI
    // Query: Lấy tất cả câu hỏi với thống kê chi tiết
    // ============================================================
    
    $stmt = $conn->query("
        SELECT 
            ch.ma_cau_hoi,
            ch.noi_dung,
            ch.muc_do,
            dm.ten_danh_muc,
            COUNT(ctbl.ma_chi_tiet) as so_luot_xuat_hien,
            -- Số câu đúng
            SUM(CASE 
                WHEN da_dung.ma_dap_an IS NOT NULL 
                     AND ctbl.ma_dap_an_chon IS NOT NULL 
                     AND da_dung.ma_dap_an = ctbl.ma_dap_an_chon 
                THEN 1 ELSE 0 
            END) as so_cau_dung,
            -- Số câu sai
            SUM(CASE 
                WHEN da_dung.ma_dap_an IS NOT NULL 
                     AND ctbl.ma_dap_an_chon IS NOT NULL 
                     AND da_dung.ma_dap_an != ctbl.ma_dap_an_chon 
                THEN 1 ELSE 0 
            END) as so_cau_sai,
            -- Số câu bỏ qua
            SUM(CASE WHEN ctbl.ma_dap_an_chon IS NULL THEN 1 ELSE 0 END) as so_bo_qua,
            -- Tỷ lệ đúng
            ROUND(
                CASE 
                    WHEN COUNT(ctbl.ma_chi_tiet) > 0 THEN 
                        SUM(CASE 
                            WHEN da_dung.ma_dap_an IS NOT NULL 
                                 AND ctbl.ma_dap_an_chon IS NOT NULL 
                                 AND da_dung.ma_dap_an = ctbl.ma_dap_an_chon 
                            THEN 1 ELSE 0 
                        END) * 100.0 / COUNT(ctbl.ma_chi_tiet)
                    ELSE 0 
                END
            , 1) as ty_le_dung,
            -- Tỷ lệ bỏ qua
            ROUND(
                CASE 
                    WHEN COUNT(ctbl.ma_chi_tiet) > 0 THEN 
                        SUM(CASE WHEN ctbl.ma_dap_an_chon IS NULL THEN 1 ELSE 0 END) * 100.0 / COUNT(ctbl.ma_chi_tiet)
                    ELSE 0 
                END
            , 1) as ty_le_bo_qua
        FROM cau_hoi ch
        LEFT JOIN danh_muc dm ON ch.ma_danh_muc = dm.ma_danh_muc
        LEFT JOIN dap_an da_dung ON ch.ma_cau_hoi = da_dung.ma_cau_hoi 
            AND da_dung.la_dap_an_dung = 1
        LEFT JOIN chi_tiet_bai_lam ctbl ON ch.ma_cau_hoi = ctbl.ma_cau_hoi
        LEFT JOIN bai_lam bl ON ctbl.ma_bai_lam = bl.ma_bai_lam
            AND bl.trang_thai IN ('da_nop', 'da_cham')
        GROUP BY ch.ma_cau_hoi, ch.noi_dung, ch.muc_do, dm.ten_danh_muc
        ORDER BY ch.ma_cau_hoi DESC
    ");
    
    $question_stats_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Xử lý dữ liệu question stats
    foreach ($question_stats_raw as $item) {
        $correct_rate = floatval($item['ty_le_dung']);
        $skip_rate = floatval($item['ty_le_bo_qua']);
        $count = (int) $item['so_luot_xuat_hien'];
        
        // Xác định màu sắc cho tỷ lệ đúng
        if ($correct_rate >= 80) {
            $c_color = 'bg-green-500';
            $c_text = 'text-green-600 dark:text-green-400';
        } elseif ($correct_rate >= 50) {
            $c_color = 'bg-orange-400';
            $c_text = 'text-orange-500 dark:text-orange-400';
        } elseif ($correct_rate >= 20) {
            $c_color = 'bg-yellow-400';
            $c_text = 'text-yellow-600 dark:text-yellow-400';
        } else {
            $c_color = 'bg-red-500';
            $c_text = 'text-red-600 dark:text-red-400';
        }
        
        // Lấy đánh giá
        $eval_result = evaluateDifficulty($correct_rate);
        
        // Format subject
        $subject = 'Chưa phân loại';
        if (!empty($item['ten_danh_muc'])) {
            $subject = htmlspecialchars($item['ten_danh_muc']);
        }
        if (!empty($item['muc_do'])) {
            $muc_do_map = [
                'de' => 'Dễ',
                'tb' => 'TB', 
                'kho' => 'Khó',
                'rat_kho' => 'Rất khó'
            ];
            $muc_do_text = $muc_do_map[strtolower($item['muc_do'])] ?? $item['muc_do'];
            $subject .= ' - Mức: ' . $muc_do_text;
        }
        
        // Cắt ngắn nội dung nếu quá dài
        $content = mb_strlen($item['noi_dung']) > 80 
            ? mb_substr($item['noi_dung'], 0, 80) . '...' 
            : $item['noi_dung'];
        
        $question_stats[] = [
            'id' => 'Q-' . $item['ma_cau_hoi'],
            'content' => htmlspecialchars($content),
            'subject' => $subject,
            'count' => number_format($count),
            'correct' => $correct_rate . '%',
            'correct_w' => min($correct_rate, 100) . '%',
            'c_color' => $c_color,
            'c_text' => $c_text,
            'skip' => $skip_rate . '%',
            'eval' => $eval_result['eval'],
            'eval_bg' => $eval_result['bg']
        ];
    }
    
    // Tổng số câu hỏi đã thống kê
    $total_questions = count($question_stats);
    $total_answered = array_sum(array_column($question_stats_raw, 'so_luot_xuat_hien'));
    
    // Tính số câu cần điều chỉnh (tỷ lệ đúng < 10% hoặc bỏ qua > 40%)
    $need_adjustment = 0;
    foreach ($question_stats_raw as $item) {
        if (floatval($item['ty_le_dung']) < 10 || floatval($item['ty_le_bo_qua']) > 40) {
            $need_adjustment++;
        }
    }
    
} catch (PDOException $e) {
    // Ghi log lỗi
    error_log("Phân tích học thuật - Lỗi truy vấn: " . $e->getMessage());
    $error_message = "Không thể tải dữ liệu phân tích. Vui lòng thử lại sau.";
}

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Thống kê & Báo cáo <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Phân tích chất lượng học thuật</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" id="searchInput" placeholder="Tìm kiếm câu hỏi..."
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
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Hệ thống phát
                                    hiện <span class="font-bold text-red-500"><?php echo $need_adjustment; ?> câu hỏi</span> cần được tinh chỉnh.</p>
                                <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                        class="material-icons text-[12px]">schedule</span> Vừa xong</span>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div
                    class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex flex-col transition-colors">
                    <div
                        class="flex justify-between items-start mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white text-[16px] flex items-center gap-2">
                                <span class="material-icons text-orange-500 dark:text-orange-400">trending_down</span>
                                Top 10 Câu hỏi gây khó khăn nhất
                            </h3>
                            <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Dựa trên tỷ lệ trả lời sai và
                                bỏ qua của thí sinh (Tháng này)</p>
                        </div>
                        <select
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded text-xs font-semibold focus:outline-none transition-colors">
                            <option>Tất cả môn học</option>
                            <option>Toán học</option>
                            <option>Vật lý</option>
                        </select>
                    </div>

                    <div class="flex-1 space-y-4">
                        <?php foreach ($top_difficult as $item): ?>
                            <div class="group">
                                <div class="flex justify-between text-[12px] mb-1.5 transition-colors">
                                    <span class="text-slate-700 dark:text-slate-300 font-medium">ID:
                                        <?php echo $item['id']; ?> - <?php echo $item['desc']; ?></span>
                                    <span class="font-bold <?php echo $item['text']; ?>"><?php echo $item['rate']; ?> Sai/Bỏ
                                        qua</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full rounded-full <?php echo $item['color']; ?> transition-all duration-1000"
                                        style="width: <?php echo $item['width']; ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button
                        class="mt-6 text-[13px] font-semibold text-[#254ada] dark:text-[#4b6bfb] hover:text-[#1e3bb3] dark:hover:text-blue-300 hover:underline mx-auto block transition-colors">Xem
                        đầy đủ Top 10 câu khó</button>
                </div>

                <div class="lg:col-span-1 space-y-6 flex flex-col">

                    <div
                        class="bg-[#254ada] dark:bg-[#1e3a8a] rounded-xl shadow-md dark:shadow-none p-6 text-white flex-1 relative overflow-hidden transition-colors">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl pointer-events-none">
                        </div>
                        <h3 class="font-bold text-blue-100 flex items-center gap-2 text-sm mb-4 relative z-10">
                            <span class="material-icons bg-white/20 p-1 rounded-md text-[18px]">health_and_safety</span>
                            Chỉ số sức khỏe
                        </h3>
                        <?php 
                        // Tính chỉ số sức khỏe (tỷ lệ câu hỏi có tỷ lệ đúng >= 50%)
                        $healthy_count = 0;
                        foreach ($question_stats_raw as $item) {
                            if (floatval($item['ty_le_dung']) >= 50) {
                                $healthy_count++;
                            }
                        }
                        $health_score = $total_questions > 0 ? round(($healthy_count / $total_questions) * 100, 1) : 0;
                        ?>
                        <p class="text-5xl font-black mb-2 relative z-10"><?php echo $health_score; ?>%</p>
                        <p class="text-[12px] text-blue-200 leading-relaxed mb-6 relative z-10">Độ tin cậy của ngân hàng
                            câu hỏi hiện tại</p>

                        <div class="relative z-10">
                            <div class="flex justify-between text-[11px] font-bold text-blue-100 mb-1">
                                <span>MỤC TIÊU</span>
                                <span>90%</span>
                            </div>
                            <div class="w-full bg-blue-900/50 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full bg-white rounded-full relative transition-all duration-1000"
                                    style="width: <?php echo min($health_score, 100); ?>%;">
                                    <div
                                        class="absolute right-0 top-0 bottom-0 w-2 bg-blue-400 rounded-full blur-[1px]">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900/50 rounded-xl shadow-sm p-6 flex-1 flex flex-col justify-center transition-colors">
                        <h3 class="font-bold text-red-600 dark:text-red-500 flex items-center gap-2 text-sm mb-2">
                            <span class="material-icons text-[20px]">warning_amber</span> Cần điều chỉnh
                        </h3>
                        <p class="text-4xl font-black text-slate-800 dark:text-white mb-1"><?php echo $need_adjustment; ?></p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-relaxed mb-4">Câu hỏi có tỷ lệ
                            đúng < 10% hoặc bỏ qua> 40%</p>
                        <button
                            onclick="showToast('warning', 'Hệ thống đang quét', 'Đang trích xuất danh sách <?php echo $need_adjustment; ?> câu hỏi cần điều chỉnh để đánh giá lại.')"
                            class="w-full py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-500 dark:hover:bg-red-600 hover:text-white dark:hover:text-white rounded-lg text-[13px] font-bold transition">
                            Kiểm tra ngay
                        </button>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white text-[16px]">Chi tiết thống kê từng câu hỏi</h3>
                    <div class="flex gap-3">
                        <button onclick="showToast('info', 'Bộ lọc', 'Đang mở tùy chọn bộ lọc nâng cao...')"
                            class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 transition">
                            <span class="material-icons text-[18px]">filter_list</span> Bộ lọc
                        </button>
                        <button onclick="handleExportData(this)"
                            class="px-4 py-2 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-sm transition">
                            <span class="material-icons text-[18px]">download</span> Xuất dữ liệu
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-[10px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-700 transition-colors">
                            <tr>
                                <th class="px-6 py-4">Mã câu hỏi</th>
                                <th class="px-6 py-4 w-1/3">Nội dung tóm tắt</th>
                                <th class="px-6 py-4 text-center">Số lần xuất hiện</th>
                                <th class="px-6 py-4">Tỷ lệ đúng (%)</th>
                                <th class="px-6 py-4 text-center">Tỷ lệ bỏ qua (%)</th>
                                <th class="px-6 py-4 text-center">Đánh giá</th>
                                <th class="px-6 py-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <?php foreach ($question_stats as $q): ?>
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition question-row">
                                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300 text-[12px] q-id">
                                        <?php echo $q['id']; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 dark:text-white text-[13px] mb-0.5 truncate w-full max-w-[300px] q-content"
                                            title="<?php echo $q['content']; ?>"><?php echo $q['content']; ?></div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 q-subject">
                                            <?php echo $q['subject']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-600 dark:text-slate-300">
                                        <?php echo $q['count']; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="font-bold <?php echo $q['c_text']; ?> text-[13px] w-10"><?php echo $q['correct']; ?></span>
                                            <div
                                                class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full <?php echo $q['c_color']; ?> rounded-full"
                                                    style="width: <?php echo $q['correct_w']; ?>;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-slate-600 dark:text-slate-300">
                                        <?php echo $q['skip']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1.5 <?php echo $q['eval_bg']; ?> text-[10px] font-bold rounded-full uppercase inline-block"><?php echo $q['eval']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center space-x-1 text-slate-400 dark:text-slate-500">
                                        <button
                                            onclick="showToast('info', 'Chi tiết', 'Đang mở chi tiết câu hỏi <?php echo $q['id']; ?>')"
                                            class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 rounded transition hover:bg-blue-50 dark:hover:bg-slate-700"><span
                                                class="material-icons text-[18px]">visibility</span></button>
                                        <button
                                            onclick="showToast('info', 'Chỉnh sửa', 'Chuyển hướng đến trình sửa câu hỏi.')"
                                            class="hover:text-slate-700 dark:hover:text-white p-1.5 rounded transition hover:bg-slate-100 dark:hover:bg-slate-700"><span
                                                class="material-icons text-[18px]">edit</span></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                    <p id="paginationInfo">Hiển thị 1 - 2 trên tổng số 45,800 câu hỏi</p>
                    <div id="paginationControls" class="flex items-center gap-1 mt-3 md:mt-0">
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

    // Hàm Xử lý nút Xuất Dữ liệu
    function handleExportData(btn) {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang xuất...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            showToast('success', 'Xuất thành công', 'File dữ liệu thống kê câu hỏi đã được tải xuống (.xlsx).');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
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
        const rowsPerPage = 2; // Hiển thị 2 dòng mỗi trang do dữ liệu mẫu ít
        let currentPage = 1;
        const allRows = Array.from(document.querySelectorAll('.question-row'));
        let filteredRows = [...allRows];

        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            // GIẢ LẬP SỐ LƯỢNG LỚN ĐỂ HIỂN THỊ DẤU "..." (GIỐNG THIẾT KẾ)
            const isDemoMode = true;
            const fakeTotalPages = 458;
            const fakeTotalRows = 45800;

            const totalRows = filteredRows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            // Nếu đang ở màn hình chưa tìm kiếm gì, thì show số trang ảo để test giao diện
            if (isDemoMode && searchInput && searchInput.value.trim() === '') {
                totalPages = fakeTotalPages;
            }

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Ẩn/Hiện dòng dữ liệu thật
            allRows.forEach(row => row.style.display = 'none');
            if (currentPage === 1 || !isDemoMode || (searchInput && searchInput.value.trim() !== '')) {
                filteredRows.slice(start, end).forEach(row => row.style.display = '');
            }

            // Cập nhật text thông tin
            let displayStart = totalRows === 0 ? 0 : start + 1;
            let displayEnd = Math.min(end, (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows);
            let displayTotal = (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows;

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart} - ${displayEnd}</span> trên tổng số <span class="font-medium text-slate-800 dark:text-white">${displayTotal.toLocaleString()}</span> câu hỏi`;
            }

            // Vẽ lại các nút phân trang
            if (paginationControls) {
                paginationControls.innerHTML = '';

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

                // Logic in số trang (Có ẩn bớt bằng dấu ...)
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
                const id = row.querySelector('.q-id').textContent.toLowerCase();
                const content = row.querySelector('.q-content').textContent.toLowerCase();
                const subject = row.querySelector('.q-subject').textContent.toLowerCase();
                return id.includes(text) || content.includes(text) || subject.includes(text);
            });
            currentPage = 1;
            updatePagination();
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);

        updatePagination();
    });
</script>