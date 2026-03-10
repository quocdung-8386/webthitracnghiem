<?php
// 1. Cấu hình thông tin trang
$title = "Giám sát trực tuyến - Hệ Thống Thi Trực Tuyến";
$active_menu = "monitor_exam";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

// Lấy tham số tìm kiếm và filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Thống kê tổng quan
$statsSql = "
SELECT 
    (SELECT COUNT(*) FROM bai_lam WHERE trang_thai = 'dang_lam') as total_examining,
    (SELECT COUNT(*) FROM vi_pham_thi WHERE thoi_gian >= DATE_SUB(NOW(), INTERVAL 1 HOUR)) as total_violations
";
$statsStmt = $conn->query($statsSql);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
$totalExamining = $stats['total_examining'] ?? 0;
$totalViolations = $stats['total_violations'] ?? 0;

// Query lấy danh sách thí sinh đang thi
// SỬA THEO DATABASE SCHEMA MỚI:
// - bai_lam.thoi_diem_bat_dau (không phải thoi_gian_bat_dau)
// - so_cau_da_lam tính từ COUNT(chi_tiet_bai_lam) WHERE ma_dap_an_chon IS NOT NULL
// - tong_so_cau tính từ COUNT(chi_tiet_de_thi) JOIN ca_thi -> de_thi
$sql = "
SELECT 
    nd.ma_nguoi_dung,
    nd.ho_ten,
    nd.ten_dang_nhap,
    bl.ma_bai_lam,
    bl.thoi_diem_bat_dau,
    bl.trang_thai,
    ct.ma_ca_thi,
    dt.ma_de_thi,
    dt.tieu_de as ten_de_thi,
    (
        SELECT COUNT(*) 
        FROM chi_tiet_bai_lam ctbl 
        WHERE ctbl.ma_bai_lam = bl.ma_bai_lam 
        AND ctbl.ma_dap_an_chon IS NOT NULL
    ) as so_cau_da_lam,
    (
        SELECT COUNT(*) 
        FROM chi_tiet_de_thi ctdt 
        INNER JOIN ca_thi ct2 ON ctdt.ma_de_thi = (
            SELECT ma_de_thi FROM ca_thi WHERE ma_ca_thi = bl.ma_ca_thi
        )
        WHERE ctdt.ma_de_thi = dt.ma_de_thi
    ) as tong_so_cau,
    (
        SELECT COUNT(*) 
        FROM vi_pham_thi vpt 
        WHERE vpt.ma_bai_lam = bl.ma_bai_lam
    ) as so_vi_pham,
    (
        SELECT GROUP_CONCAT(vpt.loai_vi_pham SEPARATOR '|') 
        FROM vi_pham_thi vpt 
        WHERE vpt.ma_bai_lam = bl.ma_bai_lam
    ) as ds_vi_pham
FROM nguoi_dung nd
INNER JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
INNER JOIN ca_thi ct ON bl.ma_ca_thi = ct.ma_ca_thi
INNER JOIN de_thi dt ON ct.ma_de_thi = dt.ma_de_thi
WHERE bl.trang_thai = 'dang_lam'
";

$params = [];
if (!empty($search)) {
    $sql .= " AND (nd.ho_ten LIKE ? OR nd.ten_dang_nhap LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam];
}

$sql .= " ORDER BY bl.thoi_diem_bat_dau DESC LIMIT 50";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý dữ liệu thành mảng $students
$students = [];
$now = new DateTime();

foreach ($result as $row) {
    $progress = $row['so_cau_da_lam'] . '/' . $row['tong_so_cau'];
    $percent = $row['tong_so_cau'] > 0 ? round(($row['so_cau_da_lam'] / $row['tong_so_cau']) * 100) : 0;
    
    // Avatar từ tên đăng nhập
    $parts = explode(' ', $row['ho_ten']);
    $avatar = count($parts) >= 2 ? substr($parts[0], 0, 1) . substr($parts[1], 0, 1) : substr($row['ho_ten'], 0, 2);
    $avatar_bg = 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400';
    
    // Xác định trạng thái
    $so_vi_pham = (int)$row['so_vi_pham'];
    $is_online = true;
    $status_type = 'normal';
    $status_msg = 'Chưa có vi phạm nào';
    
    if ($so_vi_pham >= 5) {
        $status_type = 'disconnected';
        $status_msg = 'MẤT KẾT NỐI';
        $is_online = false;
    } elseif ($so_vi_pham >= 3) {
        $status_type = 'danger';
        $violations = explode('|', $row['ds_vi_pham'] ?? '');
        $violation_count = count($violations);
        $status_msg = 'CẢNH BÁO VI PHẠM<br><span class="text-[11px] font-normal text-red-500 dark:text-red-400 mt-1 inline-block">' . htmlspecialchars($violations[0] ?? 'Vi phạm') . ' (Lần ' . $violation_count . ')</span>';
    } elseif ($so_vi_pham >= 1) {
        $status_type = 'warning';
        $violations = explode('|', $row['ds_vi_pham'] ?? '');
        $status_msg = 'CÓ ' . $so_vi_pham . ' CẢNH BÁO<br><span class="text-[11px] font-normal text-orange-500 dark:text-orange-400 mt-1 inline-block">' . htmlspecialchars($violations[0] ?? 'Vi phạm') . '</span>';
    }
    
    // Filter theo status
    if ($status_filter === 'violation' && $status_type === 'normal') {
        continue;
    }
    if ($status_filter === 'disconnected' && $status_type !== 'disconnected') {
        continue;
    }
    
    $students[] = [
        'ma_nguoi_dung' => $row['ma_nguoi_dung'],
        'ma_bai_lam' => $row['ma_bai_lam'],
        'name' => htmlspecialchars($row['ho_ten']),
        'mssv' => htmlspecialchars($row['ten_dang_nhap']),
        'thoi_diem_bat_dau' => $row['thoi_diem_bat_dau'],
        'avatar' => $avatar,
        'avatar_bg' => $avatar_bg,
        'progress' => $progress,
        'percent' => $percent,
        'status_type' => $status_type,
        'status_msg' => $status_msg,
        'online' => $is_online
    ];
}

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 relative transition-colors duration-200">
    
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Giám sát trực tuyến</span>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-900/50 px-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-2 text-orange-600 dark:text-orange-500 font-bold text-sm">
                    <span class="material-icons text-[20px]">timer</span>
                    <div class="flex flex-col">
                        <span class="text-[9px] uppercase tracking-wider text-orange-500/80 dark:text-orange-500/80 leading-none">Thời gian còn lại</span>
                        <span>01 : 42 : 15</span>
                    </div>
                </div>
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-green-600 dark:text-green-500">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> <?php echo $totalExamining; ?> Đang thi
                </div>
                <div class="w-px h-6 bg-slate-200 dark:bg-slate-700"></div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-red-500 dark:text-red-400">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> <?php echo $totalViolations; ?> Vi phạm
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative">
                    <button id="notifButton" type="button" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition relative focus:outline-none">
                        <span class="material-icons">notifications</span>
                        <span class="absolute top-0 right-0 w-2 h-2 rounded-full bg-red-500 border-2 border-white dark:border-slate-800"></span>
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo hệ thống</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                                <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span class="font-semibold text-slate-800 dark:text-white">Trần Thị B</span> vừa rời khỏi trình duyệt (Lần 2).</p>
                                <span class="text-[11px] text-red-500 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">warning</span> Vừa xong</span>
                            </a>
                        </div>
                    </div>
                </div>
                <button id="darkModeToggle" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 p-4 shrink-0 flex justify-between items-center z-10 transition-colors">
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" placeholder="Tìm tên thí sinh, MSSV..." class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] w-64 transition">
            </div>
            <select class="px-4 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-600 dark:text-slate-300 focus:outline-none focus:border-[#254ada] transition">
                <option>Tất cả trạng thái</option>
                <option>Có vi phạm</option>
                <option>Mất kết nối</option>
            </select>
        </div>
        
        <div class="flex items-center bg-slate-100 dark:bg-slate-900 p-1 rounded-lg">
            <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white dark:bg-slate-700 shadow-sm rounded text-sm font-semibold text-slate-700 dark:text-white transition">
                <span class="material-icons text-[18px]">grid_view</span> Lưới
            </button>
            <button onclick="showToast('info', 'Chưa khả dụng', 'Tính năng xem dạng danh sách đang được cập nhật.')" class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white transition">
                <span class="material-icons text-[18px]">format_list_bulleted</span> Danh sách
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-5 pb-10">
            
            <?php foreach($students as $st): ?>
            <?php
                // Xác định class style dựa trên trạng thái (Tích hợp Dark Mode)
                $cardClass = "bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-500";
                $btnWarn = "border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700";
                $btnBan = "border-slate-200 dark:border-slate-600 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30";
                $statusIcon = "";
                
                if($st['status_type'] == 'danger') {
                    $cardClass = "bg-white dark:bg-slate-800 border-red-500 dark:border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.15)] relative scale-[1.02] z-10";
                    $btnWarn = "bg-orange-500 border-orange-500 text-white hover:bg-orange-600";
                    $btnBan = "bg-red-600 border-red-600 text-white hover:bg-red-700";
                    $statusIcon = "<span class='absolute top-3 right-3 w-3 h-3 bg-red-500 rounded-full animate-ping'></span><span class='absolute top-3 right-3 w-3 h-3 bg-red-500 rounded-full'></span>";
                } elseif ($st['status_type'] == 'warning') {
                    $cardClass = "bg-orange-50/30 dark:bg-orange-900/10 border-orange-200 dark:border-orange-500/30";
                } elseif ($st['status_type'] == 'disconnected') {
                    $cardClass = "bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 opacity-70 grayscale-[0.5]";
                    $btnWarn = "border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-500 cursor-not-allowed";
                    $btnBan = "border-slate-200 dark:border-slate-700 text-slate-300 dark:text-slate-500 cursor-not-allowed";
                }
            ?>

            <div class="rounded-xl border <?php echo $cardClass; ?> p-4 flex flex-col transition-all duration-200">
                <?php echo $statusIcon; ?>
                
                <div class="flex gap-3 items-center mb-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-lg <?php echo $st['avatar_bg']; ?> flex items-center justify-center font-bold text-lg shrink-0 border border-slate-100 dark:border-transparent shadow-sm">
                            <?php echo $st['avatar']; ?>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-800 <?php echo $st['online'] ? 'bg-green-500' : 'bg-slate-400 dark:bg-slate-500'; ?>"></span>
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-800 dark:text-white text-[14px] truncate" title="<?php echo $st['name']; ?>"><?php echo $st['name']; ?></h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">MSSV:<br><?php echo $st['mssv']; ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between text-[11px] font-bold mb-1">
                        <span class="text-[#254ada] dark:text-[#4b6bfb]">Tiến độ: <br><span class="text-[14px]"><?php echo $st['progress']; ?></span></span>
                        <span class="text-slate-400 dark:text-slate-500 self-end"><?php echo $st['percent']; ?>%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                        <div class="bg-[#254ada] dark:bg-[#4b6bfb] h-1.5 rounded-full transition-all duration-500" style="width: <?php echo $st['percent']; ?>%"></div>
                    </div>
                </div>

                <div class="flex-1 flex items-center justify-center min-h-[40px] mb-4 text-center">
                    <?php if($st['status_type'] == 'danger'): ?>
                        <div class="text-[12px] font-bold text-red-600 dark:text-red-500 flex items-center gap-1 flex-col">
                            <div class="flex items-center gap-1"><span class="material-icons text-[16px]">warning</span> <?php echo $st['status_msg']; ?></div>
                        </div>
                    <?php elseif($st['status_type'] == 'warning'): ?>
                        <div class="text-[12px] font-bold text-orange-600 dark:text-orange-500 flex items-center gap-1 flex-col">
                            <div class="flex items-center gap-1"><span class="material-icons text-[16px]">info</span> <?php echo $st['status_msg']; ?></div>
                        </div>
                    <?php elseif($st['status_type'] == 'disconnected'): ?>
                        <div class="text-[12px] font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <span class="material-icons text-[16px]">wifi_off</span> <?php echo $st['status_msg']; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-[11px] font-medium text-slate-400 dark:text-slate-500 italic">
                            <?php echo $st['status_msg']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex gap-2 mt-auto">
                    <button <?php echo ($st['status_type'] == 'disconnected') ? 'disabled' : ''; ?> onclick="showToast('warning', 'Đã cảnh báo', 'Đã gửi thông báo nhắc nhở đến thí sinh <?php echo $st['name']; ?>')" class="flex-1 py-1.5 border rounded uppercase text-[10px] font-bold transition <?php echo $btnWarn; ?>">Cảnh báo</button>
                    
                    <button <?php echo ($st['status_type'] == 'disconnected') ? 'disabled' : ''; ?> onclick="confirmBan('<?php echo $st['name']; ?>')" class="flex-1 py-1.5 border rounded uppercase text-[10px] font-bold transition <?php echo $btnBan; ?>">Đình chỉ</button>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 p-4 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-500 transition min-h-[220px]">
                <div class="w-12 h-12 bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mb-3">
                    <span class="material-icons">people</span>
                </div>
                <span class="text-[13px] font-medium text-slate-500 dark:text-slate-400">+118 thí sinh khác</span>
            </div>

        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 py-2 flex justify-between items-center text-[11px] font-medium text-slate-500 dark:text-slate-400 shrink-0 transition-colors">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Hệ thống: Ổn định</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#254ada] dark:bg-[#4b6bfb]"></span> Server Latency: 45ms</span>
        </div>
        <div id="lastUpdateTime">
            Cập nhật lúc: <?php echo date('H:i:s'); ?> (Tự động sau <span id="countdownSec">5</span>s)
        </div>
    </div>
</main>

<div id="banModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col text-center">
        <div class="p-6">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-icons text-3xl">gavel</span>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Đình chỉ thi</h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">Bạn có chắc chắn muốn lập biên bản và <b class="text-red-500">đình chỉ thi</b> đối với thí sinh <span id="banStudentName" class="font-bold text-slate-800 dark:text-white"></span> không? Thao tác này không thể hoàn tác.</p>
            
            <textarea rows="2" placeholder="Nhập lý do đình chỉ (Bắt buộc)..." class="w-full border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-[#254ada] focus:outline-none mb-2 resize-none"></textarea>
        </div>
        <div class="flex border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <button type="button" onclick="closeModal('banModal')" class="flex-1 py-3 text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition">Hủy bỏ</button>
            <button type="button" onclick="executeBan()" class="flex-1 py-3 bg-red-600 text-white font-bold hover:bg-red-700 transition shadow-inner">Xác nhận Đình chỉ</button>
        </div>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
/* =================================================================
   1. CÁC HÀM GLOBAL (MODAL & TOAST)
   ================================================================= */
function openModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.remove('hidden'); 
}

function closeModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.add('hidden'); 
}

function showToast(type, title, message) {
    const container = document.getElementById('toastContainer');
    const template = document.getElementById('toastTemplate');
    if(!container || !template) return;
    
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
    setTimeout(() => { if(container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
}

/* =================================================================
   2. XỬ LÝ ĐÌNH CHỈ THÍ SINH
   ================================================================= */
function confirmBan(studentName) {
    document.getElementById('banStudentName').textContent = studentName;
    openModal('banModal');
}

function executeBan() {
    closeModal('banModal');
    const name = document.getElementById('banStudentName').textContent;
    showToast('error', 'Đã lập biên bản', `Thí sinh ${name} đã bị đình chỉ thi và ngắt kết nối khỏi hệ thống.`);
}

/* =================================================================
   3. SỰ KIỆN KHỞI TẠO & ĐẾM NGƯỢC (DOM Content Loaded)
   ================================================================= */
document.addEventListener('DOMContentLoaded', function() {
    
    // Chức năng Dark Mode
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark');
        if(darkModeIcon) darkModeIcon.textContent = 'light_mode';
    }

    darkModeToggle?.addEventListener('click', () => {
        htmlElement.classList.toggle('dark');
        const isDark = htmlElement.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        if(darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
    });

    // Chức năng Dropdown Thông báo
    const notifButton = document.getElementById('notifButton');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifButton && notifDropdown) {
        notifButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }

    // Giả lập Đếm ngược thanh trạng thái "Cập nhật lúc..."
    let secs = 5;
    const countSpan = document.getElementById('countdownSec');
    setInterval(() => {
        secs--;
        if(secs < 0) {
            secs = 5;
            // Cập nhật lại giờ hiện tại
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                               now.getMinutes().toString().padStart(2, '0') + ':' + 
                               now.getSeconds().toString().padStart(2, '0');
            document.getElementById('lastUpdateTime').innerHTML = `Cập nhật lúc: ${timeString} (Tự động sau <span id="countdownSec">5</span>s)`;
        } else {
            if(document.getElementById('countdownSec')) {
                document.getElementById('countdownSec').textContent = secs;
            }
        }
    }, 1000);
});
</script>