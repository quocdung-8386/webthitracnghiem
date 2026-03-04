<?php
// 1. Cấu hình thông tin trang
$title = "Phân tích Học thuật - Hệ Thống Thi Trực Tuyến";
$active_menu = "academic"; // Bạn nhớ thêm id này vào file sidebar.php nhé

// Dữ liệu mô phỏng Top 10 câu hỏi khó nhất
$top_difficult = [
    ['id' => 'Q-8821', 'desc' => '"Ma trận nghịch đảo bậc n..."', 'rate' => '82%', 'width' => '82%', 'color' => 'bg-red-600', 'text' => 'text-red-600'],
    ['id' => 'Q-7712', 'desc' => '"Tích phân mặt loại 2..."', 'rate' => '75%', 'width' => '75%', 'color' => 'bg-red-500', 'text' => 'text-red-500'],
    ['id' => 'Q-9034', 'desc' => '"Định luật bảo toàn năng lượng..."', 'rate' => '68%', 'width' => '68%', 'color' => 'bg-orange-500', 'text' => 'text-orange-500'],
    ['id' => 'Q-1290', 'desc' => '"Cấu trúc If-Else lồng nhau..."', 'rate' => '62%', 'width' => '62%', 'color' => 'bg-orange-400', 'text' => 'text-orange-500'],
    ['id' => 'Q-5543', 'desc' => '"Thì quá khứ hoàn thành tiếp diễn..."', 'rate' => '55%', 'width' => '55%', 'color' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
];

// Dữ liệu mô phỏng Chi tiết thống kê
$question_stats = [
    [
        'id' => 'Q-2201', 'content' => 'Giải phương trình bậc 2 có tham số m...', 'subject' => 'Toán học - Giải tích - Mức: Khó',
        'count' => '1,240', 'correct' => '18.5%', 'correct_w' => '18.5%', 'c_color' => 'bg-red-500', 'c_text' => 'text-red-600',
        'skip' => '35.2%', 'eval' => 'QUÁ KHÓ', 'eval_bg' => 'bg-red-50 text-red-600'
    ],
    [
        'id' => 'Q-1105', 'content' => 'Lựa chọn từ thích hợp điền vào chỗ trống...', 'subject' => 'Tiếng Anh - Grammar - Mức: TB',
        'count' => '5,420', 'correct' => '72.1%', 'correct_w' => '72.1%', 'c_color' => 'bg-green-500', 'c_text' => 'text-green-600',
        'skip' => '2.5%', 'eval' => 'ỔN ĐỊNH', 'eval_bg' => 'bg-green-50 text-green-600 border border-green-100'
    ],
    [
        'id' => 'Q-3094', 'content' => 'Tính động năng của vật rơi tự do tại...', 'subject' => 'Vật lý - Cơ học - Mức: TB',
        'count' => '850', 'correct' => '54.0%', 'correct_w' => '54.0%', 'c_color' => 'bg-orange-400', 'c_text' => 'text-orange-500',
        'skip' => '12.8%', 'eval' => 'BÌNH THƯỜNG', 'eval_bg' => 'bg-slate-100 text-slate-600'
    ],
    [
        'id' => 'Q-0412', 'content' => 'Câu hỏi trắc nghiệm tâm lý học đại cương...', 'subject' => 'Tâm lý học - Đại cương - Mức: Dễ',
        'count' => '9,812', 'correct' => '92.4%', 'correct_w' => '92.4%', 'c_color' => 'bg-blue-600', 'c_text' => 'text-[#1e3bb3]',
        'skip' => '0.8%', 'eval' => 'QUÁ DỄ', 'eval_bg' => 'bg-blue-50 text-[#1e3bb3]'
    ],
];

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <h2 class="text-lg font-bold text-slate-800 tracking-wide">
            Phân tích Chất lượng Ngân hàng câu hỏi 
            <span class="text-sm font-normal text-slate-400 ml-2 border-l border-slate-300 pl-2">Question Quality Analytics</span>
        </h2>
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm câu hỏi..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3bb3] w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col">
                    <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-[16px] flex items-center gap-2">
                                <span class="material-icons text-orange-500">trending_down</span> Top 10 Câu hỏi gây khó khăn nhất
                            </h3>
                            <p class="text-[12px] text-slate-500 mt-1">Dựa trên tỷ lệ trả lời sai và bỏ qua của thí sinh (Tháng này)</p>
                        </div>
                        <select class="px-3 py-1.5 border border-slate-200 rounded text-xs font-semibold text-slate-600 focus:outline-none">
                            <option>Tất cả môn học</option>
                        </select>
                    </div>

                    <div class="flex-1 space-y-4">
                        <?php foreach($top_difficult as $item): ?>
                        <div>
                            <div class="flex justify-between text-[12px] mb-1.5">
                                <span class="text-slate-700 font-medium">ID: <?php echo $item['id']; ?> - <?php echo $item['desc']; ?></span>
                                <span class="font-bold <?php echo $item['text']; ?>"><?php echo $item['rate']; ?> Sai/Bỏ qua</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full <?php echo $item['color']; ?>" style="width: <?php echo $item['width']; ?>;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="mt-6 text-[13px] font-semibold text-[#1e3bb3] hover:underline mx-auto block">Xem đầy đủ Top 10 câu khó</button>
                </div>

                <div class="lg:col-span-1 space-y-6 flex flex-col">
                    <div class="bg-[#1e3bb3] rounded-xl shadow-md p-6 text-white flex-1 relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                        <h3 class="font-bold text-blue-100 flex items-center gap-2 text-sm mb-4">
                            <span class="material-icons bg-white/20 p-1 rounded-md text-[18px]">health_and_safety</span> Chỉ số sức khỏe
                        </h3>
                        <p class="text-5xl font-black mb-2">84.5%</p>
                        <p class="text-[12px] text-blue-200 leading-relaxed mb-6">Độ tin cậy của ngân hàng câu hỏi hiện tại</p>
                        
                        <div>
                            <div class="flex justify-between text-[11px] font-bold text-blue-100 mb-1">
                                <span>MỤC TIÊU</span>
                                <span>90%</span>
                            </div>
                            <div class="w-full bg-blue-900/50 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full bg-white rounded-full relative" style="width: 84.5%;">
                                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-blue-400 rounded-full blur-[1px]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-red-200 rounded-xl shadow-sm p-6 flex-1 flex flex-col justify-center">
                        <h3 class="font-bold text-red-600 flex items-center gap-2 text-sm mb-2">
                            <span class="material-icons text-[20px]">warning_amber</span> Cần điều chỉnh
                        </h3>
                        <p class="text-4xl font-black text-slate-800 mb-1">42</p>
                        <p class="text-[12px] text-slate-500 leading-relaxed mb-4">Câu hỏi có tỷ lệ đúng < 10% hoặc bỏ qua > 40%</p>
                        <button class="w-full py-2 bg-red-50 text-red-600 hover:bg-red-500 hover:text-white rounded-lg text-[13px] font-bold transition">
                            Kiểm tra ngay
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-[16px]">Chi tiết thống kê từng câu hỏi</h3>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 flex items-center gap-2 transition">
                            <span class="material-icons text-[18px]">filter_list</span> Bộ lọc
                        </button>
                        <button class="px-4 py-2 bg-[#1e3bb3] text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-sm hover:bg-blue-800 transition">
                            <span class="material-icons text-[18px]">download</span> Xuất dữ liệu
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Mã câu hỏi</th>
                                <th class="px-6 py-4 w-1/3">Nội dung tóm tắt</th>
                                <th class="px-6 py-4 text-center">Số lần xuất hiện</th>
                                <th class="px-6 py-4">Tỷ lệ đúng (%)</th>
                                <th class="px-6 py-4 text-center">Tỷ lệ bỏ qua (%)</th>
                                <th class="px-6 py-4 text-center">Đánh giá</th>
                                <th class="px-6 py-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($question_stats as $q): ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-700 text-[12px]"><?php echo $q['id']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-[13px] mb-0.5 truncate w-full max-w-[300px]" title="<?php echo $q['content']; ?>"><?php echo $q['content']; ?></div>
                                    <div class="text-[11px] text-slate-400"><?php echo $q['subject']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo $q['count']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold <?php echo $q['c_text']; ?> text-[13px] w-10"><?php echo $q['correct']; ?></span>
                                        <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full <?php echo $q['c_color']; ?> rounded-full" style="width: <?php echo $q['correct_w']; ?>;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600"><?php echo $q['skip']; ?></td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 <?php echo $q['eval_bg']; ?> text-[10px] font-bold rounded-full uppercase inline-block"><?php echo $q['eval']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-1 text-slate-400">
                                    <button class="hover:text-[#1e3bb3] p-1.5 rounded transition hover:bg-blue-50"><span class="material-icons text-[18px]">visibility</span></button>
                                    <button class="hover:text-slate-700 p-1.5 rounded transition hover:bg-slate-100"><span class="material-icons text-[18px]">edit</span></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500 bg-white rounded-b-xl">
                    <p>Hiển thị 1 - 10 trên tổng số 45,800 câu hỏi</p>
                    <div class="flex items-center gap-1">
                        <button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded hover:bg-slate-50 text-slate-400 transition"><span class="material-icons text-[18px]">chevron_left</span></button>
                        <button class="w-8 h-8 flex items-center justify-center bg-[#1e3bb3] text-white rounded font-medium shadow-sm">1</button>
                        <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded font-medium text-slate-600 transition">2</button>
                        <button class="w-8 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded font-medium text-slate-600 transition">3</button>
                        <span class="text-slate-400 px-1">...</span>
                        <button class="w-10 h-8 flex items-center justify-center bg-white hover:bg-slate-50 rounded font-medium text-slate-600 transition">458</button>
                        <button class="w-8 h-8 flex items-center justify-center border border-slate-200 bg-white rounded hover:bg-slate-50 text-slate-600 transition"><span class="material-icons text-[18px]">chevron_right</span></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>