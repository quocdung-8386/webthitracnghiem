<?php
// 1. Cấu hình thông tin trang
$title = "Chế độ ngoại tuyến - Hệ Thống Thi Trực Tuyến";
$active_menu = "offline"; // Biến này dùng để làm sáng menu "Chế độ ngoại tuyến" trong Sidebar

// Dữ liệu mô phỏng cho bảng Lịch sử đồng bộ
$sync_history = [
    [
        'time' => '14:20 - 24/10', 'student' => 'Nguyễn Văn An', 'id' => 'SV2023001', 
        'exam' => 'Thi cuối kỳ CNTT', 'status' => 'Thành công', 
        'status_bg' => 'bg-green-50', 'status_text' => 'text-green-600', 'icon' => 'visibility'
    ],
    [
        'time' => '14:15 - 24/10', 'student' => 'Trần Thị Hoa', 'id' => 'SV2023042', 
        'exam' => 'Kiểm tra tiếng Anh K19', 'status' => 'Đang xử lý', 
        'status_bg' => 'bg-orange-50', 'status_text' => 'text-orange-500', 'icon' => 'visibility'
    ],
    [
        'time' => '13:45 - 24/10', 'student' => 'Lê Hoàng Minh', 'id' => 'SV2023115', 
        'exam' => 'Kinh tế học đại cương', 'status' => 'Lỗi gói tin', 
        'status_bg' => 'bg-red-50', 'status_text' => 'text-red-500', 'icon' => 'refresh'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">CẤU HÌNH CHẾ ĐỘ NGOẠI TUYẾN</h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm nhanh..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-6xl space-y-6">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Cấu hình chung</h3>
                        <p class="text-[13px] text-slate-500 mt-1">Kích hoạt và thiết lập quyền truy cập ngoại tuyến</p>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase tracking-wider">Sync Active</span>
                </div>

                <div class="space-y-6">
                    <div class="flex justify-between items-start gap-10">
                        <div>
                            <h4 class="font-semibold text-slate-800 text-[14px]">Cho phép thi ngoại tuyến</h4>
                            <p class="text-[13px] text-slate-500 mt-1 leading-relaxed">Khi được bật, thí sinh có thể tải dữ liệu đề thi về máy và làm bài mà không cần kết nối internet liên tục. Kết quả sẽ tự động đồng bộ khi có mạng trở lại.</p>
                        </div>
                        <div class="w-12 h-6 bg-[#1e3bb3] rounded-full relative cursor-pointer shrink-0 transition-colors duration-300">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-1 right-1 shadow transition-transform duration-300"></div>
                        </div>
                    </div>

                    <div class="w-full h-px bg-slate-100"></div>

                    <div class="flex justify-between items-start gap-10">
                        <div>
                            <h4 class="font-semibold text-slate-800 text-[14px]">Yêu cầu xác thực lại khi kết nối</h4>
                            <p class="text-[13px] text-slate-500 mt-1 leading-relaxed">Bắt buộc thí sinh phải đăng nhập lại sau khi hoàn thành bài thi ngoại tuyến để thực hiện bước đồng bộ hóa kết quả cuối cùng.</p>
                        </div>
                        <div class="w-12 h-6 bg-slate-200 rounded-full relative cursor-pointer shrink-0 transition-colors duration-300 hover:bg-slate-300">
                            <div class="w-4 h-4 bg-white rounded-full absolute top-1 left-1 shadow-sm transition-transform duration-300"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                
                <div class="lg:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <div class="mb-5">
                        <h3 class="font-bold text-slate-800 text-[15px]">Gói dữ liệu ngoại tuyến</h3>
                        <p class="text-[13px] text-slate-500 mt-1">Chuẩn bị dữ liệu cho việc sử dụng Offline</p>
                    </div>

                    <div class="flex-1 bg-slate-50 rounded-xl border border-slate-100 p-5 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-3 text-[13px] font-bold text-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="material-icons text-[#1e3bb3] text-[20px]">cloud_download</span>
                                Dữ liệu kỳ thi hiện tại
                            </div>
                            <span class="text-slate-400 font-medium">420 MB</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mb-5">
                            <div class="bg-[#1e3bb3] h-1.5 rounded-full" style="width: 65%"></div>
                        </div>
                        
                        <button class="w-full py-3 bg-[#1e3bb3] text-white rounded-lg font-semibold text-sm hover:bg-blue-800 transition flex items-center justify-center gap-2 shadow-sm mb-4">
                            <span class="material-icons text-[18px]">file_download</span> TẢI GÓI CẬP NHẬT
                        </button>
                        
                        <p class="text-center text-[11px] text-slate-400 italic">Lần cập nhật cuối: 10:45 - 24/10/2023</p>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <div class="mb-5">
                        <h3 class="font-bold text-slate-800 text-[15px]">Trạng thái đồng bộ</h3>
                        <p class="text-[13px] text-slate-500 mt-1">Theo dõi dữ liệu đang chờ xử lý</p>
                    </div>

                    <div class="flex-1 flex flex-col justify-between">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 text-center">
                                <p class="text-[10px] font-bold text-[#1e3bb3] uppercase mb-1">Chờ đồng bộ</p>
                                <p class="text-3xl font-black text-[#1e3bb3]">124</p>
                            </div>
                            <div class="bg-green-50/50 border border-green-100 rounded-xl p-4 text-center">
                                <p class="text-[10px] font-bold text-green-600 uppercase mb-1">Đã hoàn tất</p>
                                <p class="text-3xl font-black text-green-600">8.421</p>
                            </div>
                        </div>

                        <button class="w-full py-3 bg-white border border-slate-200 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-icons text-[18px]">sync</span> ĐỒNG BỘ NGAY BÂY GIỜ
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-[15px]">Lịch sử đồng bộ gần đây</h3>
                    <a href="#" class="text-[13px] text-[#1e3bb3] font-semibold hover:underline">Xem tất cả</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white text-[10px] text-slate-500 uppercase font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Thời gian</th>
                                <th class="px-6 py-4">Thí sinh</th>
                                <th class="px-6 py-4">Kỳ thi</th>
                                <th class="px-6 py-4 text-center">Trạng thái</th>
                                <th class="px-6 py-4 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($sync_history as $history): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium"><?php echo $history['time']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-[13px]"><?php echo $history['student']; ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5"><?php echo $history['id']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-[13px] text-slate-600"><?php echo $history['exam']; ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 text-[10px] font-bold rounded-full inline-block leading-tight <?php echo $history['status_bg']; ?> <?php echo $history['status_text']; ?>">
                                        <?php echo $history['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition flex items-center justify-center mx-auto">
                                        <span class="material-icons text-[18px]"><?php echo $history['icon']; ?></span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>