<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "127.0.0.1";
$dbname = "quan_ly_phong_thuc_hanh";
$username = "root";
$password = "";
$charset = "utf8mb4";

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die(
        "Không thể kết nối cơ sở dữ liệu.<br>" .
        "Kiểm tra MySQL/XAMPP, tên database, username/password trong file ketnoi.php.<br>" .
        "Chi tiết: " . htmlspecialchars($e->getMessage())
    );
}
?>
