document.addEventListener("DOMContentLoaded", function() {
    
    // --- HÀM HIỂN THỊ THÔNG BÁO TOAST ---
    const toast = document.getElementById("toastMessage");
    const toastText = document.getElementById("toastText");

    function showToast(message) {
        if (toast && toastText) {
            toastText.textContent = message; // Đổi chữ thông báo theo ý muốn
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 3000);
        }
    }

    // ==========================================
    // TRANG NGÂN HÀNG CÂU HỎI 
    // ==========================================
    const questionModal = document.getElementById("addQuestionModal");
    const btnOpenQuestion = document.getElementById("btnOpenModal");
    if (questionModal && btnOpenQuestion) {
        const btnClose = questionModal.querySelector(".close-btn");
        const btnCancel = document.getElementById("btnCancel");
        const btnSave = questionModal.querySelector(".modal-footer .btn-primary");

        btnOpenQuestion.addEventListener("click", () => questionModal.classList.add("show"));
        btnClose.addEventListener("click", () => questionModal.classList.remove("show"));
        btnCancel.addEventListener("click", (e) => { e.preventDefault(); questionModal.classList.remove("show"); });
        
        btnSave.addEventListener("click", (e) => {
            e.preventDefault();
            questionModal.classList.remove("show");
            showToast("Đã thêm câu hỏi thành công!"); 
        });
        window.addEventListener("click", (e) => { if (e.target == questionModal) questionModal.classList.remove("show"); });
    }

    // ==========================================
    // TRANG TẠO ĐỀ THI: MỞ MODAL CHỌN CÂU HỎI
    // ==========================================
    const selectModal = document.getElementById("selectQuestionModal");
    const btnOpenSelect = document.getElementById("btnOpenSelectModal");
    
    if (selectModal && btnOpenSelect) {
        const btnCloseSelect = selectModal.querySelector(".close-select-btn");
        const btnCancelSelect = document.getElementById("btnCancelSelect");
        const btnConfirmSelect = document.getElementById("btnConfirmSelect");
        const checkboxes = selectModal.querySelectorAll(".check-item");
        const selectedCountDisplay = document.getElementById("selectedCount");
        const inputSoLuongCau = document.querySelector('input[value="0"]'); // Ô input Số lượng câu

        // 1. Mở Modal
        btnOpenSelect.addEventListener("click", () => selectModal.classList.add("show"));
        
        // 2. Đóng Modal
        const closeSelectModal = () => selectModal.classList.remove("show");
        btnCloseSelect.addEventListener("click", closeSelectModal);
        btnCancelSelect.addEventListener("click", closeSelectModal);
        window.addEventListener("click", (e) => { if (e.target == selectModal) closeSelectModal(); });

        // 3. Đếm số lượng checkbox được tick
        checkboxes.forEach(cb => {
            cb.addEventListener("change", () => {
                const count = selectModal.querySelectorAll(".check-item:checked").length;
                selectedCountDisplay.textContent = count;
            });
        });

        // 4. Bấm xác nhận Thêm vào đề thi
        btnConfirmSelect.addEventListener("click", () => {
            const count = selectModal.querySelectorAll(".check-item:checked").length;
            
            if (count === 0) {
                alert("Cậu chưa tick chọn câu hỏi nào cả!");
                return;
            }

            // Đóng hộp thoại
            closeSelectModal();
            
            // Cập nhật số lượng câu trên giao diện tĩnh (cộng dồn)
            if (inputSoLuongCau) {
                let currentCount = parseInt(inputSoLuongCau.value) || 0;
                inputSoLuongCau.value = currentCount + count;
            }

            // Hiện thông báo
            showToast("Đã thêm " + count + " câu hỏi vào đề thi!");
            
            // Bỏ tick hết để lần sau mở lên làm lại từ đầu
            checkboxes.forEach(cb => cb.checked = false);
            selectedCountDisplay.textContent = "0";
        });
    }

    // ==========================================
    // TRANG BÁO CÁO THỐNG KÊ: XUẤT EXCEL
    // ==========================================
    const btnExportExcel = document.getElementById("btnExportExcel");
    if (btnExportExcel) {
        btnExportExcel.addEventListener("click", function() {
            // Đổi chữ trên nút để tạo cảm giác đang xử lý
            const originalText = this.innerHTML;
            this.innerHTML = "⏳ Đang xuất file...";
            this.style.opacity = "0.7";
            this.style.cursor = "wait";

            // Giả lập thời gian server tạo file Excel mất 1.5 giây
            setTimeout(() => {
                showToast("Đã xuất danh sách điểm thành file Excel!");
                // Trả lại nút như cũ
                this.innerHTML = originalText;
                this.style.opacity = "1";
                this.style.cursor = "pointer";
            }, 1500);
        });
    }

});