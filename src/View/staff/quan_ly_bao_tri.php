<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phòng Bảo trì Thiết bị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .main-container { max-width: 1200px; margin: 30px auto; }
    </style>
</head>
<body>
<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>PHÒNG BẢO TRÌ THIẾT BỊ</h2>
        <a href="index.php?route=devices" class="btn btn-outline-primary">← Quay lại Quản lý thiết bị</a>
    </div>

    <?php if (!empty($thongbao)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($thongbao) ?></div>
    <?php endif; ?>
    <?php if (!empty($loi)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($loi) ?></div>
    <?php endif; ?>

    <div class="card p-3 shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Mã thiết bị</th>
                    <th>Tên thiết bị</th>
                    <th>Trạng thái</th>
                    <th>Thông tin bảo trì</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($thietbi as $i => $tb): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($tb['device_code'] ?? '') ?></td>
                    <td><strong><?= htmlspecialchars($tb['device_name'] ?? '') ?></strong></td>
                    <td>
                        <span class="badge bg-<?= ($tb['device_status'] === 'Đang bảo trì') ? 'warning text-dark' : (($tb['device_status'] === 'Hỏng') ? 'danger' : 'success') ?>">
                            <?= htmlspecialchars($tb['device_status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($tb['maintenance_id'])): ?>
                            <small>
                                <strong>Phiếu #<?= $tb['maintenance_id'] ?></strong><br>
                                Ngày: <?= htmlspecialchars($tb['created_at'] ?? '') ?><br>
                                Nội dung: <?= htmlspecialchars($tb['description'] ?? '') ?>
                            </small>
                        <?php else: ?>
                            <span class="text-muted small">Chưa có lịch sử</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($tb['device_status'] === 'Hỏng'): ?>
                            <form method="POST">
                                <input type="hidden" name="device_id" value="<?= $tb['device_id'] ?>">
                                <input type="text" name="description" class="form-control form-control-sm mb-1" placeholder="Mô tả lỗi..." required>
                                <button type="submit" name="batdau_baotri" class="btn btn-sm btn-warning w-100">Bắt đầu bảo trì</button>
                            </form>
                        <?php elseif ($tb['device_status'] === 'Đang bảo trì'): ?>
                            <form method="POST">
                                <input type="hidden" name="maintenance_id" value="<?= $tb['maintenance_id'] ?>">
                                <input type="hidden" name="device_id" value="<?= $tb['device_id'] ?>">
                                <select name="status" class="form-select form-select-sm mb-1">
                                    <option value="Đang bảo trì">Đang bảo trì</option>
                                    <option value="Hoàn thành">Hoàn thành</option>
                                </select>
                                <input type="text" name="result" class="form-control form-control-sm mb-1" placeholder="Kết quả xử lý..." required>
                                <button type="submit" name="capnhat" class="btn btn-sm btn-success w-100">Cập nhật</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">Không cần bảo trì</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>