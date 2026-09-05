<?php

use App\Core\Database;
$pdo = Database::connection();

function e($value) {
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

$user_id = $_SESSION["user_id"] ?? 3;
$page = $_GET["page"] ?? "home";

$errors = [];
$success = "";

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Không tìm thấy người dùng.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "cancel_booking") {
        $page = "mybookings";
        $booking_id = $_POST["booking_id"] ?? 0;

        $stmt = $pdo->prepare("
            SELECT id, start_time, status 
            FROM bookings 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$booking_id, $user_id]);
        $booking = $stmt->fetch();

        if ($booking) {
            $now = date("Y-m-d H:i:s");
            if ($booking["status"] === "pending" || $booking["start_time"] > $now) {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                $stmt->execute([$booking_id]);
                $success = "Hủy yêu cầu đặt phòng thành công.";
            } else {
                $errors["cancel"] = "Không thể hủy yêu cầu đã/đang diễn ra hoặc đã bị từ chối.";
            }
        }
    }
}

$stmt = $pdo->query("SELECT id, room_code, room_name, capacity, status FROM rooms ORDER BY room_code");
$rooms = $stmt->fetchAll();

$search_date = $_GET["date"] ?? "";
$search_start = $_GET["start"] ?? "";
$search_end = $_GET["end"] ?? "";
$available_rooms = [];

if ($search_date && $search_start && $search_end) {
    $start_time = $search_date . " " . $search_start . ":00";
    $end_time = $search_date . " " . $search_end . ":00";

    $stmt = $pdo->prepare("
        SELECT * FROM rooms 
        WHERE status != 'maintenance' 
        AND id NOT IN (
            SELECT room_id FROM bookings 
            WHERE status IN ('pending', 'approved') 
            AND start_time < ? 
            AND end_time > ?
        )
        ORDER BY room_code
    ");
    $stmt->execute([$end_time, $start_time]);
    $available_rooms = $stmt->fetchAll();
}

$stmt = $pdo->prepare("
    SELECT b.id, r.room_code, r.room_name, b.start_time, b.end_time, b.purpose, b.status 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    WHERE b.user_id = ? 
    ORDER BY b.start_time DESC
");
$stmt->execute([$user_id]);
$my_bookings = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT b.id, r.room_code, r.room_name, u.full_name, b.start_time, b.end_time, b.purpose, b.status 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.id 
    JOIN users u ON b.user_id = u.id 
    WHERE b.status = 'approved' 
    ORDER BY b.start_time DESC
");
$schedule_list = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý phòng thực hành - Phi</title>
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
        h1, h2, h3 { color: #003399; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 22px; }
        .card a { color: #003399; text-decoration: none; font-weight: bold; }
        .rooms { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .available { background: #e5f7eb; color: #207a45; }
        .busy { background: #ffe5e5; color: #d32f2f; }
        .maintenance { background: #fff3cd; color: #856404; }
        .form { max-width: 800px; background: white; border: 1px solid #ddd; border-radius: 8px; padding: 25px; margin-bottom: 25px; }
        .form-row { display: flex; gap: 15px; }
        .group { flex: 1; margin-bottom: 17px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: Arial; }
        .success { max-width: 800px; background: #e6f7ed; color: #207a45; padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .error { max-width: 800px; background: #ffe5e5; color: #d32f2f; padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        button, .btn { background: #003399; color: white; border: 0; padding: 10px 18px; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        button:hover { background: #002266; }
        .btn-danger { background: #d32f2f; }
        .btn-danger:hover { background: #9a0007; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #003399; color: white; }
        @media(max-width: 800px) {
            .sidebar { width: 180px; }
            .main { margin-left: 180px; }
            .cards, .rooms { grid-template-columns: 1fr; }
            .form-row { flex-direction: column; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">LAB MANAGEMENT</div>
    <nav class="menu">
        <a href="?page=home" class="<?= $page == 'home' ? 'active' : '' ?>">Trang chủ</a>
        <a href="?page=rooms" class="<?= $page == 'rooms' ? 'active' : '' ?>">Danh sách phòng</a>
        <a href="?page=check" class="<?= $page == 'check' ? 'active' : '' ?>">Kiểm tra phòng trống</a>
        <a href="?page=schedule" class="<?= $page == 'schedule' ? 'active' : '' ?>">Lịch phòng</a>
        <a href="?page=mybookings" class="<?= $page == 'mybookings' ? 'active' : '' ?>">Yêu cầu của tôi</a>
    </nav>
</aside>

<main class="main">
    <header class="header">
        <span>Student Portal</span>
        <strong><?= e($user["full_name"]) ?></strong>
    </header>

    <div class="content">

        <?php if ($page == "home"): ?>
            <h1>Trang chủ</h1>
            <p>Xin chào <strong><?= e($user["full_name"]) ?></strong>.</p>
            <p>Chào mừng bạn đến với hệ thống theo dõi lịch và quản lý yêu cầu phòng thực hành.</p>

            <div class="cards">
                <div class="card">
                    <h3>Danh sách phòng</h3>
                    <p>Xem toàn bộ phòng thực hành và trạng thái phòng hiện tại.</p>
                    <a href="?page=rooms">Xem danh sách →</a>
                </div>
                <div class="card">
                    <h3>Kiểm tra phòng trống</h3>
                    <p>Tra cứu nhanh phòng còn trống theo mốc ngày và khung giờ cụ thể.</p>
                    <a href="?page=check">Kiểm tra ngay →</a>
                </div>
                <div class="card">
                    <h3>Lịch phòng</h3>
                    <p>Xem toàn bộ thời gian biểu và lịch mượn phòng đã được phê duyệt.</p>
                    <a href="?page=schedule">Xem lịch phòng →</a>
                </div>
                <div class="card">
                    <h3>Yêu cầu của tôi</h3>
                    <p>Quản lý các yêu cầu mượn phòng cá nhân và thực hiện hủy khi chưa bắt đầu.</p>
                    <a href="?page=mybookings">Xem yêu cầu →</a>
                </div>
            </div>

        <?php elseif ($page == "rooms"): ?>
            <h1>Danh sách phòng thực hành</h1>
            <div class="rooms">
                <?php foreach ($rooms as $room): ?>
                    <?php
                        if ($room["status"] == "available") { $class = "available"; $status = "Trống"; }
                        elseif ($room["status"] == "booked") { $class = "busy"; $status = "Đang sử dụng"; }
                        else { $class = "maintenance"; $status = "Bảo trì"; }
                    ?>
                    <div class="card">
                        <h3><?= e($room["room_code"]) ?></h3>
                        <p><?= e($room["room_name"]) ?></p>
                        <p>Sức chứa: <?= e($room["capacity"]) ?> người</p>
                        <span class="status <?= $class ?>"><?= $status ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($page == "check"): ?>
            <h1>Kiểm tra phòng còn trống theo ngày/giờ</h1>
            <div class="form">
                <form method="GET">
                    <input type="hidden" name="page" value="check">
                    <div class="form-row">
                        <div class="group">
                            <label>Chọn ngày</label>
                            <input type="date" name="date" value="<?= e($search_date) ?>" required>
                        </div>
                        <div class="group">
                            <label>Giờ bắt đầu</label>
                            <input type="time" name="start" value="<?= e($search_start) ?>" required>
                        </div>
                        <div class="group">
                            <label>Giờ kết thúc</label>
                            <input type="time" name="end" value="<?= e($search_end) ?>" required>
                        </div>
                    </div>
                    <button type="submit">Lọc phòng trống</button>
                </form>
            </div>

            <?php if ($search_date && $search_start && $search_end): ?>
                <h3>Kết quả lọc phòng trống (<?= e(date("d/m/Y", strtotime($search_date))) ?> từ <?= e($search_start) ?> đến <?= e($search_end) ?>):</h3>
                <div class="rooms">
                    <?php foreach ($available_rooms as $room): ?>
                        <div class="card">
                            <h3><?= e($room["room_code"]) ?></h3>
                            <p><?= e($room["room_name"]) ?></p>
                            <p>Sức chứa: <?= e($room["capacity"]) ?> người</p>
                            <span class="status available">Khả dụng</span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($available_rooms)): ?>
                        <p>Không có phòng nào còn trống trong khoảng thời gian này.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php elseif ($page == "schedule"): ?>
            <h1>Lịch sử dụng phòng thực hành</h1>
            <table>
                <tr>
                    <th>STT</th>
                    <th>Mã phòng</th>
                    <th>Tên phòng</th>
                    <th>Người đặt</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Thời gian kết thúc</th>
                    <th>Mục đích</th>
                </tr>
                <?php $stt = 1; foreach ($schedule_list as $item): ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><?= e($item["room_code"]) ?></td>
                        <td><?= e($item["room_name"]) ?></td>
                        <td><?= e($item["full_name"]) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($item["start_time"]))) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($item["end_time"]))) ?></td>
                        <td><?= e($item["purpose"]) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($schedule_list)): ?>
                    <tr><td colspan="7">Chưa có lịch đặt phòng nào được duyệt.</td></tr>
                <?php endif; ?>
            </table>

        <?php elseif ($page == "mybookings"): ?>
            <h1>Danh sách yêu cầu của tôi</h1>

            <?php if ($success): ?>
                <div class="success"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if (isset($errors["cancel"])): ?>
                <div class="error"><?= e($errors["cancel"]) ?></div>
            <?php endif; ?>

            <table>
                <tr>
                    <th>Phòng</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Thời gian kết thúc</th>
                    <th>Mục đích</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
                <?php foreach ($my_bookings as $item): ?>
                    <?php $can_cancel = ($item["status"] === "pending" || $item["start_time"] > date("Y-m-d H:i:s")) && $item["status"] !== "cancelled"; ?>
                    <tr>
                        <td><?= e($item["room_code"]) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($item["start_time"]))) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($item["end_time"]))) ?></td>
                        <td><?= e($item["purpose"]) ?></td>
                        <td><?= e($item["status"]) ?></td>
                        <td>
                            <?php if ($can_cancel): ?>
                                <form method="POST" style="margin:0;" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?');">
                                    <input type="hidden" name="action" value="cancel_booking">
                                    <input type="hidden" name="booking_id" value="<?= $item["id"] ?>">
                                    <button type="submit" class="btn btn-danger">Hủy yêu cầu</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #888; font-size: 13px;">Không thể hủy</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($my_bookings)): ?>
                    <tr><td colspan="6">Bạn chưa tạo yêu cầu đặt phòng nào.</td></tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>

    </div>
</main>

</body>
</html>