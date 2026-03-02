<?php
// 1. Cấu hình thông tin trang
$title = "Quản Lý Người Dùng - Hệ Thống Thi Trực Tuyến";
$active_menu = "users"; // Sẽ làm sáng menu Quản lý người dùng ở Sidebar

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

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="text-sm text-slate-500">Quản trị hệ thống <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Quản lý người dùng</span></div>
        <div class="flex items-center gap-4">
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Danh sách người dùng</h2>
                <p class="text-sm text-slate-500 mt-1">Quản lý tài khoản, vai trò và trạng thái hoạt động của thành viên trong hệ thống.</p>
            </div>
            <div class="flex gap-3">
                <button class="px-5 py-2.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium text-slate-700 shadow-sm transition">
                    <span class="material-icons text-[20px] text-slate-600">save_alt</span> Import từ file
                </button>
                <button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">person_add</span> Thêm người dùng mới
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            
            <div class="p-4 flex gap-4 items-center">
                <div class="relative flex-1">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" placeholder="Tìm kiếm theo tên, email hoặc mã người dùng..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition">
                </div>
                <select class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white min-w-[150px] focus:outline-none focus:border-[#254ada] text-slate-600">
                    <option>Tất cả vai trò</option>
                </select>
                <select class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm bg-white min-w-[150px] focus:outline-none focus:border-[#254ada] text-slate-600">
                    <option>Trạng thái</option>
                </select>
                <button class="w-[42px] h-[42px] flex items-center justify-center border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 transition">
                    <span class="material-icons text-[20px]">filter_list</span>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-t border-slate-100">
                    <thead class="bg-white text-[11px] text-slate-500 uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-14 text-center"><span class="material-icons text-slate-300 text-[20px]">radio_button_unchecked</span></th>
                            <th class="px-6 py-4">Họ và tên</th>
                            <th class="px-6 py-4">Mã người dùng</th>
                            <th class="px-6 py-4">Vai trò</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($users as $user): ?>
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-6 py-4 text-center"><span class="material-icons text-slate-300 text-[20px] cursor-pointer hover:text-[#254ada]">radio_button_unchecked</span></td>
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full <?php echo $user['avatar_bg']; ?> <?php echo $user['avatar_text']; ?> flex items-center justify-center font-bold text-sm">
                                    <?php echo $user['initial']; ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800"><?php echo $user['name']; ?></p>
                                    <p class="text-[12px] text-slate-400 mt-0.5"><?php echo $user['email']; ?></p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-[13px]"><?php echo $user['id']; ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 <?php echo $user['role_bg']; ?> <?php echo $user['role_text']; ?> rounded-md text-[11px] font-semibold break-words text-center inline-block max-w-[80px] leading-tight"><?php echo $user['role']; ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 <?php echo $user['status_bg']; ?> <?php echo $user['status_text']; ?> rounded-full text-[11px] font-semibold">
                                    <div class="w-1.5 h-1.5 rounded-full <?php echo $user['dot']; ?>"></div>
                                    <?php echo $user['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 text-slate-400">
                                <button class="hover:text-[#254ada] p-1.5 transition rounded-md hover:bg-blue-50"><span class="material-icons text-[18px]">edit</span></button>
                                <button class="hover:text-emerald-600 p-1.5 transition rounded-md hover:bg-emerald-50 <?php echo ($user['status'] == 'Bị khóa') ? 'text-emerald-500' : ''; ?>"><span class="material-icons text-[18px]">lock</span></button>
                                <button class="hover:text-slate-700 p-1.5 transition rounded-md hover:bg-slate-100"><span class="material-icons text-[18px]">more_horiz</span></button>
                                <button class="hover:text-red-600 p-1.5 transition rounded-md hover:bg-red-50"><span class="material-icons text-[18px]">delete</span></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                <p>Hiển thị <span class="font-medium text-slate-800">1</span> - <span class="font-medium text-slate-800">4</span> của <span class="font-medium text-slate-800">120</span> người dùng</p>
                <div class="flex items-center gap-1.5">
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded-md hover:bg-slate-50 text-slate-400 transition"><span class="material-icons text-[18px]">chevron_left</span></button>
                    <button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded-md font-medium shadow-sm">1</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600 transition">2</button>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600 transition">3</button>
                    <span class="w-8 h-8 flex items-center justify-center text-slate-400">...</span>
                    <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600 transition">30</button>
                    <button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded-md hover:bg-slate-50 text-slate-600 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                </div>
            </div>
            
        </div>
    </div>
</main>

<?php 
// 3. Nhúng Footer
include 'components/footer.php'; 
?>