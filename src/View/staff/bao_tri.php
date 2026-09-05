<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bảo trì thiết bị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
        :root { --hnmu-blue: #003399; }
        .sidebar { inline-size: 260px; block-size: 100vh; background-color: #ffffff; border-inline-end: 1px solid #dee2e6; position: fixed; inset-block-start: 0; inset-inline-start: 0; padding-block-start: 20px; }
        .sidebar .brand { font-size: 1.1rem; font-weight: bold; color: var(--hnmu-blue); padding: 0 20px 20px 20px; border-block-end: 1px solid #dee2e6; letter-spacing: 0.5px; }
        .main-content { margin-inline-start: 260px; padding: 30px; }
        .card-custom { background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); }
        .btn-primary-custom { background-color: var(--hnmu-blue); color: white; border: none; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">LAB MANAGEMENT</div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link text-dark active fw-bold" href="index.php?route=maintenance">Quản lý Bảo trì</a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="index.php?route=logout">Đăng xuất</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <h3 class="text-primary fw-bold mb-1">PHÒNG BẢO TRÌ THIẾT BỊ</h3>
        <p class="text-muted mb-4">Quản lý bảo trì và cập nhật trạng thái thiết bị</p>

        <?php if (!empty($thongbao)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($thongbao) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($loi)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($loi) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Thống kê -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <h6 class="text-muted">Tổng thiết bị</h6>
                    <h3 class="fw-bold text-dark"><?= $stats['tong'] ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <h6 class="text-muted">Hoạt động</h6>
                    <h3 class="fw-bold text-success"><?= $stats['hoatdong'] ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <h6 class="text-muted">Hỏng</h6>
                    <h3 class="fw-bold text-danger"><?= $stats['hong'] ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom p-3 text-center">
                    <h6 class="text-muted">Đang bảo trì</h6>
                    <h3 class="fw-bold text-warning"><?= $stats['baotri'] ?></h3>
                </div>
            </div>
        </div>

        <!-- Danh sách thiết bị -->
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã TB</th>
                            <th>Thiết bị</th>
                            <th>Trạng thái</th>
                            <th>Thông tin bảo trì</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($thietbi)): ?>
                            <?php foreach ($thietbi as $i => $tb): 
                                $status = trim((string)$tb["device_status"]);
                                $isBroken = in_array($status, ["Hỏng", "broken"]);
                                $isMaintenance = in_array($status, ["Đang bảo trì", "maintenance"]);
                                $badgeClass = $isBroken ? "bg-danger" : ($isMaintenance ? "bg-warning text-dark" : "bg-success");
                                $statusText = $isBroken ? "Hỏng" : ($isMaintenance ? "Đang bảo trì" : "Hoạt động");
                                $mId = (int)($tb["maintenance_id"] ?? 0);
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($tb["device_code"] ?? "") ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($tb["device_name"] ?? "") ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $statusText ?></span></td>
                                <td>
                                    <?php if ($mId > 0): ?>
                                        <strong>Phiếu #<?= $mId ?></strong><br>
                                        <small class="text-muted">Trạng thái: <?= htmlspecialchars($tb["maintenance_status"] ?? "") ?></small><br>
                                        <small>Ngày: <?= htmlspecialchars($tb["maintenance_date"] ?? $tb["created_at"] ?? "") ?></small><br>
                                        <small>Nội dung: <?= htmlspecialchars($tb["description"] ?? $tb["content"] ?? "") ?></small>
                                        <?php if (!empty($tb["result"])): ?>
                                            <br><small class="text-success">KQ: <?= htmlspecialchars($tb["result"]) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="text-muted">Chưa có lịch sử.</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($isBroken): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="device_id" value="<?= (int)$tb["device_id"] ?>">
                                            <input type="text" name="description" class="form-control form-control-sm mb-1" placeholder="Nội dung bảo trì..." required>
                                            <button type="submit" name="batdau_baotri" class="btn btn-sm btn-primary-custom w-100">Bắt đầu</button>
                                        </form>
                                    <?php elseif ($isMaintenance && $mId > 0): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="maintenance_id" value="<?= $mId ?>">
                                            <input type="hidden" name="device_id" value="<?= (int)$tb["device_id"] ?>">
                                            <select name="status" class="form-select form-select-sm mb-1">
                                                <option value="Đang bảo trì">Đang bảo trì</option>
                                                <option value="Hoàn thành">Hoàn thành</option>
                                            </select>
                                            <input type="text" name="result" class="form-control form-control-sm mb-1" placeholder="Kết quả..." required>
                                            <button type="submit" name="capnhat" class="btn btn-sm btn-warning w-100">Cập nhật</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">Không yêu cầu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">Chưa có thiết bị.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>