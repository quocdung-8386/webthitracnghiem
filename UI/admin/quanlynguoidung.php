<?php
// 1. Cấu hình thông tin trang
$title = "Quản Lý Người Dùng - Hệ Thống Thi Trực Tuyến";
$active_menu = "users";

<<<<<<< HEAD
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

$sql = "
SELECT 
    nd.ma_nguoi_dung,
    nd.ho_ten,
    nd.email,
    nd.trang_thai,
    vt.ten_vai_tro
FROM nguoi_dung nd
JOIN vai_tro vt ON nd.ma_vai_tro = vt.ma_vai_tro
ORDER BY nd.ma_nguoi_dung DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
=======
// Cập nhật mảng dữ liệu để màu sắc Avatar và Tag khớp chính xác với thiết kế
$users = [
    [
        'initial' => 'NV',
        'name' => 'Nguyễn Văn An',
        'email' => 'an.nv@university.edu.vn',
        'id' => 'ADM-001',
        'role' => 'Quản trị viên',
        'role_bg' => 'bg-blue-50',
        'role_text' => 'text-blue-600',
        'status' => 'Đang hoạt động',
        'status_bg' => 'bg-green-100',
        'status_text' => 'text-green-700',
        'dot' => 'bg-green-500',
        'avatar_bg' => 'bg-blue-50',
        'avatar_text' => 'text-blue-700'
    ],
    [
        'initial' => 'TH',
        'name' => 'Trần Thị Hoa',
        'email' => 'hoa.tt@university.edu.vn',
        'id' => 'GV-102',
        'role' => 'Giảng viên',
        'role_bg' => 'bg-purple-50',
        'role_text' => 'text-purple-600',
        'status' => 'Đang hoạt động',
        'status_bg' => 'bg-green-100',
        'status_text' => 'text-green-700',
        'dot' => 'bg-green-500',
        'avatar_bg' => 'bg-purple-100',
        'avatar_text' => 'text-purple-700'
    ],
    [
        'initial' => 'LM',
        'name' => 'Lê Minh',
        'email' => 'minh.le@student.edu.vn',
        'id' => 'TS-5021',
        'role' => 'Thí sinh',
        'role_bg' => 'bg-slate-100',
        'role_text' => 'text-slate-600',
        'status' => 'Bị khóa',
        'status_bg' => 'bg-red-50',
        'status_text' => 'text-red-600',
        'dot' => 'bg-red-500',
        'avatar_bg' => 'bg-slate-100',
        'avatar_text' => 'text-slate-600'
    ],
    [
        'initial' => 'PH',
        'name' => 'Phạm Hoàng',
        'email' => 'hoang.p@student.edu.vn',
        'id' => 'TS-5022',
        'role' => 'Thí sinh',
        'role_bg' => 'bg-slate-100',
        'role_text' => 'text-slate-600',
        'status' => 'Đang hoạt động',
        'status_bg' => 'bg-green-100',
        'status_text' => 'text-green-700',
        'dot' => 'bg-green-500',
        'avatar_bg' => 'bg-orange-100',
        'avatar_text' => 'text-orange-600'
    ],
];
>>>>>>> 0e15295 (update admin CN)

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

=======
<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản
                lý người dùng</span>
        </div>
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

                    <a href="#"
                        class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">Giảng viên <span
                                class="font-semibold text-slate-800 dark:text-white">Trần Thị Hoa</span> vừa tạo đề thi
                            mới cho môn Toán Cao Cấp.</p>
                        <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                class="material-icons text-[12px]">schedule</span> 15 phút trước</span>
                    </a>

                    <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                class="font-semibold text-slate-800 dark:text-white">5 thí sinh</span> vừa nộp bài thi
                            môn Tin học đại cương.</p>
                        <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                class="material-icons text-[12px]">schedule</span> 2 giờ trước</span>
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

            <div class="p-4 flex gap-4 items-center border-b border-slate-100 dark:border-slate-700">
                <div class="relative flex-1">
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
                                        class="px-2.5 py-1 <?php echo $user['role_bg']; ?> <?php echo $user['role_text']; ?> dark:bg-opacity-20 rounded-md text-[11px] font-semibold text-center inline-block max-w-[80px] leading-tight"><?php echo $user['role']; ?></span>
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
                                        onclick="showToast('info', 'Chỉnh sửa', 'Mở bảng chỉnh sửa người dùng <?php echo $user['name']; ?>')"
                                        class="hover:text-[#254ada] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"><span
                                            class="material-icons text-[18px]">edit</span></button>
                                    <button
                                        onclick="showToast('warning', 'Khóa tài khoản', 'Tài khoản <?php echo $user['name']; ?> đã bị khóa')"
                                        class="hover:text-emerald-600 dark:hover:text-emerald-400 p-1.5 transition rounded-md hover:bg-emerald-50 dark:hover:bg-slate-700 <?php echo ($user['status'] == 'Bị khóa') ? 'text-emerald-500' : ''; ?>"><span
                                            class="material-icons text-[18px]"><?php echo ($user['status'] == 'Bị khóa') ? 'lock' : 'lock_open'; ?></span></button>
                                    <button
                                        onclick="showToast('error', 'Xóa người dùng', 'Đã xóa người dùng khỏi hệ thống')"
                                        class="hover:text-red-600 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700"><span
                                            class="material-icons text-[18px]">delete</span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <<div
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
                class="material-icons text <input type="text" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mã người
                        dùng <span class="text-red-500">*</span></label>
                    <input type="text" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Vai
                        trò</label>
                    <select
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] outline-none">
                        <option>Thí sinh</option>
                        <option>Giảng viên</option>
                        <option>Quản trị viên</option>
                    </select>
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" required
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
        <div class="p-6">
            <div
                class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:bg-slate-50 dark:hover:bg-slate-700/50 transition cursor-pointer">
                <span class="material-icons text-[40px] text-slate-400 mb-2">cloud_upload</span>
                <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Kéo thả file Excel vào đây hoặc <span
                        class="text-[#254ada] dark:text-[#4b6bfb] hover:underline">Chọn file</span></p>
                <p class="text-[11px] text-slate-400 mt-2">Hỗ trợ .xlsx, .xls, .csv</p>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeModal('importUserModal')"
                    class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg">Hủy</button>
                <button
                    onclick="showToast('success', 'Import thành công', 'Đã thêm danh sách người dùng vào hệ thống'); closeModal('importUserModal');"
                    class="px-4 py-2 bg-[#254ada] text-white rounded-lg text-sm font-medium">Tải lên</button>
            </div>
        </div>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div
        class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 transition"><span
                class="material-icons text-[16px]">close</span></button>
    </div>
</template>
<?php
// 3. Nhúng Footer
include 'components/footer.php';
?>
<script>
    /* =================================================================
       PHẦN 1: CÁC HÀM GLOBAL (NẰM NGOÀI ĐỂ HTML CÓ THỂ GỌI QUA ONCLICK)
       ================================================================= */

    // 1. Hàm Mở / Đóng Modal
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    // 2. Hàm xử lý khi submit form Thêm người dùng
    function submitAddUser() {
        closeModal('addUserModal');
        showToast('success', 'Thành công', 'Đã thêm người dùng mới vào hệ thống.');
        document.getElementById('formAddUser').reset();
    }

    // 3. Hàm hiển thị thông báo Toast
    function showToast(type, title, message) {
        const container = document.getElementById('toastContainer');
        const template = document.getElementById('toastTemplate');

        // Nếu thiếu HTML của Toast thì báo lỗi ra Console để dễ sửa
        if (!container || !template) {
            console.error("Lỗi: Không tìm thấy HTML của toastContainer hoặc toastTemplate!");
            return;
        }

        const toastNode = template.content.cloneNode(true);
        const toastEl = toastNode.querySelector('.toast-item');
        const iconEl = toastNode.querySelector('.toast-icon');

        toastNode.querySelector('.toast-title').textContent = title;
        toastNode.querySelector('.toast-message').textContent = message;

        // Set màu và icon theo Type
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

        // Xử lý nút tắt thông báo
        toastNode.querySelector('.toast-close').onclick = () => {
            toastEl.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toastEl.remove(), 300);
        };

        container.appendChild(toastNode);

        // Hiệu ứng trượt ra
        setTimeout(() => toastEl.classList.remove('translate-x-full', 'opacity-0'), 10);

        // Tự động tắt sau 4s
        setTimeout(() => {
            if (container.contains(toastEl)) toastEl.querySelector('.toast-close').click();
        }, 4000);
    }

    /* =================================================================
       PHẦN 2: CÁC SỰ KIỆN LẮNG NGHE KHI TRANG ĐÃ TẢI XONG
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

        // 2. Chức năng Checkbox (Chọn tất cả)
        const selectAllBtn = document.getElementById('selectAllBtn');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        selectAllBtn?.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                const someChecked = Array.from(rowCheckboxes).some(c => c.checked);
                if (selectAllBtn) {
                    selectAllBtn.checked = allChecked;
                    selectAllBtn.indeterminate = someChecked && !allChecked;
                }
            });
        });

        // 3. Chức năng Tìm kiếm (Lọc dữ liệu trên bảng)
        document.getElementById('searchInput')?.addEventListener('input', function (e) {
            const text = e.target.value.toLowerCase();
            document.querySelectorAll('.user-row').forEach(row => {
                const name = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.querySelector('.user-email').textContent.toLowerCase();
                const id = row.querySelector('.user-id').textContent.toLowerCase();

                if (name.includes(text) || email.includes(text) || id.includes(text)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

    });
    // === CHỨC NĂNG DROPDOWN THÔNG BÁO ===
    const notifButton = document.getElementById('notifButton');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifButton && notifDropdown) {
        // Bật/tắt dropdown khi click vào nút chuông
        notifButton.addEventListener('click', function (e) {
            e.stopPropagation(); // Ngăn click lan ra ngoài body
            notifDropdown.classList.toggle('hidden');
        });

        // Ẩn dropdown khi click ra ngoài vùng thông báo
        document.addEventListener('click', function (e) {
            if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }

    //  CHỨC NĂNG TÌM KIẾM & PHÂN TRANG (Kết hợp)
    const rowsPerPage = 2; // Số lượng người dùng hiển thị trên 1 trang (Đang để 2 để test)
    let currentPage = 1;
    let filteredRows = []; // Chứa các hàng sau khi lọc tìm kiếm

    const allRows = Array.from(document.querySelectorAll('.user-row'));
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');
    const searchInput = document.getElementById('searchInput');

    // Hàm cập nhật hiển thị bảng và thanh phân trang
    function updatePagination() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Ẩn tất cả và chỉ hiện những hàng thuộc trang hiện tại
        allRows.forEach(row => row.style.display = 'none');
        filteredRows.slice(start, end).forEach(row => row.style.display = '');

        // Cập nhật dòng chữ "Hiển thị X - Y của Z"
        const displayStart = totalRows === 0 ? 0 : start + 1;
        const displayEnd = Math.min(end, totalRows);
        if (paginationInfo) {
            paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}</span> - <span class="font-medium text-slate-800 dark:text-white">${displayEnd}</span> của <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> người dùng`;
        }

        // Tạo các nút bấm phân trang
        renderPaginationButtons(totalPages);
    }

    // Hàm render nút bấm (Prev, 1, 2, 3, Next)
    function renderPaginationButtons(totalPages) {
        if (!paginationControls) return;
        paginationControls.innerHTML = ''; // Xóa nút cũ

        // Nút Prev (Trang trước)
        const prevBtn = document.createElement('button');
        prevBtn.className = `w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
        prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
        prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
        paginationControls.appendChild(prevBtn);

        // Các nút số trang
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            if (i === currentPage) {
                // Nút trang hiện tại (Màu xanh)
                pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-[#1e3bb3] dark:bg-[#254ada] text-white rounded-md font-medium shadow-sm';
            } else {
                // Nút trang bình thường
                pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-md font-medium text-slate-600 dark:text-slate-300 transition';
            }
            pageBtn.innerText = i;
            pageBtn.onclick = () => { currentPage = i; updatePagination(); };
            paginationControls.appendChild(pageBtn);
        }

        // Nút Next (Trang tiếp theo)
        const nextBtn = document.createElement('button');
        nextBtn.className = `w-8 h-8 flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
        nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
        nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
        paginationControls.appendChild(nextBtn);
    }

    // Bắt sự kiện gõ phím vào ô Tìm kiếm
    searchInput?.addEventListener('input', function (e) {
        const text = e.target.value.toLowerCase();
        // Lọc danh sách dựa trên chữ gõ vào
        filteredRows = allRows.filter(row => {
            const name = row.querySelector('.user-name').textContent.toLowerCase();
            const email = row.querySelector('.user-email').textContent.toLowerCase();
            const id = row.querySelector('.user-id').textContent.toLowerCase();
            return name.includes(text) || email.includes(text) || id.includes(text);
        });

        currentPage = 1; // Khi tìm kiếm thì reset về trang 1
        updatePagination();
    });

    // Khởi chạy khi load trang
    filteredRows = [...allRows];
    updatePagination();
</script>
>>>>>>> 0e15295 (update admin CN)
