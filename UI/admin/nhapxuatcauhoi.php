<?php
$title = "Nhập/Xuất câu hỏi - Hệ Thống Thi Trực Tuyến";
$active_menu = "import_q"; 

$preview_data = [
    ['stt' => 1, 'content' => 'Hàm số y = f(x) đồng biến trên khoảng nào dưới đây?', 'category' => 'Giải tích 12', 'level' => 'Trung bình', 'level_color' => 'bg-blue-50 text-blue-600', 'status' => 'Sẵn sàng', 'status_icon' => 'check_circle', 'status_color' => 'text-green-500'],
    ['stt' => 2, 'content' => 'Tìm nguyên hàm của hàm số f(x) = ...', 'category' => 'Giải tích 12', 'level' => 'Khó', 'level_color' => 'bg-orange-50 text-orange-600', 'status' => 'Thiếu đáp án', 'status_icon' => 'error', 'status_color' => 'text-red-500'],
];

include 'components/header.php';
include 'components/sidebar.php';
?>

<main class="flex-1 flex flex-col h-screen overflow-hidden">
    <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between z-10 shrink-0">
        <div class="text-sm text-slate-500">Trang chủ <span class="mx-2">›</span> Ngân hàng câu hỏi <span class="mx-2">›</span> <span class="text-slate-800 font-medium">Nhập/Xuất câu hỏi</span></div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" placeholder="Tìm kiếm tính năng..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-sm focus:outline-none w-56">
            </div>
            <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">notifications</span></button>
            <button class="text-slate-500 hover:text-[#1e3bb3]"><span class="material-icons">dark_mode</span></button>
            <button class="px-3 py-1.5 border border-slate-200 rounded-lg flex items-center gap-1 text-sm font-medium text-slate-600 hover:bg-slate-50"><span class="material-icons text-[18px]">help_outline</span> Hướng dẫn</button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8 bg-slate-50 custom-scrollbar">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">Nhập/Xuất câu hỏi từ File</h2>
            <p class="text-sm text-slate-500 mt-1">Quản lý và đồng bộ dữ liệu ngân hàng câu hỏi thông qua các tệp tin Excel hoặc Word.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2"><span class="material-icons text-slate-700">upload_file</span> Nhập câu hỏi từ tập tin</h3>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2"><span class="material-icons text-[18px]">download</span> Tải file Excel mẫu</button>
                        <button class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 flex items-center gap-2"><span class="material-icons text-[18px]">download</span> Tải file Word mẫu</button>
                    </div>
                </div>

                <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 flex flex-col items-center justify-center bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition mb-6">
                    <div class="w-16 h-16 bg-[#1e3bb3]/10 text-[#1e3bb3] rounded-full flex items-center justify-center mb-4">
                        <span class="material-icons text-3xl">cloud_upload</span>
                    </div>
                    <h4 class="font-bold text-slate-800 text-lg mb-1">Kéo và thả file tại đây</h4>
                    <p class="text-sm text-slate-500 mb-6">Hỗ trợ định dạng .xlsx, .xls, .docx, .doc (Tối đa 20MB)</p>
                    <button class="px-6 py-2.5 bg-slate-100 text-slate-400 font-medium rounded-lg opacity-50 cursor-not-allowed">Chọn file từ máy tính</button>
                </div>

                <div>
                    <div class="flex justify-between text-sm text-slate-600 font-medium mb-2">
                        <span>Đang xử lý file: NganHangCauHoi_Toan_L12.xlsx</span>
                        <span class="font-bold text-slate-800">75%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="bg-[#1e3bb3] h-1.5 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2"><span class="material-icons text-slate-700 text-[20px]">visibility</span> Xem trước và kiểm tra dữ liệu</h3>
                    <div class="text-sm font-bold flex gap-4">
                        <span class="text-green-500 flex items-center gap-1"><span class="material-icons text-[16px]">circle</span> 48 Hợp lệ</span>
                        <span class="text-red-500 flex items-center gap-1"><span class="material-icons text-[16px]">circle</span> 2 Lỗi</span>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-4 w-16 text-center">STT</th>
                                <th class="px-5 py-4">Nội dung câu hỏi</th>
                                <th class="px-5 py-4">Phân loại</th>
                                <th class="px-5 py-4 text-center">Mức độ</th>
                                <th class="px-5 py-4 text-center">Trạng thái</th>
                                <th class="px-5 py-4 text-center w-20">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($preview_data as $row): ?>
                            <tr>
                                <td class="px-5 py-4 text-center font-medium text-slate-600"><?php echo $row['stt']; ?></td>
                                <td class="px-5 py-4 font-medium text-slate-800"><?php echo $row['content']; ?></td>
                                <td class="px-5 py-4 text-slate-500 italic text-[13px]"><?php echo $row['category']; ?></td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-md <?php echo $row['level_color']; ?> text-[11px] font-bold"><?php echo $row['level']; ?></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-[12px] <?php echo $row['status_color']; ?>">
                                        <span class="material-icons text-[18px]"><?php echo $row['status_icon']; ?></span> <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button class="text-slate-400 hover:text-red-500 transition"><span class="material-icons text-[18px]">edit</span></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button class="px-6 py-2.5 border border-slate-200 rounded-lg text-slate-600 font-medium hover:bg-slate-50 transition">Hủy bỏ</button>
                    <button class="px-8 py-2.5 bg-slate-100 text-slate-400 rounded-lg font-bold cursor-not-allowed">Lưu dữ liệu</button> </div>
            </div>
        </div>
    </div>
</main>
<?php include 'components/footer.php'; ?>