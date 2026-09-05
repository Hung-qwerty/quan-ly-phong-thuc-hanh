<?php
function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý Đặt phòng Thực hành</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7f9; color: #333; }
        .sidebar { position: fixed; width: 220px; height: 100vh; background: white; border-right: 1px solid #ddd; padding: 25px 15px; }
        .logo { color: #003399; font-weight: bold; font-size: 18px; margin-bottom: 30px; }
        .menu a { display: block; padding: 12px; margin-bottom: 5px; text-decoration: none; color: #444; border-radius: 6px; }
        .menu a:hover, .menu a.active { background: #eef4ff; color: #003399; }
        .main { margin-left: 220px; }
        .header { height: 60px; background: white; border-bottom: 1px solid #ddd; padding: 0 30px; display: flex; align-items: center; justify-content: space-between; }
        .content { padding: 35px; }
        h1 { color: #003399; margin-top: 0; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #e5f7eb; color: #207a45; }
        .rejected { background: #ffe5e5; color: #d32f2f; }
        .form { max-width: 600px; background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; }
        .group { margin-bottom: 17px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: Arial; }
        textarea { min-height: 90px; resize: vertical; }
        .error { color: #d32f2f; font-size: 13px; margin-top: 5px; }
        .success { max-width: 100%; background: #e6f7ed; color: #207a45; padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .btn { background: #003399; color: white; border: 0; padding: 10px 16px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #002266; }
        .btn-danger { background: #d32f2f; padding: 5px 10px; font-size: 12px; }
        .btn-danger:hover { background: #9a0007; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }
        th { background: #003399; color: white; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">LAB BOOKING</div>
    <nav class="menu">
        <a href="index.php?route=bookings&page=list" class="<?= $page == "list" ? "active" : "" ?>">Danh sách phiếu đặt</a>
        <a href="index.php?route=bookings&page=create" class="<?= $page == "create" ? "active" : "" ?>">Tạo yêu cầu mới</a>
    </nav>
</aside>

<main class="main">
    <header class="header">
        <span>Sinh viên</span>
        <strong><?= e($user["full_name"] ?? '') ?></strong>
    </header>

    <div class="content">
        <?php if (!empty($success)): ?>
            <div class="success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if (isset($errors["general"])): ?>
            <div class="error" style="margin-bottom: 15px;"><?= $errors["general"] ?></div>
        <?php endif; ?>

        <?php if ($page === "list"): ?>
            <div class="top-bar">
                <h1>Lịch sử & Danh sách Đặt phòng</h1>
                <a href="index.php?route=bookings&page=create" class="btn">+ Đặt phòng mới</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Phòng</th>
                        <th>Thời gian bắt đầu</th>
                        <th>Thời gian kết thúc</th>
                        <th>Mục đích</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($my_bookings)): ?>
                        <?php foreach ($my_bookings as $index => $b): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><strong><?= e($b["room_code"]) ?></strong> - <?= e($b["room_name"]) ?></td>
                                <td><?= e($b["start_time"]) ?></td>
                                <td><?= e($b["end_time"]) ?></td>
                                <td><?= e($b["purpose"]) ?></td>
                                <td>
                                    <?php if ($b["status"] === "pending"): ?>
                                        <span class="status pending">Chờ duyệt</span>
                                    <?php elseif ($b["status"] === "approved"): ?>
                                        <span class="status approved">Đã duyệt</span>
                                    <?php else: ?>
                                        <span class="status rejected">Từ chối</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($b["status"] === "pending"): ?>
                                        <form method="POST" action="index.php?route=bookings&page=list" onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu này?');" style="margin:0;">
                                            <input type="hidden" name="action" value="cancel_booking">
                                            <input type="hidden" name="booking_id" value="<?= $b["id"] ?>">
                                            <button type="submit" class="btn btn-danger">Hủy</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #888; font-size: 12px;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center;">Bạn chưa tạo yêu cầu đặt phòng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($page === "create"): ?>
            <div class="top-bar">
                <h1>Tạo Yêu cầu Đặt phòng</h1>
                <a href="index.php?route=bookings&page=list" class="btn" style="background:#6c757d;">Quay lại</a>
            </div>

            <div class="form">
                <form method="POST" action="index.php?route=bookings&page=create">
                    <input type="hidden" name="action" value="create_booking">
                    
                    <div class="group">
                        <label>Chọn phòng thực hành</label>
                        <select name="room_id">
                            <option value="">-- Chọn phòng --</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room["id"] ?>">
                                    <?= e($room["room_code"] . " - " . $room["room_name"] . " (Sức chứa: " . $room["capacity"] . ")") ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors["room_id"])): ?><div class="error"><?= $errors["room_id"] ?></div><?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Ngày sử dụng</label>
                        <input type="date" name="date" min="<?= date('Y-m-d') ?>">
                        <?php if (isset($errors["date"])): ?><div class="error"><?= $errors["date"] ?></div><?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Giờ bắt đầu</label>
                        <input type="time" name="start">
                        <?php if (isset($errors["start"])): ?><div class="error"><?= $errors["start"] ?></div><?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Giờ kết thúc</label>
                        <input type="time" name="end">
                        <?php if (isset($errors["end"])): ?><div class="error"><?= $errors["end"] ?></div><?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Mục đích sử dụng</label>
                        <textarea name="purpose" placeholder="Ví dụ: Thực hành môn Mạng máy tính, Làm bài tập nhóm..."></textarea>
                        <?php if (isset($errors["purpose"])): ?><div class="error"><?= $errors["purpose"] ?></div><?php endif; ?>
                    </div>

                    <button type="submit" class="btn">Gửi yêu cầu đặt phòng</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>