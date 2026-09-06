<?php

$user = $user ?? null;
$success = $success ?? null;
$errors = $errors ?? [];
$page = $page ?? $_GET['page'] ?? 'home';
$rooms = $rooms ?? [];
$devices = $devices ?? [];

$myBookings = $myBookings ?? [];
$bookingPage = $bookingPage ?? 1;
$bookingTotalPages = $bookingTotalPages ?? 1;
$bookingOffset = $bookingOffset ?? 0;

$myReports = $myReports ?? [];
$reportPage = $reportPage ?? 1;
$reportTotalPages = $reportTotalPages ?? 1;
$reportOffset = $reportOffset ?? 0;

function e($value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function roomStatusClass(string $status): string {
    return match ($status) {
        'available' => 'available',
        'booked' => 'busy',
        default => 'maintenance'
    };
}

function roomStatusText(string $status): string {
    return match ($status) {
        'available' => 'Trống',
        'booked' => 'Đang sử dụng',
        default => 'Bảo trì'
    };
}

function bookingStatusText(string $status): string {
    return match ($status) {
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
        'cancelled' => 'Đã hủy',
        default => $status
    };
}

function reportStatusText(string $status): string {
    return match ($status) {
        'reported' => 'Đã báo',
        'processing' => 'Đang xử lý',
        'resolved' => 'Đã xử lý',
        default => $status
    };
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cổng Sinh Viên - Quản lý phòng thực hành</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; color: #222; }
        a { color: inherit; text-decoration: none; }
        .sidebar { position: fixed; top: 0; left: 0; width: 240px; height: 100vh; padding: 20px 15px; background: #003399; color: white; }
        .logo { font-size: 20px; font-weight: 700; padding: 10px 12px 25px; }
        .menu a { display: block; padding: 12px 14px; margin: 5px 0; border-radius: 6px; }
        .menu a:hover, .menu a.active { background: rgba(255,255,255,.16); }
        .logout-link { margin-top: 25px !important; background: #b42318; }
        .main { margin-left: 240px; min-height: 100vh; }
        .header { min-height: 65px; padding: 0 30px; background: white; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; }
        .content { padding: 30px; }
        h1 { margin-top: 0; color: #003399; }
        .cards, .rooms { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 7px rgba(0,0,0,.04); }
        .card h3 { margin-top: 0; }
        .link { display: inline-block; margin-top: 10px; color: #003399; font-weight: 600; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 5px; font-size: 13px; font-weight: 600; }
        .available { background: #d1fae5; color: #065f46; }
        .busy { background: #fee2e2; color: #991b1b; }
        .maintenance { background: #fef3c7; color: #92400e; }
        .form { max-width: 700px; background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 2px 7px rgba(0,0,0,.04); }
        .group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 7px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 5px; font: inherit; }
        textarea { min-height: 110px; resize: vertical; }
        button { padding: 10px 18px; border: 0; border-radius: 5px; background: #003399; color: white; cursor: pointer; font-weight: 600; }
        button:hover { background: #002266; }
        button.danger { background: #b42318; padding: 6px 12px; font-size: 13px; }
        .success { padding: 12px 15px; border-radius: 5px; margin-bottom: 15px; background: #d1fae5; color: #065f46; }
        .error { padding: 12px 15px; border-radius: 5px; margin-bottom: 15px; background: #fee2e2; color: #991b1b; }
        .field-error { margin-top: 6px; color: #b42318; font-size: 14px; }
        .table-wrap { overflow-x: auto; background: white; border: 1px solid #ddd; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #eee; text-align: left; vertical-align: middle; font-size: 14px; }
        th { background: #003399; color: white; }
        .pagination-bar { display: flex; justify-content: flex-end; gap: 5px; padding: 15px; }
        .pagination-bar a { padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; color: #003399; font-weight: 600; font-size: 13px; }
        .pagination-bar a.active { background: #003399; color: white; border-color: #003399; }
        @media (max-width: 800px) { .sidebar { width: 180px; } .main { margin-left: 180px; } .content { padding: 18px; } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">LAB MANAGEMENT</div>
    <nav class="menu">
        <a href="index.php?route=bookings&page=home" class="<?= $page === 'home' ? 'active' : '' ?>">Trang chủ</a>
        <a href="index.php?route=bookings&page=rooms" class="<?= $page === 'rooms' ? 'active' : '' ?>">Phòng thực hành</a>
        <a href="index.php?route=bookings&page=booking" class="<?= $page === 'booking' ? 'active' : '' ?>">Đặt phòng</a>
        <a href="index.php?route=bookings&page=mybookings" class="<?= $page === 'mybookings' ? 'active' : '' ?>">Yêu cầu đặt phòng</a>
        <a href="index.php?route=bookings&page=report" class="<?= $page === 'report' ? 'active' : '' ?>">Báo hỏng thiết bị</a>
        <a href="index.php?route=bookings&page=myreports" class="<?= $page === 'myreports' ? 'active' : '' ?>">Báo hỏng của tôi</a>
        <a href="index.php?route=logout" class="logout-link">Đăng xuất</a>
    </nav>
</aside>

<main class="main">
    <header class="header">
        <span>Student Portal</span>
        <strong><?= e($user['full_name'] ?? '') ?> (<?= e($user['username'] ?? '') ?>)</strong>
    </header>

    <div class="content">
        <?php if ($success): ?>
            <div class="success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if (isset($errors['cancel'])): ?>
            <div class="error"><?= e($errors['cancel']) ?></div>
        <?php endif; ?>

        <!-- TRANG CHỦ -->
        <?php if ($page === 'home'): ?>
            <h1>Trang chủ</h1>
            <p>Xin chào <strong><?= e($user['full_name'] ?? '') ?></strong></p>
            <div class="cards">
                <div class="card">
                    <h3>Phòng thực hành</h3>
                    <p>Xem danh sách và tình trạng phòng.</p>
                    <a class="link" href="index.php?route=bookings&page=rooms">Xem phòng →</a>
                </div>
                <div class="card">
                    <h3>Đặt phòng</h3>
                    <p>Đăng ký lịch sử dụng phòng lab.</p>
                    <a class="link" href="index.php?route=bookings&page=booking">Đặt phòng →</a>
                </div>
                <div class="card">
                    <h3>Lịch sử đặt phòng</h3>
                    <p>Theo dõi tiến trình duyệt đơn.</p>
                    <a class="link" href="index.php?route=bookings&page=mybookings">Xem đơn đặt →</a>
                </div>
                <div class="card">
                    <h3>Báo hỏng</h3>
                    <p>Phản ánh sự cố thiết bị.</p>
                    <a class="link" href="index.php?route=bookings&page=report">Báo hỏng →</a>
                </div>
            </div>

        <!-- DANH SÁCH PHÒNG -->
        <?php elseif ($page === 'rooms'): ?>
            <h1>Phòng thực hành</h1>
            <div class="rooms">
                <?php foreach ($rooms as $room): ?>
                    <div class="card">
                        <h3><?= e($room['room_code']) ?></h3>
                        <p><?= e($room['room_name']) ?></p>
                        <p>Sức chứa: <strong><?= e($room['capacity']) ?></strong> người</p>
                        <span class="status <?= roomStatusClass($room['status']) ?>">
                            <?= e(roomStatusText($room['status'])) ?>
                        </span>
                        <?php if ($room['status'] === 'available'): ?>
                            <p><a class="link" href="index.php?route=bookings&page=booking">Đặt phòng này →</a></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <!-- ĐẶT PHÒNG -->
        <?php elseif ($page === 'booking'): ?>
            <h1>Đặt phòng thực hành</h1>
            <div class="form">
                <form method="POST" action="index.php?route=bookings&page=booking">
                    <input type="hidden" name="action" value="create_booking">
                    <div class="group">
                        <label>Chọn phòng</label>
                        <select name="room_id">
                            <option value="">-- Chọn phòng thực hành --</option>
                            <?php foreach ($rooms as $room): if ($room['status'] !== 'available') continue; ?>
                                <option value="<?= e($room['id']) ?>"><?= e($room['room_code']) ?> - <?= e($room['room_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['room_id'])): ?>
                            <div class="field-error"><?= e($errors['room_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Ngày sử dụng</label>
                        <input type="date" name="date" value="<?= e($_POST['date'] ?? '') ?>">
                        <?php if (isset($errors['date'])): ?>
                            <div class="field-error"><?= e($errors['date']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Giờ bắt đầu</label>
                        <input type="time" name="start" value="<?= e($_POST['start'] ?? '') ?>">
                        <?php if (isset($errors['start'])): ?>
                            <div class="field-error"><?= e($errors['start']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Giờ kết thúc</label>
                        <input type="time" name="end" value="<?= e($_POST['end'] ?? '') ?>">
                        <?php if (isset($errors['end'])): ?>
                            <div class="field-error"><?= e($errors['end']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Mục đích sử dụng</label>
                        <textarea name="purpose" placeholder="Ví dụ: Học bù nhóm thực hành mạng máy tính..."><?= e($_POST['purpose'] ?? '') ?></textarea>
                        <?php if (isset($errors['purpose'])): ?>
                            <div class="field-error"><?= e($errors['purpose']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit">Gửi yêu cầu đặt phòng</button>
                </form>
            </div>

        <!-- DANH SÁCH ĐẶT PHÒNG CỦA TÔI -->
        <?php elseif ($page === 'mybookings'): ?>
            <h1>Yêu cầu đặt phòng của tôi</h1>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th width="60">STT</th>
                            <th>Phòng</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Mục đích</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($myBookings)): ?>
                            <tr><td colspan="7" style="text-align:center;color:#888;padding:25px;">Bạn chưa có yêu cầu đặt phòng nào.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($myBookings as $idx => $booking): ?>
                            <tr>
                                <td><?= $bookingOffset + $idx + 1 ?></td>
                                <td><strong><?= e($booking['room_code']) ?></strong></td>
                                <td><?= e($booking['start_time']) ?></td>
                                <td><?= e($booking['end_time']) ?></td>
                                <td><?= e($booking['purpose']) ?></td>
                                <td>
                                    <span class="status <?= $booking['status'] === 'approved' ? 'available' : ($booking['status'] === 'rejected' ? 'busy' : 'maintenance') ?>">
                                        <?= e(bookingStatusText($booking['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $canCancel = $booking['status'] !== 'cancelled' && ($booking['status'] === 'pending' || $booking['start_time'] > date('Y-m-d H:i:s'));
                                    if ($canCancel): 
                                    ?>
                                        <form method="POST" action="index.php?route=bookings&page=mybookings" onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu này?');">
                                            <input type="hidden" name="action" value="cancel_booking">
                                            <input type="hidden" name="booking_id" value="<?= e($booking['id']) ?>">
                                            <button type="submit" class="danger">Hủy</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#aaa">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($bookingTotalPages > 1): ?>
                    <div class="pagination-bar">
                        <?php for ($i = 1; $i <= $bookingTotalPages; $i++): ?>
                            <a href="index.php?route=bookings&page=mybookings&p_b=<?= $i ?>" class="<?= $i === $bookingPage ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

        <!-- BÁO HỎNG THIẾT BỊ -->
        <?php elseif ($page === 'report'): ?>
            <h1>Báo hỏng thiết bị</h1>
            <div class="form">
                <form method="POST" action="index.php?route=bookings&page=report">
                    <input type="hidden" name="action" value="report">
                    <div class="group">
                        <label>Thiết bị cần báo hỏng</label>
                        <select name="device_id">
                            <option value="">-- Chọn thiết bị trong phòng bạn đã mượn --</option>
                            <?php foreach ($devices as $device): ?>
                                <option value="<?= e($device['id']) ?>">
                                    <?= e($device['device_code']) ?> - <?= e($device['device_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($devices)): ?>
                            <small style="color:#b42318;margin-top:5px;display:block;">
                                * Lưu ý: Bạn chỉ có thể báo hỏng thiết bị ở các phòng bạn đã có lịch được duyệt.
                            </small>
                        <?php endif; ?>
                        <?php if (isset($errors['device_id'])): ?>
                            <div class="field-error"><?= e($errors['device_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="group">
                        <label>Nội dung sự cố</label>
                        <textarea name="description" placeholder="Mô tả cụ thể sự cố (VD: Liệt phím cách, chuột đơ click trái...)"><?= e($_POST['description'] ?? '') ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="field-error"><?= e($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit">Gửi thông báo hỏng</button>
                </form>
            </div>

        <!-- BÁO HỎNG CỦA TÔI -->
        <?php elseif ($page === 'myreports'): ?>
            <h1>Báo hỏng của tôi</h1>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th width="60">STT</th>
                            <th>Mã & Tên thiết bị</th>
                            <th>Nội dung sự cố</th>
                            <th>Trạng thái xử lý</th>
                            <th>Thời gian gửi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($myReports)): ?>
                            <tr><td colspan="5" style="text-align:center;color:#888;padding:25px;">Bạn chưa gửi báo hỏng nào.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($myReports as $idx => $report): ?>
                            <tr>
                                <td><?= $reportOffset + $idx + 1 ?></td>
                                <td><strong><?= e($report['device_code']) ?></strong> - <?= e($report['device_name']) ?></td>
                                <td><?= e($report['description']) ?></td>
                                <td>
                                    <span class="status <?= $report['status'] === 'resolved' ? 'available' : 'busy' ?>">
                                        <?= e(reportStatusText($report['status'])) ?>
                                    </span>
                                </td>
                                <td><?= e($report['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($reportTotalPages > 1): ?>
                    <div class="pagination-bar">
                        <?php for ($i = 1; $i <= $reportTotalPages; $i++): ?>
                            <a href="index.php?route=bookings&page=myreports&p_r=<?= $i ?>" class="<?= $i === $reportPage ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>