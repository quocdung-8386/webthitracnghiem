<?php
session_start();
class SinhVien
{
    // Properties
    public $MaSV;
    public $TenSV;
    public $NamSinh;
    public $DiemTB;

    // Constructor
    public function __construct($MaSV, $TenSV, $NamSinh, $DiemTB)
    {
        $this->MaSV = $MaSV;
        $this->TenSV = $TenSV;
        $this->NamSinh = $NamSinh;
        $this->DiemTB = $DiemTB;
    }

    public function TinhTuoi()
    {
        return date("Y") - $this->NamSinh;
    }

    public function XepLoai()
    {
        if ($this->DiemTB >= 8) {
            return "Giỏi";
        } elseif ($this->DiemTB >= 6.5) {
            return "Khá";
        } elseif ($this->DiemTB >= 5) {
            return "Trung Bình";
        } else {
            return "Yếu";
        }
    }
}

if (!isset($_SESSION['DanhSachSV'])) {
    $_SESSION['DanhSachSV'] = [];
}

if (isset($_POST['ThemSV'])) {
    $sv = new SinhVien(
        $_POST['MaSV'],
        $_POST['TenSV'],
        $_POST['NamSinh'],
        $_POST['DiemTB']
    );

    $_SESSION['DanhSachSV'][] = $sv;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập thông tin sinh viên</title>
</head>

<body>
    <h2>Nhập thông tin sinh viên</h2>
    <form method="post">
        <label>Mã SV:</label>
        <input type="number" name="MaSV" required><br><br>

        <label>Họ Tên:</label>
        <input type="text" name="TenSV" required><br><br>

        <label>Năm Sinh:</label>
        <input type="number" name="NamSinh" required><br><br>

        <label>Điểm TB:</label>
        <input type="number" step="0.1" name="DiemTB" required><br><br>

        <input type="submit" value="Thêm Sinh Viên" name="ThemSV">
    </form>

    <hr>

    <h2>THÔNG TIN SINH VIÊN</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Mã SV</th>
            <th>Họ Tên</th>
            <th>Tuổi</th>
            <th>Điểm TB</th>
            <th>Xếp loại</th>
        </tr>
        <?php
        if (!empty($_SESSION['DanhSachSV'])) {
            foreach ($_SESSION['DanhSachSV'] as $sv) {
                echo '<tr>';
                echo '<td>' . $sv->MaSV . '</td>';
                echo '<td>' . $sv->TenSV . '</td>';
                echo '<td>' . $sv->TinhTuoi() . '</td>';
                echo '<td>' . $sv->DiemTB . '</td>';
                echo '<td>' . $sv->XepLoai() . '</td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
</body>

</html>