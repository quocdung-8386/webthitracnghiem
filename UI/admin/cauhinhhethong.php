<?php
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
        <div class="flex items-center gap-4">
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#254ada] transition"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex border-b border-slate-200 px-6 pt-2">
                <button class="px-4 py-3 border-b-2 border-[#254ada] text-[#254ada] font-semibold text-sm">Thiết lập tham số chung</button>
                <button class="px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium text-sm transition">Cấu hình Email/SMTP</button>
                <button class="px-4 py-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium text-sm transition">Phân quyền</button>
            </div>

            <form class="p-8">
                <div class="mb-8">
                    <h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">info</span> THÔNG TIN HỆ THỐNG
                    </h3>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tên hệ thống</label>
                            <input type="text" value="Hệ thống thi trắc nghiệm trực tuyến - EduExam" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Logo hệ thống</label>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 border border-slate-200 rounded flex items-center justify-center"><span class="material-icons text-slate-400">image</span></div>
                                <button type="button" class="px-4 py-2 bg-slate-50 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-100 border border-slate-200">Thay đổi ảnh</button>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Quy định thi chung</label>
                            <textarea rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada] text-slate-600">1. Thí sinh không được sử dụng tài liệu trong quá trình làm bài.&#10;2. Hệ thống sẽ tự động nộp bài khi hết thời gian.&#10;3. Việc mất kết nối camera quá 2 lần sẽ bị đình chỉ thi.</textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 mb-8">
                    <h3 class="text-sm font-bold uppercase text-slate-800 flex items-center gap-2 mb-5">
                        <span class="material-icons text-[#254ada] text-[20px] bg-blue-50 p-1 rounded-full">email</span> CẤU HÌNH EMAIL/SMTP
                    </h3>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">SMTP Server</label>
                            <input type="text" value="smtp.gmail.com" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">SMTP Port</label>
                            <input type="text" value="587" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Email User (Tài khoản)</label>
                            <input type="text" value="notification@system.edu.vn" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Mật khẩu (Password)</label>
                            <div class="relative">
                                <input type="password" value="password123" class="w-full pl-4 pr-10 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#254ada]">
                                <span class="material-icons absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm cursor-pointer">visibility</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-blue-50/50 text-[#254ada] text-sm rounded-lg flex gap-3 items-start border border-blue-100">
                        <span class="material-icons mt-0.5 text-[20px]">help</span>
                        <p>Email SMTP được sử dụng để gửi thông báo kết quả thi, mã OTP khôi phục mật khẩu và thông tin tài khoản cho người dùng.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" class="px-6 py-2.5 text-slate-600 hover:bg-slate-100 rounded-lg font-medium text-sm transition">Hủy bỏ</button>
                    <button type="submit" class="px-6 py-2.5 bg-[#254ada] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 font-medium text-sm transition shadow-sm">
                        <span class="material-icons text-[20px]">save</span> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>