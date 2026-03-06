<?php
require_once __DIR__ . '/../../app/config/Database.php';

$conn = Database::getConnection();

/* Hàm đếm số user theo vai trò */
function countUsersByRole($conn,$role_id){
    $stmt = $conn->prepare("SELECT COUNT(*) FROM nguoi_dung WHERE ma_vai_tro = ?");
    $stmt->execute([$role_id]);
    return $stmt->fetchColumn();
}

// 1. Cấu hình thông tin trang
$title = "Quản Trị Phân Quyền - Hệ Thống Thi Trực Tuyến";
$active_menu = "roles";

// Mảng vai trò
$roles = [
    [
        'id'=>1,
        'name' => 'Quản trị viên (Admin)', 
        'desc' => 'Toàn quyền truy cập hệ thống.', 
        'users' => countUsersByRole($conn,1),
        'badge_bg' => 'bg-blue-50', 
        'badge_text' => 'text-[#1e3bb3]'
    ],
    [
        'id'=>2,
        'name' => 'Giảng viên', 
        'desc' => 'Quản lý ngân hàng câu hỏi, tạo đề thi, xem điểm.', 
        'users' => countUsersByRole($conn,2),
        'badge_bg' => 'bg-purple-50', 
        'badge_text' => 'text-purple-600'
    ],
    [
        'id'=>3,
        'name' => 'Thí sinh', 
        'desc' => 'Tham gia thi, xem kết quả cá nhân.', 
        'users' => countUsersByRole($conn,3),
        'badge_bg' => 'bg-slate-100', 
        'badge_text' => 'text-slate-600'
    ],
];

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">

<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
<div class="text-sm text-slate-500">
Quản trị hệ thống <span class="mx-2">›</span>
<span class="text-slate-800 font-medium">Quản trị phân quyền</span>
</div>

<div class="flex items-center gap-4">
<button class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">dark_mode</span>
</button>

<button class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">notifications</span>
</button>
</div>
</header>

<div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">

<div class="flex justify-between items-start mb-6">

<div>
<h2 class="text-2xl font-bold text-slate-800">
Quản lý Vai trò & Phân quyền
</h2>

<p class="text-sm text-slate-500 mt-1">
Thiết lập nhóm quyền và giới hạn truy cập cho các chức năng trong hệ thống.
</p>
</div>

<a href="role_add.php"
class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">

<span class="material-icons text-[20px]">person_add_alt_1</span>
Thêm vai trò mới
</a>

</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col mb-6">

<div class="overflow-x-auto">

<table class="w-full text-left">

<thead class="bg-white text-[11px] text-slate-500 uppercase font-semibold border-b border-slate-100">
<tr>
<th class="px-6 py-5">Tên vai trò</th>
<th class="px-6 py-5">Mô tả quyền hạn</th>
<th class="px-6 py-5">Số thành viên</th>
<th class="px-6 py-5 text-right">Thao tác</th>
</tr>
</thead>

<tbody class="divide-y divide-slate-100 text-sm">

<?php foreach($roles as $role): ?>

<tr class="hover:bg-slate-50/50 transition group">

<td class="px-6 py-4">
<span class="px-3 py-1.5 <?php echo $role['badge_bg']; ?> <?php echo $role['badge_text']; ?> rounded-full font-semibold text-[12px] inline-block">
<?php echo $role['name']; ?>
</span>
</td>

<td class="px-6 py-4 text-slate-600 text-[13px]">
<?php echo $role['desc']; ?>
</td>

<td class="px-6 py-4 font-bold text-slate-800">
<?php echo $role['users']; ?>
</td>

<td class="px-6 py-4 text-right space-x-1 text-slate-400">

<a href="role_permission.php?id=<?php echo $role['id']; ?>"
class="hover:text-[#254ada] p-1.5 transition rounded-md hover:bg-blue-50"
title="Cấu hình quyền">
<span class="material-icons text-[18px]">security</span>
</a>

<a href="role_edit.php?id=<?php echo $role['id']; ?>"
class="hover:text-slate-600 p-1.5 transition rounded-md hover:bg-slate-100"
title="Sửa tên">
<span class="material-icons text-[18px]">edit</span>
</a>

<a href="role_delete.php?id=<?php echo $role['id']; ?>"
onclick="return confirm('Bạn có chắc muốn xóa vai trò này?')"
class="hover:text-red-600 p-1.5 transition rounded-md hover:bg-red-50"
title="Xóa">
<span class="material-icons text-[18px]">delete_outline</span>
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
<p>Hiển thị 3 vai trò trên trang này</p>

<div class="flex items-center gap-1.5">
<button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-slate-50 rounded-md text-slate-300 cursor-not-allowed">
<span class="material-icons text-[18px]">chevron_left</span>
</button>

<button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white hover:bg-slate-50 rounded-md text-slate-400 transition">
<span class="material-icons text-[18px]">chevron_right</span>
</button>
</div>

</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6"> <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-xl"> <div class="flex items-center gap-2 text-[#1e3bb3] font-bold mb-3"> <span class="material-icons text-[20px]">info</span> Về vai trò </div> <p class="text-[13px] text-blue-800 leading-relaxed">Vai trò giúp nhóm các quyền hạn lại với nhau để dễ dàng quản lý quyền truy cập cho nhiều người dùng cùng lúc.</p> </div> <div class="bg-orange-50/50 border border-orange-100 p-5 rounded-xl"> <div class="flex items-center gap-2 text-orange-600 font-bold mb-3"> <span class="material-icons text-[20px]">warning</span> Lưu ý bảo mật </div> <p class="text-[13px] text-orange-800 leading-relaxed">Việc thay đổi quyền hạn của một vai trò sẽ áp dụng ngay lập tức cho tất cả người dùng thuộc vai trò đó.</p> </div> <div class="bg-emerald-50/50 border border-emerald-100 p-5 rounded-xl"> <div class="flex items-center gap-2 text-emerald-600 font-bold mb-3"> <span class="material-icons text-[20px]">bolt</span> Phân quyền nhanh </div> <p class="text-[13px] text-emerald-800 leading-relaxed">Sử dụng các mẫu (template) có sẵn để thiết lập nhanh vai trò cho các bộ phận mới trong tổ chức.</p> </div> </div> </div>

</div>

</main>

<?php include 'components/footer.php'; ?>