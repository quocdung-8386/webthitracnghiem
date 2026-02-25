<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sắp xếp mảng</title>
</head>
<body>
    <form method="post">
        <h2>Sắp xếp mảng</h2>
        Nhập mảng (cách nhau bằng dấu phẩy): 
        <input type="text" name="input" required> <br><br>
        <input type="submit" value="Sắp xếp">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $chuoi = $_POST["input"];
        $array = explode(",", $chuoi);

        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = (int)trim($array[$i]);
        }

        $originalArray = $array;
        $n = count($array);

        // Bubble Sort - sắp xếp tăng dần
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($array[$j] > $array[$j + 1]) {
                    $temp = $array[$j];
                    $array[$j] = $array[$j + 1];
                    $array[$j + 1] = $temp;
                }
            }
        }

        echo "<h3>Mảng ban đầu: [" . implode(", ", $originalArray) . "]</h3>";
        echo "<h3>Mảng sau khi sắp xếp tăng dần: [" . implode(", ", $array) . "]</h3>";
    }
    ?>
    <hr>
    <form method="post">
        <h2>Kiểm tra số nguyên tố</h2>
        Nhập n: <input type="number" name="songuyen" required><br><br>
        <input type="submit" value="Kiểm tra">
    </form>
    <?php
     function CheckSoNguyenTo($n): bool {
        if ($n < 2) return false;
        for ($i = 2; $i <= sqrt($n); $i++) {
            if ($n % $i == 0) return false;
        }
        return true;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["songuyen"])) {
        $n = (int)$_POST["songuyen"];
        if (CheckSoNguyenTo($n)) {
            echo "<h3>$n là số nguyên tố.</h3>";
        } else {
            echo "<h3>$n không phải là số nguyên tố.</h3>";
        }
    }
    ?>
</body>
</html>
