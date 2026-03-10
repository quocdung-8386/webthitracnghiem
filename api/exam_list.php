<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../app/config/Database.php';

$conn = Database::getConnection();

$sql = "SELECT ma_de_thi, tieu_de FROM de_thi ORDER BY ma_de_thi DESC";
$result = $conn->query($sql);

$exams = [];
while ($row = $result->fetch_assoc()) {
    $exams[] = [
        'ma_de_thi' => $row['ma_de_thi'],
        'tieu_de' => htmlspecialchars($row['tieu_de'], ENT_QUOTES, 'UTF-8')
    ];
}

echo json_encode([
    'success' => true,
    'data' => $exams
]);

$conn->close();

