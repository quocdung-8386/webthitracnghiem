<?php
// 1. Cấu hình thông tin trang
$title = "Quản Lý Người Dùng - Hệ Thống Thi Trực Tuyến";
$active_menu = "users";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

$sql = "
SELECT 
    nd.ma_nguoi_dung as id,
    nd.ho_ten as name,
    nd.email,
    nd.trang_thai as status,
    vt.ten_vai_tro as role
FROM nguoi_dung nd
JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
ORDER BY nd.ma_nguoi_dung DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tạo mảng users đã được format thêm các trường cần thiết cho giao diện
$users = [];
$colors = ['bg-blue-100 text-blue-600', 'bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-purple-100 text-purple-600'];

foreach ($usersData as $index => $u) {
    $initial = strtoupper(substr($u['name'], 0, 1));
    $colorClass = $colors[$index % count($colors)];

    // Xử lý Role style
    $role_bg = 'bg-blue-50 text-blue-600';
    if ($u['role'] == 'Quản trị viên')
        $role_bg = 'bg-purple-50 text-purple-600';
    if ($u['role'] == 'Giảng viên')
        $role_bg = 'bg-emerald-50 text-emerald-600';

    // Xử lý Status style
    $status = ($u['status'] == 1) ? 'Đang hoạt động' : 'Bị khóa';
    $status_bg = ($status == 'Đang hoạt động') ? 'bg-green-50' : 'bg-red-50';
    $status_text = ($status == 'Đang hoạt động') ? 'text-green-600' : 'text-red-500';
    $dot = ($status == 'Đang hoạt động') ? 'bg-green-500' : 'bg-red-500';

    $users[] = [
        'id' => $u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $u['role'],
        'status' => $status,
        'initial' => $initial,
        'avatar_bg' => explode(' ', $colorClass)[0],
        'avatar_text' => explode(' ', $colorClass)[1],
        'role_bg' => explode(' ', $role_bg)[0],
        'role_text' => explode(' ', $role_bg)[1],
        'status_bg' => $status_bg,
        'status_text' => $status_text,
        'dot' => $dot
    ];
}

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản
                lý người dùng</span>
        </div>
        <div class="relative flex items-center gap-4">
            <button id="notifButton" type="button"
                class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons">notifications</span>
                <span
                    class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
            </button>

            <div id="notifDropdown"
                class="hidden absolute right-10 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                <div
                    class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                    <a href="#" class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh
                        dấu đã đọc</a>
                </div>

                <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                    <a href="#"
                        class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa hoàn tất sao
                            lưu dữ liệu tự động.</p>
                        <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                    </a>
                </div>

                <a href="#"
                    class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">
                    Xem tất cả thông báo
                </a>
            </div>
            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div
        class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Danh sách người dùng</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý tài khoản, vai trò và trạng thái hoạt
                    động của thành viên trong hệ thống.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="openModal('importUserModal')"
                    class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-300 shadow-sm transition">
                    <span class="material-icons text-[20px] text-slate-600 dark:text-slate-400">save_alt</span> Import
                    từ file
                </button>
                <button onclick="openModal('addUserModal')"
                    class="px-5 py-2.5 bg-[#1e3bb3] dark:bg-[#254ada] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 dark:hover:bg-[#1e3bb3] text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">person_add</span> Thêm người dùng mới
                </button>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm transition-colors">

            <div
                class="p-4 flex flex-wrap md:flex-nowrap gap-4 items-center border-b border-slate-100 dark:border-slate-700">
                <div class="relative flex-1 min-w-[250px]">
                    <span
                        class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, email hoặc mã người dùng..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] dark:text-white transition">
                </div>
                <select
                    class="px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 dark:text-white min-w-[150px] focus:outline-none focus:border-[#254ada] text-slate-600 transition">
                    <option>Tất cả vai trò</option>
                    <option>Quản trị viên</option>
                    <option>Giảng viên</option>
                    <option>Thí sinh</option>
                </select>
                <select
                    class="px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 dark:text-white min-w-[150px] focus:outline-none focus:border-[#254ada] text-slate-600 transition">
                    <option>Trạng thái</option>
                    <option>Đang hoạt động</option>
                    <option>Bị khóa</option>
                </select>
                <button
                    class="w-[42px] h-[42px] flex items-center justify-center border border-slate-200 dark:border-slate-600 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <span class="material-icons text-[20px]">filter_list</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left" id="usersTable">
                    <thead
                        class="bg-slate-50 dark:bg-slate-900/50 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4 w-14 text-center">
                                <input type="checkbox" id="selectAllBtn"
                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                            </th>
                            <th class="px-6 py-4">Họ và tên</th>
                            <th class="px-6 py-4">Mã người dùng</th>
                            <th class="px-6 py-4">Vai trò</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group user-row">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full <?php echo $user['avatar_bg']; ?> <?php echo $user['avatar_text']; ?> dark:bg-opacity-20 flex items-center justify-center font-bold text-sm shrink-0">
                                        <?php echo $user['initial']; ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 dark:text-white user-name">
                                            <?php echo $user['name']; ?>
                                        </p>
                                        <p class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5 user-email">
                                            <?php echo $user['email']; ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-[13px] user-id">
                                    <?php echo $user['id']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 <?php echo $user['role_bg']; ?> <?php echo $user['role_text']; ?> dark:bg-opacity-20 rounded-md text-[11px] font-semibold text-center inline-block min-w-[80px] leading-tight">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 <?php echo $user['status_bg']; ?> <?php echo $user['status_text']; ?> dark:bg-opacity-20 rounded-full text-[11px] font-semibold">
                                        <div class="w-1.5 h-1.5 rounded-full <?php echo $user['dot']; ?>"></div>
                                        <?php echo $user['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                                    <button
                                        onclick="editUser('<?php echo $user['id']; ?>', '<?php echo addslashes($user['name']); ?>', '<?php echo $user['email']; ?>', '<?php echo addslashes($user['role']); ?>')"
                                        class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700">
                                        <span class="material-icons text-[18px]">edit</span>
                                    </button>
                                    <button
                                        onclick="toggleUserStatus('<?php echo $user['id']; ?>', '<?php echo addslashes($user['name']); ?>', '<?php echo ($user['status'] == 'Bị khóa') ? '1' : '0'; ?>')"
                                        class="hover:text-emerald-600 dark:hover:text-emerald-400 p-1.5 transition rounded-md hover:bg-emerald-50 dark:hover:bg-slate-700 <?php echo ($user['status'] == 'Bị khóa') ? 'text-emerald-500' : ''; ?>">
                                        <span
                                            class="material-icons text-[18px]"><?php echo ($user['status'] == 'Bị khóa') ? 'lock' : 'lock_open'; ?></span>
                                    </button>
                                    <button
                                        onclick="deleteUser('<?php echo $user['id']; ?>', '<?php echo addslashes($user['name']); ?>')"
                                        class="hover:text-red-600 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700">
                                        <span class="material-icons text-[18px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị <span class="font-medium text-slate-800 dark:text-white">0</span> -
                    <span class="font-medium text-slate-800 dark:text-white">0</span> của <span
                        class="font-medium text-slate-800 dark:text-white">0</span> người dùng
                </p>

                <div id="paginationControls" class="flex items-center gap-1.5">
                </div>
            </div>
        </div>
    </div>
</main>

<div id="addUserModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">person_add</span> Thêm người dùng mới
            </h3>
            <button onclick="closeModal('addUserModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form id="formAddUser" onsubmit="event.preventDefault(); submitAddUser();"
            class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Họ và tên <span
                        class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" id="add_ho_ten" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mã người
                        dùng <span class="text-red-500">*</span></label>
                    <input type="text" name="ma_nguoi_dung" id="add_ma_nguoi_dung" required readonly
                        class="w-full border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-600 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Vai
                        trò</label>
                    <select name="vai_tro" id="add_vai_tro"
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none cursor-pointer">
                        <option value="Thí sinh">Thí sinh</option>
                        <option value="Giảng viên">Giảng viên</option>
                        <option value="Quản trị viên">Quản trị viên</option>
                    </select>
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" name="email" id="add_email" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('addUserModal')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2">Lưu
                    thông tin</button>
            </div>
        </form>
    </div>
</div>

<div id="importUserModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Nhập dữ liệu từ Excel</h3>
            <button onclick="closeModal('importUserModal')" class="text-slate-400 hover:text-red-500 transition"><span
                    class="material-icons">close</span></button>
        </div>
        <form id="formImportUser" onsubmit="event.preventDefault(); submitImportUser();" class="p-6">
            <div id="dropZone"
                class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:bg-slate-50 dark:hover:bg-slate-700/50 transition cursor-pointer">
                <span class="material-icons text-[40px] text-slate-400 mb-2">cloud_upload</span>
                <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Kéo thả file Excel vào đây hoặc <span
                        class="text-[#254ada] dark:text-[#4b6bfb] hover:underline">Chọn file</span></p>
                <p class="text-[11px] text-slate-400 mt-2">Hỗ trợ .xlsx, .xls, .csv</p>
                <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" class="hidden">
            </div>
            <p id="fileName" class="text-sm text-slate-600 dark:text-slate-400 mt-3 text-center hidden"></p>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal('importUserModal')"
                    class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 bg-[#254ada] text-white rounded-lg text-sm font-medium">Tải lên</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">edit</span> Chỉnh sửa người dùng
            </h3>
            <button onclick="closeModal('editUserModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form id="formEditUser" onsubmit="event.preventDefault(); submitEditUser();"
            class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <input type="hidden" name="ma_nguoi_dung" id="edit_ma_nguoi_dung">
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Họ và tên <span
                        class="text-red-500">*</span></label>
                <input type="text" name="ho_ten" id="edit_ho_ten" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mã người
                        dùng <span class="text-red-500">*</span></label>
                    <input type="text" id="edit_ma_nguoi_dung_display" required readonly
                        class="w-full border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-600 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Vai
                        trò</label>
                    <select name="vai_tro" id="edit_vai_tro"
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none cursor-pointer">
                        <option value="Thí sinh">Thí sinh</option>
                        <option value="Giảng viên">Giảng viên</option>
                        <option value="Quản trị viên">Quản trị viên</option>
                    </select>
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" name="email" id="edit_email" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('editUserModal')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy</button>
                <button type="submit"
                    class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] text-white rounded-lg text-sm font-medium transition flex items-center gap-2">Lưu
                    thay đổi</button>
            </div>
        </form>
    </div>
</div>

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
    // ==================== QUẢN LÝ NGƯỜI DÙNG API ====================
    
    // Hàm Mở / Đóng Modal
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    // Hàm hiển thị Toast
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

    // Hàm reload lại danh sách người dùng
    async function loadUsers() {
        try {
            const response = await fetch('../../api/user_list.php');
            const data = await response.json();
            
            if (data.success) {
                renderUsersTable(data.users);
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể tải danh sách người dùng');
            console.error(error);
        }
    }

    // Hàm render lại bảng người dùng
    function renderUsersTable(users) {
        const tbody = document.querySelector('#usersTable tbody');
        if (!tbody) return;

        const colors = ['bg-blue-100 text-blue-600', 'bg-orange-100 text-orange-600', 'bg-green-100 text-green-600', 'bg-purple-100 text-purple-600'];

        tbody.innerHTML = users.map((user, index) => {
            const initial = user.initial;
            const colorClass = colors[index % colors.length];
            
            const role_bg = user.role_text.includes('purple') ? 'bg-purple-50' : (user.role_text.includes('emerald') ? 'bg-emerald-50' : 'bg-blue-50');
            const role_text = user.role_text;
            
            const isLocked = user.status_value == 0;
            
            return `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition group user-row">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" class="row-checkbox w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700 cursor-pointer">
                    </td>
                    <td class="px-6 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full ${user.avatar_bg} ${user.avatar_text} dark:bg-opacity-20 flex items-center justify-center font-bold text-sm shrink-0">
                            ${initial}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white user-name">${user.name}</p>
                            <p class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5 user-email">${user.email}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-[13px] user-id">${user.id}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 ${role_bg} ${role_text} dark:bg-opacity-20 rounded-md text-[11px] font-semibold text-center inline-block min-w-[80px] leading-tight">${user.role}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 ${user.status_bg} ${user.status_text} dark:bg-opacity-20 rounded-full text-[11px] font-semibold">
                            <div class="w-1.5 h-1.5 rounded-full ${user.dot}"></div>
                            ${user.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                        <button onclick="editUser('${user.id}', '${user.name.replace(/'/g, "\\'")}', '${user.email}', '${user.role.replace(/'/g, "\\'")}')" class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700">
                            <span class="material-icons text-[18px]">edit</span>
                        </button>
                        <button onclick="toggleUserStatus('${user.id}', '${user.name.replace(/'/g, "\\'")}', '${isLocked ? '1' : '0'}')" class="hover:text-emerald-600 dark:hover:text-emerald-400 p-1.5 transition rounded-md hover:bg-emerald-50 dark:hover:bg-slate-700 ${isLocked ? 'text-emerald-500' : ''}">
                            <span class="material-icons text-[18px]">${isLocked ? 'lock' : 'lock_open'}</span>
                        </button>
                        <button onclick="deleteUser('${user.id}', '${user.name.replace(/'/g, "\\'")}')" class="hover:text-red-600 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700">
                            <span class="material-icons text-[18px]">delete</span>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Cập nhật lại sự kiện cho checkbox
        initCheckboxEvents();
    }

    // ==================== THÊM NGƯỜI DÙNG ====================
    
    async function submitAddUser() {
        const ho_ten = document.getElementById('add_ho_ten').value.trim();
        const email = document.getElementById('add_email').value.trim();
        const vai_tro = document.getElementById('add_vai_tro').value;

        if (!ho_ten || !email) {
            showToast('error', 'Lỗi', 'Vui lòng nhập đầy đủ thông tin');
            return;
        }

        try {
            const response = await fetch('../../api/user_add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ho_ten, email, vai_tro })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', 'Thành công', data.message);
                closeModal('addUserModal');
                document.getElementById('formAddUser').reset();
                loadUsers(); // Reload bảng
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể thêm người dùng');
            console.error(error);
        }
    }

    // ==================== SỬA NGƯỜI DÙNG ====================
    
    function editUser(id, name, email, role) {
        document.getElementById('edit_ma_nguoi_dung').value = id;
        document.getElementById('edit_ma_nguoi_dung_display').value = id;
        document.getElementById('edit_ho_ten').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_vai_tro').value = role;
        openModal('editUserModal');
    }

    async function submitEditUser() {
        const ma_nguoi_dung = document.getElementById('edit_ma_nguoi_dung').value;
        const ho_ten = document.getElementById('edit_ho_ten').value.trim();
        const email = document.getElementById('edit_email').value.trim();
        const vai_tro = document.getElementById('edit_vai_tro').value;

        if (!ho_ten || !email) {
            showToast('error', 'Lỗi', 'Vui lòng nhập đầy đủ thông tin');
            return;
        }

        try {
            const response = await fetch('../../api/user_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ma_nguoi_dung, ho_ten, email, vai_tro })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', 'Thành công', data.message);
                closeModal('editUserModal');
                loadUsers(); // Reload bảng
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể cập nhật người dùng');
            console.error(error);
        }
    }

    // ==================== KHÓA/MỞ KHÓA ====================
    
    async function toggleUserStatus(id, name, currentStatus) {
        const action = currentStatus === '1' ? 'mở khóa' : 'khóa';
        
        if (!confirm(`Bạn có chắc chắn muốn ${action} tài khoản của "${name}"?`)) {
            return;
        }

        try {
            const response = await fetch('../../api/user_toggle_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ma_nguoi_dung: id })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', 'Thành công', data.message);
                loadUsers(); // Reload bảng
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể thay đổi trạng thái');
            console.error(error);
        }
    }

    // ==================== XÓA NGƯỜI DÙNG ====================
    
    async function deleteUser(id, name) {
        if (!confirm(`Bạn có chắc chắn muốn xóa người dùng "${name}"?`)) {
            return;
        }

        try {
            const response = await fetch('../../api/user_delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ma_nguoi_dung: id })
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', 'Thành công', data.message);
                loadUsers(); // Reload bảng
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể xóa người dùng');
            console.error(error);
        }
    }

    // ==================== IMPORT NGƯỜI DÙNG ====================
    
    // Xử lý click vào dropzone
    document.getElementById('dropZone')?.addEventListener('click', function() {
        document.getElementById('importFile').click();
    });

    // Hiển thị tên file khi chọn
    document.getElementById('importFile')?.addEventListener('change', function(e) {
        const fileNameEl = document.getElementById('fileName');
        if (this.files.length > 0) {
            fileNameEl.textContent = 'Đã chọn: ' + this.files[0].name;
            fileNameEl.classList.remove('hidden');
        } else {
            fileNameEl.classList.add('hidden');
        }
    });

    async function submitImportUser() {
        const fileInput = document.getElementById('importFile');
        
        if (!fileInput.files.length) {
            showToast('error', 'Lỗi', 'Vui lòng chọn file để import');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        try {
            const response = await fetch('../../api/user_import.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', 'Thành công', data.message);
                closeModal('importUserModal');
                document.getElementById('formImportUser').reset();
                document.getElementById('fileName').classList.add('hidden');
                loadUsers(); // Reload bảng
            } else {
                showToast('error', 'Lỗi', data.message);
            }
        } catch (error) {
            showToast('error', 'Lỗi', 'Không thể import người dùng');
            console.error(error);
        }
    }

    // ==================== SỰ KIỆN CHECKBOX ====================
    
    function initCheckboxEvents() {
        const selectAllBtn = document.getElementById('selectAllBtn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        selectAllBtn?.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') cb.checked = this.checked;
            });
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const visibleCheckboxes = Array.from(rowCheckboxes).filter(c => c.closest('tr').style.display !== 'none');
                const allChecked = visibleCheckboxes.every(c => c.checked);
                const someChecked = visibleCheckboxes.some(c => c.checked);
                if (selectAllBtn) {
                    selectAllBtn.checked = allChecked && visibleCheckboxes.length > 0;
                    selectAllBtn.indeterminate = someChecked && !allChecked;
                }
            });
        });
    }

    // ==================== KHỞI TẠO ====================
    
    document.addEventListener('DOMContentLoaded', function () {
        
        // Dark Mode
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

        // Dropdown Notifications
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

        // Checkbox events
        initCheckboxEvents();

        // TÌM KIẾM VÀ PHÂN TRANG
        const rowsPerPage = 5;
        let currentPage = 1;
        const allRows = Array.from(document.querySelectorAll('.user-row'));
        let filteredRows = [...allRows];

        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            const totalRows = filteredRows.length;
            let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            // Demo mode
            const isDemoMode = true;
            const fakeTotalPages = 458;
            const fakeTotalRows = 45800;
            if (isDemoMode && searchInput && searchInput.value.trim() === '') {
                totalPages = fakeTotalPages;
            }

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            if (currentPage === 1 || !isDemoMode || (searchInput && searchInput.value.trim() !== '')) {
                filteredRows.slice(start, end).forEach(row => row.style.display = '');
            }

            let displayStart = totalRows === 0 ? 0 : start + 1;
            let displayEnd = Math.min(end, (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows);
            let displayTotal = (isDemoMode && searchInput && searchInput.value.trim() === '') ? fakeTotalRows : totalRows;

            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart} - ${displayEnd}</span> của <span class="font-medium text-slate-800 dark:text-white">${displayTotal.toLocaleString()}</span> người dùng`;
            }

            if (paginationControls) {
                paginationControls.innerHTML = '';

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

                const nextBtn = document.createElement('button');
                nextBtn.className = `w-8 h-8 flex items-center justify-center border rounded transition ${currentPage === totalPages ? 'border-slate-100 dark:border-slate-800 opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-600' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-600'}`;
                nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
            
            const selectAllBtn = document.getElementById('selectAllBtn');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            if (selectAllBtn) { selectAllBtn.checked = false; selectAllBtn.indeterminate = false; }
            rowCheckboxes.forEach(cb => cb.checked = false);
        }

        searchInput?.addEventListener('input', function (e) {
            const text = e.target.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.querySelector('.user-email').textContent.toLowerCase();
                const id = row.querySelector('.user-id').textContent.toLowerCase();
                return name.includes(text) || email.includes(text) || id.includes(text);
            });
            currentPage = 1;
            updatePagination();
        });

        updatePagination();
    });
</script>
