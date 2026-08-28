<?php

session_start();

$host = "127.0.0.1";
$dbname = "quan_ly_phong_thuc_hanh";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Không thể kết nối database: " . $e->getMessage());
}

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

$stmt = $pdo->query("
    SELECT id, room_code, room_name, capacity, status
    FROM rooms
    ORDER BY room_code
");
$rooms = $stmt->fetchAll();

$stmt = $pdo->query("
    SELECT id, device_code, device_name, room_id, status
    FROM devices
    ORDER BY device_code
");
$devices = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    if ($action === "booking") {

        $page = "booking";

        $room_id = $_POST["room_id"] ?? "";
        $date = $_POST["date"] ?? "";
        $start = $_POST["start"] ?? "";
        $end = $_POST["end"] ?? "";
        $purpose = trim($_POST["purpose"] ?? "");

        if ($room_id === "") {
            $errors["room_id"] = "Vui lòng chọn phòng.";
        }

        if ($date === "") {
            $errors["date"] = "Vui lòng chọn ngày.";
        }

        if ($start === "") {
            $errors["start"] = "Vui lòng chọn giờ bắt đầu.";
        }

        if ($end === "") {
            $errors["end"] = "Vui lòng chọn giờ kết thúc.";
        }

        if ($start !== "" && $end !== "" && $end <= $start) {
            $errors["end"] = "Giờ kết thúc phải lớn hơn giờ bắt đầu.";
        }

        if ($purpose === "") {
            $errors["purpose"] = "Vui lòng nhập mục đích.";
        }

        if (empty($errors)) {

            $stmt = $pdo->prepare("
                SELECT id
                FROM rooms
                WHERE id = ?
                AND status != 'maintenance'
            ");

            $stmt->execute([$room_id]);

            if (!$stmt->fetch()) {
                $errors["room_id"] = "Phòng không tồn tại hoặc đang bảo trì.";
            }
        }

        if (empty($errors)) {

            $start_time = $date . " " . $start . ":00";
            $end_time = $date . " " . $end . ":00";

            $stmt = $pdo->prepare("
                SELECT id
                FROM bookings
                WHERE room_id = ?
                AND status IN ('pending','approved')
                AND start_time < ?
                AND end_time > ?
            ");

            $stmt->execute([
                $room_id,
                $end_time,
                $start_time
            ]);

            if ($stmt->fetch()) {
                $errors["room_id"] = "Phòng đã được đặt trong thời gian này.";
            }
        }

        if (empty($errors)) {

            $stmt = $pdo->prepare("
                INSERT INTO bookings
                (user_id, room_id, start_time, end_time, purpose, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");

            $stmt->execute([
                $user_id,
                $room_id,
                $start_time,
                $end_time,
                $purpose
            ]);

            $success = "Đặt phòng thành công.";

            $room_id = "";
            $date = "";
            $start = "";
            $end = "";
            $purpose = "";
        }
    }

    if ($action === "report") {

        $page = "report";

        $device_id = $_POST["device_id"] ?? "";
        $description = trim($_POST["description"] ?? "");

        if ($device_id === "") {
            $errors["device_id"] = "Vui lòng chọn thiết bị.";
        }

        if ($description === "") {
            $errors["description"] = "Vui lòng nhập nội dung báo hỏng.";
        }

        if (empty($errors)) {

            $stmt = $pdo->prepare("
                SELECT id
                FROM devices
                WHERE id = ?
            ");

            $stmt->execute([$device_id]);

            if (!$stmt->fetch()) {
                $errors["device_id"] = "Thiết bị không tồn tại.";
            }
        }

        if (empty($errors)) {

            $stmt = $pdo->prepare("
                INSERT INTO device_reports
                (device_id, user_id, description, status)
                VALUES (?, ?, ?, 'reported')
            ");

            $stmt->execute([
                $device_id,
                $user_id,
                $description
            ]);

            $success = "Báo hỏng thiết bị thành công.";

            $device_id = "";
            $description = "";
        }
    }
}

$stmt = $pdo->prepare("
    SELECT
        b.id,
        r.room_code,
        r.room_name,
        b.start_time,
        b.end_time,
        b.purpose,
        b.status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.start_time DESC
");

$stmt->execute([$user_id]);
$my_bookings = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Quản lý phòng thực hành</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7f9;
    color: #333;
}

.sidebar {
    position: fixed;
    width: 220px;
    height: 100vh;
    background: white;
    border-right: 1px solid #ddd;
    padding: 25px 15px;
}

.logo {
    color: #003399;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 30px;
}

.menu a {
    display: block;
    padding: 12px;
    margin-bottom: 5px;
    text-decoration: none;
    color: #444;
    border-radius: 6px;
}

.menu a:hover,
.menu a.active {
    background: #eef4ff;
    color: #003399;
}

.main {
    margin-left: 220px;
}

.header {
    height: 60px;
    background: white;
    border-bottom: 1px solid #ddd;
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.content {
    padding: 35px;
}

h1,
h2,
h3 {
    color: #003399;
}

.cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 22px;
}

.card a {
    color: #003399;
    text-decoration: none;
    font-weight: bold;
}

.rooms {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.available {
    background: #e5f7eb;
    color: #207a45;
}

.busy {
    background: #ffe5e5;
    color: #d32f2f;
}

.maintenance {
    background: #fff3cd;
    color: #856404;
}

.form {
    max-width: 700px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 25px;
}

.group {
    margin-bottom: 17px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
}

input,
select,
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-family: Arial;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.error {
    color: #d32f2f;
    font-size: 13px;
    margin-top: 5px;
}

.success {
    max-width: 700px;
    background: #e6f7ed;
    color: #207a45;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 15px;
}

button {
    background: #003399;
    color: white;
    border: 0;
    padding: 11px 18px;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #002266;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th,
td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background: #003399;
    color: white;
}

@media(max-width: 800px) {

    .sidebar {
        width: 180px;
    }

    .main {
        margin-left: 180px;
    }

    .cards,
    .rooms {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>

<aside class="sidebar">

<div class="logo">
LAB MANAGEMENT
</div>

<nav class="menu">

<a href="?page=home"
class="<?= $page == "home" ? "active" : "" ?>">
Trang chủ
</a>

<a href="?page=rooms"
class="<?= $page == "rooms" ? "active" : "" ?>">
Phòng thực hành
</a>

<a href="?page=booking"
class="<?= $page == "booking" ? "active" : "" ?>">
Đặt phòng
</a>

<a href="?page=mybookings"
class="<?= $page == "mybookings" ? "active" : "" ?>">
Yêu cầu của tôi
</a>

<a href="?page=report"
class="<?= $page == "report" ? "active" : "" ?>">
Báo hỏng thiết bị
</a>

</nav>

</aside>

<main class="main">

<header class="header">

<span>Student Portal</span>

<strong>
<?= e($user["full_name"]) ?>
</strong>

</header>

<div class="content">

<?php if ($page == "home"): ?>

<h1>Trang chủ</h1>

<p>
Xin chào <strong><?= e($user["full_name"]) ?></strong>.
</p>

<p>
Chào mừng bạn đến với hệ thống quản lý phòng thực hành.
</p>

<div class="cards">

<div class="card">

<h3>Phòng thực hành</h3>

<p>Xem danh sách phòng và trạng thái phòng.</p>

<a href="?page=rooms">
Xem phòng →
</a>

</div>

<div class="card">

<h3>Đặt phòng</h3>

<p>Gửi yêu cầu sử dụng phòng thực hành.</p>

<a href="?page=booking">
Đặt phòng →
</a>

</div>

<div class="card">

<h3>Báo hỏng</h3>

<p>Báo cáo thiết bị gặp sự cố.</p>

<a href="?page=report">
Báo hỏng →
</a>

</div>

</div>

<?php elseif ($page == "rooms"): ?>

<h1>Phòng thực hành</h1>

<div class="rooms">

<?php foreach ($rooms as $room): ?>

<?php

if ($room["status"] == "available") {
    $class = "available";
    $status = "Trống";
} elseif ($room["status"] == "booked") {
    $class = "busy";
    $status = "Đang sử dụng";
} else {
    $class = "maintenance";
    $status = "Bảo trì";
}

?>

<div class="card">

<h3><?= e($room["room_code"]) ?></h3>

<p><?= e($room["room_name"]) ?></p>

<p>
Sức chứa: <?= e($room["capacity"]) ?> người
</p>

<span class="status <?= $class ?>">
<?= $status ?>
</span>

<?php if ($room["status"] == "available"): ?>

<p>
<a href="?page=booking">
Đặt phòng →
</a>
</p>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

<?php elseif ($page == "booking"): ?>

<h1>Đặt phòng</h1>

<?php if ($success): ?>

<div class="success">
<?= e($success) ?>
</div>

<?php endif; ?>

<div class="form">

<form method="POST">

<input type="hidden" name="action" value="booking">

<div class="group">

<label>Phòng</label>

<select name="room_id">

<option value="">
-- Chọn phòng --
</option>

<?php foreach ($rooms as $room): ?>

<?php if ($room["status"] != "maintenance"): ?>

<option value="<?= e($room["id"]) ?>">

<?= e($room["room_code"]) ?> -
<?= e($room["room_name"]) ?>

</option>

<?php endif; ?>

<?php endforeach; ?>

</select>

<?php if (isset($errors["room_id"])): ?>

<div class="error">
<?= e($errors["room_id"]) ?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Ngày</label>

<input type="date" name="date">

<?php if (isset($errors["date"])): ?>

<div class="error">
<?= e($errors["date"]) ?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Giờ bắt đầu</label>

<input type="time" name="start">

<?php if (isset($errors["start"])): ?>

<div class="error">
<?= e($errors["start"]) ?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Giờ kết thúc</label>

<input type="time" name="end">

<?php if (isset($errors["end"])): ?>

<div class="error">
<?= e($errors["end"]) ?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Mục đích</label>

<textarea name="purpose"></textarea>

<?php if (isset($errors["purpose"])): ?>

<div class="error">
<?= e($errors["purpose"]) ?>
</div>

<?php endif; ?>

</div>

<button type="submit">
Gửi yêu cầu
</button>

</form>

</div>

<?php elseif ($page == "mybookings"): ?>

<h1>Yêu cầu đặt phòng của tôi</h1>

<table>

<tr>
<th>Phòng</th>
<th>Bắt đầu</th>
<th>Kết thúc</th>
<th>Mục đích</th>
<th>Trạng thái</th>
</tr>

<?php foreach ($my_bookings as $item): ?>

<tr>

<td><?= e($item["room_code"]) ?></td>

<td><?= e($item["start_time"]) ?></td>

<td><?= e($item["end_time"]) ?></td>

<td><?= e($item["purpose"]) ?></td>

<td><?= e($item["status"]) ?></td>

</tr>

<?php endforeach; ?>

<?php if (empty($my_bookings)): ?>

<tr>

<td colspan="5">
Bạn chưa có yêu cầu đặt phòng.
</td>

</tr>

<?php endif; ?>

</table>

<?php elseif ($page == "report"): ?>

<h1>Báo hỏng thiết bị</h1>

<?php if ($success): ?>

<div class="success">
<?= e($success) ?>
</div>

<?php endif; ?>

<div class="form">

<form method="POST">

<input type="hidden" name="action" value="report">

<div class="group">

<label>Thiết bị</label>

<select name="device_id">

<option value="">
-- Chọn thiết bị --
</option>

<?php foreach ($devices as $device): ?>

<option value="<?= e($device["id"]) ?>">

<?= e($device["device_code"]) ?> -
<?= e($device["device_name"]) ?>

</option>

<?php endforeach; ?>

</select>

<?php if (isset($errors["device_id"])): ?>

<div class="error">
<?= e($errors["device_id"]) ?>
</div>

<?php endif; ?>

</div>

<div class="group">

<label>Nội dung báo hỏng</label>

<textarea
name="description"
placeholder="Mô tả tình trạng thiết bị..."
></textarea>

<?php if (isset($errors["description"])): ?>

<div class="error">
<?= e($errors["description"]) ?>
</div>

<?php endif; ?>

</div>

<button type="submit">
Gửi báo hỏng
</button>

</form>

</div>

<?php endif; ?>

</table>

<?php endif; ?>

</div>

</main>

</body>

</html>