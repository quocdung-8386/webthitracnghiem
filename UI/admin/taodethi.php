<?php
// 1. Cấu hình thông tin trang
$title = "Tạo đề thi mới - Hệ Thống Thi Trực Tuyến";
$active_menu = "create_exam"; // Biến này sẽ làm sáng menu "Tạo đề thi" trong Sidebar

// Nhúng Header và Sidebar
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
    <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between z-10 shrink-0">
        <div class="flex items-center gap-2">
            <a href="danhsachdethi.php" class="text-slate-400 hover:text-slate-600 transition p-1 rounded-full hover:bg-slate-100 flex items-center justify-center">
                <span class="material-icons">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Tạo đề thi mới</h2>
        </div>
        <div class="flex items-center gap-4">
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3] transition"><span class="material-icons">dark_mode</span></button>
            <button class="px-5 py-2 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                <span class="material-icons text-[18px]">save</span> Lưu đề thi
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
        <div class="max-w-5xl mx-auto space-y-6">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 flex items-center gap-2 mb-6">
                    <span class="material-icons text-slate-400 text-[20px]">info</span> Thông tin cơ bản
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-5">
                    <div class="md:col-span-2">
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tên đề thi <span class="text-red-500">*</span></label>
                        <input type="text" placeholder="Nhập tên đề thi (vd: Đề thi giữa kỳ môn Toán)" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] focus:ring-1 focus:ring-[#1e3bb3] transition">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Môn học <span class="text-red-500">*</span></label>
                        <select class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] text-slate-600 transition">
                            <option value="">Chọn môn học</option>
                            <option value="toan">Toán học</option>
                            <option value="ly">Vật lý</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Thời gian (phút) <span class="text-red-500">*</span></label>
                        <input type="number" value="60" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] focus:ring-1 focus:ring-[#1e3bb3] transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-5">
                    <div class="md:col-span-1">
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Số lượng câu hỏi</label>
                        <input type="number" value="40" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] focus:ring-1 focus:ring-[#1e3bb3] transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Mô tả đề thi</label>
                    <textarea rows="3" placeholder="Ghi chú thêm về đề thi..." class="w-full px-4 py-3 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3] focus:ring-1 focus:ring-[#1e3bb3] transition text-slate-600 resize-none"></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border-2 border-[#1e3bb3] bg-blue-50/30 rounded-xl p-5 flex items-start gap-4 cursor-pointer shadow-sm relative overflow-hidden">
                    <div class="w-12 h-12 bg-blue-100 text-[#1e3bb3] rounded-lg flex items-center justify-center shrink-0">
                        <span class="material-icons text-[24px]">grid_view</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#1e3bb3] mb-1">Tạo tự động từ ma trận</h4>
                        <p class="text-[13px] text-slate-500 leading-relaxed">Hệ thống tự động chọn câu hỏi tỉ lệ độ khó.</p>
                    </div>
                </div>

                <div class="border-2 border-slate-200 bg-white rounded-xl p-5 flex items-start gap-4 cursor-pointer hover:border-slate-300 hover:bg-slate-50 transition">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center shrink-0">
                        <span class="material-icons text-[24px]">touch_app</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 mb-1">Chọn câu hỏi thủ công</h4>
                        <p class="text-[13px] text-slate-500 leading-relaxed">Tự tay chọn từng câu hỏi từ ngân hàng câu hỏi.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-icons text-slate-600 text-[20px]">settings</span> Cấu hình ma trận đề thi
                    </h3>
                    <div class="text-[13px] text-slate-500">
                        Tổng điểm: <span class="font-bold text-slate-800">10.0</span> 
                        <span class="mx-3 text-slate-300">|</span> 
                        Đã cấu hình: <span class="font-bold text-[#1e3bb3]">40 / 40 câu</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-[11px] text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="pb-4 pt-2 px-2">Cấp độ / Chủ đề</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24">Dễ</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24">Trung bình</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24">Khó</th>
                                <th class="pb-4 pt-2 px-2 text-center w-32">Tổng số câu</th>
                                <th class="pb-4 pt-2 px-2 text-center w-24">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-2">
                                    <div class="font-bold text-slate-700">Chủ đề 1: Đạo hàm & Tích phân</div>
                                    <div class="text-[11px] text-slate-400 mt-1">Kho lưu trữ: 150 câu</div>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="5" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="3" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="2" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center font-bold text-slate-800">10</td>
                                <td class="py-4 px-2 text-center">
                                    <button class="w-8 h-8 mx-auto bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-md flex items-center justify-center transition" title="Xóa chủ đề">
                                        <span class="material-icons text-[18px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-2">
                                    <div class="font-bold text-slate-700">Chủ đề 2: Số phức & Hình học</div>
                                    <div class="text-[11px] text-slate-400 mt-1">Kho lưu trữ: 85 câu</div>
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="8" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="5" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center">
                                    <input type="number" value="2" class="w-16 h-10 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#1e3bb3]">
                                </td>
                                <td class="py-4 px-2 text-center font-bold text-slate-800">15</td>
                                <td class="py-4 px-2 text-center">
                                    <button class="w-8 h-8 mx-auto bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-md flex items-center justify-center transition" title="Xóa chủ đề">
                                        <span class="material-icons text-[18px]">delete</span>
                                    </button>
                                </td>
                            </tr>
                            
                            <tr class="bg-slate-50/50">
                                <td class="py-4 px-2 text-right font-bold text-slate-800 pr-6">TỔNG CỘNG</td>
                                <td class="py-4 px-2 text-center font-bold text-slate-800">13</td>
                                <td class="py-4 px-2 text-center font-bold text-slate-800">8</td>
                                <td class="py-4 px-2 text-center font-bold text-slate-800">4</td>
                                <td class="py-4 px-2 text-center font-bold text-[#1e3bb3]">25 / 40</td>
                                <td class="py-4 px-2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-center">
                    <button class="flex items-center gap-1.5 text-[#1e3bb3] font-medium hover:text-blue-800 transition px-4 py-2 hover:bg-blue-50 rounded-lg">
                        <span class="material-icons text-[20px]">add</span> Thêm chương/chủ đề từ ngân hàng
                    </button>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>