<?php
    $tenTK = $_POST['tenTK'];
    $MK = $_POST['MK'];

    if ($tenTK == "longnong" && $MK == "123456") {
        echo "Đăng nhập thành công! Xin chào " . $tenTK;
    } else {
        echo "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
?>