<?php
session_start();
$LoaiXe = $_POST['LoaiXe'] ?? '';

class Xe
{
    public $MaXe;
    public $TenXe;
    public $HangXe;
    public $GiaGoc;

    public function __construct($MaXe, $TenXe, $HangXe, $GiaGoc)
    {
        $this->MaXe = $MaXe;
        $this->TenXe = $TenXe;
        $this->HangXe = $HangXe;
        $this->GiaGoc = $GiaGoc;
    }

    public function TinhGiaBan()
    {
        return $this->GiaGoc;
    }

    public function getThongTin()
    {
        return [
            'MaXe' => $this->MaXe,
            'TenXe' => $this->TenXe,
            'HangXe' => $this->HangXe,
            'GiaBan' => $this->TinhGiaBan()
        ];
    }
}

class XeMay extends Xe
{
    public $ThueTB;
    public $PhiDKy;

    public function __construct($MaXe, $TenXe, $HangXe, $GiaGoc, $ThueTB, $PhiDKy)
    {
        parent::__construct($MaXe, $TenXe, $HangXe, $GiaGoc);
        $this->ThueTB = $ThueTB;
        $this->PhiDKy = $PhiDKy;
    }

    public function TinhGiaBan()
    {
        return $this->GiaGoc + $this->ThueTB + $this->PhiDKy;
    }
}

class OTo extends Xe
{
    public $ThueTTDB;
    public $PhiBaoTri;

    public function __construct($MaXe, $TenXe, $HangXe, $GiaGoc, $ThueTTDB, $PhiBaoTri)
    {
        parent::__construct($MaXe, $TenXe, $HangXe, $GiaGoc);
        $this->ThueTTDB = $ThueTTDB;
        $this->PhiBaoTri = $PhiBaoTri;
    }

    public function TinhGiaBan()
    {
        return $this->GiaGoc + $this->ThueTTDB + $this->PhiBaoTri;
    }
}

class XeTai extends Xe
{
    public $ThueVanTai;
    public $PhiKiemDinh;

    public function __construct($MaXe, $TenXe, $HangXe, $GiaGoc, $ThueVanTai, $PhiKiemDinh)
    {
        parent::__construct($MaXe, $TenXe, $HangXe, $GiaGoc);
        $this->ThueVanTai = $ThueVanTai;
        $this->PhiKiemDinh = $PhiKiemDinh;
    }

    public function TinhGiaBan()
    {
        return $this->GiaGoc + $this->ThueVanTai + $this->PhiKiemDinh;
    }
}

if (!isset($_SESSION['DSXe'])) {
    $_SESSION['DSXe'] = [];
}

if (isset($_POST['btnThem'])) {

    $LoaiXe = $_POST['LoaiXe'];
    $MaXe = $_POST['MaXe'];
    $TenXe = $_POST['TenXe'];
    $HangXe = $_POST['HangXe'];
    $GiaGoc = $_POST['GiaGoc'];

    switch ($LoaiXe) {
        case "XeMay":
            $xe = new XeMay(
                $MaXe,
                $TenXe,
                $HangXe,
                $GiaGoc,
                $_POST['ThueTB'],
                $_POST['PhiDKy']
            );
            break;

        case "OTo":
            $xe = new OTo(
                $MaXe,
                $TenXe,
                $HangXe,
                $GiaGoc,
                $_POST['ThueTTDB'],
                $_POST['PhiBaoTri']
            );
            break;

        case "XeTai":
            $xe = new XeTai(
                $MaXe,
                $TenXe,
                $HangXe,
                $GiaGoc,
                $_POST['ThueVanTai'],
                $_POST['PhiKiemDinh']
            );
            break;
    }

    $_SESSION['DSXe'][] = $xe->getThongTin();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Xe</title>
</head>

<body>
<h2>Nhập thông tin xe</h2>

<form method="post">
    Mã xe: <input type="text" name="MaXe" required><br><br>
    Tên xe: <input type="text" name="TenXe" required><br><br>
    Hãng xe: <input type="text" name="HangXe" required><br><br>
    Giá gốc: <input type="number" name="GiaGoc" required><br><br>

    Loại xe:
    <select name="LoaiXe" onchange="this.form.submit()" required>
        <option value="">-- Chọn loại xe --</option>
        <option value="XeMay" <?= $LoaiXe=="XeMay"?"selected":"" ?>>Xe máy</option>
        <option value="OTo" <?= $LoaiXe=="OTo"?"selected":"" ?>>Ô tô</option>
        <option value="XeTai" <?= $LoaiXe=="XeTai"?"selected":"" ?>>Xe tải</option>
    </select><br><br>

    <?php if ($LoaiXe == "XeMay"): ?>
        <b>Thuế xe máy</b><br><br>
        Thuế trước bạ: <input type="number" name="ThueTB" required><br><br>
        Phí đăng ký: <input type="number" name="PhiDKy" required><br>
    <?php endif; ?>

    <?php if ($LoaiXe == "OTo"): ?>
        <b>Thuế ô tô</b><br><br>
        Thuế TTĐB: <input type="number" name="ThueTTDB" required><br><br>
        Phí bảo trì: <input type="number" name="PhiBaoTri" required><br>
    <?php endif; ?>

    <?php if ($LoaiXe == "XeTai"): ?>
        <b>Thuế xe tải</b><br><br>
        Thuế vận tải: <input type="number" name="ThueVanTai" required><br><br>
        Phí kiểm định: <input type="number" name="PhiKiemDinh" required><br>
    <?php endif; ?>

    <br>
    <button type="submit">Thêm xe</button>
</form>

<h2>Danh sách xe</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Mã xe</th>
        <th>Tên xe</th>
        <th>Hãng xe</th>
        <th>Giá bán</th>
    </tr>
    <?php foreach ($_SESSION['DSXe'] as $xe): ?>
        <tr>
            <td><?= $xe['MaXe'] ?></td>
            <td><?= $xe['TenXe'] ?></td>
            <td><?= $xe['HangXe'] ?></td>
            <td><?= number_format($xe['GiaBan']) ?> VND</td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
