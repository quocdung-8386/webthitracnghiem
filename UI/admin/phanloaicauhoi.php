<?php
$title = "Phân loại câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "category_q"; 
include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="text-sm text-slate-500">Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Phân loại & Quản lý danh mục</span></div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm danh mục..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:ring-1 focus:ring-[#1e3bb3] focus:outline-none w-64 transition">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">dark_mode</span></button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Cấu trúc danh mục</h2>
                <p class="text-sm text-slate-500 mt-1">Kéo thả để sắp xếp vị trí danh mục</p>
            </div>
            <div class="flex gap-3">
                <button class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-2 hover:bg-slate-50 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">download</span> Xuất danh mục
                </button>
                <button class="px-5 py-2.5 bg-[#1e3bb3] text-white rounded-lg flex items-center gap-2 hover:bg-blue-800 text-sm font-medium shadow-sm transition">
                    <span class="material-icons text-[20px]">add</span> Thêm danh mục mới
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 shadow-sm p-5 h-fit">
                <div class="flex items-center gap-3 p-3 bg-blue-50 text-[#1e3bb3] rounded-lg font-bold text-sm mb-2 cursor-pointer border border-blue-100">
                    <span class="material-icons text-slate-400 text-[18px]">drag_indicator</span>
                    <span class="material-icons text-[20px]">folder</span> Khoa Công nghệ Thông tin
                </div>
                
                <div class="ml-9 border-l border-slate-200 pl-4 py-1 space-y-2">
                    <div>
                        <div class="flex items-center gap-2 text-slate-700 font-medium text-sm cursor-pointer hover:text-[#1e3bb3]">
                            <span class="material-icons text-slate-300 text-[18px]">drag_indicator</span>
                            <span class="material-icons text-orange-400 text-[20px]">folder</span> Lập trình Cơ bản
                        </div>
                        <div class="ml-6 border-l border-slate-200 pl-4 py-2 space-y-2">
                            <div class="flex items-center gap-2 text-slate-500 text-[13px] hover:text-[#1e3bb3] cursor-pointer">
                                <span class="material-icons text-[18px]">folder_open</span> Cấu trúc điều khiển
                            </div>
                            <div class="flex items-center justify-between p-2 bg-blue-50 rounded-md text-[#1e3bb3] font-medium text-[13px] cursor-pointer">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons text-[18px]">folder_open</span> Hàm và Đệ quy
                                </div>
                                <div class="flex gap-1 opacity-100">
                                    <span class="material-icons text-[16px] hover:text-blue-800">edit</span>
                                    <span class="material-icons text-[16px] text-slate-400 hover:text-red-500">delete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 text-slate-700 font-medium text-sm cursor-pointer hover:text-[#1e3bb3] mt-2">
                        <span class="material-icons text-slate-300 text-[18px]">drag_indicator</span>
                        <span class="material-icons text-orange-400 text-[20px]">folder</span> Cơ sở Dữ liệu
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 text-slate-700 hover:bg-slate-50 rounded-lg font-bold text-sm mt-2 cursor-pointer border border-transparent">
                    <span class="material-icons text-slate-300 text-[18px]">drag_indicator</span>
                    <span class="material-icons text-[#1e3bb3] text-[20px]">folder</span> Khoa Kinh tế
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-6">
                        <div class="flex items-center gap-3">
                            <span class="material-icons text-[#1e3bb3] bg-blue-50 p-1.5 rounded-lg">analytics</span>
                            <h3 class="font-bold text-slate-800 text-lg">Chi tiết danh mục</h3>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-[#1e3bb3] text-[11px] font-bold rounded-full uppercase">Đang xem: Hàm và Đệ quy</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50">
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">Tổng số câu hỏi</p>
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-black text-slate-800">452</span>
                                <span class="text-xs font-semibold text-green-500 mb-1 flex items-center"><span class="material-icons text-[14px]">trending_up</span> +5% tuần này</span>
                            </div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50">
                            <p class="text-xs font-bold text-slate-500 uppercase mb-2">Mức độ khó TB</p>
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-black text-orange-500">3.8</span><span class="text-sm font-bold text-slate-400 mb-1">/5</span>
                                <div class="flex text-orange-400 mb-1.5 ml-2">
                                    <span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star</span><span class="material-icons text-[16px]">star_half</span><span class="material-icons text-[16px] text-slate-300">star</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-slate-800 mb-4">Thông tin chi tiết</h4>
                        <div class="space-y-4 text-sm">
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 col-span-1">Tên chủ đề:</span>
                                <span class="font-semibold text-slate-800 col-span-3">Hàm và Đệ quy</span>
                            </div>
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 col-span-1">Danh mục cha:</span>
                                <span class="font-medium text-orange-600 col-span-3">Lập trình Cơ bản</span>
                            </div>
                            <div class="grid grid-cols-4">
                                <span class="text-slate-500 col-span-1">Mô tả:</span>
                                <span class="text-slate-600 italic col-span-3">Các khái niệm về hàm, truyền tham số, tham trị và các bài toán giải bằng phương pháp đệ quy.</span>
                            </div>
                            <div class="grid grid-cols-4 items-center">
                                <span class="text-slate-500 col-span-1">Người quản lý:</span>
                                <div class="col-span-3 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold flex items-center justify-center">GV</div>
                                    <span class="font-medium text-slate-700">TS. Nguyễn Văn A</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3 flex items-center gap-2"><span class="material-icons text-[18px] text-slate-400">local_offer</span> Thẻ (Tags) phổ biến</h4>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">#Recursion (124)</span>
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">#Functions (98)</span>
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">#Fibonacci (45)</span>
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">#Parameters (67)</span>
                            <button class="px-3 py-1.5 border border-dashed border-slate-300 text-slate-400 hover:text-slate-600 rounded-full text-xs font-medium flex items-center gap-1"><span class="material-icons text-[14px]">add</span> Gắn thẻ mới</button>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button class="flex-1 py-3 bg-white border border-slate-200 rounded-xl text-slate-700 font-bold hover:bg-slate-50 transition flex justify-center items-center gap-2 shadow-sm">
                        <span class="material-icons text-[20px]">playlist_add</span> Tạo câu hỏi mới
                    </button>
                    <button class="flex-1 py-3 bg-white border border-slate-200 rounded-xl text-slate-700 font-bold hover:bg-slate-50 transition flex justify-center items-center gap-2 shadow-sm">
                        <span class="material-icons text-[20px]">download</span> Xuất danh sách
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'components/footer.php'; ?>