<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
</head>

<body>
    <h1>Chào mừng đến với Trang Chủ</h1>
    <form action="TrangTinh.PHP" method="post">
        <h2>Tính tổng</h2>
        <input type="submit" value="Tính Tổng">
    </form>
    <form action="TrangTinhMang.PHP" method="post">
        <h2>Tính tổng Mảng</h2>
        <input type="submit" value="Tính Tổng">
    </form>
    <form action="TrangTinhMangMau.PHP" method="post">
        <h2>Tính tổng Mảng Màu</h2>
        <input type="submit" value="Tính Tổng">
    </form>
    <form action="TrangTinhMangSinhVien.PHP" method="post">
        <h2>Tính tổng Mảng Sinh Viên</h2>
        <input type="submit" value="Tính Tổng">
    </form>
    <form action="TrangTinhNam.PHP" method="post">
        <h2>Tính Năm</h2>
        Nhập Tháng: <br> <input type="number" name="month" min="1" max="12" required><br><br>
        Nhập Năm: <br> <input type="number" name="year" min="0"><br><br>
        <input type="submit" value="Tính">
    </form>
</body>

</html>