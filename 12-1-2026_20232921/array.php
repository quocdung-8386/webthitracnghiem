<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra array</title>
</head>
<body>
    <form method="post">
        <h2>Tính tổng mảng</h2>
        Nhập chuỗi (cách nhau bằng dấu phẩy):  
        <input type="text" name="array" required><br><br>
        <input type="submit" value="Tính Tổng">
    </form>
    <hr>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mang = $_POST["array"];
        $array = explode(",", $mang);

        $sumAll = 0;

        foreach ($array as $num) {
            $num = (int)trim($num);
            $sumAll += $num;
        }

        echo "<h3>Mảng đã nhập: [" . implode(", ", $array) . "]</h3>";
        echo "<h3>Tổng tất cả phần tử: $sumAll</h3>";
    }
    ?>
</body>
</html>
