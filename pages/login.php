<?php
session_start();
session_unset();
session_destroy();
session_start(); 

require_once '../config/database.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập tên đăng nhập và mật khẩu!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (isset($user['status']) && $user['status'] == 'pending') {
                $error = "Tài khoản của bạn đang chờ Admin phê duyệt!";
            } elseif (isset($user['status']) && $user['status'] == 'rejected') {
                $error = "Tài khoản của bạn đã bị từ chối truy cập!";
            } else {
                // Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Dùng đường dẫn chuẩn xác tuyệt đối từ thư mục gốc của project
                $base_url = "/quan-ly-phong-thuc-hanh";

                if ($user['role'] == 'admin') {
                    header("Location: " . $base_url . "/pages/admin/quan_ly_user.php");
                    exit;
                } elseif ($user['role'] == 'staff') {
                    header("Location: " . $base_url . "/pages/staff/formbaotritv5.php");
                    exit;
                } else {
                    header("Location: " . $base_url . "/pages/student/TV3-quanlybooking.php");
                    exit;
                }
            }
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f9; font-family: 'Segoe UI', sans-serif; }
        .card-custom { border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card card-custom p-4 bg-white" style="width: 400px;">
        <h3 class="text-center mb-3 text-primary">Đăng Nhập Hệ Thống</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập username..." required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
        </form>
        <div class="text-center mt-3">
            <small>Chưa có tài khoản sinh viên? <a href="register.php">Đăng ký ngay</a></small>
        </div>
    </div>
</body>
</html>
