<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS `quan_ly_phong_thuc_hanh` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $conn->exec("USE `quan_ly_phong_thuc_hanh`");

    $sql_file = __DIR__ . '/database/database.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        $conn->exec($sql_content);
        echo "<h3 style='color: green; font-family: sans-serif;'>✅ Thiết lập CSDL thành công! Các bảng đã được tạo tự động.</h3>";
        echo "<p><a href='index.php'>Bấm vào đây để về trang chủ</a></p>";
    } else {
        echo "<h3 style='color: red;'>❌ Không tìm thấy file database.sql trong thư mục database/</h3>";
    }

} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Lỗi kết nối: " . $e->getMessage() . "</h3>";
}
?>