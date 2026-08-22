<?php
require_once '../../config/database.php';

$errors = [];
$success_msg = "";
$u = "";
$n = "";
$r = "staff"; // Mặc định cho form thêm nội bộ là staff

// 1. Xử lý DUYỆT tài khoản sinh viên
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $approve_id = intval($_GET['id']);
    $stmt_app = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt_app->execute([$approve_id]);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?msg=approved");
    exit;
}

// 2. Xử lý XÓA tài khoản
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $stmt_del = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_del->execute([$delete_id]);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?msg=deleted");
    exit;
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'approved') $success_msg = "Phê duyệt tài khoản sinh viên thành công!";
    if ($_GET['msg'] == 'deleted') $success_msg = "Xóa tài khoản thành công!";
}

// 3. Xử lý THÊM tài khoản nội bộ (Admin / Staff)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_internal_user'])) {
    $u = trim($_POST['username'] ?? '');
    $n = trim($_POST['fullname'] ?? '');
    $r = $_POST['role'] ?? 'staff';
    $password_raw = trim($_POST['password'] ?? '');

    if (empty($u)) $errors['username'] = "Tên đăng nhập không được để trống!";
    if (empty($n)) $errors['fullname'] = "Họ tên không được để trống!";
    if (empty($password_raw)) $errors['password'] = "Mật khẩu không được để trống!";

    if (empty($errors)) {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->execute([$u]);
        if ($stmt_check->rowCount() > 0) {
            $errors['username'] = "Tên đăng nhập này đã tồn tại!";
        } else {
            // Tài khoản nội bộ do Admin tạo sẽ có status là 'active' luôn
            $hashed_pass = password_hash($password_raw, PASSWORD_DEFAULT);
            $stmt_ins = $conn->prepare("INSERT INTO users (username, password, full_name, role, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt_ins->execute([$u, $hashed_pass, $n, $r]);
            $success_msg = "Thêm tài khoản nội bộ thành công!";
            $u = $n = "";
        }
    }
}

// Lấy danh sách tài khoản sinh viên (phục vụ Mục 2)
$stmt_students = $conn->query("SELECT * FROM users WHERE role = 'student' ORDER BY (status = 'pending') DESC, id DESC");
$students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách tài khoản nội bộ Admin/Staff (phục vụ Mục 1 bổ sung)
$stmt_internals = $conn->query("SELECT * FROM users WHERE role IN ('admin', 'staff') ORDER BY id DESC");
$internals = $stmt_internals->fetchAll(PDO::FETCH_ASSOC);

function getStatusBadge($status) {
    if ($status == 'pending') return '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
    if ($status == 'active') return '<span class="badge bg-success">Đã kích hoạt</span>';
    return '<span class="badge bg-danger">Từ chối</span>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài khoản - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f9; font-family: 'Segoe UI', sans-serif; }
        .card-custom { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        :root { --hnmu-blue: #003399; }
        .btn-primary-custom { background-color: var(--hnmu-blue); color: white; border: none; }
        .btn-primary-custom:hover { background-color: #002266; color: white; }
        h3, h5 { color: var(--hnmu-blue); }
    </style>
</head>
<body class="py-4">
    <div class="container" style="max-width: 1100px;">
        <div class="card card-custom p-4 mb-4">
            <h3 class="mb-1">QUẢN LÝ HỆ THỐNG TÀI KHOẢN</h3>
            <p class="text-muted mb-0">Phân tách rõ ràng giữa cấp tài khoản nội bộ và duyệt sinh viên</p>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <!-- ================= MỤC 1: THÊM TÀI KHOẢN ADMIN / STAFF ================= -->
        <div class="card card-custom p-4 mb-4 border-start border-primary border-4">
            <h5 class="mb-3">1. Thêm tài khoản Nội bộ (Admin / Cán bộ Lab)</h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="add_internal_user" value="1">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Tên đăng nhập</label>
                    <input type="text" name="username" class="form-control" placeholder="username..." value="<?php echo htmlspecialchars($u); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" placeholder="Họ tên đầy đủ..." value="<?php echo htmlspecialchars($n); ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu..." required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="staff" <?php echo ($r == 'staff') ? 'selected' : ''; ?>>Cán bộ Lab</option>
                        <option value="admin" <?php echo ($r == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-custom w-100">Tạo tài khoản</button>
                </div>
            </form>

            <!-- Bảng danh sách tài khoản nội bộ đang có -->
            <div class="mt-4">
                <h6 class="text-secondary">Danh sách Admin & Cán bộ Lab hiện tại:</h6>
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Vai trò</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($internals as $idx => $internal): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($internal['username']); ?></td>
                            <td><?php echo htmlspecialchars($internal['full_name']); ?></td>
                            <td><span class="badge bg-<?php echo $internal['role'] == 'admin' ? 'danger' : 'warning text-dark'; ?>"><?php echo $internal['role']; ?></span></td>
                            <td class="text-center">
                                <?php if ($internal['username'] !== 'admin'): // Không cho xóa tài khoản admin gốc ?>
                                    <a href="?action=delete&id=<?php echo $internal['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?');">Xóa</a>
                                <?php else: ?>
                                    <span class="text-muted small">Mặc định</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= MỤC 2: QUẢN LÝ VÀ DUYỆT SINH VIÊN ================= -->
        <div class="card card-custom p-4 border-start border-success border-4">
            <h5 class="mb-3">2. Quản lý & Phê duyệt tài khoản Sinh viên đăng ký</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>STT</th>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo getStatusBadge($student['status']); ?></td>
                                <td class="text-center">
                                    <?php if ($student['status'] == 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success me-1">Duyệt</a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');">Xóa</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Chưa có sinh viên nào đăng ký tài khoản.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>