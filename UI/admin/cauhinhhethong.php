<?php
require_once __DIR__ . '/../../app/config/Database.php';
$conn = Database::getConnection();

/* =============================
LẤY CẤU HÌNH HỆ THỐNG
============================= */

$stmt = $conn->query("SELECT * FROM cau_hinh_he_thong LIMIT 1");
$setting = $stmt->fetch(PDO::FETCH_ASSOC);

/* =============================
XỬ LÝ LƯU CẤU HÌNH
============================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ten_he_thong = $_POST['ten_he_thong'];
    $quy_dinh_thi = $_POST['quy_dinh_thi'];
    $smtp_server = $_POST['smtp_server'];
    $smtp_port = $_POST['smtp_port'];
    $smtp_email = $_POST['smtp_email'];
    $smtp_password = $_POST['smtp_password'];

    /* Upload logo */

    $logo = $setting['logo'];

    if (!empty($_FILES['logo']['name'])) {

        $target_dir = "../uploads/";
        if(!is_dir($target_dir)){
            mkdir($target_dir,0777,true);
        }

        $logo = time() . "_" . $_FILES["logo"]["name"];
        $target_file = $target_dir . $logo;

        move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
    }

    /* UPDATE DATABASE */

    $sql = "UPDATE cau_hinh_he_thong SET
            ten_he_thong = ?,
            logo = ?,
            quy_dinh_thi = ?,
            smtp_server = ?,
            smtp_port = ?,
            smtp_email = ?,
            smtp_password = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        $ten_he_thong,
        $logo,
        $quy_dinh_thi,
        $smtp_server,
        $smtp_port,
        $smtp_email,
        $smtp_password
    ]);

    header("Location: settings.php?success=1");
    exit();
}

$title = "Cấu Hình Hệ Thống - Hệ Thống Thi Trực Tuyến";
$active_menu = "settings";

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">

<header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10">
    <div class="flex items-center gap-2">
        <span class="material-icons text-slate-400">settings</span>
        <span class="font-bold text-slate-800">Cấu hình hệ thống</span>
    </div>
</header>

<div class="flex-1 overflow-y-auto p-8 bg-slate-50">

<?php if(isset($_GET['success'])){ ?>

<div class="mb-6 p-4 bg-green-100 text-green-700 rounded">
Lưu cấu hình thành công
</div>

<?php } ?>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm">

<form method="POST" enctype="multipart/form-data" class="p-8">

<!-- ============================= -->
<!-- THÔNG TIN HỆ THỐNG -->
<!-- ============================= -->

<div class="mb-8">

<h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
<span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">info</span>
THÔNG TIN HỆ THỐNG
</h3>

<div class="grid grid-cols-2 gap-x-8 gap-y-6">

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Tên hệ thống
</label>

<input 
type="text"
name="ten_he_thong"
value="<?= htmlspecialchars($setting['ten_he_thong']) ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>

<label class="block text-sm font-semibold text-slate-700 mb-2">
Logo hệ thống
</label>

<div class="flex items-center gap-3">

<?php if(!empty($setting['logo'])){ ?>

<img 
src="../uploads/<?= $setting['logo'] ?>"
class="w-10 h-10 object-cover rounded border"
/>

<?php } ?>

<input type="file" name="logo" class="text-sm">

</div>

</div>

<div class="col-span-2">

<label class="block text-sm font-semibold text-slate-700 mb-2">
Quy định thi chung
</label>

<textarea
name="quy_dinh_thi"
rows="4"
class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
><?= htmlspecialchars($setting['quy_dinh_thi']) ?></textarea>

</div>

</div>
</div>

<!-- ============================= -->
<!-- SMTP -->
<!-- ============================= -->

<div class="pt-8 border-t border-slate-100 mb-8">

<h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
<span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">email</span>
CẤU HÌNH EMAIL / SMTP
</h3>

<div class="grid grid-cols-2 gap-x-8 gap-y-6">

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
SMTP Server
</label>

<input
type="text"
name="smtp_server"
value="<?= $setting['smtp_server'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
SMTP Port
</label>

<input
type="text"
name="smtp_port"
value="<?= $setting['smtp_port'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Email User
</label>

<input
type="text"
name="smtp_email"
value="<?= $setting['smtp_email'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

<div>
<label class="block text-sm font-semibold text-slate-700 mb-2">
Mật khẩu
</label>

<input
type="password"
name="smtp_password"
value="<?= $setting['smtp_password'] ?>"
class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]"
>
</div>

</div>

</div>

<!-- ============================= -->
<!-- BUTTON -->
<!-- ============================= -->

<div class="flex justify-end gap-3 pt-6 border-t border-slate-100">

<button
type="submit"
class="px-6 py-2.5 bg-[#254ada] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 font-medium text-sm transition shadow-sm"
>

<span class="material-icons text-[20px]">save</span>
Lưu thay đổi

</button>

</div>

</form>

</div>
</div>
</main>

<?php include 'components/footer.php'; ?>