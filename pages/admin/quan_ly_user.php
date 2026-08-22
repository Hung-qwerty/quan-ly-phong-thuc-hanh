<?php
require_once '../../config/database.php';

$errors = [];
$success_msg = "";
$u = "";
$n = "";
$r = "staff";

// Xác định tab đang chọn (mặc định là 'internal' nếu không có tab trên URL)
$tab = $_GET['tab'] ?? 'internal';

// 1. Xử lý DUYỆT tài khoản sinh viên
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $approve_id = intval($_GET['id']);
    $stmt_app = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
    $stmt_app->execute([$approve_id]);
    header("Location: quan_ly_user.php?tab=students&msg=approved");
    exit;
}

// 2. Xử lý XÓA tài khoản
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $stmt_del = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_del->execute([$delete_id]);
    header("Location: quan_ly_user.php?tab=" . $tab . "&msg=deleted");
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
            $hashed_pass = password_hash($password_raw, PASSWORD_DEFAULT);
            $stmt_ins = $conn->prepare("INSERT INTO users (username, password, full_name, role, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt_ins->execute([$u, $hashed_pass, $n, $r]);
            $success_msg = "Thêm tài khoản nội bộ thành công!";
            $u = $n = "";
        }
    }
}

// Lấy dữ liệu
$stmt_students = $conn->query("SELECT * FROM users WHERE role = 'student' ORDER BY (status = 'pending') DESC, id DESC");
$students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Quản lý Hệ thống Tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
        :root { --hnmu-blue: #003399; }
        
        /* Sidebar styling giống mẫu */
        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }
        .sidebar .brand {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--hnmu-blue);
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid #dee2e6;
            letter-spacing: 0.5px;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 6px;
            margin: 4px 12px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #eef2ff;
            color: var(--hnmu-blue);
        }

        /* Main content styling */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 30px;
            margin-left: 260px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .btn-primary-custom {
            background-color: var(--hnmu-blue);
            color: white;
            border: none;
        }
        .btn-primary-custom:hover {
            background-color: #002266;
            color: white;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR BÊN TRÁI -->
    <div class="sidebar">
        <div class="brand">LAB MANAGEMENT</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?php echo ($tab == 'internal') ? 'active' : ''; ?>" href="?tab=internal">
                    Quản lý tài khoản Nội bộ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($tab == 'students') ? 'active' : ''; ?>" href="?tab=students">
                    Duyệt tài khoản Sinh viên
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="../login.php">Đăng xuất</a>
            </li>
        </ul>
    </div>

    <!-- NAVBAR PHÍA TRÊN -->
    <div class="top-navbar">
        <span class="text-muted small">Admin Portal</span>
        <span class="fw-bold text-primary">Admin Hệ Thống</span>
    </div>

    <!-- KHU VỰC HIỂN THỊ NỘI DUNG CHÍNH (BÊN PHẢI) -->
    <div class="main-content">
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($errors['username'])): ?>
            <div class="alert alert-danger"><?php echo $errors['username']; ?></div>
        <?php endif; ?>

        <!-- MỤC 1: QUẢN LÝ TÀI KHOẢN NỘI BỘ (ADMIN / STAFF) -->
        <?php if ($tab == 'internal'): ?>
            <div class="card card-custom p-4 mb-4">
                <h4 class="text-primary mb-3">Thêm mới tài khoản Nội bộ</h4>
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
                        <button type="submit" class="btn btn-primary-custom w-100">Thêm mới</button>
                    </div>
                </form>
            </div>

            <div class="card card-custom p-4">
                <h4 class="text-primary mb-3">Danh sách Admin & Cán bộ Lab</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                                    <?php if ($internal['role'] !== 'admin'): ?>
                                        <a href="?tab=internal&action=delete&id=<?php echo $internal['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?');">Xóa</a>
                                    <?php else: ?>
                                        <span class="text-muted small">Quản trị viên chính</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- MỤC 2: DUYỆT TÀI KHOẢN SINH VIÊN -->
        <?php if ($tab == 'students'): ?>
            <div class="card card-custom p-4">
                <h4 class="text-primary mb-3">Danh sách & Phê duyệt tài khoản Sinh viên</h4>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
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
                                            <a href="?tab=students&action=approve&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success me-1">Duyệt</a>
                                        <?php endif; ?>
                                        <a href="?tab=students&action=delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');">Xóa</a>
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
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>