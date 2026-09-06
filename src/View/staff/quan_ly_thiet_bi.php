<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thiết bị & Duyệt Đặt phòng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f9; font-family: "Segoe UI", Arial, sans-serif; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background: #fff; border-right: 1px solid #dee2e6; padding: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .nav-link.active { background: #eaf0fa; color: #003399; font-weight: bold; }
    </style>
</head>
<body>
<div class="sidebar">
    <h4 class="text-primary mb-4">LAB MANAGEMENT</h4>
    <nav class="nav flex-column">
        <a class="nav-link <?= $page === 'devices' ? 'active' : '' ?>" href="index.php?route=devices&page=devices">Quản lý Thiết bị</a>
        <a class="nav-link <?= $page === 'bookings' ? 'active' : '' ?>" href="index.php?route=devices&page=bookings">Duyệt Yêu cầu</a>
        <a class="nav-link text-warning mt-3" href="index.php?route=maintenance">Quản lý Bảo trì</a>
        <a class="nav-link text-danger mt-4" href="index.php?route=logout">Đăng xuất</a>
    </nav>
</div>

<div class="main-content">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($page === 'devices'): ?>
        <h2>Quản lý Thiết bị</h2>
        <div class="card p-3 mb-4 shadow-sm">
            <h5>Thêm thiết bị mới</h5>
            <form method="POST" class="row g-3">
                <input type="hidden" name="action" value="add">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Tên thiết bị..." required>
                </div>
                <div class="col-md-3">
                    <select name="type_id" class="form-select" required>
                        <option value="">-- Chọn loại --</option>
                        <?php foreach ($deviceTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="room_id" class="form-select" required>
                        <option value="">-- Chọn phòng --</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['room_name'] ?? $room['display_name'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Thêm mới</button>
                </div>
            </form>
        </div>

        <div class="card p-3 shadow-sm">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên thiết bị</th>
                        <th>Loại</th>
                        <th>Phòng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devices)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Chưa có thiết bị nào trong hệ thống.</td></tr>
                    <?php else: ?>
                        <?php foreach ($devices as $idx => $dev): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($dev['device_name'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($dev['type_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dev['room_display_name'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge bg-<?= ($dev['status'] === 'Đang bảo trì') ? 'warning text-dark' : 'success' ?>">
                                    <?= htmlspecialchars($dev['status'] ?? 'Hoạt động') ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $dev['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <h2>Duyệt Yêu cầu Đặt phòng</h2>
        <div class="card p-3 shadow-sm">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Người đặt</th>
                        <th>Phòng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Chưa có yêu cầu đặt phòng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $idx => $b): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><?= htmlspecialchars($b['user_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($b['room_display_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($b['start_time'] ?? '') ?> - <?= htmlspecialchars($b['end_time'] ?? '') ?></td>
                            <td>
                                <?php if (($b['status'] ?? '') === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                <?php elseif (($b['status'] ?? '') === 'approved'): ?>
                                    <span class="badge bg-success">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($b['status'] ?? '') === 'pending'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="booking">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-success">Đồng ý</button>
                                    </form>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="booking">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-danger">Từ chối</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">Đã xử lý</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>