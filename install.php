<?php
// install.php - Tự động thiết lập, làm sạch CSDL và nạp tài khoản mẫu cho nhóm
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

    // 4. Tắt kiểm tra khóa ngoại tạm thời để xóa bảng an toàn
    $conn->exec("SET foreign_key_checks = 0");

    // 5. Đọc nội dung file database.sql để tạo cấu trúc bảng
    $sql_file = __DIR__ . '/database/database.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        $conn->exec($sql_content);
    } else {
        throw new Exception("Không tìm thấy file database/database.sql!");
    }

    // Bật lại kiểm tra khóa ngoại
    $conn->exec("SET foreign_key_checks = 1");

    // 6. Tự động thêm 3 tài khoản mẫu (admin, staff, student) với mật khẩu là '123'
    // Sử dụng INSERT IGNORE hoặc kiểm tra để không bị lỗi nếu chạy lại nhiều lần
    $pass_admin = password_hash('123', PASSWORD_DEFAULT);
    $pass_staff = password_hash('123', PASSWORD_DEFAULT);
    $pass_student = password_hash('123', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (username, password, full_name, role, status) VALUES
        ('admin', ?, 'Quản Trị Viên', 'admin', 'active'),
        ('staff', ?, 'Cán Bộ Phòng Máy', 'staff', 'active'),
        ('student', ?, 'Sinh Viên Thực Hành', 'student', 'active')
        ON DUPLICATE KEY UPDATE password = VALUES(password)
    ");
    
    $stmt->execute([$pass_admin, $pass_staff, $pass_student]);

    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h2 style='color: green;'>✅ Thiết lập CSDL và nạp tài khoản mẫu thành công!</h2>";
    echo "<p>Danh sách tài khoản test nhanh (Mật khẩu chung: <strong>123</strong>):</p>";
    echo "<ul style='list-style: none; padding: 0;'>";
    echo "<li>👑 <strong>Admin:</strong> admin / 123</li>";
    echo "<li>🛠️ <strong>Staff:</strong> staff / 123</li>";
    echo "<li>🎓 <strong>Student:</strong> student / 123</li>";
    echo "</ul>";
    echo "<br><a href='public/index.php' style='padding: 10px 20px; background: #003399; color: white; text-decoration: none; border-radius: 5px;'>Về trang chủ đăng nhập</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h3 style='color: red; text-align: center; margin-top: 50px;'>❌ Lỗi cài đặt CSDL: " . $e->getMessage() . "</h3>";
}
?>