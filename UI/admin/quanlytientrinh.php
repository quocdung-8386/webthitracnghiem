<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý tiến trình - Hệ Thống Thi Trắc Nghiệm";
$active_menu = "progress"; // Làm sáng menu "Quản lý tiến trình" trong Sidebar

// Include header and sidebar
include 'components/header.php';
include 'components/sidebar.php';

// Get initial data from API for server-side rendering (SEO friendly)
$initialData = [];
$initialStats = [];
$initialClasses = [];

try {
    require_once __DIR__ . '/../../app/config/Database.php';
    $conn = Database::getConnection();
    
    // Get role ID for thi_sinh
    $roleStmt = $conn->prepare("SELECT ma_vai_tro FROM vai_tro WHERE ten_vai_tro = 'thi_sinh' LIMIT 1");
    $roleStmt->execute();
    $roleRow = $roleStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($roleRow) {
        $thiSinhRoleId = $roleRow['ma_vai_tro'];
        
        // Get total exam shifts
        $examShiftStmt = $conn->query("SELECT COUNT(*) as total FROM ca_thi");
        $totalTasks = 15;
        if ($examShiftStmt) {
            $totalTasks = (int)$examShiftStmt->fetch(PDO::FETCH_ASSOC)['total'];
            if ($totalTasks == 0) $totalTasks = 15;
        }
        
        // Get stats
        $statsSql = "SELECT 
                        ROUND(AVG(progress_percent), 1) as avg_progress,
                        ROUND(AVG(avg_score), 1) as avg_score,
                        COUNT(*) as total_students,
                        SUM(CASE WHEN progress_percent >= 70 THEN 1 ELSE 0 END) as completed_count
                     FROM (
                        SELECT 
                            nd.ma_nguoi_dung,
                            (COUNT(bl.ma_bai_lam) / :total_tasks * 100) as progress_percent,
                            AVG(bl.tong_diem) as avg_score
                        FROM nguoi_dung nd
                        LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
                        WHERE nd.ma_vai_tro = :role_id
                        GROUP BY nd.ma_nguoi_dung
                     ) as sub";
        $statsStmt = $conn->prepare($statsSql);
        $statsStmt->execute([':total_tasks' => $totalTasks, ':role_id' => $thiSinhRoleId]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        $initialStats = [
            ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => ($stats['avg_progress'] ?: '0') . '%', 'icon' => 'trending_up', 'color' => 'blue'],
            ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => round(($stats['completed_count'] / max($stats['total_students'], 1)) * 100, 1) . '%', 'icon' => 'check_circle_outline', 'color' => 'green'],
            ['title' => 'ĐIỂM TRUNG BÌNH', 'value' => ($stats['avg_score'] ?: '0'), 'icon' => 'school', 'color' => 'orange'],
            ['title' => 'TỔNG SINH VIÊN', 'value' => number_format($stats['total_students'] ?: 0), 'icon' => 'groups', 'color' => 'purple']
        ];
        
        // Get classes from email domains
        $classStmt = $conn->prepare("SELECT DISTINCT SUBSTRING_INDEX(email, '@', -1) as domain FROM nguoi_dung WHERE ma_vai_tro = :role_id ORDER BY domain");
        $classStmt->execute([':role_id' => $thiSinhRoleId]);
        $initialClasses = $classStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Get initial data (first page)
        $dataSql = "SELECT 
                        nd.ma_nguoi_dung as student_id,
                        nd.ten_dang_nhap as student_code,
                        nd.ho_ten as name,
                        nd.email,
                        COUNT(bl.ma_bai_lam) as completed_tasks,
                        :total_tasks as total_tasks,
                        COALESCE(AVG(bl.tong_diem), 0) as avg_score
                    FROM nguoi_dung nd
                    LEFT JOIN bai_lam bl ON nd.ma_nguoi_dung = bl.ma_nguoi_dung
                    WHERE nd.ma_vai_tro = :role_id
                    GROUP BY nd.ma_nguoi_dung, nd.ten_dang_nhap, nd.ho_ten, nd.email
                    ORDER BY nd.ma_nguoi_dung DESC
                    LIMIT 10";
        $dataStmt = $conn->prepare($dataSql);
        $dataStmt->execute([':total_tasks' => $totalTasks, ':role_id' => $thiSinhRoleId]);
        $results = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $avatarColors = [
            'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400',
            'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-400',
            'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400',
            'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400'
        ];
        $barColors = [
            'bg-blue-600 dark:bg-blue-500',
            'bg-orange-500',
            'bg-green-500 dark:bg-green-400',
            'bg-purple-500 dark:bg-purple-400'
        ];
        $statusStyles = [
            'VƯỢT TIẾN ĐỘ' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400'],
            'HOÀN THÀNH TỐT' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400'],
            'CHẬM TIẾN ĐỘ' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-400'],
            'ĐÚNG LỘ TRÌNH' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400'],
            'ĐANG HỌC' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-400']
        ];
        
        foreach ($results as $index => $row) {
            $nameParts = explode(' ', $row['name']);
            $initials = '';
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= mb_substr($part, 0, 1);
                    if (strlen($initials) >= 2) break;
                }
            }
            $initials = strtoupper($initials);
            
            $completed = (int)$row['completed_tasks'];
            $total = (int)$row['total_tasks'];
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
            
            $status = 'ĐANG HỌC';
            if ($percent >= 80) $status = 'VƯỢT TIẾN ĐỘ';
            elseif ($percent >= 50) $status = 'ĐÚNG LỘ TRÌNH';
            elseif ($percent > 0 && $percent < 50) $status = 'CHẬM TIẾN ĐỘ';
            
            $style = isset($statusStyles[$status]) ? $statusStyles[$status] : $statusStyles['ĐANG HỌC'];
            
            $barColor = $barColors[0];
            if ($percent >= 80) $barColor = $barColors[2];
            elseif ($percent < 50 && $percent > 0) $barColor = $barColors[1];
            
            // Get department and class from email
            $email = $row['email'];
            $department = 'THI_SINH';
            $class = 'Khoa';
            if (strpos($email, '@') !== false) {
                $domain = explode('@', $email)[1];
                $department = strtoupper(explode('.', $domain)[0]);
                $classPart = explode('@', $email)[0];
                $parts = explode('.', $classPart);
                $class = end($parts);
            }
            
            $initialData[] = [
                'id' => htmlspecialchars($row['student_code']),
                'name' => htmlspecialchars($row['name']),
                'avatar' => $initials,
                'avatar_bg' => $avatarColors[$index % count($avatarColors)],
                'dept' => $department,
                'class' => $class,
                'completed' => $completed,
                'total_tasks' => $total,
                'percent' => $percent,
                'bar_color' => $barColor,
                'score' => number_format($row['avg_score'] ?: 0, 1),
                'status' => $status,
                'status_bg' => $style['bg'],
                'status_text' => $style['text']
            ];
        }
    }
    
} catch (Exception $e) {
    // Fallback to empty data if database error
    $initialStats = [
        ['title' => 'TIẾN ĐỘ TRUNG BÌNH', 'value' => '0%', 'icon' => 'trending_up', 'color' => 'blue'],
        ['title' => 'TỶ LỆ HOÀN THÀNH', 'value' => '0%', 'icon' => 'check_circle_outline', 'color' => 'green'],
        ['title' => 'ĐIỂM TRUNG BÌNH', 'value' => '0', 'icon' => 'school', 'color' => 'orange'],
        ['title' => 'TỔNG SINH VIÊN', 'value' => '0', 'icon' => 'groups', 'color' => 'purple']
    ];
}
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
    class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
    
    <!-- Breadcrumb -->
    <div class="text-sm text-slate-500 dark:text-slate-400">
        Thí sinh & Làm bài <span class="mx-2">›</span>
        <span class="text-slate-800 dark:text-white font-medium">
            Quản lý tiến trình học tập
        </span>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-5">

        <!-- Search -->
        <div class="relative hidden md:block">
            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">
                search
            </span>
            <input type="text" placeholder="Tìm kiếm nhanh..."
                class="pl-10 pr-4 py-1.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
        </div>

        <div class="flex items-center gap-4">

            <!-- Notification -->
            <div class="relative">
                <button id="notifButton" type="button"
                    class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
                    <span class="material-icons">notifications</span>
                    <span
                        class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                </button>

                <div id="notifDropdown"
                    class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">

                    <div
                        class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <span class="font-bold text-sm text-slate-800 dark:text-white">
                            Thông báo mới
                        </span>
                        <a href="#"
                            class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">
                            Đánh dấu đã đọc
                        </a>
                    </div>

                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#"
                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug">
                                Hệ thống đang cập nhật dữ liệu tiến trình học tập mới nhất.
                            </p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                                <span class="material-icons text-[12px]">schedule</span>
                                Vừa xong
                            </span>
                        </a>
                    </div>

                    <a href="#"
                        class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Xem tất cả
                    </a>

                </div>
            </div>

            <!-- Dark mode -->
            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>

        </div>
    </div>
</header>
    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" id="statsContainer">
            <?php foreach ($initialStats as $stat): ?>
                <?php
                $bgClass = "bg-{$stat['color']}-50 dark:bg-{$stat['color']}-900/20";
                $textClass = "text-{$stat['color']}-600 dark:text-{$stat['color']}-400";
                ?>
                <div
                    class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5 transition-colors">
                    <div
                        class="w-14 h-14 rounded-full <?php echo $bgClass; ?> <?php echo $textClass; ?> flex items-center justify-center shrink-0">
                        <span class="material-icons text-[28px]"><?php echo $stat['icon']; ?></span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                            <?php echo $stat['title']; ?></p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $stat['value']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col transition-colors">

            <div
                class="p-5 border-b border-slate-100 dark:border-slate-700 flex flex-wrap lg:flex-nowrap justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <div class="relative min-w-[200px]">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">filter_alt</span>
                        <select id="classFilter"
                            class="w-full pl-9 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-300 focus:outline-none focus:border-[#254ada] appearance-none cursor-pointer transition">
                            <option value="all">Tất cả Lớp/Đơn vị</option>
                            <?php foreach ($initialClasses as $class): ?>
                            <option value="<?php echo htmlspecialchars($class); ?>"><?php echo htmlspecialchars($class); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span
                            class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>

                    <div class="relative flex-1 lg:min-w-[300px]">
                        <span
                            class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                        <input type="text" id="searchInput" placeholder="Tìm tên thí sinh, MSSV..."
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-800 dark:text-white focus:outline-none focus:border-[#254ada] transition">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="exportProgress()"
                        class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-white rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-600 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">download</span> Xuất báo cáo
                    </button>
                    <button onclick="sendReminder()"
                        class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] text-white rounded-lg flex items-center gap-2 hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">mail</span> Nhắc nhở thí sinh
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="progressTable">
                    <thead
                        class="bg-slate-50/50 dark:bg-slate-900/30 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-5">Thí sinh</th>
                            <th class="px-6 py-5">Lớp / Ngành</th>
                            <th class="px-6 py-5 text-center">Bài<br>luyện tập</th>
                            <th class="px-6 py-5 w-[25%]">Tiến độ</th>
                            <th class="px-6 py-5 text-center">Điểm<br>trung bình</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-center">Chi<br>tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="tableBody">
                        <?php foreach ($initialData as $row): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition progress-row">

                                <td class="px-6 py-4 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full <?php echo $row['avatar_bg']; ?> flex items-center justify-center font-bold text-[13px] shrink-0 border border-slate-100 dark:border-slate-700">
                                        <?php echo $row['avatar']; ?>
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px] leading-tight">
                                        <span class="p-name"><?php echo str_replace(' ', '<br>', $row['name']); ?></span>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 font-normal mt-1 p-id">
                                            ID: <?php echo $row['id']; ?></div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700 dark:text-slate-300 text-[13px] p-dept">
                                        <?php echo $row['dept']; ?></div>
                                    <div class="text-[12px] text-slate-500 dark:text-slate-400 p-class">
                                        <?php echo $row['class']; ?></div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-0.5">Đã hoàn thành</div>
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px]">
                                        <?php echo $row['completed']; ?>/<?php echo $row['total_tasks']; ?></div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?php echo $row['bar_color']; ?> transition-all duration-1000"
                                                style="width: <?php echo $row['percent']; ?>%"></div>
                                        </div>
                                        <span
                                            class="font-bold text-slate-700 dark:text-slate-300 text-[13px] w-8 text-right"><?php echo $row['percent']; ?>%</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-bold text-slate-800 dark:text-white text-[14px]"><?php echo $row['score']; ?></span><span
                                        class="text-slate-400 dark:text-slate-500 text-[12px]"> / 10</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight <?php echo $row['status_bg']; ?> <?php echo $row['status_text']; ?>">
                                        <?php echo str_replace(' ', '<br>', $row['status']); ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button
                                        onclick="showToast('info', 'Chi tiết học tập', 'Đang mở hồ sơ học tập của <?php echo $row['name']; ?>')"
                                        class="w-8 h-8 rounded-full border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 hover:text-[#1e3bb3] dark:hover:text-white hover:border-[#1e3bb3] dark:hover:border-slate-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition flex items-center justify-center mx-auto"
                                        title="Xem chi tiết">
                                        <span class="material-icons text-[18px]">visibility</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị 1-<?php echo count($initialData); ?> trong số <?php echo count($initialData); ?> sinh viên</p>
                <div id="paginationControls" class="flex items-center gap-2 mt-3 md:mt-0">
                </div>
            </div>
        </div>

    </div>
</main>

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
    /* =================================================================
       API CONFIGURATION
       ================================================================= */
    const API_BASE = '/api';
    let currentPage = 1;
    let rowsPerPage = 10;
    let totalRecords = 0;
    let totalPages = 1;
    let allData = [];
    let currentSearch = '';
    let currentClassFilter = 'all';
    let isLoading = false;

    /* =================================================================
       HÀM HIỂN THỊ THÔNG BÁO (TOAST)
       ================================================================= */
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

    /* =================================================================
       API FUNCTIONS
       ================================================================= */
    async function fetchProgressData() {
        if (isLoading) return;
        isLoading = true;
        
        try {
            const params = new URLSearchParams({
                page: currentPage,
                limit: rowsPerPage,
                search: currentSearch,
                class: currentClassFilter
            });
            
            const response = await fetch(`${API_BASE}/get_progress.php?${params}`);
            const result = await response.json();
            
            if (result.success) {
                allData = result.data;
                totalRecords = result.pagination.total_records;
                totalPages = result.pagination.total_pages;
                
                // Update stats
                updateStats(result.stats);
                
                // Update class filter options
                updateClassFilter(result.classes);
                
                // Render table
                renderTable();
                
                // Update pagination
                updatePagination();
            } else {
                showToast('error', 'Lỗi', result.message || 'Không thể tải dữ liệu');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showToast('error', 'Lỗi kết nối', 'Không thể kết nối với máy chủ');
        } finally {
            isLoading = false;
        }
    }

    function updateStats(stats) {
        const container = document.getElementById('statsContainer');
        if (!container || !stats) return;
        
        const colorClasses = {
            'blue': { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400' },
            'green': { bg: 'bg-green-50 dark:bg-green-900/20', text: 'text-green-600 dark:text-green-400' },
            'orange': { bg: 'bg-orange-50 dark:bg-orange-900/20', text: 'text-orange-600 dark:text-orange-400' },
            'purple': { bg: 'bg-purple-50 dark:bg-purple-900/20', text: 'text-purple-600 dark:text-purple-400' }
        };
        
        let html = '';
        stats.forEach(stat => {
            const colors = colorClasses[stat.color] || colorClasses.blue;
            html += `
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-5 transition-colors">
                    <div class="w-14 h-14 rounded-full ${colors.bg} ${colors.text} flex items-center justify-center shrink-0">
                        <span class="material-icons text-[28px]">${stat.icon}</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">${stat.title}</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white">${stat.value}</p>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function updateClassFilter(classes) {
        const select = document.getElementById('classFilter');
        if (!select) return;
        
        // Keep first option (All)
        const firstOption = select.options[0];
        select.innerHTML = '';
        select.appendChild(firstOption);
        
        // Add classes from API
        classes.forEach(cls => {
            const option = document.createElement('option');
            option.value = cls;
            option.textContent = cls;
            select.appendChild(option);
        });
        
        // Restore selected value
        select.value = currentClassFilter;
    }

    function renderTable() {
        const tbody = document.getElementById('tableBody');
        if (!tbody) return;
        
        if (allData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <span class="material-icons text-4xl mb-2">search_off</span>
                        <p>Không tìm thấy dữ liệu</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        allData.forEach(row => {
            html += `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition progress-row">
                    <td class="px-6 py-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full ${row.avatar_bg} flex items-center justify-center font-bold text-[13px] shrink-0 border border-slate-100 dark:border-slate-700">
                            ${row.avatar}
                        </div>
                        <div class="font-bold text-slate-800 dark:text-white text-[14px] leading-tight">
                            <span class="p-name">${row.name.replace(/ /g, '<br>')}</span>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 font-normal mt-1 p-id">ID: ${row.id}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-700 dark:text-slate-300 text-[13px] p-dept">${row.dept}</div>
                        <div class="text-[12px] text-slate-500 dark:text-slate-400 p-class">${row.class}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-0.5">Đã hoàn thành</div>
                        <div class="font-bold text-slate-800 dark:text-white text-[14px]">${row.completed}/${row.total_tasks}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full ${row.bar_color} transition-all duration-1000" style="width: ${row.percent}%"></div>
                            </div>
                            <span class="font-bold text-slate-700 dark:text-slate-300 text-[13px] w-8 text-right">${row.percent}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-slate-800 dark:text-white text-[14px]">${row.score}</span><span class="text-slate-400 dark:text-slate-500 text-[12px]"> / 10</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight ${row.status_bg} ${row.status_text}">
                            ${row.status.replace(/ /g, '<br>')}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="showToast('info', 'Chi tiết học tập', 'Đang mở hồ sơ học tập của ${row.name}')"
                            class="w-8 h-8 rounded-full border border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 hover:text-[#1e3bb3] dark:hover:text-white hover:border-[#1e3bb3] dark:hover:border-slate-400 hover:bg-blue-50 dark:hover:bg-slate-700 transition flex items-center justify-center mx-auto"
                            title="Xem chi tiết">
                            <span class="material-icons text-[18px]">visibility</span>
                        </button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function updatePagination() {
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        
        if (paginationInfo) {
            const start = totalRecords === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
            const end = Math.min(currentPage * rowsPerPage, totalRecords);
            paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${start}-${end}</span> trong số <span class="font-medium text-slate-800 dark:text-white">${totalRecords}</span> sinh viên`;
        }
        
        if (paginationControls) {
            paginationControls.innerHTML = '';
            
            // Prev Button
            const prevBtn = document.createElement('button');
            prevBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
            prevBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_left</span>';
            prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; fetchProgressData(); } };
            paginationControls.appendChild(prevBtn);
            
            // Page Numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement('button');
                if (i === currentPage) {
                    pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md bg-[#254ada] text-white font-bold text-xs shadow-sm';
                } else {
                    pageBtn.className = 'w-8 h-8 flex items-center justify-center rounded-md border border-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium text-xs';
                }
                pageBtn.innerText = i;
                pageBtn.onclick = () => { currentPage = i; fetchProgressData(); };
                paginationControls.appendChild(pageBtn);
            }
            
            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.className = `w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 transition ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'}`;
            nextBtn.innerHTML = '<span class="material-icons text-[18px]">chevron_right</span>';
            nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; fetchProgressData(); } };
            paginationControls.appendChild(nextBtn);
        }
    }

    /* =================================================================
       EXPORT & REMINDER FUNCTIONS
       ================================================================= */
    function exportProgress() {
        showToast('info', 'Xuất báo cáo', 'Hệ thống đang chuẩn bị dữ liệu xuất Excel...');
        
        const params = new URLSearchParams({
            search: currentSearch,
            class: currentClassFilter
        });
        
        // Open export in new tab
        setTimeout(() => {
            window.open(`${API_BASE}/export_progress.php?${params}`, '_blank');
            showToast('success', 'Xuất báo cáo', 'Đã bắt đầu tải file Excel');
        }, 500);
    }

    async function sendReminder() {
        showToast('info', 'Gửi nhắc nhở', 'Đang gửi email nhắc nhở...');
        
        try {
            const response = await fetch(`${API_BASE}/send_reminder.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Đã gửi thông báo', result.message);
            } else {
                showToast('error', 'Lỗi gửi nhắc nhở', result.message);
            }
        } catch (error) {
            console.error('Send reminder error:', error);
            showToast('error', 'Lỗi kết nối', 'Không thể gửi nhắc nhở');
        }
    }

    /* =================================================================
       SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
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

        // 2. Chức năng Dropdown Thông báo
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

        // 3. Search and Filter with debounce
        const searchInput = document.getElementById('searchInput');
        const classFilter = document.getElementById('classFilter');
        
        let searchTimeout;
        
        searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = e.target.value;
                currentPage = 1;
                fetchProgressData();
            }, 300);
        });
        
        classFilter?.addEventListener('change', (e) => {
            currentClassFilter = e.target.value;
            currentPage = 1;
            fetchProgressData();
        });

        // 4. Initial data fetch from API
        fetchProgressData();
    });
</script>

