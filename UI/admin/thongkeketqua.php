<?php
// 1. Cấu hình thông tin trang
$title = "Thống kê tổng quan - Hệ Thống Thi Trực Tuyến";
$active_menu = "stat_result"; // Biến active menu ở thanh sidebar

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">TỔNG QUAN THỐNG KÊ HỆ THỐNG</h2>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase">Báo cáo thời gian thực</span>
        </div>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm dữ liệu..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <span class="material-icons text-[20px]">library_books</span>
                        </div>
                        <span class="px-2.5 py-1 bg-green-50 text-green-600 text-[11px] font-bold rounded-md">Tháng này: +15%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Tổng lượt thi</p>
                    <p class="text-3xl font-black text-slate-800 mb-2">48,250</p>
                    <p class="text-[11px] text-slate-400">Dữ liệu tính từ đầu năm 2024</p>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center">
                            <span class="material-icons text-[20px]">calculate</span>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[11px] font-bold rounded-md uppercase">Hệ 10</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Điểm trung bình</p>
                    <p class="text-3xl font-black text-slate-800 mb-2">7.42</p>
                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-orange-500 rounded-full" style="width: 74.2%"></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                            <span class="material-icons text-[20px]">check_circle</span>
                        </div>
                        <span class="px-2.5 py-1 bg-green-50 text-green-600 text-[11px] font-bold rounded-md">Tăng 2%</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Tỷ lệ đỗ</p>
                    <p class="text-3xl font-black text-slate-800 mb-2">82.5%</p>
                    <p class="text-[11px] text-slate-400">Dựa trên 5.0 điểm liệt</p>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 border-l-4 border-l-red-500">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center">
                            <span class="material-icons text-[20px]">warning_amber</span>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-500 mb-1">Câu hỏi khó nhất</p>
                    <p class="text-2xl font-black text-slate-800 mb-2">ID: #Q-9942</p>
                    <p class="text-[12px] text-slate-500">Tỷ lệ trả lời sai: <span class="font-bold text-red-500">88%</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="font-bold text-slate-800 text-[16px]">Xu hướng thí sinh tham gia</h3>
                            <p class="text-[12px] text-slate-500">Thống kê theo từng tháng trong năm 2024</p>
                        </div>
                        <button class="px-3 py-1.5 border border-slate-200 rounded text-xs font-semibold text-slate-600">2024</button>
                    </div>

                    <div class="flex-1 relative mt-4">
                        <div class="absolute inset-0 flex flex-col justify-between text-[10px] text-slate-400 pb-6">
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">10k</span><div class="flex-1 border-t border-slate-100"></div></div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">7.5k</span><div class="flex-1 border-t border-slate-100"></div></div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">5k</span><div class="flex-1 border-t border-slate-100"></div></div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">2.5k</span><div class="flex-1 border-t border-slate-100"></div></div>
                            <div class="flex items-center gap-4 w-full"><span class="w-6 text-right">0</span><div class="flex-1 border-t border-slate-200"></div></div>
                        </div>
                        
                        <div class="absolute inset-0 flex justify-between items-end pl-10 pr-4 pb-6 pt-2">
                            <?php 
                            $chart_data = [
                                ['label' => 'T.1', 'height' => '30%'],
                                ['label' => 'T.2', 'height' => '45%'],
                                ['label' => 'T.3', 'height' => '35%'],
                                ['label' => 'T.4', 'height' => '65%'],
                                ['label' => 'T.5', 'height' => '85%'],
                                ['label' => 'T.6', 'height' => '40%']
                            ];
                            foreach($chart_data as $data): ?>
                            <div class="flex flex-col items-center h-full justify-end w-10 relative">
                                <div class="w-0.5 bg-blue-100 relative" style="height: <?php echo $data['height']; ?>;">
                                    <div class="absolute -top-1 -left-1 w-2.5 h-2.5 bg-[#1e3bb3] rounded-full shadow border-2 border-white"></div>
                                </div>
                                <span class="absolute -bottom-6 text-[11px] font-semibold text-slate-400"><?php echo $data['label']; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <h3 class="font-bold text-slate-800 text-[16px] mb-8">Tỷ lệ xếp loại kết quả</h3>
                    
                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="w-48 h-48 rounded-full relative flex items-center justify-center mb-8 shadow-sm" 
                             style="background: conic-gradient(#3b82f6 0% 25%, #22c55e 25% 65%, #f97316 65% 85%, #ef4444 85% 100%);">
                            <div class="w-36 h-36 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
                                <span class="text-4xl font-black text-slate-800 leading-none">85%</span>
                                <span class="text-[9px] font-bold text-slate-400 mt-1">TRÊN TRUNG BÌNH</span>
                            </div>
                        </div>

                        <div class="w-full space-y-3 px-2 text-[13px] font-medium text-slate-600">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Giỏi</div>
                                <span class="font-bold text-slate-800">25%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span> Khá</div>
                                <span class="font-bold text-slate-800">40%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-500"></span> Trung bình</div>
                                <span class="font-bold text-slate-800">20%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span> Yếu</div>
                                <span class="font-bold text-slate-800">15%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-[16px]">Dữ liệu chi tiết theo Kỳ thi gần đây</h3>
                    <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center gap-2 transition">
                        <span class="material-icons text-[18px]">download</span> Tải báo cáo
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Tên kỳ thi</th>
                                <th class="px-6 py-4 text-center">Tổng lượt thi</th>
                                <th class="px-6 py-4 text-center">Điểm TB</th>
                                <th class="px-6 py-4 w-[25%]">Tỷ lệ đỗ</th>
                                <th class="px-6 py-4 text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-800 text-[13px]">Kỳ thi Tiếng Anh Chuyên ngành B1</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600">1,250</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">8.2</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500 rounded-full" style="width: 92%"></div>
                                        </div>
                                        <span class="text-[12px] font-bold text-slate-600">92%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-[#1e3bb3] transition"><span class="material-icons text-[20px]">visibility</span></button>
                                </td>
                            </tr>
                            
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-800 text-[13px]">Lý thuyết Lập trình C++ nâng cao</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600">840</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">6.5</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full" style="width: 75%"></div>
                                        </div>
                                        <span class="text-[12px] font-bold text-slate-600">75%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-slate-400 hover:text-[#1e3bb3] transition"><span class="material-icons text-[20px]">visibility</span></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>