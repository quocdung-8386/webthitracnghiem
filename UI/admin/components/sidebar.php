<?php
// Xử lý logic để xác định nhóm menu nào đang được Active
$active_menu = isset($active_menu) ? $active_menu : '';

// Khai báo các trang thuộc từng nhóm
$group_sys = ['users', 'roles', 'settings', 'logs', 'backup'];
$group_bank = ['update_q', 'category_q', 'import_q', 'version_q', 'approve_q'];
$group_exam = ['create_exam', 'shift_exam', 'monitor_exam'];
$group_candidate = ['take_exam', 'results', 'progress', 'nav_bar', 'offline'];
$group_report = ['stat_result', 'academic', 'export_report'];

// Kiểm tra xem trang hiện tại có nằm trong nhóm nào không
$is_sys_active = in_array($active_menu, $group_sys);
$is_bank_active = in_array($active_menu, $group_bank);
$is_exam_active = in_array($active_menu, $group_exam);
$is_candidate_active = in_array($active_menu, $group_candidate);
$is_report_active = in_array($active_menu, $group_report);
?>

<aside class="w-[260px] bg-[#254ada] text-white flex flex-col shadow-xl z-20 flex-shrink-0">
    <div class="h-16 flex items-center px-6 border-b border-white/10 gap-3 shrink-0">
        <span class="material-icons text-3xl">school</span>
        <div>
            <h1 class="font-bold text-sm tracking-wide leading-tight uppercase">Hệ Thống Thi<br><span class="font-normal text-blue-200 text-[11px]">Trực Tuyến</span></h1>
        </div>
    </div>
    
    <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar">
        
        <div class="px-4 mb-2">
            <a href="bangdieukhientongquan.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo ($active_menu == 'dashboard') ? 'bg-white/20 text-white font-medium shadow-sm' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <span class="material-icons text-[20px]">dashboard</span> Bảng điều khiển
            </a>
        </div>
        
        <div class="px-4 mb-2">
            <div onclick="toggleMenu('menu-sys', 'icon-sys')" class="flex items-center justify-between px-3 py-2 cursor-pointer transition-all duration-200 rounded-lg select-none <?php echo $is_sys_active ? 'text-white font-medium' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <div class="flex items-center gap-3"><span class="material-icons text-[20px]">settings</span> Quản trị hệ thống</div>
                <span id="icon-sys" class="material-icons text-sm transition-transform duration-300 <?php echo $is_sys_active ? 'rotate-180' : ''; ?>">expand_more</span>
            </div>
            
            <div id="menu-sys" class="<?php echo $is_sys_active ? 'block' : 'hidden'; ?> pl-11 pr-3 py-1 mt-1 space-y-1 overflow-hidden transition-all">
                <a href="quanlynguoidung.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'users') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Quản trị người dùng</a>
                <a href="quantriphanquyen.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'roles') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Quản trị phân quyền</a>
                <a href="cauhinhhethong.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'settings') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Cấu hình hệ thống</a>
                <a href="nhatkyhethong.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'logs') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Nhật ký hệ thống</a>
                <a href="saoluuvakhoiphuc.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'backup') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Sao lưu & Phục hồi</a>
            </div>
        </div>

        <div class="px-4 mb-2">
            <div onclick="toggleMenu('menu-bank', 'icon-bank')" class="flex items-center justify-between px-3 py-2 cursor-pointer transition-all duration-200 rounded-lg select-none <?php echo $is_bank_active ? 'text-white font-medium' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <div class="flex items-center gap-3"><span class="material-icons text-[20px]">library_books</span> Ngân hàng câu hỏi</div>
                <span id="icon-bank" class="material-icons text-sm transition-transform duration-300 <?php echo $is_bank_active ? 'rotate-180' : ''; ?>">expand_more</span>
            </div>
            
            <div id="menu-bank" class="<?php echo $is_bank_active ? 'block' : 'hidden'; ?> pl-11 pr-3 py-1 mt-1 space-y-1 overflow-hidden transition-all">
                <a href="danhsachcauhoi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'update_q') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Cập nhật câu hỏi</a>
                <a href="phanloaicauhoi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'category_q') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Phân loại câu hỏi</a>
                <a href="nhapxuatcauhoi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'import_q') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Nhập/Xuất câu hỏi</a>
                <a href="quanlyphienban.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'version') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Quản lý phiên bản</a>
                <a href="duyetcauhoi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all <?php echo ($active_menu == 'approve_q') ? 'bg-white/20 text-white font-medium' : 'text-blue-200 hover:text-white hover:bg-white/10'; ?>">Duyệt câu hỏi</a>
            </div>
        </div>

        <div class="px-4 mb-2">
            <div onclick="toggleMenu('menu-exam', 'icon-exam')" class="flex items-center justify-between px-3 py-2 cursor-pointer transition-all duration-200 rounded-lg select-none <?php echo $is_exam_active ? 'text-white font-medium' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <div class="flex items-center gap-3"><span class="material-icons text-[20px]">assignment</span> Kỳ thi & Đề thi</div>
                <span id="icon-exam" class="material-icons text-sm transition-transform duration-300 <?php echo $is_exam_active ? 'rotate-180' : ''; ?>">expand_more</span>
            </div>
            
            <div id="menu-exam" class="<?php echo $is_exam_active ? 'block' : 'hidden'; ?> pl-11 pr-3 py-1 mt-1 space-y-1 overflow-hidden transition-all">
                <a href="taodethi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Tạo đề thi</a>
                <a href="quanlycathi.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Quản lý ca thi</a>
                <a href="giamsattructuyen.php" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Giám sát trực tuyến</a>
            </div>
        </div>
        
        <div class="px-4 mb-2">
            <div onclick="toggleMenu('menu-candidate', 'icon-candidate')" class="flex items-center justify-between px-3 py-2 cursor-pointer transition-all duration-200 rounded-lg select-none <?php echo $is_candidate_active ? 'text-white font-medium' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <div class="flex items-center gap-3"><span class="material-icons text-[20px]">people_alt</span> Thí sinh & Làm bài</div>
                <span id="icon-candidate" class="material-icons text-sm transition-transform duration-300 <?php echo $is_candidate_active ? 'rotate-180' : ''; ?>">expand_more</span>
            </div>
            
            <div id="menu-candidate" class="<?php echo $is_candidate_active ? 'block' : 'hidden'; ?> pl-11 pr-3 py-1 mt-1 space-y-1 overflow-hidden transition-all">
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Danh sách thí sinh</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Kết quả & Lời giải</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Quản lý tiến trình</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Thanh điều hướng</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Chế độ ngoại tuyến</a>
            </div>
        </div>

        <div class="px-4 mb-2">
            <div onclick="toggleMenu('menu-report', 'icon-report')" class="flex items-center justify-between px-3 py-2 cursor-pointer transition-all duration-200 rounded-lg select-none <?php echo $is_report_active ? 'text-white font-medium' : 'text-blue-100 hover:text-white hover:bg-white/10'; ?>">
                <div class="flex items-center gap-3"><span class="material-icons text-[20px]">bar_chart</span> Thống kê & Báo cáo</div>
                <span id="icon-report" class="material-icons text-sm transition-transform duration-300 <?php echo $is_report_active ? 'rotate-180' : ''; ?>">expand_more</span>
            </div>
            
            <div id="menu-report" class="<?php echo $is_report_active ? 'block' : 'hidden'; ?> pl-11 pr-3 py-1 mt-1 space-y-1 overflow-hidden transition-all">
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Thống kê kết quả</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Phân tích học thuật</a>
                <a href="#" class="block py-2 px-3 -ml-3 rounded-lg text-sm transition-all text-blue-200 hover:text-white hover:bg-white/10">Xuất báo cáo</a>
            </div>
        </div>
    </nav>

    <div class="p-4 bg-black/10 flex items-center gap-3 border-t border-white/10 shrink-0">
        <div class="w-10 h-10 rounded-full bg-blue-400 flex items-center justify-center font-bold text-white shadow-inner">A</div>
        <div class="flex-1 overflow-hidden">
            <p class="text-sm font-semibold truncate">Admin User</p>
            <p class="text-[11px] text-blue-300 truncate">admin@system.edu.vn</p>
        </div>
        <a href="../login.php" title="Đăng xuất" class="text-blue-300 hover:text-white transition-colors p-1.5 rounded-md hover:bg-white/10">
            <span class="material-icons text-[20px]">login</span>
        </a>
    </div>
</aside>

<script>
    function toggleMenu(menuId, iconId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(iconId);
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            menu.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>