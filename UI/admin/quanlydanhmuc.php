<?php
$title = "Phân loại câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "category_q"; 
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden transition-colors duration-200">
    <header class="h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-8 flex items-center justify-between z-10 shrink-0 transition-colors">
        <div class="text-sm text-slate-500 dark:text-slate-400">
            Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 dark:text-white font-medium">Phân loại & Quản lý danh mục</span>
        </div>
        
        <div class="flex items-center gap-5">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm danh mục..." class="pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-full text-sm text-slate-800 dark:text-white focus:ring-1 focus:ring-[#254ada] focus:outline-none w-64 transition">
            </div>

            <div class="relative">
                <button id="notifButton" type="button" class="relative text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                    <span class="material-icons">notifications</span>
                    <span class="absolute top-0 right-1 w-2 h-2 bg-red-500 rounded-full border border-white dark:border-slate-800"></span>
                </button>
                <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-100 dark:border-slate-700 z-50 overflow-hidden transform transition-all">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <span class="font-bold text-sm text-slate-800 dark:text-white">Thông báo mới</span>
                        <a href="#" class="text-[11px] text-[#254ada] dark:text-[#4b6bfb] hover:underline font-medium">Đánh dấu đã đọc</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                        <a href="#" class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 border-b border-slate-50 dark:border-slate-700 transition">
                            <p class="text-[13px] text-slate-700 dark:text-slate-300 leading-snug"><span class="font-semibold text-slate-800 dark:text-white">Hệ thống</span> vừa hoàn tất cập nhật danh mục.</p>
                            <span class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1"><span class="material-icons text-[12px]">schedule</span> Vừa xong</span>
                        </a>
                    </div>
                    <a href="#" class="block px-4 py-2.5 text-center text-sm text-[#254ada] dark:text-[#4b6bfb] font-medium bg-slate-50 dark:bg-slate-700/30 hover:bg-slate-100 dark:hover:bg-slate-700 transition border-t border-slate-100 dark:border-slate-700">Xem tất cả</a>
                </div>
            </div>

            <button id="darkModeToggle" class="text-slate-500 dark:text-slate-400 hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition focus:outline-none">
                <span class="material-icons" id="darkModeIcon">dark_mode</span>
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 dark:bg-slate-900 custom-scrollbar transition-colors duration-200">
        
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Cấu trúc danh mục</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kéo thả để sắp xếp vị trí danh mục</p>
            </div>
            <div class="flex gap-3">
                <button onclick="showToast('info', 'Đang xuất dữ liệu', 'Danh mục đang được tải xuống dưới định dạng .xlsx')" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">download</span> Xuất danh mục
                </button>
                <button onclick="openModal('addCategoryModal')" class="px-5 py-2.5 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg flex items-center gap-2 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">add</span> Thêm danh mục mới
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 h-fit transition-colors">
                
                <div class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 text-[#254ada] dark:text-[#4b6bfb] rounded-lg font-bold text-sm mb-2 cursor-pointer border border-blue-100 dark:border-blue-800/50 transition">
                    <span class="material-icons text-slate-400 dark:text-slate-500 text-[18px]">drag_indicator</span>
                    <span class="material-icons text-[20px]">folder</span> Khoa Công nghệ Thông tin
                </div>
                
                <div class="ml-9 border-l border-slate-200 dark:border-slate-700 pl-4 py-1 space-y-2">
                    <div>
                        <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium text-sm cursor-pointer hover:text-[#254ada] dark:hover:text-[#4b6bfb] transition">
                            <span class="material-icons text-slate-300 dark:text-slate-500 text-[18px]">drag_indicator</span>
                            <span class="material-icons text-orange-400 text-[20px]">folder</span> Lập trình Cơ bản
                        </div>
                        <div class="ml-6 border-l border-slate-200 dark:border-slate-700 pl-4 py-2 space-y-2">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-[13px] hover:text-[#254ada] dark:hover:text-[#4b6bfb] cursor-pointer transition">
                                <span class="material-icons text-[18px]">folder_open</span> Cấu trúc điều khiển
                            </div>
                            
                            <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-md text-[#254ada] dark:text-[#4b6bfb] font-medium text-[13px] cursor-pointer transition group">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-[18px]">folder_open</span> Hàm và Đệ quy
                                </div>
                                <div class="flex gap-1 opacity-100">
                                    <span onclick="showToast('info', 'Sửa', 'Sửa thông tin mục Hàm và Đệ quy')" class="material-icons text-[16px] hover:text-blue-800 dark:hover:text-white">edit</span>
                                    <span onclick="showToast('error', 'Xóa', 'Xóa mục Hàm và Đệ quy')" class="material-icons text-[16px] text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400">delete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium text-sm cursor-pointer hover:text-[#254ada] dark:hover:text-[#4b6bfb] mt-2 transition">
                        <span class="material-icons text-slate-300 dark:text-slate-500 text-[18px]">drag_indicator</span>
                        <span class="material-icons text-orange-400 text-[20px]">folder</span> Cơ sở Dữ liệu
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 rounded-lg font-bold text-sm mt-2 cursor-pointer border border-transparent transition">
                    <span class="material-icons text-slate-300 dark:text-slate-500 text-[18px]">drag_indicator</span>
                    <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] text-[20px]">folder</span> Khoa Kinh tế
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 transition-colors">
                    
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-[#254ada] dark:text-[#4b6bfb] bg-blue-50 dark:bg-blue-900/30 p-1.5 rounded-lg">analytics</span>
                            <h3 class="font-bold text-slate-800 dark:text-white text-lg">Chi tiết danh mục</h3>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-[#254ada] dark:text-[#4b6bfb] text-[11px] font-bold rounded-full uppercase">Đang xem: Hàm và Đệ quy</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="border border-slate-200 dark:border-slate-600 rounded-xl p-5 bg-slate-50/50 dark:bg-slate-900/30 transition-colors">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Tổng số câu hỏi</p>
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-black text-slate-800 dark:text-white">452</span>
                                <span class="text-xs font-semibold text-green-500 dark:text-green-400 mb-1 flex items-center"><span class="material-icons text-[14px]">trending_up</span> +5% tuần này</span>
                            </div>
                        </div>
                        <div class="border border-slate-200 dark:border-slate-600 rounded-xl p-5 bg-slate-50/50 dark:bg-slate-900/30 transition-colors">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Mức độ khó TB</p>
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-black text-orange-500 dark:text-orange-400">3.8</span><span class="text-sm font-bold text-slate-400 dark:text-slate-500 mb-1">/5</span>
                                <div class="flex text-orange-400 dark:text-orange-300 mb-1.5 ml-2">
                                    <span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star_half</span><span class="material-icons text-[16px] text-slate-300 dark:text-slate-600">star</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700 rounded-xl p-5 mb-6 transition-colors">
                        <h4 class="font-bold text-slate-800 dark:text-white mb-4">Thông tin chi tiết</h4>
                        <div class="space-y-4 text-sm">
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 dark:text-slate-400 col-span-1">Tên chủ đề:</span>
                                <span class="font-semibold text-slate-800 dark:text-white col-span-3">Hàm và Đệ quy</span>
                            </div>
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 dark:text-slate-400 col-span-1">Danh mục cha:</span>
                                <span class="font-medium text-orange-600 dark:text-orange-400 col-span-3">Lập trình Cơ bản</span>
                            </div>
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 dark:text-slate-400 col-span-1">Mô tả:</span>
                                <span class="text-slate-600 dark:text-slate-300 italic col-span-3">Các khái niệm về hàm, truyền tham số, tham trị và các bài toán giải bằng phương pháp đệ quy.</span>
                            </div>
                            <div class="grid grid-cols-4 items-center">
                                <span class="text-slate-500 dark:text-slate-400 col-span-1">Người quản lý:</span>
                                <div class="col-span-3 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold flex items-center justify-center">GV</div>
                                    <span class="font-medium text-slate-700 dark:text-slate-300">TS. Nguyễn Văn A</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2"><span class="material-icons text-[18px] text-slate-400 dark:text-slate-500">local_offer</span> Thẻ (Tags) phổ biến</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-medium">#Recursion (124)</span>
                            <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-medium">#Functions (98)</span>
                            <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-medium">#Fibonacci (45)</span>
                            <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-medium">#Parameters (67)</span>
                            <button onclick="showToast('success', 'Gắn thẻ mới', 'Mở popup nhập tag')" class="px-3 py-1.5 border border-dashed border-slate-300 dark:border-slate-500 text-slate-400 dark:text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full text-xs font-medium flex items-center gap-1 transition"><span class="material-icons text-[14px]">add</span> Gắn thẻ mới</button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button onclick="showToast('info', 'Tạo câu hỏi', 'Mở bảng tạo câu hỏi với danh mục mặc định là Hàm và Đệ quy')" class="flex-1 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition flex justify-center items-center gap-2 shadow-sm">
                        <span class="material-icons text-[20px]">playlist_add</span> Tạo câu hỏi mới
                    </button>
                    <button onclick="showToast('info', 'Xuất danh sách', 'Đang xuất file...')" class="flex-1 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition flex justify-center items-center gap-2 shadow-sm">
                        <span class="material-icons text-[20px]">download</span> Xuất danh sách
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<div id="addCategoryModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-[500px] overflow-hidden transform transition-all border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">
        
        <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons text-[#254ada] dark:text-[#4b6bfb]">create_new_folder</span> Thêm danh mục mới
            </h3>
            <button type="button" onclick="closeModal('addCategoryModal')" class="text-slate-400 hover:text-red-500 transition focus:outline-none"><span class="material-icons">close</span></button>
        </div>
        
        <form id="formAddCategory" onsubmit="event.preventDefault(); submitAddCategory();" class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tên danh mục <span class="text-red-500">*</span></label>
                <input type="text" placeholder="VD: Lập trình Web..." required class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition">
            </div>
            
            <div class="mb-4">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Danh mục cha</label>
                <select class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition appearance-none">
                    <option value="root">-- Đây là danh mục gốc --</option>
                    <option value="cntt">Khoa Công nghệ Thông tin</option>
                    <option value="ltcb">&nbsp;&nbsp;&nbsp;Lập trình Cơ bản</option>
                    <option value="csdl">&nbsp;&nbsp;&nbsp;Cơ sở Dữ liệu</option>
                    <option value="kinhte">Khoa Kinh tế</option>
                </select>
            </div>
            
            <div class="mb-5">
                <label class="block text-[13px] font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mô tả chi tiết (Tùy chọn)</label>
                <textarea rows="3" placeholder="Nhập mô tả cho danh mục này..." class="w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-[#254ada] focus:outline-none transition resize-y"></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-5 mt-4 border-t border-slate-100 dark:border-slate-700">
                <button type="button" onclick="closeModal('addCategoryModal')" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition">Hủy bỏ</button>
                <button type="submit" id="btnSubmitCategory" class="px-4 py-2 bg-[#254ada] hover:bg-[#1e3bb3] dark:bg-[#4b6bfb] dark:hover:bg-[#254ada] text-white rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
                    <span class="material-icons text-[18px]">save</span> Lưu danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3 pointer-events-none"></div>
<template id="toastTemplate">
    <div class="toast-item pointer-events-auto flex items-start gap-3 p-4 bg-white dark:bg-slate-800 border-l-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0 max-w-sm border-slate-200 dark:border-slate-700">
        <div class="toast-icon shrink-0 mt-0.5"></div>
        <div class="flex-1">
            <h4 class="toast-title text-[14px] font-bold text-slate-800 dark:text-white leading-tight"></h4>
            <p class="toast-message text-[12px] text-slate-500 dark:text-slate-400 mt-1"></p>
        </div>
        <button class="toast-close text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"><span class="material-icons text-[16px]">close</span></button>
    </div>
</template>

<?php include 'components/footer.php'; ?>

<script>
/* =================================================================
   1. CÁC HÀM GLOBAL (MODAL & LƯU FORM)
   ================================================================= */
function openModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.remove('hidden'); 
}

function closeModal(id) { 
    const modal = document.getElementById(id);
    if(modal) modal.classList.add('hidden'); 
}

// Hàm giả lập việc Lưu danh mục mới
function submitAddCategory() {
    const btn = document.getElementById('btnSubmitCategory');
    const originalText = btn.innerHTML;
    
    // Đổi nút thành trạng thái đang xoay (Loading)
    btn.innerHTML = '<span class="material-icons animate-spin text-[18px]">autorenew</span> Đang lưu...';
    btn.disabled = true;
    btn.classList.add('opacity-70');

    // Chờ 1 giây để giả lập API
    setTimeout(() => {
        closeModal('addCategoryModal'); // Đóng Popup
        showToast('success', 'Thành công', 'Đã thêm danh mục mới vào cấu trúc hệ thống.'); // Báo thành công
        document.getElementById('formAddCategory').reset(); // Xóa trắng Form
        
        // Trả nút về như cũ
        btn.innerHTML = originalText;
        btn.disabled = false;
        btn.classList.remove('opacity-70');
    }, 1000);
}

/* =================================================================
   2. HÀM HIỂN THỊ THÔNG BÁO (TOAST)
   ================================================================= */
function showToast(type, title, message) {
    const container = document.getElementById('toastContainer');
    const template = document.getElementById('toastTemplate');
    if(!container || !template) return;
    
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
    setTimeout(() => { if(container.contains(toastEl)) toastEl.querySelector('.toast-close').click(); }, 4000);
}

/* =================================================================
   3. SỰ KIỆN KHỞI TẠO (DOM Content Loaded)
   ================================================================= */
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Chức năng Dark Mode
    const darkModeToggle = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const htmlElement = document.documentElement;

    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark');
        if(darkModeIcon) darkModeIcon.textContent = 'light_mode';
    }

    darkModeToggle?.addEventListener('click', () => {
        htmlElement.classList.toggle('dark');
        const isDark = htmlElement.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        if(darkModeIcon) darkModeIcon.textContent = isDark ? 'light_mode' : 'dark_mode';
    });

    // 2. Chức năng Dropdown Thông báo
    const notifButton = document.getElementById('notifButton');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifButton && notifDropdown) {
        notifButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.classList.add('hidden');
            }
        });
    }
});
</script>