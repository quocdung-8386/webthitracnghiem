<?php
$title = "Nhật Ký Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "logs";

require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* =========================
   LẤY FILTER
========================= */

$search = $_GET['search'] ?? '';
$user = $_GET['user'] ?? '';
$date = $_GET['date'] ?? '';

/* =========================
   QUERY DATABASE
========================= */

$sql = "SELECT * FROM nhat_ky_he_thong WHERE 1=1";
$params = [];

if($search != ''){
    $sql .= " AND noi_dung LIKE ?";
    $params[] = "%$search%";
}

if($user != ''){
    $sql .= " AND ten_nguoi_dung = ?";
    $params[] = $user;
}

if($date != ''){
    $sql .= " AND DATE(thoi_gian) = ?";
    $params[] = $date;
}

$sql .= " ORDER BY thoi_gian DESC LIMIT 100";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   CHUYỂN DATA HIỂN THỊ
========================= */

$logs = [];

foreach ($data as $row) {

    $badge = "bg-slate-200 text-slate-700";

    if ($row['hanh_dong'] == "LOGIN") {
        $badge = "bg-green-100 text-green-700";
    } elseif ($row['hanh_dong'] == "CREATE") {
        $badge = "bg-blue-100 text-blue-700";
    } elseif ($row['hanh_dong'] == "UPDATE") {
        $badge = "bg-orange-100 text-orange-700";
    } elseif ($row['hanh_dong'] == "DELETE") {
        $badge = "bg-red-100 text-red-700";
    }

    $logs[] = [
        'time' => date("H:i:s", strtotime($row['thoi_gian'])),
        'date' => date("d/m/Y", strtotime($row['thoi_gian'])),
        'initial' => strtoupper(substr($row['ten_nguoi_dung'],0,2)),
        'name' => $row['ten_nguoi_dung'],
        'role' => $row['vai_tro'],
        'action' => $row['hanh_dong'],
        'badge' => $badge,
        'desc' => $row['noi_dung'],
        'ip' => $row['ip_address']
    ];
}

/* =========================
   EXPORT EXCEL
========================= */

if(isset($_GET['export'])){

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=logs.xls");

echo "Time\tUser\tRole\tAction\tDescription\tIP\n";

$stmt = $conn->query("SELECT * FROM nhat_ky_he_thong ORDER BY thoi_gian DESC");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

echo $row['thoi_gian']."\t".
$row['ten_nguoi_dung']."\t".
$row['vai_tro']."\t".
$row['hanh_dong']."\t".
$row['noi_dung']."\t".
$row['ip_address']."\n";

}

exit;
}

/* =========================
   LẤY DANH SÁCH USER FILTER
========================= */

$users = $conn->query("SELECT DISTINCT ten_nguoi_dung FROM nhat_ky_he_thong")
              ->fetchAll(PDO::FETCH_ASSOC);


include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">

<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10">
<div class="flex items-center gap-2">
<span class="material-icons text-slate-400">history</span>
<span class="font-bold text-slate-800">Nhật ký hoạt động hệ thống</span>
</div>

<div class="flex items-center gap-4">
<a href="?export=1" class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">download</span>
</a>

<button onclick="location.reload()" class="text-slate-500 hover:text-[#254ada] transition">
<span class="material-icons">refresh</span>
</button>
</div>
</header>


<div class="flex-1 overflow-y-auto p-8 bg-slate-50">

<form method="GET">

<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6 grid grid-cols-4 gap-4 items-end">

<div>
<label class="block text-xs font-semibold text-slate-500 uppercase mb-2">TÌM KIẾM</label>

<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>

<input
name="search"
value="<?= htmlspecialchars($search) ?>"
type="text"
placeholder="Tìm theo nội dung..."
class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>

</div>
</div>


<div>
<label class="block text-xs font-semibold text-slate-500 uppercase mb-2">NGÀY</label>

<input
type="date"
name="date"
value="<?= $date ?>"
class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#254ada]"
>

</div>


<div>
<label class="block text-xs font-semibold text-slate-500 uppercase mb-2">NGƯỜI THỰC HIỆN</label>

<select name="user"
class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:border-[#254ada]">

<option value="">Tất cả</option>

<?php foreach($users as $u): ?>

<option value="<?= $u['ten_nguoi_dung'] ?>" <?= ($user==$u['ten_nguoi_dung'])?'selected':'' ?>>

<?= $u['ten_nguoi_dung'] ?>

</option>

<?php endforeach; ?>

</select>

</div>


<div>
<button class="w-full py-2 bg-[#254ada] text-white rounded-lg flex items-center justify-center gap-2 font-medium text-sm shadow-sm hover:bg-blue-800 transition">

<span class="material-icons text-[18px]">filter_alt</span>
Áp dụng bộ lọc

</button>
</div>

</div>

</form>


<div class="bg-white rounded-xl border border-slate-200 shadow-sm">

<div class="p-4 border-b border-slate-200 flex justify-between items-center">

<h3 class="font-bold text-slate-800">Chi tiết lịch sử thao tác</h3>

</div>


<table class="w-full text-left text-sm">

<thead class="bg-slate-50 text-xs text-slate-500 uppercase font-semibold">

<tr>

<th class="px-6 py-4">Thời gian</th>
<th class="px-6 py-4">Người thực hiện</th>
<th class="px-6 py-4">Hành động</th>
<th class="px-6 py-4">Nội dung chi tiết</th>
<th class="px-6 py-4 text-right">Địa chỉ IP</th>

</tr>

</thead>


<tbody class="divide-y divide-slate-100">

<?php foreach($logs as $log): ?>

<tr class="hover:bg-slate-50 transition">

<td class="px-6 py-4">
<div class="font-bold text-slate-800"><?= $log['time'] ?></div>
<div class="text-[11px] text-slate-400 mt-0.5"><?= $log['date'] ?></div>
</td>


<td class="px-6 py-4 flex items-center gap-3">

<div class="w-8 h-8 rounded-full bg-slate-100 text-[#254ada] flex items-center justify-center font-bold text-xs">

<?= $log['initial'] ?>

</div>

<div>

<div class="font-semibold text-slate-800"><?= $log['name'] ?></div>

<div class="text-[11px] text-slate-400 mt-0.5"><?= $log['role'] ?></div>

</div>

</td>


<td class="px-6 py-4">

<span class="px-2.5 py-1 <?= $log['badge'] ?> text-[10px] font-bold rounded uppercase">

<?= $log['action'] ?>

</span>

</td>


<td class="px-6 py-4 text-slate-600">

<?= $log['desc'] ?>

</td>


<td class="px-6 py-4 text-right text-slate-500 font-mono text-xs">

<?= $log['ip'] ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>


<div class="p-4 border-t border-slate-200 flex justify-between items-center text-sm text-slate-500">

<p>Hiển thị <?= count($logs) ?> bản ghi</p>

</div>

</div>

</div>

</main>

<?php include 'components/footer.php'; ?>