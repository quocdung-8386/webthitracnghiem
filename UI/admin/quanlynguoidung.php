<?php
// 1. Cấu hình thông tin trang
$title = "Quản Lý Người Dùng - Hệ Thống Thi Trực Tuyến";
$active_menu = "users";

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

// 2. Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">

<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">

<div class="text-sm text-slate-500">
Quản trị hệ thống 
<span class="mx-2">›</span>
<span class="text-slate-800 font-medium">Quản lý người dùng</span>
</div>

<div class="flex items-center gap-4">
<button class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">notifications</span>
</button>

<button class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">dark_mode</span>
</button>
</div>

</header>


<div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">

<div class="flex justify-between items-start mb-6">

<div>
<h2 class="text-2xl font-bold text-slate-800">
Danh sách người dùng
</h2>

<p class="text-sm text-slate-500 mt-1">
Quản lý tài khoản, vai trò và trạng thái hoạt động của thành viên trong hệ thống.
</p>
</div>

<div class="flex gap-3">

<button class="px-5 py-2.5 bg-white border border-slate-200 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium text-slate-700 shadow-sm transition">
<span class="material-icons text-[20px] text-slate-600">save_alt</span>
Import từ file
</button>

<button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
<span class="material-icons text-[20px]">person_add</span>
Thêm người dùng mới
</button>

</div>
</div>


<div class="bg-white rounded-xl border border-slate-200 shadow-sm">

<div class="p-4 flex gap-4 items-center">

<div class="relative flex-1">

<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">
search
</span>

<input 
type="text"
placeholder="Tìm kiếm theo tên, email hoặc mã người dùng..."
class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#254ada] focus:ring-1 focus:ring-[#254ada] transition"
/>

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

<th class="px-6 py-4 w-14 text-center">
<span class="material-icons text-slate-300 text-[20px]">radio_button_unchecked</span>
</th>

<th class="px-6 py-4">Họ và tên</th>

<th class="px-6 py-4">Mã người dùng</th>

<th class="px-6 py-4">Vai trò</th>

<th class="px-6 py-4">Trạng thái</th>

<th class="px-6 py-4 text-right">Thao tác</th>

</tr>

</thead>


<tbody class="divide-y divide-slate-100 text-sm">

<?php foreach($users as $user): 

$ho_ten = htmlspecialchars($user['ho_ten'] ?? '');
$email = htmlspecialchars($user['email'] ?? '');
$role = htmlspecialchars($user['ten_vai_tro'] ?? '');
$id = $user['ma_nguoi_dung'];

// avatar chữ cái đầu
$initial = strtoupper(mb_substr($ho_ten,0,1,'UTF-8'));

$status = "Không xác định";
$status_bg = "bg-gray-100";
$status_text = "text-gray-600";
$dot = "bg-gray-400";

if($user['trang_thai'] == 'hoat_dong'){
$status = "Đang hoạt động";
$status_bg = "bg-green-100";
$status_text = "text-green-700";
$dot = "bg-green-500";
}

elseif($user['trang_thai'] == 'bi_khoa'){
$status = "Bị khóa";
$status_bg = "bg-red-100";
$status_text = "text-red-600";
$dot = "bg-red-500";
}

elseif($user['trang_thai'] == 'ngung_hoat_dong'){
$status = "Ngừng hoạt động";
$status_bg = "bg-gray-100";
$status_text = "text-gray-600";
$dot = "bg-gray-400";
}

?>

<tr class="hover:bg-slate-50/50 transition group">

<td class="px-6 py-4 text-center">
<span class="material-icons text-slate-300 text-[20px]">
radio_button_unchecked
</span>
</td>


<td class="px-6 py-4 flex items-center gap-4">

<div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
<?= $initial ?>
</div>

<div>
<p class="font-semibold text-slate-800"><?= $ho_ten ?></p>
<p class="text-[12px] text-slate-400 mt-0.5"><?= $email ?></p>
</div>

</td>


<td class="px-6 py-4 text-slate-500 text-[13px]">
USR-<?= $id ?>
</td>


<td class="px-6 py-4">

<span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-md text-[11px] font-semibold">
<?= $role ?>
</span>

</td>


<td class="px-6 py-4">

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 <?= $status_bg ?> <?= $status_text ?> rounded-full text-[11px] font-semibold">

<div class="w-1.5 h-1.5 rounded-full <?= $dot ?>"></div>

<?= $status ?>

</span>

</td>


<td class="px-6 py-4 text-right space-x-1 text-slate-400">

<button class="hover:text-[#254ada] p-1.5">
<span class="material-icons text-[18px]">edit</span>
</button>

<button class="hover:text-red-600 p-1.5">
<span class="material-icons text-[18px]">delete</span>
</button>

</td>

</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>


<div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">

<p>
Hiển thị 
<span class="font-medium text-slate-800">1</span> - 
<span class="font-medium text-slate-800">10</span> 
</p>

<div class="flex items-center gap-1.5">

<button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded-md hover:bg-slate-50 text-slate-400 transition">
<span class="material-icons text-[18px]">chevron_left</span>
</button>

<button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded-md font-medium shadow-sm">
1
</button>

<button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded-md font-medium text-slate-600 transition">
2
</button>

<button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded-md hover:bg-slate-50 text-slate-600 transition">
<span class="material-icons text-[18px]">chevron_right</span>
</button>

</div>

</div>

</div>
</div>

</main>

<?php include 'components/footer.php'; ?>