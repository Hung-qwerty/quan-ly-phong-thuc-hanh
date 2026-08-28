<?php

$deviceTypes = [
    ["id" => 1, "name" => "Máy tính"],
    ["id" => 2, "name" => "Bàn phím"],
    ["id" => 3, "name" => "TV"],
    ["id" => 4, "name" => "Máy chiếu"]
];

$rooms = [
    ["id" => 1, "name" => "A3 - 210"],
    ["id" => 2, "name" => "A3 - 211"],
    ["id" => 3, "name" => "A3 - 212"]
];

$devices = [
    [
        "id" => 1,
        "name" => "Máy tính DELL",
        "type_id" => 1,
        "room_id" => 1
    ],
    [
        "id" => 2,
        "name" => "Bàn phím Fulhen",
        "type_id" => 2,
        "room_id" => 2
    ],
    [
        "id" => 3,
        "name" => "TV LG",
        "type_id" => 3,
        "room_id" => 1
    ]
];

$users = [
    ["id" => 1, "name" => "Nguyễn Văn An"],
    ["id" => 2, "name" => "Trần Thị Lan"],
    ["id" => 3, "name" => "Lê Minh Quân"]
];

$bookings = [
    [
        "id" => 1,
        "user_id" => 1,
        "room_id" => 1,
        "start_time" => "08:00 25/08/2026",
        "end_time" => "10:00 25/08/2026",
        "status" => "pending"
    ],
    [
        "id" => 2,
        "user_id" => 2,
        "room_id" => 2,
        "start_time" => "13:00 25/08/2026",
        "end_time" => "15:00 25/08/2026",
        "status" => "pending"
    ],
    [
        "id" => 3,
        "user_id" => 3,
        "room_id" => 3,
        "start_time" => "08:00 26/08/2026",
        "end_time" => "10:00 26/08/2026",
        "status" => "approved"
    ]
];

function timTenLoai($id, $deviceTypes)
{
    foreach ($deviceTypes as $type) {
        if ($type["id"] == $id) {
            return $type["name"];
        }
    }

    return "";
}

function timTenPhong($id, $rooms)
{
    foreach ($rooms as $room) {
        if ($room["id"] == $id) {
            return $room["name"];
        }
    }

    return "";
}

function timTenUser($id, $users)
{
    foreach ($users as $user) {
        if ($user["id"] == $id) {
            return $user["name"];
        }
    }

    return "";
}

function kiemTraThietBi($name, $typeId, $roomId)
{
    $errors = [];

    if ($name == "") {
        $errors[] = "Vui lòng nhập tên thiết bị!";
    }

    if ($typeId == "") {
        $errors[] = "Vui lòng chọn loại thiết bị!";
    }

    if ($roomId == "") {
        $errors[] = "Vui lòng chọn phòng!";
    }

    return $errors;
}

$message = "";
$messageType = "";

$page = $_GET["page"] ?? "devices";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST["action"] ?? "";

    if ($action == "add") {

        $name = trim($_POST["name"] ?? "");
        $typeId = $_POST["type_id"] ?? "";
        $roomId = $_POST["room_id"] ?? "";

        $errors = kiemTraThietBi($name, $typeId, $roomId);

        if (empty($errors)) {

            $devices[] = [
                "id" => count($devices) + 1,
                "name" => $name,
                "type_id" => $typeId,
                "room_id" => $roomId
            ];

            $message = "Thêm thiết bị thành công!";
            $messageType = "success";

        } else {

            $message = implode("<br>", $errors);
            $messageType = "error";
        }
    }

    if ($action == "edit") {

        $id = $_POST["id"] ?? "";
        $name = trim($_POST["name"] ?? "");
        $typeId = $_POST["type_id"] ?? "";
        $roomId = $_POST["room_id"] ?? "";

        $errors = kiemTraThietBi($name, $typeId, $roomId);

        if (empty($errors)) {

            foreach ($devices as &$device) {

                if ($device["id"] == $id) {

                    $device["name"] = $name;
                    $device["type_id"] = $typeId;
                    $device["room_id"] = $roomId;

                    break;
                }
            }

            unset($device);

            $message = "Cập nhật thiết bị thành công!";
            $messageType = "success";

        } else {

            $message = implode("<br>", $errors);
            $messageType = "error";
        }
    }

    if ($action == "delete") {

        $id = $_POST["id"] ?? "";

        foreach ($devices as $index => $device) {

            if ($device["id"] == $id) {

                unset($devices[$index]);

                $devices = array_values($devices);

                break;
            }
        }

        $message = "Xóa thiết bị thành công!";
        $messageType = "success";
    }

    if ($action == "booking") {

        $id = $_POST["id"] ?? "";
        $status = $_POST["status"] ?? "";

        if ($status == "approved" || $status == "rejected") {

            foreach ($bookings as &$booking) {

                if ($booking["id"] == $id) {

                    $booking["status"] = $status;

                    break;
                }
            }

            unset($booking);

            if ($status == "approved") {
                $message = "Đã đồng ý yêu cầu đặt phòng!";
            } else {
                $message = "Đã từ chối yêu cầu đặt phòng!";
            }

            $messageType = "success";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quản lý phòng thực hành</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f4f7f9;
    color: #333;
}

.header {
    height: 70px;
    background: #003399;
    border-bottom: 1px solid #002F62;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
}

.header-title {
    color: #ffffff;
    font-size: 15px;
    font-weight: 600;
}

.staff {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #ffffff;
    color: #003B7A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.staff-name {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
}

.layout {
    display: flex;
    min-height: calc(100vh - 70px);
}

.sidebar {
    width: 245px;
    flex-shrink: 0;
    background: #ffffff;
    border-right: 1px solid #dee2e6;
    padding: 25px 16px;
}

.logo {
    padding: 0 12px 25px;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 22px;
}

.logo h2 {
    margin: 0;
    color: #003399;
    font-size: 20px;
    line-height: 1.35;
}

.logo p {
    margin: 7px 0 0;
    color: #777;
    font-size: 13px;
}

.menu-title {
    padding: 0 12px;
    margin-bottom: 8px;
    color: #999;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}

.menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    margin-bottom: 5px;
    border-radius: 7px;
    color: #555;
    text-decoration: none;
    font-size: 14px;
}

.menu a:hover {
    background: #f1f4f8;
    color: #003399;
}

.menu a.active {
    background: #eaf0fa;
    color: #003399;
    font-weight: 600;
}

.icon {
    width: 22px;
    text-align: center;
    font-size: 16px;
}

.main {
    flex: 1;
    min-width: 0;
    width: calc(100% - 245px);
}

.content {
    width: 100%;
    padding: 30px;
}

.page-title {
    margin-bottom: 25px;
}

.page-title h1 {
    margin: 0;
    color: #003399;
    font-size: 25px;
}

.page-title p {
    margin: 6px 0 0;
    color: #777;
    font-size: 14px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.stat {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 18px 20px;
}

.stat-label {
    color: #777;
    font-size: 13px;
    margin-bottom: 8px;
}

.stat-number {
    color: #003399;
    font-size: 25px;
    font-weight: 700;
}

.card {
    width: 100%;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.card-header {
    padding: 18px 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-header h3 {
    margin: 0;
    color: #003399;
    font-size: 17px;
}

.card-header p {
    margin: 4px 0 0;
    color: #888;
    font-size: 13px;
}

.form-area {
    padding: 20px;
    background: #fafbfc;
    border-bottom: 1px solid #eee;
}

.form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 600;
}

input,
select {
    width: 100%;
    height: 40px;
    padding: 0 11px;
    border: 1px solid #d7dce1;
    border-radius: 6px;
    background: white;
    outline: none;
    font-family: inherit;
}

input:focus,
select:focus {
    border-color: #003399;
    box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.10);
}

button {
    border: 0;
    border-radius: 6px;
    padding: 10px 15px;
    color: white;
    cursor: pointer;
    font-family: inherit;
    font-size: 13px;
}

.primary {
    background: #003399;
}

.primary:hover {
    background: #002266;
}

.secondary {
    background: #6c757d;
}

.secondary:hover {
    background: #565e64;
}

.danger {
    background: #dc3545;
}

.danger:hover {
    background: #b02a37;
}

.success-btn {
    background: #198754;
}

.success-btn:hover {
    background: #146c43;
}

.message {
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 14px;
}

.message.success {
    background: #e8f5ee;
    color: #198754;
    border: 1px solid #c8e6d3;
}

.message.error {
    background: #fdeaea;
    color: #dc3545;
    border: 1px solid #f5c2c7;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #003399;
    color: white;
    padding: 13px 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
}

td {
    padding: 14px 15px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

tr:hover td {
    background: #f8fafc;
}

.actions {
    display: flex;
    gap: 6px;
}

.edit-area {
    display: none;
    padding: 18px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.edit-area.active {
    display: table-row;
}

.edit-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 12px;
    align-items: end;
}

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
}

.pending {
    background: #fff3cd;
    color: #856404;
}

.approved {
    background: #e8f5ee;
    color: #198754;
}

.rejected {
    background: #fdeaea;
    color: #dc3545;
}

@media (max-width: 900px) {

    .sidebar {
        width: 210px;
    }

    .main {
        width: calc(100% - 210px);
    }

    .content {
        padding: 25px;
    }

    .form-grid,
    .edit-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 700px) {

    .header {
        padding: 0 20px;
    }

    .layout {
        display: block;
    }

    .sidebar {
        width: 100%;
        border-right: 0;
        border-bottom: 1px solid #dee2e6;
    }

    .main {
        width: 100%;
    }

    .menu {
        display: flex;
        gap: 5px;
    }

    .menu a {
        flex: 1;
    }

    .content {
        padding: 20px;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .form-grid,
    .edit-grid {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body>

<header class="header">

    <div class="header-title">
        Hệ thống bài tập nhóm - Đại học Thủ đô Hà Nội (HNMU)
    </div>

    <div class="staff">

        <div class="avatar">
            S
        </div>

        <div class="staff-name">
            Đặng Đình Thái An
        </div>

    </div>

</header>

<div class="layout">

    <aside class="sidebar">

        <div class="logo">

            <h2>
                QUẢN LÝ PHÒNG<br>
                THỰC HÀNH
            </h2>

            <p>
                Cán bộ phòng Lab
            </p>

        </div>

        <div class="menu-title">
            DANH MỤC
        </div>

        <nav class="menu">

            <a
                href="?page=devices"
                class="<?php echo $page == 'devices' ? 'active' : ''; ?>"
            >

                <span class="icon">
                    ▣
                </span>

                <span>
                    Quản lý thiết bị
                </span>

            </a>

            <a
                href="?page=bookings"
                class="<?php echo $page == 'bookings' ? 'active' : ''; ?>"
            >

                <span class="icon">
                    ▤
                </span>

                <span>
                    Duyệt yêu cầu 
                </span>

            </a>

        </nav>

    </aside>

    <main class="main">

        <div class="content">

            <?php if ($message != ""): ?>

                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>

            <?php endif; ?>

            <?php if ($page == "devices"): ?>

                <div class="page-title">

                    <h1>
                        Quản lý thiết bị
                    </h1>

                    <p>
                        Quản lý danh sách thiết bị đang được sử dụng trong phòng Lab.
                    </p>

                </div>

                <div class="stats">

                    <div class="stat">

                        <div class="stat-label">
                            Tổng số thiết bị
                        </div>

                        <div class="stat-number">
                            <?php echo count($devices); ?>
                        </div>

                    </div>

                    <div class="stat">

                        <div class="stat-label">
                            Loại thiết bị
                        </div>

                        <div class="stat-number">
                            <?php echo count($deviceTypes); ?>
                        </div>

                    </div>

                    <div class="stat">

                        <div class="stat-label">
                            Phòng sử dụng
                        </div>

                        <div class="stat-number">
                            <?php echo count($rooms); ?>
                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <div>

                            <h3>
                                Danh sách thiết bị
                            </h3>

                            <p>
                                Thêm, sửa hoặc xóa thiết bị trong hệ thống.
                            </p>

                        </div>

                    </div>

                    <div class="form-area">

                        <form method="POST">

                            <input
                                type="hidden"
                                name="action"
                                value="add"
                            >

                            <div class="form-grid">

                                <div class="form-group">

                                    <label>
                                        Tên thiết bị
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        maxlength="50"
                                        placeholder="Nhập tên thiết bị"
                                        required
                                    >

                                </div>

                                <div class="form-group">

                                    <label>
                                        Loại thiết bị
                                    </label>

                                    <select
                                        name="type_id"
                                        required
                                    >

                                        <option value="">
                                            Chọn loại
                                        </option>

                                        <?php foreach ($deviceTypes as $type): ?>

                                            <option
                                                value="<?php echo $type["id"]; ?>"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $type["name"]
                                                );
                                                ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Phòng
                                    </label>

                                    <select
                                        name="room_id"
                                        required
                                    >

                                        <option value="">
                                            Chọn phòng
                                        </option>

                                        <?php foreach ($rooms as $room): ?>

                                            <option
                                                value="<?php echo $room["id"]; ?>"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $room["name"]
                                                );
                                                ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <button
                                    type="submit"
                                    class="primary"
                                >
                                    + Thêm thiết bị
                                </button>

                            </div>

                        </form>

                    </div>

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>

                                    <th width="70">
                                        STT
                                    </th>

                                    <th>
                                        Tên thiết bị
                                    </th>

                                    <th>
                                        Loại thiết bị
                                    </th>

                                    <th>
                                        Phòng
                                    </th>

                                    <th width="150">
                                        Thao tác
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($devices as $index => $device): ?>

                                    <tr>

                                        <td>
                                            <?php echo $index + 1; ?>
                                        </td>

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                $device["name"]
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                timTenLoai(
                                                    $device["type_id"],
                                                    $deviceTypes
                                                )
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                timTenPhong(
                                                    $device["room_id"],
                                                    $rooms
                                                )
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <div class="actions">

                                                <button
                                                    type="button"
                                                    class="secondary"
                                                    onclick="moSua(<?php echo $device['id']; ?>)"
                                                >
                                                    Sửa
                                                </button>

                                                <form method="POST">

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="delete"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?php echo $device['id']; ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="danger"
                                                        onclick="return confirm('Bạn có chắc muốn xóa thiết bị này không?')"
                                                    >
                                                        Xóa
                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                    <tr
                                        id="edit-<?php echo $device['id']; ?>"
                                        class="edit-area"
                                    >

                                        <td colspan="5">

                                            <form method="POST">

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="edit"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?php echo $device['id']; ?>"
                                                >

                                                <div class="edit-grid">

                                                    <div class="form-group">

                                                        <label>
                                                            Tên thiết bị
                                                        </label>

                                                        <input
                                                            type="text"
                                                            name="name"
                                                            maxlength="50"
                                                            value="<?php echo htmlspecialchars($device['name']); ?>"
                                                            required
                                                        >

                                                    </div>

                                                    <div class="form-group">

                                                        <label>
                                                            Loại thiết bị
                                                        </label>

                                                        <select
                                                            name="type_id"
                                                            required
                                                        >

                                                            <?php foreach ($deviceTypes as $type): ?>

                                                                <option
                                                                    value="<?php echo $type['id']; ?>"
                                                                    <?php
                                                                    echo $type["id"] == $device["type_id"]
                                                                        ? "selected"
                                                                        : "";
                                                                    ?>
                                                                >

                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $type["name"]
                                                                    );
                                                                    ?>

                                                                </option>

                                                            <?php endforeach; ?>

                                                        </select>

                                                    </div>

                                                    <div class="form-group">

                                                        <label>
                                                            Phòng
                                                        </label>

                                                        <select
                                                            name="room_id"
                                                            required
                                                        >

                                                            <?php foreach ($rooms as $room): ?>

                                                                <option
                                                                    value="<?php echo $room['id']; ?>"
                                                                    <?php
                                                                    echo $room["id"] == $device["room_id"]
                                                                        ? "selected"
                                                                        : "";
                                                                    ?>
                                                                >

                                                                    <?php
                                                                    echo htmlspecialchars(
                                                                        $room["name"]
                                                                    );
                                                                    ?>

                                                                </option>

                                                            <?php endforeach; ?>

                                                        </select>

                                                    </div>

                                                    <div class="actions">

                                                        <button
                                                            type="submit"
                                                            class="primary"
                                                        >
                                                            Lưu
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="secondary"
                                                            onclick="dongSua(<?php echo $device['id']; ?>)"
                                                        >
                                                            Hủy
                                                        </button>

                                                    </div>

                                                </div>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php else: ?>

                <div class="page-title">

                    <h1>
                        DUYỆT YÊU CẦU
                    </h1>

                </div>

                <div class="stats">

                    <div class="stat">

                        <div class="stat-label">
                            Tổng yêu cầu
                        </div>

                        <div class="stat-number">
                            <?php echo count($bookings); ?>
                        </div>

                    </div>

                    <div class="stat">

                        <div class="stat-label">
                            Chờ duyệt
                        </div>

                        <div class="stat-number">

                            <?php

                            $pending = 0;

                            foreach ($bookings as $booking) {

                                if ($booking["status"] == "pending") {
                                    $pending++;
                                }

                            }

                            echo $pending;

                            ?>

                        </div>

                    </div>

                    <div class="stat">

                        <div class="stat-label">
                            Đã xử lý
                        </div>

                        <div class="stat-number">

                            <?php

                            $processed = 0;

                            foreach ($bookings as $booking) {

                                if ($booking["status"] != "pending") {
                                    $processed++;
                                }

                            }

                            echo $processed;

                            ?>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <div>

                            <h3>
                                Danh sách yêu cầu
                            </h3>

                        </div>

                    </div>

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        STT
                                    </th>

                                    <th>
                                        Người đặt
                                    </th>

                                    <th>
                                        Phòng
                                    </th>

                                    <th>
                                        Bắt đầu
                                    </th>

                                    <th>
                                        Kết thúc
                                    </th>

                                    <th>
                                        Trạng thái
                                    </th>

                                    <th>
                                        Thao tác
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($bookings as $index => $booking): ?>

                                    <tr>

                                        <td>
                                            <?php echo $index + 1; ?>
                                        </td>

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                timTenUser(
                                                    $booking["user_id"],
                                                    $users
                                                )
                                            );
                                            ?>

                                        </td>

                                        <td>

                                            <?php
                                            echo htmlspecialchars(
                                                timTenPhong(
                                                    $booking["room_id"],
                                                    $rooms
                                                )
                                            );
                                            ?>

                                        </td>

                                        <td>
                                            <?php echo $booking["start_time"]; ?>
                                        </td>

                                        <td>
                                            <?php echo $booking["end_time"]; ?>
                                        </td>

                                        <td>

                                            <?php if ($booking["status"] == "pending"): ?>

                                                <span class="status pending">
                                                    Chờ duyệt
                                                </span>

                                            <?php elseif ($booking["status"] == "approved"): ?>

                                                <span class="status approved">
                                                    Đã duyệt
                                                </span>

                                            <?php else: ?>

                                                <span class="status rejected">
                                                    Từ chối
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?php if ($booking["status"] == "pending"): ?>

                                                <div class="actions">

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="booking"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="id"
                                                            value="<?php echo $booking['id']; ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="approved"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="success-btn"
                                                        >
                                                            Đồng ý
                                                        </button>

                                                    </form>

                                                    <form method="POST">

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="booking"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="id"
                                                            value="<?php echo $booking['id']; ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="status"
                                                            value="rejected"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="danger"
                                                        >
                                                            Từ chối
                                                        </button>

                                                    </form>

                                                </div>

                                            <?php else: ?>

                                                <span style="color:#888;">
                                                    Đã xử lý
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </main>

</div>

<script>

function moSua(id) {

    document
        .getElementById("edit-" + id)
        .classList
        .add("active");

}

function dongSua(id) {

    document
        .getElementById("edit-" + id)
        .classList
        .remove("active");

}

</script>

</body>

</html>