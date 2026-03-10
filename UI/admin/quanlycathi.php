<?php
// 1. Cấu hình thông tin trang
$title = "Quản lý Ca thi - Hệ Thống Thi Trực Tuyến";
$active_menu = "shift_exam"; // Làm sáng menu "Quản lý ca thi" trong Sidebar

// Dữ liệu mô phỏng cho Bảng Ca thi (Đã bổ sung class Dark Mode vào badge/avatar)
$shifts = [
    [
        'name' => 'Ca sáng - Đợt 1',
        'id' => 'SH-001',
        'time' => '07:30 - 09:30',
        'date' => '20/12/2023',
        'location_icon' => 'business',
        'location' => 'Phòng Lab 402',
        'students_assigned' => 50,
        'students_total' => 50,
        'avatars' => ['SV', 'SV', '+48'],
        'avatar_bg' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-300',
        'status' => 'ĐANG THI',
        'status_bg' => 'bg-green-100 dark:bg-green-900/30',
        'status_text' => 'text-green-700 dark:text-green-400',
        'is_ended' => false
    ],
    [
        'name' => 'Ca sáng - Đợt 2',
        'id' => 'SH-002',
        'time' => '10:00 - 12:00',
        'date' => '20/12/2023',
        'location_icon' => 'business',
        'location' => 'Phòng Lab 402',
        'students_assigned' => 0,
        'students_total' => 50,
        'avatars' => ['?'],
        'avatar_bg' => 'bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300',
        'status' => 'SẮP TỚI',
        'status_bg' => 'bg-blue-50 dark:bg-blue-900/30',
        'status_text' => 'text-blue-600 dark:text-blue-400',
        'is_ended' => false
    ],
    [
        'name' => 'Ca chiều (Online)',
        'id' => 'SH-003',
        'time' => '13:30 - 15:30',
        'date' => '20/12/2023',
        'location_icon' => 'link',
        'location' => 'exam.edu.vn/online/...',
        'students_assigned' => 200,
        'students_total' => 200,
        'avatars' => ['SV', '+199'],
        'avatar_bg' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/50 dark:text-orange-300',
        'status' => 'SẮP TỚI',
        'status_bg' => 'bg-blue-50 dark:bg-blue-900/30',
        'status_text' => 'text-blue-600 dark:text-blue-400',
        'is_ended' => false
    ],
    [
        'name' => 'Ca tối (Dự phòng)',
        'id' => 'SH-004',
        'time' => '18:00 - 20:00',
        'date' => '19/12/2023',
        'location_icon' => 'business',
        'location' => 'Phòng B.301',
        'students_assigned' => 120,
        'students_total' => 120,
        'avatars' => [],
        'avatar_bg' => '',
        'status' => 'ĐÃ KẾT THÚC',
        'status_bg' => 'bg-slate-100 dark:bg-slate-700',
        'status_text' => 'text-slate-600 dark:text-slate-400',
        'is_ended' => true
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main
    class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
    <header
        class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Kỳ thi & Đề thi<span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Quản lý Ca thi và Phân bổ thí sinh</span>
        </div>

        <div class="flex items-center gap-5">
            <div class="relative">
                <span
                    class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" id="searchInput" placeholder="Tìm kiếm ca thi..."
                    class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-[#254ada] w-64 transition">
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
                        <a href="#"
                            class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu
                            đã đọc</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#"
                            class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span
                                    class="font-semibold text-slate-800 dark:text-white">Ca sáng - Đợt 1</span> đã bắt
                                đầu tính thời gian làm bài.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span
                                    class="material-icons text-[12px]">schedule</span> 5 phút trước</span>
                        </a>
                    </div>
                    <a href="#"
                        class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">Xem
                        tất cả</a>
                </div>
            </div>

            <button id="darkModeToggle"
                class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar transition-colors duration-200">

        <div class="mb-6">
            <div class="text-[13px] text-slate-500 dark:text-slate-400 mb-2">
                Kỳ thi & Đề thi <span class="mx-2">›</span> <span
                    class="text-slate-800 dark:text-white font-medium">Toán Cao Cấp A1 - Học kỳ 1 2023</span>
            </div>
            <div class="flex justify-between items-end">
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[28px]">calendar_month</span>
                    Danh sách các ca thi
                </h2>
                <div class="flex gap-3">
                    <button onclick="openModal('assignStudentsModal')"
                        class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">person_add_alt_1</span> Gán thí sinh hàng loạt
                    </button>
                    <button onclick="openModal('addShiftModal')"
                        class="px-5 py-2.5 bg-[#254ada] dark:bg-[#4b6bfb] hover:bg-[#1e3bb3] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 text-sm font-medium shadow-sm transition">
                        <span class="material-icons text-[20px]">add</span> Thêm ca thi
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div
                class="bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800/50 text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">event_note</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-0.5">
                        Tổng số ca thi</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white">08</p>
                </div>
            </div>

            <div
                class="bg-green-50/50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/50 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">how_to_reg</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-green-600 dark:text-green-400 uppercase tracking-wide mb-0.5">
                        Thí sinh đã gán</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white">1,215 <span
                            class="text-lg text-slate-400 dark:text-slate-500 font-semibold">/ 1,240</span></p>
                </div>
            </div>

            <div
                class="bg-orange-50/50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800/50 rounded-xl p-5 flex items-center gap-4 transition-colors">
                <div
                    class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-800/50 text-orange-500 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <span class="material-icons text-[24px]">warning</span>
                </div>
                <div>
                    <p
                        class="text-[11px] font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wide mb-0.5">
                        Thí sinh chưa gán</p>
                    <p class="text-3xl font-black text-slate-800 dark:text-white">25</p>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6 transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="shiftsTable">
                    <thead
                        class="bg-white dark:bg-slate-800 text-[11px] text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-100 dark:border-slate-700 transition-colors">
                        <tr>
                            <th class="px-6 py-5 w-14 text-center">
                                <input type="checkbox" id="selectAllBtn"
                                    class="w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                            </th>
                            <th class="px-6 py-5">Tên ca thi</th>
                            <th class="px-6 py-5">Thời gian</th>
                            <th class="px-6 py-5">Địa điểm / Link</th>
                            <th class="px-6 py-5 text-center">Thí sinh</th>
                            <th class="px-6 py-5 text-center">Trạng thái</th>
                            <th class="px-6 py-5 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700" id="shiftsTableBody">
                        <?php foreach ($shifts as $shift): ?>
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/50 transition shift-row">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox w-4 h-4 text-[#254ada] rounded border-slate-300 dark:border-slate-600 dark:bg-slate-700 focus:ring-[#254ada] cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white text-[14px] shift-name">
                                        <?php echo $shift['name']; ?>
                                    </div>
                                    <div class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5 shift-id">ID:
                                        <?php echo $shift['id']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700 dark:text-slate-300">
                                        <?php echo $shift['time']; ?>
                                    </div>
                                    <div class="text-[12px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        <?php echo $shift['date']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium shift-location">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="material-icons text-[18px] text-slate-400 dark:text-slate-500"><?php echo $shift['location_icon']; ?></span>
                                        <?php echo $shift['location']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if (count($shift['avatars']) > 0): ?>
                                        <div class="flex justify-center -space-x-2 mb-1">
                                            <?php foreach ($shift['avatars'] as $av): ?>
                                                <div
                                                    class="w-6 h-6 rounded-full <?php echo $shift['avatar_bg']; ?> border-2 border-white dark:border-slate-800 flex items-center justify-center text-[8px] font-bold z-10">
                                                    <?php echo $av; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div
                                        class="text-[12px] <?php echo ($shift['students_assigned'] == 0) ? 'text-orange-500 dark:text-orange-400 font-bold' : 'text-[#1e3bb3] dark:text-[#4b6bfb] font-semibold'; ?>">
                                        <?php echo $shift['students_assigned']; ?>/<?php echo $shift['students_total']; ?>
                                        thí sinh
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-full <?php echo $shift['status_bg']; ?> <?php echo $shift['status_text']; ?> uppercase inline-block leading-tight text-center">
                                        <?php echo str_replace(' ', '<br>', $shift['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 text-slate-400 dark:text-slate-500">
                                    <?php if (!$shift['is_ended']): ?>
                                        <button
                                            onclick="showToast('info', 'Thêm thí sinh', 'Mở bảng thêm thí sinh cho <?php echo $shift['name']; ?>')"
                                            class="hover:text-[#1e3bb3] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"
                                            title="Thêm thí sinh"><span
                                                class="material-icons text-[18px]">person_add</span></button>
                                    <?php else: ?>
                                        <button
                                            onclick="showToast('success', 'Thống kê điểm', 'Mở báo cáo điểm cho <?php echo $shift['name']; ?>')"
                                            class="hover:text-[#1e3bb3] dark:hover:text-[#4b6bfb] p-1.5 transition rounded-md hover:bg-blue-50 dark:hover:bg-slate-700"
                                            title="Xem thống kê điểm"><span
                                                class="material-icons text-[18px]">insert_chart_outlined</span></button>
                                    <?php endif; ?>
                                    <button onclick="showToast('info', 'Chỉnh sửa', 'Chỉnh sửa thông tin ca thi')"
                                        class="hover:text-slate-700 dark:hover:text-white p-1.5 transition rounded-md hover:bg-slate-100 dark:hover:bg-slate-700"
                                        title="Chỉnh sửa"><span class="material-icons text-[18px]">edit</span></button>
                                    <button onclick="showToast('error', 'Xóa ca thi', 'Đã xóa ca thi khỏi hệ thống')"
                                        class="hover:text-red-500 dark:hover:text-red-400 p-1.5 transition rounded-md hover:bg-red-50 dark:hover:bg-slate-700"
                                        title="Xóa"><span class="material-icons text-[18px]">delete</span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div
                class="p-4 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 rounded-b-xl transition-colors">
                <p id="paginationInfo">Hiển thị 1-4 trên tổng số 8 ca thi</p>
                <div id="paginationControls" class="flex items-center gap-2">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="bg-blue-50/30 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/50 rounded-xl p-6 transition-colors">
                <h3 class="font-bold text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center gap-2 mb-4">
                    <span class="material-icons text-[20px]">info</span> Hướng dẫn phân bổ
                </h3>
                <ul class="space-y-3 text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span
                            class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Sử dụng <b>Gán thí sinh hàng loạt</b> để tự động chia thí sinh vào các ca thi theo bảng
                            chữ cái hoặc ngẫu nhiên.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span
                            class="material-icons text-[#1e3bb3] dark:text-[#4b6bfb] text-[18px] shrink-0 mt-0.5">check_circle</span>
                        <span>Mỗi ca thi có thể cấu hình giới hạn số lượng thí sinh hoặc gán theo phòng học cụ
                            thể.</span>
                    </li>
                </ul>
            </div>

            <div onclick="showToast('info', 'Tải file', 'Mở popup upload file Excel danh sách thí sinh')"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-6 flex flex-col items-center justify-center text-center shadow-sm cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-500 transition group">
                <div
                    class="w-12 h-12 rounded-full bg-slate-800 dark:bg-slate-700 text-white flex items-center justify-center mb-3 group-hover:-translate-y-1 transition-transform shadow-md">
                    <span class="material-icons text-[24px]">upload_file</span>
                </div>
                <h3 class="font-bold text-slate-800 dark:text-white mb-1">Nhập danh sách từ Excel</h3>
                <p class="text-[13px] text-slate-500 dark:text-slate-400">Tải lên file danh sách phân bổ ca thi theo
                    định dạng mẫu của hệ thống.</p>
            </div>
        </div>

    </div>
</main>

<div id="addShiftModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">add_task</span> Thêm ca thi mới
            </h3>
            <button type="button" onclick="closeModal('addShiftModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>
        <form onsubmit="event.preventDefault(); submitAddShift();" class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tên ca thi
                    <span class="text-red-500">*</span></label>
                <input type="text" placeholder="VD: Ca sáng - Đợt 3" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ngày thi
                        <span class="text-red-500">*</span></label>
                    <input type="date" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Giờ bắt đầu
                        <span class="text-red-500">*</span></label>
                    <input type="time" required
                        class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Địa điểm /
                    Phòng máy <span class="text-red-500">*</span></label>
                <input type="text" placeholder="VD: Phòng Lab 405" required
                    class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-[#254ada] focus:outline-none">
            </div>

            <div class="flex justify-end gap-3 pt-5 mt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('addShiftModal')"
                    class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                    bỏ</button>
                <button type="submit" id="btnSubmitShift"
                    class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2">Tạo
                    ca thi</button>
            </div>
        </form>
    </div>
</div>
<div id="assignStudentsModal"
    class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700">

        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">person_add_alt_1</span> Gán thí sinh tự
                động
            </h3>
            <button type="button" onclick="closeModal('assignStudentsModal')"
                class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span
                    class="material-icons">close</span></button>
        </div>

        <form onsubmit="event.preventDefault(); submitAssignStudents();"
            class="flex-1 overflow-y-auto custom-scrollbar p-6">

            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-xl p-4 flex items-center justify-between mb-6 transition-colors">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-800/50 text-[#1e3bb3] dark:text-[#4b6bfb] flex items-center justify-center">
                        <span class="material-icons">groups</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white">Thí sinh chờ phân bổ</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-0.5">Thuộc danh sách thi: Toán Cao
                            Cấp A1</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-[#254ada] dark:text-[#4b6bfb]">25</span>
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">thí sinh</span>
                </div>
            </div>

            <h4 class="text-[13px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide mb-3">1. Phương
                thức phân bổ</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <label
                    class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-[#254ada] dark:hover:border-[#4b6bfb] hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors has-[:checked]:border-[#254ada] has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:border-[#4b6bfb]">
                    <div class="mt-0.5">
                        <input type="radio" name="allocation_method" value="random" checked
                            class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Chia ngẫu nhiên</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Hệ thống sẽ bốc thăm và chia đều
                            thí sinh vào các ca thi đã chọn.</p>
                    </div>
                </label>

                <label
                    class="flex items-start gap-3 p-4 border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer hover:border-[#254ada] dark:hover:border-[#4b6bfb] hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors has-[:checked]:border-[#254ada] has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-blue-900/20 dark:has-[:checked]:border-[#4b6bfb]">
                    <div class="mt-0.5">
                        <input type="radio" name="allocation_method" value="alpha"
                            class="w-4 h-4 text-[#254ada] border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight">Theo bảng chữ cái
                            (A-Z)</p>
                        <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1">Ưu tiên điền đầy ca thi theo thứ
                            tự chữ cái tên thí sinh.</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-between items-end mb-3">
                <h4 class="text-[13px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">2. Chọn ca
                    thi áp dụng</h4>
                <label
                    class="flex items-center gap-1.5 cursor-pointer text-[12px] font-medium text-[#254ada] dark:text-[#4b6bfb] hover:underline">
                    <input type="checkbox" checked
                        class="w-3.5 h-3.5 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada]">
                    Chọn tất cả ca chưa đầy
                </label>
            </div>

            <div class="space-y-2.5 mb-6 max-h-[160px] overflow-y-auto custom-scrollbar pr-2">
                <label
                    class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 rounded-lg cursor-not-allowed opacity-60">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" disabled class="w-4 h-4 text-slate-300 border-slate-200 rounded">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Ca sáng - Đợt 1</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">07:30 - 09:30 | Phòng Lab 402</p>
                        </div>
                    </div>
                    <span class="text-[12px] font-bold text-red-500">Đã đầy (50/50)</span>
                </label>

                <label
                    class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-600 hover:border-[#254ada] rounded-lg cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" checked
                            class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Ca sáng - Đợt 2</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">10:00 - 12:00 | Phòng Lab
                                402</p>
                        </div>
                    </div>
                    <span class="text-[12px] font-bold text-green-600 dark:text-green-400">Trống (0/50)</span>
                </label>

                <label
                    class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-600 hover:border-[#254ada] rounded-lg cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" checked
                            class="w-4 h-4 text-[#254ada] rounded border-slate-300 focus:ring-[#254ada] dark:border-slate-600 dark:bg-slate-700">
                        <div>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Ca chiều (Online)</p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">13:30 - 15:30 | Mạng trực
                                tuyến</p>
                        </div>
                    </div>
                    <span class="text-[12px] font-bold text-green-600 dark:text-green-400">Trống (200/250)</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('assignStudentsModal')"
                    class="px-5 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy
                    bỏ</button>
                <button type="submit" id="btnSubmitAssign"
                    class="px-6 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-md">
                    <span class="material-icons text-[18px]">bolt</span> Tiến hành phân bổ
                </button>
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
    /* =================================================================
       HÀM HIỂN THỊ THÔNG BÁO (TOAST) & MODAL
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

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    function submitAddShift() {
        const btn = document.getElementById('btnSubmitShift');
        const originalText = btn.innerHTML;

        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang lưu...';
        btn.disabled = true;
        btn.classList.add('opacity-70');

        setTimeout(() => {
            closeModal('addShiftModal');
            showToast('success', 'Thêm thành công', 'Ca thi mới đã được tạo.');

            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70');
        }, 1000);
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

        // 3. Chức năng Checkbox All
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

        // 4. Chức năng Tìm kiếm & Phân trang
        const rowsPerPage = 3;
        let currentPage = 1;
        let filteredRows = [];

        const allRows = Array.from(document.querySelectorAll('.shift-row'));
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const searchInput = document.getElementById('searchInput');

        function updatePagination() {
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            const displayStart = totalRows === 0 ? 0 : start + 1;
            const displayEnd = Math.min(end, totalRows);
            if (paginationInfo) {
                paginationInfo.innerHTML = `Hiển thị <span class="font-medium text-slate-800 dark:text-white">${displayStart}-${displayEnd}</span> trên tổng số <span class="font-medium text-slate-800 dark:text-white">${totalRows}</span> ca thi`;
            }

            if (paginationControls) {
                paginationControls.innerHTML = '';

                // Nút Trước
                const prevBtn = document.createElement('button');
                prevBtn.className = `px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition font-medium ${currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
                prevBtn.innerText = 'Trước';
                prevBtn.onclick = () => { if (currentPage > 1) { currentPage--; updatePagination(); } };
                paginationControls.appendChild(prevBtn);

                // Nút Số
                for (let i = 1; i <= totalPages; i++) {
                    const pageBtn = document.createElement('button');
                    if (i === currentPage) {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-[#254ada] text-white rounded-md font-medium shadow-sm';
                    } else {
                        pageBtn.className = 'w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-md text-slate-600 dark:text-slate-300 transition';
                    }
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => { currentPage = i; updatePagination(); };
                    paginationControls.appendChild(pageBtn);
                }

                // Nút Sau
                const nextBtn = document.createElement('button');
                nextBtn.className = `px-3 py-1.5 border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-md transition font-medium ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed text-slate-300 dark:text-slate-500' : 'hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300'}`;
                nextBtn.innerText = 'Sau';
                nextBtn.onclick = () => { if (currentPage < totalPages) { currentPage++; updatePagination(); } };
                paginationControls.appendChild(nextBtn);
            }
        }

        // Bắt sự kiện Tìm kiếm
        searchInput?.addEventListener('input', function (e) {
            const text = e.target.value.toLowerCase();
            filteredRows = allRows.filter(row => {
                const name = row.querySelector('.shift-name').textContent.toLowerCase();
                const id = row.querySelector('.shift-id').textContent.toLowerCase();
                const loc = row.querySelector('.shift-location').textContent.toLowerCase();
                return name.includes(text) || id.includes(text) || loc.includes(text);
            });
            currentPage = 1;
            updatePagination();
        });

        filteredRows = [...allRows];
        updatePagination();
    });

    // Hàm xử lý khi bấm "Tiến hành phân bổ"
    function submitAssignStudents() {
        const btn = document.getElementById('btnSubmitAssign');
        const originalText = btn.innerHTML;

        // Đổi nút thành trạng thái Đang xử lý
        btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang chia phòng...';
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');

        // Giả lập thời gian server chạy mất 1.5 giây
        setTimeout(() => {
            closeModal('assignStudentsModal');
            // Hiện thông báo thành công
            showToast('success', 'Phân bổ thành công', 'Đã gán thành công 25 thí sinh vào các ca thi được chọn.');

            // Khôi phục nút
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
        }, 1500);
    }

</script>