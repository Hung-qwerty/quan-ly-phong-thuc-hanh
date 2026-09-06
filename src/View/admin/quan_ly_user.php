<?php
$offset = $offset ?? 0;
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
$search = $search ?? '';
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
        .pagination .page-link { color: var(--hnmu-blue); }
        .pagination .page-item.active .page-link { background-color: var(--hnmu-blue); border-color: var(--hnmu-blue); color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">LAB MANAGEMENT</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?php echo (($tab ?? 'internal') == 'internal') ? 'active' : ''; ?>" href="index.php?route=users&tab=internal">
                    Quản lý tài khoản Nội bộ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (($tab ?? '') == 'students') ? 'active' : ''; ?>" href="index.php?route=users&tab=students">
                    Duyệt tài khoản Sinh viên
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="index.php?route=logout">Đăng xuất</a>
            </li>
        </ul>
    </div>

    <div class="top-navbar">
        <span class="text-muted small">Admin Portal</span>
        <span class="fw-bold text-primary">Admin Hệ Thống</span>
    </div>

    <div class="main-content">
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <?php if (!empty($errors['username'])): ?>
            <div class="alert alert-danger"><?php echo $errors['username']; ?></div>
        <?php endif; ?>

        <!-- TAB NỘI BỘ -->
        <?php if (($tab ?? 'internal') == 'internal'): ?>
            <div class="card card-custom p-4 mb-4">
                <h4 class="text-primary mb-3">Thêm mới tài khoản Nội bộ</h4>
                <form method="POST" action="index.php?route=users&tab=internal" class="row g-3">
                    <input type="hidden" name="add_internal_user" value="1">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="username..." value="<?php echo htmlspecialchars($u ?? ''); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Họ tên đầy đủ..." value="<?php echo htmlspecialchars($n ?? ''); ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu..." required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="staff" <?php echo (($r ?? '') == 'staff') ? 'selected' : ''; ?>>Cán bộ Lab</option>
                            <option value="admin" <?php echo (($r ?? '') == 'admin') ? 'selected' : ''; ?>>Admin</option>
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
                            <?php if (!empty($internals)): ?>
                                <?php foreach ($internals as $idx => $internal): ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($internal['username']); ?></td>
                                    <td><?php echo htmlspecialchars($internal['full_name']); ?></td>
                                    <td><span class="badge bg-<?php echo $internal['role'] == 'admin' ? 'danger' : 'warning text-dark'; ?>"><?php echo $internal['role']; ?></span></td>
                                    <td class="text-center">
                                        <?php if ($internal['role'] !== 'admin'): ?>
                                            <a href="index.php?route=users&tab=internal&action=delete&id=<?php echo $internal['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?');">Xóa</a>
                                        <?php else: ?>
                                            <span class="text-muted small">Quản trị viên chính</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- TAB SINH VIÊN -->
        <?php if (($tab ?? '') == 'students'): ?>
            <div class="card card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-primary m-0">Quản lý tài khoản Sinh viên</h4>
                    
                    <!-- Form Tìm kiếm -->
                    <form method="GET" action="index.php" class="d-flex">
                        <input type="hidden" name="route" value="users">
                        <input type="hidden" name="tab" value="students">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Tìm tên hoặc mã SV..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 250px;">
                        <button type="submit" class="btn btn-sm btn-primary-custom">Tìm kiếm</button>
                        <?php if(!empty($search)): ?>
                            <a href="index.php?route=users&tab=students" class="btn btn-sm btn-outline-secondary ms-1">Xóa lọc</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Form Duyệt hàng loạt -->
                <form method="POST" action="index.php?route=users&tab=students">
                    <input type="hidden" name="action" value="approve_multiple">
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Duyệt tất cả tài khoản đã chọn?');">
                            ✓ Duyệt các mục đã chọn
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 40px;">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </th>
                                    <th>STT</th>
                                    <th>Mã sinh viên</th>
                                    <th>Họ tên</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students) && count($students) > 0): ?>
                                    <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td>
                                            <?php if (($student['status'] ?? '') == 'pending'): ?>
                                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $offset + $index + 1; ?></td>
                                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($student['username']); ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td>
                                            <?php 
                                                $status = $student['status'] ?? '';
                                                if ($status == 'pending') echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                                elseif ($status == 'active') echo '<span class="badge bg-success">Đã kích hoạt</span>';
                                                else echo '<span class="badge bg-danger">Từ chối</span>';
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($student['status'] ?? '') == 'pending'): ?>
                                                <a href="index.php?route=users&tab=students&action=approve&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success me-1">Duyệt nhanh</a>
                                            <?php endif; ?>
                                            <a href="index.php?route=users&tab=students&action=delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?');">Xóa</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Không tìm thấy sinh viên nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Phân trang -->
                <?php if (($totalPages ?? 0) > 1): ?>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-end mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?route=users&tab=students&search=<?php echo urlencode($search); ?>&p=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script hỗ trợ Checkbox "Chọn tất cả"
        document.getElementById('selectAll')?.addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.student-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    </script>
</body>
</html>