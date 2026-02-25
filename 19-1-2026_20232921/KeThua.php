<?php
session_start();
class NhanVien
{
    public $MaNV;
    public $HoTen;
    public $NamSinh;
    public $GioiTinh;
    public $LuongCB;

    public function __construct($MaNV, $HoTen, $NamSinh, $GioiTinh, $LuongCB)
    {
        $this->MaNV = $MaNV;
        $this->HoTen = $HoTen;
        $this->NamSinh = $NamSinh;
        $this->LuongCB = $LuongCB;
        $this->GioiTinh = $GioiTinh;
    }

    public function TinhTuoi()
    {
        return date("Y") - $this->NamSinh;
    }

    public function TinhLuong()
    {
        return $this->LuongCB;
    }

    public function FormatLuong()
    {
        return number_format($this->TinhLuong(), 0, ',', '.');
    }
}

class GiaoVien extends NhanVien
{
    public $PhuCap;

    public function __construct($MaNV, $HoTen, $NamSinh, $GioiTinh, $LuongCB, $PhuCap)
    {
        parent::__construct($MaNV, $HoTen, $NamSinh, $GioiTinh, $LuongCB);
        $this->PhuCap = $PhuCap;
    }

    public function TinhLuong()
    {
        return $this->LuongCB + $this->PhuCap;
    }
}

class NhanVienVanPhong extends NhanVien
{
    public function __construct($MaNV, $HoTen, $NamSinh, $GioiTinh, $LuongCB)
    {
        parent::__construct($MaNV, $HoTen, $NamSinh, $GioiTinh, $LuongCB);
    }

    public function TinhLuong()
    {
        return $this->LuongCB;
    }
}


if (!isset($_SESSION['DanhSachNV'])) {
    $_SESSION['DanhSachNV'] = [];
}

if (isset($_POST['ThemNV'])) {
    if ($_POST['LoaiNV'] == 'GiaoVien') {
        $nv = new GiaoVien(
            $_POST['MaNV'],
            $_POST['HoTen'],
            $_POST['NamSinh'],
            $_POST['GioiTinh'],
            $_POST['LuongCB'],
            $_POST['PhuCap']
        );
    } else {
        $nv = new NhanVienVanPhong(
            $_POST['MaNV'],
            $_POST['HoTen'],
            $_POST['NamSinh'],
            $_POST['GioiTinh'],
            $_POST['LuongCB']
        );
    }

    $_SESSION['DanhSachNV'][] = $nv;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kế Thừa</title>
</head>
<body>
    <h2>Nhập Thông Tin Nhân Viên</h2>
    <form method="post">
        Mã NV: <input type="text" name="MaNV" required><br><br>
        Họ Tên: <input type="text" name="HoTen" required><br><br>
        Năm Sinh: <input type="number" name="NamSinh" required><br><br>
        Giới Tính:
        <select name="GioiTinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select><br><br>
        Lương Cơ Bản: <input type="number" name="LuongCB" required><br><br>
        Loại Nhân Viên:
        <select name="LoaiNV">
            <option value="GiaoVien">Giáo Viên</option>
            <option value="NhanVienVanPhong">Nhân Viên Văn Phòng</option>
        </select><br><br>
        Phụ Cấp (Chỉ dành cho Giáo Viên): <input type="number" name="PhuCap"><br><br>
        <input type="submit" value="Thêm Nhân Viên" name="ThemNV">
    </form>
    <hr>
    <?php if (isset($_SESSION['DanhSachNV']) && !empty($_SESSION['DanhSachNV'])): ?>
        <h2>THÔNG TIN NHÂN VIÊN</h2>
        <table border="1" cellpadding="10">
            <tr>
                <th>Mã NV</th>
                <th>Họ Tên</th>
                <th>Tuổi</th>
                <th>Giới Tính</th>
                <th>Lương</th>
            </tr>
            <?php foreach ($_SESSION['DanhSachNV'] as $nv): ?>
            <tr>
                <td><?php echo $nv->MaNV; ?></td>
                <td><?php echo $nv->HoTen; ?></td>
                <td><?php echo $nv->TinhTuoi(); ?></td>
                <td><?php echo $nv->GioiTinh; ?></td>
                <td><?php echo $nv->FormatLuong(); ?> VNĐ</td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>