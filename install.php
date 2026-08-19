<?php
// install.php - Tự động thiết lập và làm sạch CSDL cho thành viên trong nhóm
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'quan_ly_phong_thuc_hanh';

try {
    // 1. Kết nối vào MySQL (chưa chọn database)
    $conn = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Tạo database nếu chưa có
    $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. Chọn database vừa tạo
    $conn->exec("USE `$dbname`");

    // 4. Tắt kiểm tra khóa ngoại tạm thời để có thể xóa bảng theo đúng thứ tự an toàn
    $conn->exec("SET foreign_key_checks = 0");

    // 5. Đọc nội dung file database.sql
    $sql_file = __DIR__ . '/database/database.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);

        // Chạy toàn bộ câu lệnh từ file SQL (file SQL cần có lệnh DROP TABLE IF EXISTS ở đầu mỗi bảng)
        $conn->exec($sql_content);

        // Bật lại kiểm tra khóa ngoại
        $conn->exec("SET foreign_key_checks = 1");

        echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
        echo "<h2 style='color: green;'>✅ Thiết lập và đồng bộ CSDL thành công!</h2>";
        echo "<p>Các thành viên có thể sử dụng hệ thống ngay lập tức.</p>";
        echo "<br><a href='index.php' style='padding: 10px 20px; background: #003399; color: white; text-decoration: none; border-radius: 5px;'>Về trang chủ</a>";
        echo "</div>";
    } else {
        echo "<h3 style='color: red; text-align: center; margin-top: 50px;'>❌ Không tìm thấy file database/database.sql!</h3>";
    }

} catch (PDOException $e) {
    echo "<h3 style='color: red; text-align: center; margin-top: 50px;'>❌ Lỗi kết nối hoặc chạy SQL: " . $e->getMessage() . "</h3>";
}
?>