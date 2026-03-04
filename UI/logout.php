<?php
// 1. Khởi động session để có thể thao tác với nó
session_start();

// 2. Xóa toàn bộ các biến lưu trữ trong session (bao gồm thông tin đăng nhập, vai trò...)
session_unset();

// 3. Phá hủy hoàn toàn phiên làm việc hiện tại
session_destroy();

// 4. Chuyển hướng người dùng về lại trang đăng nhập
header("Location: ../login.php");
exit();
?>