<?php
$month = $_POST['month'];
$year = $_POST['year'];

switch ($month) {
    case 1:
    case 3:
    case 5:
    case 7:
    case 8:
    case 10:
    case 12:
        echo 'Tháng này có 31 ngày.';
        break;
    case 4:
    case 6:
    case 9:
    case 11:
        echo 'Tháng này có 30 ngày.';
        break;
    case 2:
        if ($year == "") {
            echo "Vui lòng nhập năm để kiểm tra tháng 2!";
        } else {
            if (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)) {
                echo 'Tháng này có 29 ngày, năm nhuận.';
            } else {
                echo 'Tháng này có 28 ngày, không phải năm nhuận.';
            }
        }
        break;
    default:
        echo "Tháng không hợp lệ";
        break;
}
?>