<?php
// 1. Cấu hình thông tin trang
$title = "Danh sách thí sinh - Hệ Thống Thi Trực Tuyến";
$active_menu = "list_candidates"; 

// 2. Kết nối Database
require_once __DIR__ . '/../../app/config/Database.php';

// 3. Xử lý Backend CRUD
$message = '';
$messageType = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $conn = Database::getConnection();
        
        // THÊM THÍ SINH
        if($action === 'add_student') {
            $ho_ten = trim($_POST['ho_ten'] ?? '');
            $ten_dang_nhap = trim($_POST['ten_dang_nhap'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $lop = trim($_POST['lop'] ?? '');
            $gui_tai_khoan = isset($_POST['gui_tai_khoan']);
            
            if(empty($ho_ten) || empty($ten_dang_nhap) || empty($email)) {
                $message = 'Họ tên, mã số và email là bắt buộc';
                $messageType = 'error';
            } else {
                // Kiểm tra email đã tồn tại
                $checkStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ?");
                $checkStmt->execute([$email]);
                if($checkStmt->fetch()) {
                    $message = 'Email đã tồn tại trong hệ thống';
                    $messageType = 'error';
                } else {
                    // Kiểm tra ten_dang_nhap đã tồn tại
                    $checkUserStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE ten_dang_nhap = ?");
                    $checkUserStmt->execute([$ten_dang_nhap]);
                    if($checkUserStmt->fetch()) {
                        $message = 'Mã số thí sinh đã tồn tại trong hệ thống';
                        $messageType = 'error';
                    } else {
                        // Lấy ma_vai_tro của sinh viên
                        $roleStmt = $conn->query("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = 'Thí sinh' OR ten_vai_tro = 'sinh_vien' LIMIT 1");
                        $role = $roleStmt->fetch(PDO::FETCH_ASSOC);
                        $ma_vai_tro = $role ? $role['ma_vai_tro'] : 3;
                        
                        // Tạo mật khẩu ngẫu nhiên
                        $mat_khau = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
                        $mat_khau_hash = password_hash($mat_khau, PASSWORD_BCRYPT);
                        
                        // Insert thí sinh
                        $insertStmt = $conn->prepare("
                            INSERT INTO nguoi_dung (ma_nguoi_dung, ho_ten, ten_dang_nhap, email, mat_khau, ma_vai_tro, trang_thai)
                            VALUES (?, ?, ?, ?, ?, ?, 1)
                        ");
                        $insertStmt->execute([
                            $ten_dang_nhap,
                            $ho_ten,
                            $ten_dang_nhap,
                            $email,
                            $mat_khau_hash,
                            $ma_vai_tro
                        ]);
                        
                        $message = 'Thêm thí sinh thành công!';
                        $messageType = 'success';
                    }
                }
            }
        }
        
        // SỬA THÍ SINH
        if($action === 'edit_student') {
            $ma_nguoi_dung = $_POST['ma_nguoi_dung'] ?? '';
            $ho_ten = trim($_POST['ho_ten'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $trang_thai = $_POST['trang_thai'] ?? 'hoat_dong';
            
            if(empty($ma_nguoi_dung) || empty($ho_ten) || empty($email)) {
                $message = 'Họ tên và email là bắt buộc';
                $messageType = 'error';
            } else {
                // Kiểm tra email đã tồn tại (trừ chính mình)
                $checkStmt = $conn->prepare("SELECT ma_nguoi_dung FROM nguoi_dung WHERE email = ? AND ma_nguoi_dung != ?");
                $checkStmt->execute([$email, $ma_nguoi_dung]);
                if($checkStmt->fetch()) {
                    $message = 'Email đã tồn tại trong hệ thống';
                    $messageType = 'error';
                } else {
                    // Map trạng thái
                    $trang_thai_map = [
                        'hoat_dong' => 1,
                        'dang_thi' => 1, // Giữ nguyên hoạt động
                        'bi_khoa' => 0
                    ];
                    $trang_thai_value = isset($trang_thai_map[$trang_thai]) ? $trang_thai_map[$trang_thai] : 1;
                    
                    $updateStmt = $conn->prepare("
                        UPDATE nguoi_dung 
                        SET ho_ten = ?, email = ?, trang_thai = ?
                        WHERE ma_nguoi_dung = ?
                    ");
                    $updateStmt->execute([$ho_ten, $email, $trang_thai_value, $ma_nguoi_dung]);
                    
                    $message = 'Cập nhật thông tin thí sinh thành công!';
                    $messageType = 'success';
                }
            }
        }
        
        // XÓA THÍ SINH
        if($action === 'delete_student') {
            $ma_nguoi_dung = $_POST['ma_nguoi_dung'] ?? '';
            
            if(!empty($ma_nguoi_dung)) {
                $deleteStmt = $conn->prepare("DELETE FROM nguoi_dung WHERE ma_nguoi_dung = ?");
                $deleteStmt->execute([$ma_nguoi_dung]);
                
                $message = 'Xóa thí sinh thành công!';
                $messageType = 'success';
            }
        }
        
        // Redirect để tránh form resubmission
        if(!empty($message)) {
            $redirectUrl = $_SERVER['PHP_SELF'] . '?msg=' . urlencode($message) . '&type=' . $messageType;
            header('Location: ' . $redirectUrl);
            exit;
        }
        
    } catch (Exception $e) {
        $message = 'Lỗi: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Lấy message từ URL nếu có redirect
$pageMessage = isset($_GET['msg']) ? $_GET['msg'] : '';
$pageMessageType = isset($_GET['type']) ? $_GET['type'] : 'info';

// 4. LẤY DANH SÁCH THÍ SINH TỪ DATABASE
$candidates = [];
$conn = Database::getConnection();

$sql = "
SELECT 
    nd.ma_nguoi_dung,
    nd.ho_ten,
    nd.ten_dang_nhap,
    nd.email,
    nd.trang_thai,
    nd.ngay_tao
FROM nguoi_dung nd
JOIN vai_tro vt 
    ON nd.ma_vai_tro = vt.ma_vai_tro
WHERE vt.ten_vai_tro = 'thi_sinh'
ORDER BY nd.ma_nguoi_dung DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map dữ liệu database sang format giao diện
    $avatarColors = [
        ['bg' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400', 'letter' => 'B'],
        ['bg' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400', 'letter' => 'C'],
        ['bg' => 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400', 'letter' => 'D'],
        ['bg' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400', 'letter' => 'E'],
        ['bg' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400', 'letter' => 'F'],
        ['bg' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400', 'letter' => 'G'],
        ['bg' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400', 'letter' => 'H'],
        ['bg' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300', 'letter' => 'K']
    ];
    
    $colorIndex = 0;
    foreach($students as $st) {
        // Tạo initial từ họ tên
        $words = explode(' ', trim($st['ho_ten']));
        $initial = '';
        if(count($words) >= 2) {
            $initial = strtoupper(substr($words[0], 0, 1) . end($words));
        } else {
            $initial = strtoupper(substr($st['ho_ten'], 0, 2));
        }
        
        // Tạo class từ ten_dang_nhap hoặc mặc định
        $class = 'Chưa phân lớp';
        if(!empty($st['ten_dang_nhap'])) {
            // Nếu có định dạng SV..., lấy làm class
            if(stripos($st['ten_dang_nhap'], 'SV') === 0) {
                $class = 'SV-' . strtoupper($st['ten_dang_nhap']);
            }
        }
        
        // Map trạng thái
        $status = 'Đang hoạt động';
        $status_type = 'active';
        
        if($st['trang_thai'] == 0) {
            $status = 'Đang khóa';
            $status_type = 'locked';
        }
        
        // Chọn màu avatar theo ký tự đầu
        $firstLetter = strtoupper(substr($st['ho_ten'], 0, 1));
        $charOrd = ord($firstLetter);
        if($charOrd >= 65 && $charOrd <= 90) {
            $colorIndex = ($charOrd - 65) % count($avatarColors);
        }
        
        $candidates[] = [
            'id' => $st['ma_nguoi_dung'],
            'name' => $st['ho_ten'],
            'initial' => $initial,
            'avatar_bg' => $avatarColors[$colorIndex]['bg'],
            'class' => $class,
            'email' => $st['email'],
            'status' => $status,
            'status_type' => $status_type
        ];
        
        $colorIndex = ($colorIndex + 1) % count($avatarColors);
    }
    
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Thí sinh & Làm bài <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Danh sách thí sinh</span>
        </div>
        
        <div class="flex items-center gap-5">
            <div class="relative hidden md:block">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..." class="pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
            </div>

            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="notifButton" type="button" class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
                        <span class="material-icons">notifications</span>
                        <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                    </button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                            <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                            <a href="#" class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu đã đọc</a>
                        </div>
                        <div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">Không có thông báo mới.</div>
                    </div>
                </div>
                <button id="darkModeToggle" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
                    <span class="material-icons" id="darkModeIcon">dark_mode</span>
                </button>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">
        
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative min-w-[180px]">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_alt</span>
                    <select id="classFilter" class="pl-9 pr-4 py-2.5 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none transition appearance-none cursor-pointer">
                        <option value="all">Tất cả Lớp/Đơn vị</option>
                        <option value="CNTT K20A">CNTT K20A</option>
                        <option value="CNTT K20B">CNTT K20B</option>
                        <option value="Kinh tế K21">Kinh tế K21</option>
                        <option value="Ngôn ngữ Anh K19">Ngôn ngữ Anh K19</option>
                    </select>
                </div>
                <div class="relative w-full md:w-[350px]">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm theo mã, tên hoặc email..." class="pl-10 pr-4 py-2.5 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition shadow-sm">
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <button onclick="showToast('info', 'Import', 'Mở bảng nhập dữ liệu từ Excel')" class="flex-1 md:flex-none px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">upload_file</span> Nhập từ Excel
                </button>
                <button onclick="openModal('addStudentModal')" class="flex-1 md:flex-none px-5 py-2.5 bg-[#1e3bb3] dark:bg-[#254ada] text-white rounded-lg flex items-center justify-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">person_add</span> Thêm thí sinh
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50 dark:bg-slate-900/30 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5 w-12 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-[#254ada] focus:ring-[#254ada]">
                            </th>
                            <th class="px-6 py-5">Mã thí sinh</th>
                            <th class="px-6 py-5">Họ tên</th>
                            <th class="px-6 py-5">Lớp / Đơn vị</th>
                            <th class="px-6 py-5">Email</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                        <?php foreach($candidates as $st): ?>
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition group candidate-row">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="candidate-checkbox w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-[#254ada] focus:ring-[#254ada]">
                            </td>
                            <td class="px-6 py-4 font-bold text-[#1e3bb3] dark:text-blue-400 text-sm c-id">
                                <span class="cursor-pointer hover:underline"><?php echo $st['id']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full <?php echo $st['avatar_bg']; ?> flex items-center justify-center font-bold text-[11px] shrink-0 border border-white dark:border-slate-700">
                                        <?php echo $st['initial']; ?>
                                    </div>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200 text-sm c-name"><?php echo $st['name']; ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm c-class"><?php echo $st['class']; ?></td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm c-email"><?php echo $st['email']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php 
                                    $badgeClass = "";
                                    if($st['status_type'] == 'active') $badgeClass = "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400";
                                    if($st['status_type'] == 'testing') $badgeClass = "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400";
                                    if($st['status_type'] == 'locked') $badgeClass = "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400";
                                ?>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold <?php echo $badgeClass; ?> whitespace-nowrap">
                                    <?php echo $st['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="showToast('info', 'Sửa', 'Chỉnh sửa thông tin thí sinh')" class="p-1.5 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Sửa"><span class="material-icons text-[20px]">edit</span></button>
                                    <button onclick="showToast('error', 'Đã xóa', 'Đã xóa thí sinh khỏi hệ thống')" class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition" title="Xóa"><span class="material-icons text-[20px]">delete_outline</span></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-5 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4 transition-colors">
                <p id="paginationInfo" class="text-sm text-slate-500 dark:text-slate-400"></p>
                <div id="paginationControls" class="flex items-center gap-1.5">
                    </div>
            </div>
        </div>
    </div>
</main>

<div id="addStudentModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-[550px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col">
        
        <div class="flex justify-between items-center p-6 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-800/50">
                    <span class="material-icons text-[26px]">person_add_alt</span>
                </div>
                <div>
                    <h3 class="font-bold text-xl text-slate-800 dark:text-white leading-tight">Thêm thí sinh mới</h3>
                    <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5">Nhập thông tin cá nhân của thí sinh vào hệ thống</p>
                </div>
            </div>
            <button onclick="closeModal('addStudentModal')" class="text-slate-400 hover:text-red-500 transition focus:outline-none p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-full">
                <span class="material-icons">close</span>
            </button>
        </div>

<form id="formAddStudent" action="" method="POST" class="p-8 space-y-5 overflow-y-auto custom-scrollbar flex-1">
            <input type="hidden" name="action" value="add_student">
            <div>
                <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" placeholder="VD: Nguyễn Văn A" required class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-[#254ada] focus:ring-4 focus:ring-blue-500/5 transition text-slate-800 dark:text-white placeholder-slate-400">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1">Mã số thí sinh <span class="text-red-500">*</span></label>
                    <input type="text" name="ten_dang_nhap" placeholder="VD: SV123456" required class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-[#254ada] focus:ring-4 focus:ring-blue-500/5 transition text-slate-800 dark:text-white placeholder-slate-400">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2">Ngày sinh</label>
                    <div class="relative">
                        <input type="date" class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-[#254ada] transition text-slate-800 dark:text-white appearance-none">
                        <span class="material-icons absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">calendar_today</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1">Lớp / Đơn vị <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="lop" required class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-[#254ada] transition text-slate-800 dark:text-white appearance-none cursor-pointer">
                        <option value="" disabled selected>Chọn lớp / phòng ban</option>
                        <option value="cntt-k20a">CNTT K20A</option>
                        <option value="cntt-k20b">CNTT K20B</option>
                        <option value="kt-k21">Kinh tế K21</option>
                        <option value="anh-k19">Ngôn ngữ Anh K19</option>
                    </select>
                    <span class="material-icons absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
            </div>

            <div>
                <label class="block text-[13px] font-bold text-slate-700 dark:text-slate-300 mb-2 flex items-center gap-1">Email liên hệ <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <span class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#254ada] transition-colors text-[18px]">email</span>
                    <input type="email" name="email" placeholder="example@domain.com" required class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:border-[#254ada] transition text-slate-800 dark:text-white placeholder-slate-400">
                </div>
            </div>

            <div class="p-4 bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 rounded-2xl flex items-center justify-between transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center text-[#254ada] dark:text-[#4b6bfb]">
                        <span class="material-icons">send</span>
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-slate-800 dark:text-white">Gửi thông tin đăng nhập</p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Tự động gửi tài khoản & mật khẩu qua email</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#254ada]"></div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" onclick="closeModal('addStudentModal')" class="px-6 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition">Hủy bỏ</button>
                <button type="submit" class="px-8 py-3 bg-[#254ada] hover:bg-[#1e3bb3] text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/25 flex items-center gap-2 transition transform active:scale-95">
                    <span class="material-icons text-[20px]">save</span> Lưu thông tin
                </button>
            </div>
        </form>
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
// Hàm xử lý Modal & Toast
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

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

document.addEventListener('DOMContentLoaded', function() {
    
    // 0. Xử lý hiển thị message từ URL (sau khi redirect)
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    const msgType = urlParams.get('type');
    if (msg) {
        const title = msgType === 'success' ? 'Thành công' : (msgType === 'error' ? 'Lỗi' : 'Thông báo');
        showToast(msgType || 'info', title, msg);
        // Xóa query string sau khi hiển thị
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // 1. Dark Mode Toggle
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

    // 2. Dropdown Thông báo
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

    // 3. Logic Checkbox Chọn tất cả
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.candidate-checkbox');

    if(selectAll) {
        selectAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => {
                if(cb.closest('tr').style.display !== 'none') {
                    cb.checked = e.target.checked;
                }
            });
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const visibleCheckboxes = Array.from(checkboxes).filter(c => c.closest('tr').style.display !== 'none');
                const allChecked = visibleCheckboxes.every(c => c.checked);
                const someChecked = visibleCheckboxes.some(c => c.checked);
                
                selectAll.checked = allChecked && visibleCheckboxes.length > 0;
                selectAll.indeterminate = someChecked && !allChecked;
            });
        });
    }

    // 4. Logic Phân trang thông minh (Smart Pagination) & Tìm kiếm
    const rowsPerPage = 4; // Hiển thị 4 người mỗi trang để dễ test phân trang
    let currentPage = 1;
    let filteredRows = []; 
    
    const allRows = Array.from(document.querySelectorAll('.candidate-row'));
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const searchInput = document.getElementById('searchInput');
    const classFilter = document.getElementById('classFilter');

    // Hàm cập nhật hiển thị bảng và nút phân trang
    function updatePagination() {
        const totalRows = filteredRows.length;
        
        // Mô phỏng nếu hệ thống có rất nhiều dữ liệu (Ví dụ 12,450 sinh viên)
        // Để demo logic dấu ..., mình sẽ gán cứng totalPages giả lập.
        // Tuy nhiên ở đây code đang dùng mảng thật để hiển thị nên mình sẽ set totalPages theo mảng thật,
        // NHƯNG nếu bạn muốn xem dấu ... hoạt động, mình sẽ giả lập số trang lớn.
        
        // Nếu muốn demo thật, gán totalPages = 458;
        // Ở đây mình dùng số lượng thật từ mảng PHP:
        let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
        
        // --- ĐỂ DEMO GIAO DIỆN GIỐNG HÌNH (Dấu ... 458) ---
        const isDemoMode = true; 
        let fakeTotalPages = 458;
        let fakeTotalRows = 12450;
        
        if(isDemoMode && totalRows > 0) {
             totalPages = fakeTotalPages;
        }
        // --------------------------------------------------

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Ẩn tất cả và chỉ hiện những row thuộc trang hiện tại (nếu ở mode demo thì trang > 2 sẽ ko có data)
        allRows.forEach(row => row.style.display = 'none'); 
        if (currentPage === 1 || !isDemoMode) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
        }

        // Cập nhật text hiển thị
        let displayStart = totalRows === 0 ? 0 : start + 1;
        let displayEnd = Math.min(end, isDemoMode ? fakeTotalRows : totalRows);
        let displayTotal = isDemoMode ? fakeTotalRows : totalRows;
        
        if(paginationInfo) {
            paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}-${displayEnd}</span> trong số <span class="font-medium text-slate-800 dark:text-white">${displayTotal}</span> thí sinh`;
        }

        // Render các nút phân trang thông minh
        if(paginationControls) {
            paginationControls.innerHTML = ''; 
            
            // Nút Prev
            const prevBtn = document.createElement('button');
            prevBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border transition ${currentPage === 1 ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
            prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { if(currentPage > 1) { currentPage--; updatePagination(); } };
            paginationControls.appendChild(prevBtn);

            // Hàm tạo nút số
            const createPageBtn = (i) => {
                const pageBtn = document.createElement('button');
                if (i === currentPage) {
                    pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md bg-[#254ada] text-white font-bold text-xs shadow-sm transition transform scale-105';
                } else {
                    pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium text-xs';
                }
                pageBtn.innerText = i;
                pageBtn.onclick = () => { currentPage = i; updatePagination(); };
                return pageBtn;
            };

            // Hàm tạo dấu ...
            const createDots = () => {
                const dots = document.createElement('span');
                dots.className = 'px-1 text-slate-400 text-xs tracking-widest';
                dots.innerText = '...';
                return dots;
            }

            // Logic in số trang
            if (totalPages <= 5) {
                // In tất cả nếu ít trang
                for (let i = 1; i <= totalPages; i++) {
                    paginationControls.appendChild(createPageBtn(i));
                }
            } else {
                // Luôn in trang 1
                paginationControls.appendChild(createPageBtn(1));
                
                if (currentPage > 3) {
                    paginationControls.appendChild(createDots());
                }
                
                // Các trang ở giữa
                let startPage = Math.max(2, currentPage - 1);
                let endPage = Math.min(totalPages - 1, currentPage + 1);
                
                if (currentPage === 1) endPage = 3;
                if (currentPage === totalPages) startPage = totalPages - 2;
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationControls.appendChild(createPageBtn(i));
                }
                
                if (currentPage < totalPages - 2) {
                    paginationControls.appendChild(createDots());
                }
                
                // Luôn in trang cuối cùng
                paginationControls.appendChild(createPageBtn(totalPages));
            }

            // Nút Next
            const nextBtn = document.createElement('button');
            nextBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border transition ${currentPage === totalPages ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
            nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { if(currentPage < totalPages) { currentPage++; updatePagination(); } };
            paginationControls.appendChild(nextBtn);
        }

        // Reset nút Select All
        if(selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
        checkboxes.forEach(cb => cb.checked = false);
    }

    function applyFilters() {
        const text = searchInput.value.toLowerCase();
        const selectedClass = classFilter.value;

        filteredRows = allRows.filter(row => {
            const name = row.querySelector('.c-name').textContent.toLowerCase();
            const id = row.querySelector('.c-id').textContent.toLowerCase();
            const email = row.querySelector('.c-email').textContent.toLowerCase();
            const cClass = row.querySelector('.c-class').textContent;

            const matchSearch = name.includes(text) || id.includes(text) || email.includes(text);
            const matchClass = (selectedClass === 'all' || cClass === selectedClass);

            return matchSearch && matchClass;
        });

        currentPage = 1; 
        updatePagination();
    }

    // Gắn sự kiện Lọc & Tìm kiếm
    searchInput?.addEventListener('input', applyFilters);
    classFilter?.addEventListener('change', applyFilters);

    // Khởi chạy phân trang lần đầu
    filteredRows = [...allRows];
    updatePagination();
});
</script>