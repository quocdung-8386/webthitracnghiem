<?php
// 1. Cấu hình thông tin trang
$title = "Giám sát trực tuyến - Hệ Thống Thi Trực Tuyến";
$active_menu = "monitor_exam"; // Làm sáng menu "Giám sát trực tuyến" trong Sidebar

// Dữ liệu mô phỏng trạng thái các thí sinh
$students = [
    [
        'name' => 'Nguyễn Văn A', 'mssv' => '20204512', 'avatar' => 'NV', 'avatar_bg' => 'bg-slate-800 text-white',
        'progress' => '32/40', 'percent' => 80, 
        'status_type' => 'normal', 'status_msg' => 'Chưa có vi phạm nào', 
        'online' => true
    ],
    [
        'name' => 'Trần Thị B', 'mssv' => '20204555', 'avatar' => 'TB', 'avatar_bg' => 'bg-blue-100 text-blue-600',
        'progress' => '15/40', 'percent' => 37, 
        'status_type' => 'danger', 'status_msg' => 'CẢNH BÁO VI PHẠM<br><span class="text-[11px] font-normal text-red-500">Rời khỏi trình duyệt (Lần 2)</span>', 
        'online' => true
    ],
    [
        'name' => 'Lê Minh C', 'mssv' => '20201234', 'avatar' => 'LM', 'avatar_bg' => 'bg-slate-200 text-slate-500',
        'progress' => '05/40', 'percent' => 12, 
        'status_type' => 'disconnected', 'status_msg' => 'MẤT KẾT NỐI (2P)', 
        'online' => false
    ],
    [
        'name' => 'Phạm Thanh D', 'mssv' => '20207890', 'avatar' => 'PD', 'avatar_bg' => 'bg-orange-100 text-orange-600',
        'progress' => '20/40', 'percent' => 50, 
        'status_type' => 'normal', 'status_msg' => 'Chưa có vi phạm nào', 
        'online' => true
    ],
    [
        'name' => 'Đỗ Mỹ Linh', 'mssv' => '20209999', 'avatar' => 'DL', 'avatar_bg' => 'bg-purple-100 text-purple-600',
        'progress' => '38/40', 'percent' => 95, 
        'status_type' => 'normal', 'status_msg' => 'Chưa có vi phạm nào', 
        'online' => true
    ],
    [
        'name' => 'Vũ Đức Tài', 'mssv' => '20202222', 'avatar' => 'VT', 'avatar_bg' => 'bg-emerald-100 text-emerald-600',
        'progress' => '25/40', 'percent' => 62, 
        'status_type' => 'warning', 'status_msg' => 'CÓ 2 CẢNH BÁO<br><span class="text-[11px] font-normal text-orange-500">Phát hiện giọng nói lạ...</span>', 
        'online' => true
    ],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative">
    
    <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between z-10 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
            <div>
                <h2 class="text-[15px] font-bold text-slate-800 leading-tight">Giám sát thi trực tuyến thời gian thực</h2>
                <div class="text-[12px] text-slate-500">Môn: Toán Cao Cấp A1 <span class="mx-1">|</span> Mã ca: MAT101_E1</div>
            </div>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4 bg-slate-50 px-4 py-1.5 rounded-full border border-slate-100">
                <div class="flex items-center gap-2 text-orange-600 font-bold text-sm">
                    <span class="material-icons text-[20px]">timer</span>
                    <div class="flex flex-col">
                        <span class="text-[9px] uppercase tracking-wider text-orange-500/80 leading-none">Thời gian còn lại</span>
                        <span>01 : 42 : 15</span>
                    </div>
                </div>
                <div class="w-px h-6 bg-slate-200"></div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-green-600">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> 124 Đang thi
                </div>
                <div class="w-px h-6 bg-slate-200"></div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-red-500">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> 3 Vi phạm
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="text-slate-500 hover:text-[#1e3bb3] transition relative">
                    <span class="material-icons">notifications</span>
                    <span class="absolute top-0 right-0 w-2 h-2 rounded-full bg-red-500 border-2 border-white"></span>
                </button>
                <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
            </div>
        </div>
    </header>

    <div class="bg-white border-b border-slate-200 p-4 shrink-0 flex justify-between items-center z-10">
        <div class="flex items-center gap-3">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" placeholder="Tìm tên thí sinh, MSSV..." class="pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] w-64 transition">
            </div>
            <select class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:border-[#1e3bb3]">
                <option>Tất cả trạng thái</option>
                <option>Có vi phạm</option>
                <option>Mất kết nối</option>
            </select>
        </div>
        
        <div class="flex items-center bg-slate-100 p-1 rounded-lg">
            <button class="flex items-center gap-1.5 px-3 py-1.5 bg-white shadow-sm rounded text-sm font-semibold text-slate-700">
                <span class="material-icons text-[18px]">grid_view</span> Lưới
            </button>
            <button class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm font-medium text-slate-500 hover:text-slate-700">
                <span class="material-icons text-[18px]">format_list_bulleted</span> Danh sách
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-5 pb-10">
            
            <?php foreach($students as $st): ?>
            <?php
                // Xác định class style dựa trên trạng thái
                $cardClass = "bg-white border-slate-200 hover:border-slate-300";
                $btnWarn = "border-slate-200 text-slate-500 hover:bg-slate-50";
                $btnBan = "border-slate-200 text-red-400 hover:bg-red-50 hover:text-red-600";
                $statusIcon = "";
                
                if($st['status_type'] == 'danger') {
                    $cardClass = "bg-white border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.15)] relative scale-[1.02] z-10";
                    $btnWarn = "bg-orange-500 border-orange-500 text-white hover:bg-orange-600";
                    $btnBan = "bg-red-600 border-red-600 text-white hover:bg-red-700";
                    $statusIcon = "<span class='absolute top-3 right-3 w-3 h-3 bg-red-500 rounded-full animate-ping'></span><span class='absolute top-3 right-3 w-3 h-3 bg-red-500 rounded-full'></span>";
                } elseif ($st['status_type'] == 'warning') {
                    $cardClass = "bg-orange-50/30 border-orange-200";
                } elseif ($st['status_type'] == 'disconnected') {
                    $cardClass = "bg-slate-50 border-slate-200 opacity-70 grayscale-[0.5]";
                    $btnWarn = "border-slate-200 text-slate-300 cursor-not-allowed";
                    $btnBan = "border-slate-200 text-slate-300 cursor-not-allowed";
                }
            ?>

            <div class="rounded-xl border <?php echo $cardClass; ?> p-4 flex flex-col transition-all duration-200">
                <?php echo $statusIcon; ?>
                
                <div class="flex gap-3 items-center mb-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-lg <?php echo $st['avatar_bg']; ?> flex items-center justify-center font-bold text-lg shrink-0 border border-slate-100 shadow-sm">
                            <?php echo $st['avatar']; ?>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white <?php echo $st['online'] ? 'bg-green-500' : 'bg-slate-400'; ?>"></span>
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-800 text-[14px] truncate" title="<?php echo $st['name']; ?>"><?php echo $st['name']; ?></h3>
                        <p class="text-[11px] text-slate-500">MSSV:<br><?php echo $st['mssv']; ?></p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between text-[11px] font-bold mb-1">
                        <span class="text-[#1e3bb3]">Tiến độ: <br><span class="text-[14px]"><?php echo $st['progress']; ?></span></span>
                        <span class="text-slate-400 self-end"><?php echo $st['percent']; ?>%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-[#1e3bb3] h-1.5 rounded-full" style="width: <?php echo $st['percent']; ?>%"></div>
                    </div>
                </div>

                <div class="flex-1 flex items-center justify-center min-h-[40px] mb-4 text-center">
                    <?php if($st['status_type'] == 'danger'): ?>
                        <div class="text-[12px] font-bold text-red-600 flex items-center gap-1 flex-col">
                            <div class="flex items-center gap-1"><span class="material-icons text-[16px]">warning</span> <?php echo $st['status_msg']; ?></div>
                        </div>
                    <?php elseif($st['status_type'] == 'warning'): ?>
                        <div class="text-[12px] font-bold text-orange-600 flex items-center gap-1 flex-col">
                            <div class="flex items-center gap-1"><span class="material-icons text-[16px]">info</span> <?php echo $st['status_msg']; ?></div>
                        </div>
                    <?php elseif($st['status_type'] == 'disconnected'): ?>
                        <div class="text-[12px] font-bold text-slate-500 flex items-center gap-1">
                            <span class="material-icons text-[16px]">wifi_off</span> <?php echo $st['status_msg']; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-[11px] font-medium text-slate-400 italic">
                            <?php echo $st['status_msg']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex gap-2 mt-auto">
                    <button class="flex-1 py-1.5 border rounded uppercase text-[10px] font-bold transition <?php echo $btnWarn; ?>">Cảnh báo</button>
                    <button class="flex-1 py-1.5 border rounded uppercase text-[10px] font-bold transition <?php echo $btnBan; ?>">Đình chỉ</button>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-4 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition min-h-[220px]">
                <div class="w-12 h-12 bg-slate-200 text-slate-400 rounded-full flex items-center justify-center mb-3">
                    <span class="material-icons">people</span>
                </div>
                <span class="text-[13px] font-medium text-slate-500">+118 thí sinh khác</span>
            </div>

        </div>
    </div>

    <div class="bg-white border-t border-slate-200 px-6 py-2 flex justify-between items-center text-[11px] font-medium text-slate-500 shrink-0">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-500"></span> Hệ thống: Ổn định</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-[#1e3bb3]"></span> Server Latency: 45ms</span>
        </div>
        <div>
            Cập nhật lúc: <?php echo date('H:i:s'); ?> (Tự động sau 5s)
        </div>
    </div>

</main>

<?php include 'components/footer.php'; ?>