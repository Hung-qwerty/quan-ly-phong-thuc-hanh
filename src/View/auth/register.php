<?php
require_once '../config/database.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');

    if (empty($username) || empty($password) || empty($fullname)) {
        $error = "Vui lòng điền đầy đủ tất cả các trường!";
    } else {
        // Kiểm tra username đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Tên đăng nhập này đã tồn tại!";
        } else {
            // Lưu với role = student và status = pending (chờ admin duyệt)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_ins = $conn->prepare("INSERT INTO users (username, password, full_name, role, status) VALUES (?, ?, ?, 'student', 'pending')");
            if ($stmt_ins->execute([$username, $hashed_password, $fullname])) {
                $success = "Đăng ký thành công! Tài khoản của bạn đang chờ Admin phê duyệt.";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản Sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #f4f7f9; font-family: 'Segoe UI', sans-serif; }</style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow-sm" style="width: 400px; border-radius: 8px;">
        <h3 class="text-center mb-3 text-primary">Đăng ký Sinh viên</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2"><?php echo $success; ?></div>
            <a href="index.php?route=login" class="btn btn-primary w-100 mt-2">Đăng nhập ngay</a>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
            </form>
            <div class="text-center mt-3">
                <small>Đã có tài khoản? <a href="login.php">Đăng nhập</a></small>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>