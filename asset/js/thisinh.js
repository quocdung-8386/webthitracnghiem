/* ==========================================================================
   TỔNG HỢP JAVASCRIPT CHO ACTOR THÍ SINH
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {

    // ---------------------------------------------------------
    // 1. DÀNH CHO TRANG LÀM BÀI THI (lambaithi.php)
    // ---------------------------------------------------------
    const examOptions = document.querySelectorAll('.option-item');
    if (examOptions.length > 0) {
        examOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class từ các anh em cùng nhóm (cùng câu hỏi)
                const parentList = this.parentElement;
                parentList.querySelectorAll('.option-item').forEach(opt => opt.classList.remove('selected'));
                
                // Add class cho option được chọn
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
                
                // Demo gọi AJAX Lưu bài định kỳ
                console.log("Đang lưu ngầm đáp án: ", this.querySelector('input[type="radio"]').value);
                // fetch('app/services/LuuBaiDinhKy.php', { ... });
            });
        });
    }

    // ---------------------------------------------------------
    // 2. DÀNH CHO TRANG TÌM KIẾM (timkiemvathamgiathi.php)
    // ---------------------------------------------------------
    const filterBtn = document.querySelector('.btn-filter');
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            // Lấy dữ liệu từ các input và select để tạo query string filter
            console.log("Thực hiện lọc danh sách kỳ thi...");
        });
    }

    // ---------------------------------------------------------
    // 3. DÀNH CHO TRANG PHÚC KHẢO (phuckhaokhieunai.php)
    // ---------------------------------------------------------
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const formPhucKhao = document.getElementById('formPhucKhao');

    if (uploadArea && fileInput) {
        // Mở hộp thoại chọn file khi click vào vùng upload
        uploadArea.addEventListener('click', () => fileInput.click());
        
        // BỔ SUNG: Hiển thị tên file khi người dùng Click chọn file bình thường
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                uploadArea.querySelector('.upload-text').innerHTML = `Đã chọn: <span style="color: var(--primary); font-weight: bold;">${this.files[0].name}</span>`;
            }
        });

        // Hiệu ứng Kéo thả (Drag & Drop)
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault(); 
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault(); 
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                uploadArea.querySelector('.upload-text').innerHTML = `Đã chọn: <span style="color: var(--primary); font-weight: bold;">${e.dataTransfer.files[0].name}</span>`;
            }
        });

        formPhucKhao.addEventListener('submit', function(e) {
            e.preventDefault();
            alert("Đã gửi yêu cầu phúc khảo thành công vào hệ thống!");
            this.reset();
            uploadArea.querySelector('.upload-text').innerHTML = '<span>Tải tệp lên</span> hoặc kéo thả vào đây';
        });
    }

    // ---------------------------------------------------------
    // 4. DÀNH CHO TRANG KẾT QUẢ & CHUNG (xemketqua.php, header.php)
    // ---------------------------------------------------------
    const btnExport = document.getElementById('btnExportPDF');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            alert("Đang tạo file PDF kết quả...");
            // Thực tế sẽ gọi thư viện TCPDF hoặc DomPDF ở backend
        });
    }

    // Tính năng Dark Mode dùng chung
    const btnTheme = document.getElementById('btnToggleTheme');
    if (btnTheme) {
        // Kiểm tra cache xem user đã chọn dark mode chưa
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            btnTheme.innerText = 'light_mode';
        }

        btnTheme.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            btnTheme.innerText = isDark ? 'light_mode' : 'dark_mode'; 
        });
    }
});