<?php
$devices = $devices ?? [];
$deviceTypes = $deviceTypes ?? [];
$rooms = $rooms ?? [];
$bookings = $bookings ?? [];
$message = $message ?? '';

function deviceStatusText($status)
{
    switch ($status) {
        case 'active':
            return 'Tốt';
        case 'maintenance':
            return 'Đang bảo trì';
        case 'broken':
            return 'Hỏng';
        default:
            return $status;
    }
}

function deviceStatusClass($status)
{
    switch ($status) {
        case 'active':
            return 'device-good';
        case 'maintenance':
            return 'device-maintenance';
        case 'broken':
            return 'device-broken';
        default:
            return '';
    }
}

function bookingStatusText($status)
{
    switch ($status) {
        case 'pending':
            return 'Chờ duyệt';
        case 'approved':
            return 'Đã duyệt';
        case 'rejected':
            return 'Từ chối';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return $status;
    }
}

function bookingStatusClass($status)
{
    switch ($status) {
        case 'pending':
            return 'status-pending';
        case 'approved':
            return 'status-approved';
        case 'rejected':
            return 'status-rejected';
        case 'cancelled':
            return 'status-cancelled';
        default:
            return '';
    }
}

function formatDateTime($datetime)
{
    if (empty($datetime)) {
        return '';
    }

    $time = strtotime($datetime);

    if ($time === false) {
        return $datetime;
    }

    return date('H:i d/m/Y', $time);
}

$totalDevices = count($devices);
$activeDevices = 0;
$maintenanceDevices = 0;
$brokenDevices = 0;

foreach ($devices as $device) {
    if (($device['status'] ?? '') === 'active') {
        $activeDevices++;
    }

    if (($device['status'] ?? '') === 'maintenance') {
        $maintenanceDevices++;
    }

    if (($device['status'] ?? '') === 'broken') {
        $brokenDevices++;
    }
}

$totalBookings = count($bookings);
$pendingBookings = 0;
$processedBookings = 0;

foreach ($bookings as $booking) {
    if (($booking['status'] ?? '') === 'pending') {
        $pendingBookings++;
    } else {
        $processedBookings++;
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
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            color: #222;
        }

        .header {
            height: 65px;
            background: #003399;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 25px;
            font-size: 20px;
            font-weight: bold;
        }

        .layout {
            display: flex;
            min-height: calc(100vh - 65px);
        }

        .sidebar {
            width: 245px;
            background: white;
            border-right: 1px solid #ddd;
            padding: 20px 12px;
            flex-shrink: 0;
        }

        .sidebar-title {
            font-size: 14px;
            color: #777;
            padding: 10px 12px;
            margin-bottom: 8px;
        }

        .menu-item {
            display: block;
            padding: 13px 15px;
            margin-bottom: 5px;
            border-radius: 6px;
            color: #333;
            text-decoration: none;
            font-size: 15px;
        }

        .menu-item:hover {
            background: #eef3ff;
            color: #003399;
        }

        .menu-item.active {
            background: #003399;
            color: white;
        }

        .main {
            flex: 1;
            padding: 25px;
            min-width: 0;
        }

        .page-title {
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 22px;
            color: #222;
        }

        .message {
            padding: 13px 16px;
            background: #eaf7ee;
            border: 1px solid #b9e4c5;
            color: #216b36;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 18px;
            border: 1px solid #e3e3e3;
        }

        .stat-title {
            color: #777;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 27px;
            font-weight: bold;
            color: #003399;
        }

        .content-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select {
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 0 12px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #003399;
        }

        .form-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        .btn {
            border: 0;
            border-radius: 5px;
            padding: 10px 17px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-primary {
            background: #003399;
            color: white;
        }

        .btn-primary:hover {
            background: #002b80;
        }

        .btn-edit {
            background: #f0ad4e;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-approve {
            background: #198754;
            color: white;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-cancel {
            background: #777;
            color: white;
        }

        .action-group {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f5f6f8;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            padding: 13px 12px;
            border-bottom: 1px solid #ddd;
        }

        td {
            padding: 13px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .device-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: bold;
        }

        .device-status::before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .device-good::before {
            background: #198754;
        }

        .device-maintenance::before {
            background: #f0ad4e;
        }

        .device-broken::before {
            background: #dc3545;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-rejected {
            background: #f8d7da;
            color: #842029;
        }

        .status-cancelled {
            background: #e2e3e5;
            color: #41464b;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            background: white;
        }

        .tab {
            padding: 15px 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 3px solid transparent;
            color: #666;
        }

        .tab.active {
            color: #003399;
            border-bottom-color: #003399;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #777;
            font-size: 14px;
        }

        .calendar {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 18px;
            border-bottom: 1px solid #ddd;
        }

        .calendar-title {
            font-size: 17px;
            font-weight: bold;
        }

        .calendar-buttons {
            display: flex;
            gap: 7px;
        }

        .calendar-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #ccc;
            background: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .calendar-btn:hover {
            background: #f1f3f5;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .calendar-day-name {
            background: #f5f6f8;
            padding: 11px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            border-right: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
        }

        .calendar-cell {
            min-height: 105px;
            padding: 8px;
            border-right: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
        }

        .calendar-cell.other-month {
            background: #fafafa;
            color: #aaa;
        }

        .calendar-date {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .calendar-event {
            background: #eaf0ff;
            color: #003399;
            padding: 4px 5px;
            margin-bottom: 3px;
            border-radius: 3px;
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-event.booking {
            background: #e8f5e9;
            color: #1b5e20;
        }

        .calendar-event.pending {
            background: #fff3cd;
            color: #856404;
        }

        .calendar-more {
            font-size: 10px;
            color: #666;
        }

        .section-spacing {
            margin-top: 25px;
        }

        @media (max-width: 1000px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 210px;
            }
        }

        @media (max-width: 700px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                border-right: 0;
                border-bottom: 1px solid #ddd;
            }

            .main {
                padding: 15px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .content-card {
                overflow-x: auto;
            }

            table {
                min-width: 850px;
            }

            .calendar {
                overflow-x: auto;
            }

            .calendar-grid {
                min-width: 700px;
            }
        }
    </style>
</head>

<body>

<div class="header">
    HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ
</div>

<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-title">CÁN BỘ LAB</div>

        <a href="index.php?page=devices" class="menu-item active">
            Quản lý thiết bị
        </a>

        <a href="index.php?page=devices#bookings" class="menu-item">
            Duyệt yêu cầu phòng
        </a>

        <a href="index.php?page=devices#calendar" class="menu-item">
            Lịch sử dụng
        </a>
    </aside>

    <main class="main">

        <div class="page-title">
            Quản lý thiết bị
        </div>

        <?php if ($message !== ''): ?>
            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-title">Tổng thiết bị</div>
                <div class="stat-number"><?= $totalDevices ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Đang hoạt động</div>
                <div class="stat-number"><?= $activeDevices ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Đang bảo trì</div>
                <div class="stat-number"><?= $maintenanceDevices ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Hỏng</div>
                <div class="stat-number"><?= $brokenDevices ?></div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <div class="card-title">Thêm thiết bị</div>
            </div>

            <div class="card-body">
                <form method="POST" action="index.php?page=devices">

                    <input type="hidden" name="action" value="add">

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Mã thiết bị</label>
                            <input
                                type="text"
                                name="device_code"
                                placeholder="VD: TB001"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Tên thiết bị</label>
                            <input
                                type="text"
                                name="device_name"
                                placeholder="Nhập tên thiết bị"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label>Loại thiết bị</label>
                            <select name="type_id" required>
                                <option value="">-- Chọn loại thiết bị --</option>

                                <?php foreach ($deviceTypes as $type): ?>
                                    <option value="<?= (int)$type['id'] ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <label>Phòng</label>
                            <select name="room_id" required>
                                <option value="">-- Chọn phòng --</option>

                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= (int)$room['id'] ?>">
                                        <?= htmlspecialchars($room['room_code']) ?>
                                        -
                                        <?= htmlspecialchars($room['room_name']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status">
                                <option value="active">Tốt</option>
                                <option value="maintenance">Đang bảo trì</option>
                                <option value="broken">Hỏng</option>
                            </select>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Thêm thiết bị
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <div class="card-title">Danh sách thiết bị</div>
            </div>

            <?php if (empty($devices)): ?>

                <div class="empty">
                    Chưa có thiết bị nào trong hệ thống.
                </div>

            <?php else: ?>

                <div class="card-body">

                    <table>
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã thiết bị</th>
                                <th>Tên thiết bị</th>
                                <th>Loại thiết bị</th>
                                <th>Phòng</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($devices as $index => $device): ?>

                            <tr id="device-row-<?= (int)$device['id'] ?>">

                                <td><?= $index + 1 ?></td>

                                <td>
                                    <?= htmlspecialchars($device['device_code']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($device['device_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($device['type_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($device['room_code']) ?>
                                    -
                                    <?= htmlspecialchars($device['room_name']) ?>
                                </td>

                                <td>
                                    <span class="device-status <?= deviceStatusClass($device['status']) ?>">
                                        <?= htmlspecialchars(deviceStatusText($device['status'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="action-group">

                                        <button
                                            type="button"
                                            class="btn btn-edit"
                                            onclick="editDevice(<?= (int)$device['id'] ?>)"
                                        >
                                            Sửa
                                        </button>

                                        <form
                                            method="POST"
                                            action="index.php?page=devices"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa thiết bị này không?')"
                                        >
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int)$device['id'] ?>">

                                            <button type="submit" class="btn btn-delete">
                                                Xóa
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                            <tr
                                id="edit-row-<?= (int)$device['id'] ?>"
                                style="display:none;"
                            >
                                <td colspan="7">

                                    <form
                                        method="POST"
                                        action="index.php?page=devices"
                                    >

                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="<?= (int)$device['id'] ?>">

                                        <div class="form-grid">

                                            <div class="form-group">
                                                <label>Mã thiết bị</label>
                                                <input
                                                    type="text"
                                                    name="device_code"
                                                    value="<?= htmlspecialchars($device['device_code']) ?>"
                                                    required
                                                >
                                            </div>

                                            <div class="form-group">
                                                <label>Tên thiết bị</label>
                                                <input
                                                    type="text"
                                                    name="device_name"
                                                    value="<?= htmlspecialchars($device['device_name']) ?>"
                                                    required
                                                >
                                            </div>

                                            <div class="form-group">
                                                <label>Loại thiết bị</label>
                                                <select name="type_id" required>

                                                    <?php foreach ($deviceTypes as $type): ?>

                                                        <option
                                                            value="<?= (int)$type['id'] ?>"
                                                            <?= (int)$type['id'] === (int)$device['type_id'] ? 'selected' : '' ?>
                                                        >
                                                            <?= htmlspecialchars($type['name']) ?>
                                                        </option>

                                                    <?php endforeach; ?>

                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Phòng</label>
                                                <select name="room_id" required>

                                                    <?php foreach ($rooms as $room): ?>

                                                        <option
                                                            value="<?= (int)$room['id'] ?>"
                                                            <?= (int)$room['id'] === (int)$device['room_id'] ? 'selected' : '' ?>
                                                        >
                                                            <?= htmlspecialchars($room['room_code']) ?>
                                                            -
                                                            <?= htmlspecialchars($room['room_name']) ?>
                                                        </option>

                                                    <?php endforeach; ?>

                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Trạng thái</label>
                                                <select name="status">

                                                    <option
                                                        value="active"
                                                        <?= $device['status'] === 'active' ? 'selected' : '' ?>
                                                    >
                                                        Tốt
                                                    </option>

                                                    <option
                                                        value="maintenance"
                                                        <?= $device['status'] === 'maintenance' ? 'selected' : '' ?>
                                                    >
                                                        Đang bảo trì
                                                    </option>

                                                    <option
                                                        value="broken"
                                                        <?= $device['status'] === 'broken' ? 'selected' : '' ?>
                                                    >
                                                        Hỏng
                                                    </option>

                                                </select>
                                            </div>

                                        </div>

                                        <div class="form-actions">

                                            <button
                                                type="submit"
                                                class="btn btn-primary"
                                            >
                                                Lưu thay đổi
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-cancel"
                                                onclick="editDevice(<?= (int)$device['id'] ?>)"
                                                style="margin-left:8px;"
                                            >
                                                Hủy
                                            </button>

                                        </div>

                                    </form>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>

            <?php endif; ?>

        </div>

        <div class="content-card section-spacing" id="bookings">

            <div class="tabs">

                <div
                    class="tab active"
                    onclick="showTab('booking-tab', this)"
                >
                    Duyệt yêu cầu phòng
                </div>

                <div
                    class="tab"
                    onclick="showTab('calendar-tab', this)"
                >
                    Lịch sử dụng
                </div>

                <div
                    class="tab"
                    onclick="showTab('borrow-tab', this)"
                >
                    Yêu cầu mượn thiết bị
                </div>

            </div>

            <div id="booking-tab" class="tab-content active">

                <div class="card-body">

                    <div class="stats">

                        <div class="stat-card">
                            <div class="stat-title">Tổng yêu cầu</div>
                            <div class="stat-number">
                                <?= $totalBookings ?>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-title">Chờ duyệt</div>
                            <div class="stat-number">
                                <?= $pendingBookings ?>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-title">Đã xử lý</div>
                            <div class="stat-number">
                                <?= $processedBookings ?>
                            </div>
                        </div>

                    </div>

                    <?php if (empty($bookings)): ?>

                        <div class="empty">
                            Chưa có yêu cầu đặt phòng.
                        </div>

                    <?php else: ?>

                        <table>

                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Người đặt</th>
                                    <th>Phòng</th>
                                    <th>Bắt đầu</th>
                                    <th>Kết thúc</th>
                                    <th>Mục đích</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($bookings as $index => $booking): ?>

                                <tr>

                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <?= htmlspecialchars($booking['user_name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['room_code']) ?>
                                        -
                                        <?= htmlspecialchars($booking['room_name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(formatDateTime($booking['start_time'])) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(formatDateTime($booking['end_time'])) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($booking['purpose']) ?>
                                    </td>

                                    <td>
                                        <span class="status <?= bookingStatusClass($booking['status']) ?>">
                                            <?= htmlspecialchars(bookingStatusText($booking['status'])) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <?php if ($booking['status'] === 'pending'): ?>

                                            <div class="action-group">

                                                <form
                                                    method="POST"
                                                    action="index.php?page=devices"
                                                    onsubmit="return confirm('Bạn có chắc muốn duyệt yêu cầu này không?')"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="booking"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int)$booking['id'] ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="status"
                                                        value="approved"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-approve"
                                                    >
                                                        Duyệt
                                                    </button>

                                                </form>

                                                <form
                                                    method="POST"
                                                    action="index.php?page=devices"
                                                    onsubmit="return confirm('Bạn có chắc muốn từ chối yêu cầu này không?')"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="booking"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int)$booking['id'] ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="status"
                                                        value="rejected"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-reject"
                                                    >
                                                        Từ chối
                                                    </button>

                                                </form>

                                            </div>

                                        <?php else: ?>

                                            <span style="color:#777;">
                                                Đã xử lý
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    <?php endif; ?>

                </div>

            </div>

            <div id="calendar-tab" class="tab-content">

                <div class="card-body">

                    <div class="calendar" id="calendar">

                        <div class="calendar-header">

                            <div class="calendar-title" id="calendarTitle"></div>

                            <div class="calendar-buttons">

                                <button
                                    type="button"
                                    class="calendar-btn"
                                    onclick="changeMonth(-1)"
                                >
                                    ‹
                                </button>

                                <button
                                    type="button"
                                    class="calendar-btn"
                                    onclick="goToday()"
                                >
                                    Hôm nay
                                </button>

                                <button
                                    type="button"
                                    class="calendar-btn"
                                    onclick="changeMonth(1)"
                                >
                                    ›
                                </button>

                            </div>

                        </div>

                        <div class="calendar-grid">

                            <div class="calendar-day-name">T2</div>
                            <div class="calendar-day-name">T3</div>
                            <div class="calendar-day-name">T4</div>
                            <div class="calendar-day-name">T5</div>
                            <div class="calendar-day-name">T6</div>
                            <div class="calendar-day-name">T7</div>
                            <div class="calendar-day-name">CN</div>

                        </div>

                        <div
                            class="calendar-grid"
                            id="calendarDays"
                        ></div>

                    </div>

                </div>

            </div>

            <div id="borrow-tab" class="tab-content">

                <div class="card-body">

                    <div class="empty">
                        Chức năng duyệt yêu cầu mượn thiết bị chưa được kết nối
                        vì CSDL hiện tại của nhóm chưa có bảng lưu yêu cầu mượn thiết bị.
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<script>
    function editDevice(id) {
        const row = document.getElementById('edit-row-' + id);

        if (!row) {
            return;
        }

        if (row.style.display === 'none' || row.style.display === '') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }

    function showTab(tabId, element) {
        const tabs = document.querySelectorAll('.tab-content');
        const buttons = document.querySelectorAll('.tab');

        tabs.forEach(function(tab) {
            tab.classList.remove('active');
        });

        buttons.forEach(function(button) {
            button.classList.remove('active');
        });

        const selectedTab = document.getElementById(tabId);

        if (selectedTab) {
            selectedTab.classList.add('active');
        }

        if (element) {
            element.classList.add('active');
        }

        if (tabId === 'calendar-tab') {
            renderCalendar();
        }
    }

    const bookings = <?= json_encode($bookings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    let calendarDate = new Date();

    function dateKey(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return year + '-' + month + '-' + day;
    }

    function bookingDateKey(datetime) {
        if (!datetime) {
            return '';
        }

        const value = String(datetime);

        if (value.length >= 10) {
            return value.substring(0, 10);
        }

        return '';
    }

    function renderCalendar() {
        const calendarTitle = document.getElementById('calendarTitle');
        const calendarDays = document.getElementById('calendarDays');

        if (!calendarTitle || !calendarDays) {
            return;
        }

        const year = calendarDate.getFullYear();
        const month = calendarDate.getMonth();

        const monthNames = [
            'Tháng 1',
            'Tháng 2',
            'Tháng 3',
            'Tháng 4',
            'Tháng 5',
            'Tháng 6',
            'Tháng 7',
            'Tháng 8',
            'Tháng 9',
            'Tháng 10',
            'Tháng 11',
            'Tháng 12'
        ];

        calendarTitle.textContent = monthNames[month] + ' ' + year;

        calendarDays.innerHTML = '';

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

        let startDay = firstDay.getDay();

        if (startDay === 0) {
            startDay = 6;
        } else {
            startDay = startDay - 1;
        }

        const previousMonthLastDay = new Date(year, month, 0).getDate();

        for (let i = startDay - 1; i >= 0; i--) {
            const day = previousMonthLastDay - i;
            const cellDate = new Date(year, month - 1, day);

            createCalendarCell(
                cellDate,
                true,
                calendarDays
            );
        }

        for (let day = 1; day <= lastDay.getDate(); day++) {
            const cellDate = new Date(year, month, day);

            createCalendarCell(
                cellDate,
                false,
                calendarDays
            );
        }

        const totalCells = startDay + lastDay.getDate();
        const remainingCells = (7 - (totalCells % 7)) % 7;

        for (let day = 1; day <= remainingCells; day++) {
            const cellDate = new Date(year, month + 1, day);

            createCalendarCell(
                cellDate,
                true,
                calendarDays
            );
        }
    }

    function createCalendarCell(date, otherMonth, container) {
        const cell = document.createElement('div');
        cell.className = 'calendar-cell';

        if (otherMonth) {
            cell.classList.add('other-month');
        }

        const dateNumber = document.createElement('div');
        dateNumber.className = 'calendar-date';
        dateNumber.textContent = date.getDate();

        cell.appendChild(dateNumber);

        const key = dateKey(date);

        const dayBookings = bookings.filter(function(booking) {
            return bookingDateKey(booking.start_time) === key;
        });

        dayBookings.slice(0, 3).forEach(function(booking) {
            const event = document.createElement('div');
            event.className = 'calendar-event';

            if (booking.status === 'approved') {
                event.classList.add('booking');
            }

            if (booking.status === 'pending') {
                event.classList.add('pending');
            }

            const startTime = formatTime(booking.start_time);
            const room = booking.room_code || '';

            event.textContent =
                startTime + ' ' + room;

            event.title =
                (booking.user_name || '') +
                ' - ' +
                (booking.room_name || '') +
                ' - ' +
                (booking.purpose || '');

            cell.appendChild(event);
        });

        if (dayBookings.length > 3) {
            const more = document.createElement('div');
            more.className = 'calendar-more';
            more.textContent =
                '+' + (dayBookings.length - 3) + ' lịch khác';

            cell.appendChild(more);
        }

        container.appendChild(cell);
    }

    function formatTime(datetime) {
        if (!datetime || String(datetime).length < 16) {
            return '';
        }

        return String(datetime).substring(11, 16);
    }

    function changeMonth(value) {
        calendarDate.setMonth(
            calendarDate.getMonth() + value
        );

        renderCalendar();
    }

    function goToday() {
        calendarDate = new Date();
        renderCalendar();
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#bookings') {
            document.getElementById('bookings').scrollIntoView();
        }

        if (window.location.hash === '#calendar') {
            document.getElementById('bookings').scrollIntoView();
            showTab(
                'calendar-tab',
                document.querySelectorAll('.tab')[1]
            );
        }
    });
</script>

</body>
</html>