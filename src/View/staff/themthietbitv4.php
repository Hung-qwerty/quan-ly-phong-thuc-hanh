<?php

$statusText = [
    'active' => 'Hoạt động',
    'maintenance' => 'Đang bảo trì',
    'broken' => 'Hỏng'
];

$devices = $devices ?? [];
$deviceTypes = $deviceTypes ?? [];
$rooms = $rooms ?? [];
$bookings = $bookings ?? [];
$borrowings = $borrowings ?? [];

$message = $message ?? '';
$messageType = $messageType ?? '';

$pendingBookings = $pendingBookings ?? 0;
$processedBookings = $processedBookings ?? 0;

$pendingBorrowings = $pendingBorrowings ?? 0;
$processedBorrowings = $processedBorrowings ?? 0;

$totalDevices = $totalDevices ?? count($devices);
$activeDevices = $activeDevices ?? 0;
$maintenanceDevices = $maintenanceDevices ?? 0;
$brokenDevices = $brokenDevices ?? 0;

$maintenanceDevicesList = $maintenanceDevicesList ?? [];
$maintenanceStats = $maintenanceStats ?? [
    'tong' => count($maintenanceDevicesList),
    'hoatdong' => 0,
    'hong' => 0,
    'baotri' => 0
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý phòng thực hành</title>

<style>
*{box-sizing:border-box}

body{
    margin:0;
    font-family:"Segoe UI",Arial,sans-serif;
    background:#f4f7f9;
    color:#333
}

.header{
    height:70px;
    background:#003399;
    border-bottom:1px solid #002F62;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 30px
}

.header-title{
    color:#fff;
    font-size:15px;
    font-weight:600
}

.staff{
    display:flex;
    align-items:center;
    gap:10px
}

.avatar{
    width:36px;
    height:36px;
    border-radius:50%;
    background:#fff;
    color:#003B7A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700
}

.staff-name{
    font-size:14px;
    font-weight:600;
    color:#fff
}

.layout{
    display:flex;
    min-height:calc(100vh - 70px)
}

.sidebar{
    width:245px;
    flex-shrink:0;
    background:#fff;
    border-right:1px solid #dee2e6;
    padding:25px 16px
}

.logo{
    padding:0 12px 25px;
    border-bottom:1px solid #dee2e6;
    margin-bottom:22px
}

.logo h2{
    margin:0;
    color:#003399;
    font-size:20px;
    line-height:1.35
}

.logo p{
    margin:7px 0 0;
    color:#777;
    font-size:13px
}

.menu-title{
    padding:0 12px;
    margin-bottom:8px;
    color:#999;
    font-size:11px;
    font-weight:700;
    text-transform:uppercase
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px;
    margin-bottom:5px;
    border-radius:7px;
    color:#555;
    text-decoration:none;
    font-size:14px
}

.menu a:hover,
.menu a.active{
    background:#eaf0fa;
    color:#003399
}

.menu a.active{
    font-weight:600
}

.icon{
    width:22px;
    text-align:center;
    font-size:16px
}

.main{
    flex:1;
    min-width:0;
    width:calc(100% - 245px)
}

.content{
    width:100%;
    padding:30px
}

.page-title{
    margin-bottom:25px
}

.page-title h1{
    margin:0;
    color:#003399;
    font-size:25px
}

.page-title p{
    margin:6px 0 0;
    color:#777;
    font-size:14px
}

.stats{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:18px;
    margin-bottom:25px
}

.stat{
    background:#fff;
    border:1px solid #dee2e6;
    border-radius:8px;
    padding:18px 20px
}

.stat-label{
    color:#777;
    font-size:13px;
    margin-bottom:8px
}

.stat-number{
    color:#003399;
    font-size:25px;
    font-weight:700
}

.card{
    width:100%;
    background:#fff;
    border:1px solid #dee2e6;
    border-radius:8px;
    overflow:hidden
}

.card-header{
    padding:18px 20px;
    border-bottom:1px solid #dee2e6;
    display:flex;
    align-items:center;
    justify-content:space-between
}

.card-header h3{
    margin:0;
    color:#003399;
    font-size:17px
}

.card-header p{
    margin:4px 0 0;
    color:#888;
    font-size:13px
}

.form-area{
    padding:20px;
    background:#fafbfc;
    border-bottom:1px solid #eee
}

.form-grid{
    display:grid;
    grid-template-columns:1.2fr 2fr 1fr 1fr 1fr auto;
    gap:15px;
    align-items:end
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-size:13px;
    font-weight:600
}

input,
select,
textarea{
    width:100%;
    padding:10px 11px;
    border:1px solid #d7dce1;
    border-radius:6px;
    background:#fff;
    outline:none;
    font-family:inherit
}

input,
select{
    height:40px
}

textarea{
    min-height:90px;
    resize:vertical
}

input:focus,
select:focus,
textarea:focus{
    border-color:#003399;
    box-shadow:0 0 0 3px rgba(0,51,153,.1)
}

button{
    border:0;
    border-radius:6px;
    padding:10px 15px;
    color:#fff;
    cursor:pointer;
    font-family:inherit;
    font-size:13px
}

.primary{
    background:#003399
}

.primary:hover{
    background:#002266
}

.secondary{
    background:#6c757d
}

.secondary:hover{
    background:#565e64
}

.danger{
    background:#dc3545
}

.danger:hover{
    background:#b02a37
}

.success-btn{
    background:#198754
}

.success-btn:hover{
    background:#146c43
}

.message{
    padding:12px 15px;
    border-radius:6px;
    margin-bottom:20px;
    font-size:14px
}

.message.success{
    background:#e8f5ee;
    color:#198754;
    border:1px solid #c8e6d3
}

.message.error{
    background:#fdeaea;
    color:#dc3545;
    border:1px solid #f5c2c7
}

.table-wrapper{
    width:100%;
    overflow-x:auto
}

table{
    width:100%;
    border-collapse:collapse
}

th{
    background:#003399;
    color:#fff;
    padding:13px 15px;
    text-align:left;
    font-size:13px;
    font-weight:600
}

td{
    padding:14px 15px;
    border-bottom:1px solid #eee;
    font-size:13px
}

tr:hover td{
    background:#f8fafc
}

.actions{
    display:flex;
    gap:6px
}

.edit-area{
    display:none;
    background:#f8f9fa;
    border-bottom:1px solid #eee
}

.edit-area.active{
    display:table-row
}

.edit-grid{
    display:grid;
    grid-template-columns:1.2fr 2fr 1fr 1fr 1fr auto;
    gap:12px;
    align-items:end
}

.status{
    display:inline-block;
    padding:5px 10px;
    border-radius:15px;
    font-size:11px;
    font-weight:600
}

.pending{
    background:#fff3cd;
    color:#856404
}

.approved{
    background:#e8f5ee;
    color:#198754
}

.rejected{
    background:#fdeaea;
    color:#dc3545
}

.device-status{
    display:inline-flex;
    align-items:center;
    gap:7px;
    font-weight:600;
    font-size:13px
}

.device-status-dot{
    width:10px;
    height:10px;
    border-radius:50%;
    display:inline-block
}

.device-active .device-status-dot{
    background:#198754
}

.device-maintenance .device-status-dot{
    background:#ffc107
}

.device-broken .device-status-dot{
    background:#dc3545
}

.request-tabs{
    display:flex;
    gap:8px;
    padding:0 0 15px;
    margin-bottom:20px;
    border-bottom:1px solid #dee2e6
}

.request-tabs a{
    padding:10px 15px;
    border:1px solid #dee2e6;
    border-radius:6px;
    color:#555;
    text-decoration:none;
    font-size:13px;
    background:#f8f9fa;
    white-space:nowrap
}

.request-tabs a.active{
    color:#003399;
    background:#fff;
    font-weight:600
}

.calendar-card{
    margin-top:25px
}

.calendar{
    padding:20px
}

.calendar-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:15px;
    margin-bottom:15px
}

.calendar-toolbar h3{
    margin:0;
    color:#003399;
    font-size:17px
}

.calendar-nav{
    display:flex;
    gap:6px
}

.calendar-nav button{
    padding:8px 12px
}

.calendar-grid{
    display:grid;
    grid-template-columns:repeat(7,minmax(0,1fr));
    border-top:1px solid #dee2e6;
    border-left:1px solid #dee2e6
}

.calendar-weekday{
    background:#f1f4f8;
    color:#555;
    font-size:12px;
    font-weight:700;
    text-align:center;
    padding:10px 5px;
    border-right:1px solid #dee2e6;
    border-bottom:1px solid #dee2e6
}

.calendar-day{
    min-height:125px;
    padding:7px;
    border-right:1px solid #dee2e6;
    border-bottom:1px solid #dee2e6;
    background:#fff;
    overflow:hidden
}

.calendar-day.other-month{
    background:#fafbfc;
    color:#aaa
}

.calendar-day.today{
    box-shadow:inset 0 0 0 2px #003399
}

.calendar-date{
    font-size:12px;
    font-weight:700;
    margin-bottom:6px
}

.calendar-events{
    display:flex;
    flex-direction:column;
    gap:4px
}

.calendar-event{
    padding:5px 6px;
    border-radius:4px;
    font-size:10px;
    line-height:1.25;
    overflow:hidden;
    white-space:nowrap;
    text-overflow:ellipsis
}

.calendar-event.room{
    background:#eaf0fa;
    color:#003399;
    border-left:3px solid #003399
}

.calendar-event.borrow{
    background:#fff3cd;
    color:#856404;
    border-left:3px solid #ffc107
}

.calendar-more{
    color:#003399;
    font-size:10px;
    font-weight:600;
    padding:2px 4px
}

.calendar-legend{
    display:flex;
    gap:18px;
    margin-top:12px;
    font-size:12px;
    color:#666
}

.legend-item{
    display:flex;
    align-items:center;
    gap:6px
}

.legend-dot{
    width:9px;
    height:9px;
    border-radius:50%
}

.legend-room{
    background:#003399
}

.legend-borrow{
    background:#ffc107
}

.request-table-status{
    white-space:nowrap
}

.info-box{
    padding:15px 20px;
    background:#f8f9fa;
    border-bottom:1px solid #eee;
    color:#666;
    font-size:13px;
    line-height:1.6
}

.maintenance-description{
    max-width:300px;
    line-height:1.5
}

.maintenance-form{
    background:#fafbfc;
    padding:20px;
    border-bottom:1px solid #eee
}

.maintenance-form-grid{
    display:grid;
    grid-template-columns:1fr 2fr auto;
    gap:15px;
    align-items:end
}

.maintenance-result{
    min-width:220px
}

.maintenance-actions{
    display:flex;
    gap:6px;
    flex-wrap:wrap
}

@media(max-width:1100px){

    .form-grid,
    .edit-grid{
        grid-template-columns:1fr 1fr 1fr
    }

    .maintenance-form-grid{
        grid-template-columns:1fr 1fr
    }
}

@media(max-width:900px){

    .sidebar{
        width:210px
    }

    .main{
        width:calc(100% - 210px)
    }

    .content{
        padding:25px
    }

    .calendar-day{
        min-height:105px
    }

    .calendar-event{
        font-size:9px
    }
}

@media(max-width:700px){

    .header{
        padding:0 20px
    }

    .layout{
        display:block
    }

    .sidebar{
        width:100%;
        border-right:0;
        border-bottom:1px solid #dee2e6
    }

    .main{
        width:100%
    }

    .content{
        padding:20px
    }

    .stats{
        grid-template-columns:1fr
    }

    .form-grid,
    .edit-grid,
    .maintenance-form-grid{
        grid-template-columns:1fr
    }

    .request-tabs{
        overflow-x:auto
    }
}
</style>
</head>

<body>

<header class="header">

    <div class="header-title">
        HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ
    </div>

    <div class="staff">

        <div class="avatar">S</div>

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

    </div>

    <div class="menu-title">
        DANH MỤC
    </div>

    <nav class="menu">

        <a href="?page=devices"
           class="<?php echo $page === 'devices' ? 'active' : ''; ?>">

            <span class="icon">▣</span>
            <span>Quản lý thiết bị</span>

        </a>

        <a href="?page=staff_bookings"
           class="<?php echo in_array($page, ['staff_bookings','borrowings'], true) ? 'active' : ''; ?>">

            <span class="icon">▤</span>
            <span>Duyệt yêu cầu phòng</span>

        </a>

        <a href="?page=maintenance"
           class="<?php echo $page === 'maintenance' ? 'active' : ''; ?>">

            <span class="icon">⚙</span>
            <span>Bảo trì</span>

        </a>

    </nav>

</aside>

<main class="main">

<div class="content">

<?php if ($message !== ''): ?>

<div class="message <?php echo htmlspecialchars($messageType); ?>">
    <?php echo htmlspecialchars($message); ?>
</div>

<?php endif; ?>


<?php if ($page === 'devices'): ?>

<div class="page-title">

    <h1>Quản lý thiết bị</h1>

    <p>
        Quản lý thông tin, vị trí và trạng thái thiết bị
    </p>

</div>

<div class="stats">

    <div class="stat">

        <div class="stat-label">
            Tổng thiết bị
        </div>

        <div class="stat-number">
            <?php echo $totalDevices; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đang hoạt động
        </div>

        <div class="stat-number">
            <?php echo $activeDevices; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đang bảo trì / Hỏng
        </div>

        <div class="stat-number">
            <?php echo $maintenanceDevices + $brokenDevices; ?>
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
                Quản lý thông tin và trạng thái thiết bị
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
                        Mã thiết bị
                    </label>

                    <input
                        type="text"
                        name="device_code"
                        maxlength="50"
                        placeholder="TB004"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Tên thiết bị
                    </label>

                    <input
                        type="text"
                        name="device_name"
                        maxlength="100"
                        placeholder="Nhập tên thiết bị"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Loại thiết bị
                    </label>

                    <select name="type_id" required>

                        <option value="">
                            Chọn loại
                        </option>

                        <?php foreach ($deviceTypes as $type): ?>

                        <option value="<?php echo $type['id']; ?>">

                            <?php echo htmlspecialchars($type['name']); ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Phòng
                    </label>

                    <select name="room_id" required>

                        <option value="">
                            Chọn phòng
                        </option>

                        <?php foreach ($rooms as $room): ?>

                        <option value="<?php echo $room['id']; ?>">

                            <?php echo htmlspecialchars($room['room_code']); ?>

                        </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Trạng thái
                    </label>

                    <select name="status" required>

                        <option value="active">
                            Hoạt động
                        </option>

                        <option value="maintenance">
                            Đang bảo trì
                        </option>

                        <option value="broken">
                            Hỏng
                        </option>

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

                <th width="60">STT</th>
                <th>Mã thiết bị</th>
                <th>Tên thiết bị</th>
                <th>Loại thiết bị</th>
                <th>Phòng</th>
                <th width="150">Trạng thái</th>
                <th width="150">Thao tác</th>

            </tr>

            </thead>

            <tbody>

            <?php if (empty($devices)): ?>

            <tr>

                <td
                    colspan="7"
                    style="text-align:center;color:#888"
                >
                    Chưa có thiết bị.
                </td>

            </tr>

            <?php endif; ?>

            <?php foreach ($devices as $index => $device): ?>

                <tr>

                    <td>
                        <?php echo $index + 1; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($device['device_code']); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($device['device_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($device['type_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($device['room_code']); ?>
                    </td>

                    <td>

                        <span class="device-status device-<?php echo htmlspecialchars($device['status']); ?>">

                            <span class="device-status-dot"></span>

                            <?php echo $statusText[$device['status']] ?? ''; ?>

                        </span>

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

                    <td colspan="7">

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
                                        Mã thiết bị
                                    </label>

                                    <input
                                        type="text"
                                        name="device_code"
                                        maxlength="50"
                                        value="<?php echo htmlspecialchars($device['device_code']); ?>"
                                        required
                                    >

                                </div>

                                <div class="form-group">

                                    <label>
                                        Tên thiết bị
                                    </label>

                                    <input
                                        type="text"
                                        name="device_name"
                                        maxlength="100"
                                        value="<?php echo htmlspecialchars($device['device_name']); ?>"
                                        required
                                    >

                                </div>

                                <div class="form-group">

                                    <label>
                                        Loại thiết bị
                                    </label>

                                    <select name="type_id" required>

                                        <?php foreach ($deviceTypes as $type): ?>

                                        <option
                                            value="<?php echo $type['id']; ?>"
                                            <?php echo (int)$type['id'] === (int)$device['type_id'] ? 'selected' : ''; ?>
                                        >

                                            <?php echo htmlspecialchars($type['name']); ?>

                                        </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Phòng
                                    </label>

                                    <select name="room_id" required>

                                        <?php foreach ($rooms as $room): ?>

                                        <option
                                            value="<?php echo $room['id']; ?>"
                                            <?php echo (int)$room['id'] === (int)$device['room_id'] ? 'selected' : ''; ?>
                                        >

                                            <?php echo htmlspecialchars($room['room_code']); ?>

                                        </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="form-group">

                                    <label>
                                        Trạng thái
                                    </label>

                                    <select name="status" required>

                                        <option
                                            value="active"
                                            <?php echo $device['status'] === 'active' ? 'selected' : ''; ?>
                                        >
                                            Hoạt động
                                        </option>

                                        <option
                                            value="maintenance"
                                            <?php echo $device['status'] === 'maintenance' ? 'selected' : ''; ?>
                                        >
                                            Đang bảo trì
                                        </option>

                                        <option
                                            value="broken"
                                            <?php echo $device['status'] === 'broken' ? 'selected' : ''; ?>
                                        >
                                            Hỏng
                                        </option>

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


<?php elseif ($page === 'staff_bookings' || $page === 'borrowings'): ?>

<div class="page-title">

    <h1>DUYỆT YÊU CẦU</h1>

    <p>
        Cán bộ Lab xem và xử lý các yêu cầu từ người dùng
    </p>

</div>

<div class="request-tabs">

    <a
        href="?page=staff_bookings"
        class="<?php echo $page === 'staff_bookings' ? 'active' : ''; ?>"
    >
        Duyệt yêu cầu đặt phòng
    </a>

    <a
        href="?page=borrowings"
        class="<?php echo $page === 'borrowings' ? 'active' : ''; ?>"
    >
        Duyệt yêu cầu mượn thiết bị
    </a>

</div>


<?php if ($page === 'staff_bookings'): ?>

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
            <?php echo $pendingBookings; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đã xử lý
        </div>

        <div class="stat-number">
            <?php echo $processedBookings; ?>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Danh sách yêu cầu đặt phòng
            </h3>

            <p>
                Xem và duyệt các yêu cầu sử dụng phòng thực hành
            </p>

        </div>

    </div>

    <div class="table-wrapper">

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

            <?php if (empty($bookings)): ?>

            <tr>

                <td
                    colspan="8"
                    style="text-align:center;color:#888"
                >
                    Chưa có yêu cầu đặt phòng.
                </td>

            </tr>

            <?php endif; ?>

            <?php foreach ($bookings as $index => $booking): ?>

                <tr>

                    <td>
                        <?php echo $index + 1; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['user_name']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['room_code']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['start_time']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['end_time']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($booking['purpose']); ?>
                    </td>

                    <td>

                        <?php if ($booking['status'] === 'pending'): ?>

                            <span class="status pending">
                                Chờ duyệt
                            </span>

                        <?php elseif ($booking['status'] === 'approved'): ?>

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

                    <?php if ($booking['status'] === 'pending'): ?>

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

                        <span style="color:#888">
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


<?php else: ?>

<div class="stats">

    <div class="stat">

        <div class="stat-label">
            Tổng yêu cầu mượn
        </div>

        <div class="stat-number">
            <?php echo count($borrowings); ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Chờ duyệt
        </div>

        <div class="stat-number">
            <?php echo $pendingBorrowings; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đã xử lý
        </div>

        <div class="stat-number">
            <?php echo $processedBorrowings; ?>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Danh sách yêu cầu mượn thiết bị
            </h3>

            <p>
                Cán bộ Lab xem và duyệt các yêu cầu mượn thiết bị
            </p>

        </div>

    </div>

    <div class="info-box">

        CSDL nhóm hiện chưa có bảng lưu yêu cầu mượn thiết bị.
        Chức năng này sẽ được kết nối khi có bảng dữ liệu tương ứng.

    </div>

    <div class="table-wrapper">

        <table>

            <thead>

            <tr>

                <th>STT</th>
                <th>Người yêu cầu</th>
                <th>Mã thiết bị</th>
                <th>Thiết bị</th>
                <th>Bắt đầu</th>
                <th>Kết thúc</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>

            </tr>

            </thead>

            <tbody>

            <tr>

                <td
                    colspan="8"
                    style="text-align:center;color:#888"
                >
                    Chưa có dữ liệu yêu cầu mượn thiết bị.
                </td>

            </tr>

            </tbody>

        </table>

    </div>

</div>

<?php endif; ?>


<?php elseif ($page === 'maintenance'): ?>

<div class="page-title">

    <h1>Bảo trì thiết bị</h1>

    <p>
        Quản lý tình trạng hỏng hóc và quá trình bảo trì thiết bị
    </p>

</div>

<div class="stats">

    <div class="stat">

        <div class="stat-label">
            Tổng thiết bị
        </div>

        <div class="stat-number">
            <?php echo $maintenanceStats['tong']; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đang hoạt động
        </div>

        <div class="stat-number">
            <?php echo $maintenanceStats['hoatdong']; ?>
        </div>

    </div>

    <div class="stat">

        <div class="stat-label">
            Đang bảo trì / Hỏng
        </div>

        <div class="stat-number">
            <?php echo $maintenanceStats['baotri'] + $maintenanceStats['hong']; ?>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Danh sách thiết bị bảo trì
            </h3>

            <p>
                Theo dõi tình trạng và cập nhật quá trình bảo trì
            </p>

        </div>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>

            <tr>

                <th>STT</th>
                <th>Mã thiết bị</th>
                <th>Tên thiết bị</th>
                <th>Phòng</th>
                <th>Trạng thái</th>
                <th>Nội dung</th>
                <th>Kết quả</th>
                <th>Thao tác</th>

            </tr>

            </thead>

            <tbody>

            <?php if (empty($maintenanceDevicesList)): ?>

            <tr>

                <td
                    colspan="8"
                    style="text-align:center;color:#888"
                >
                    Chưa có dữ liệu thiết bị.
                </td>

            </tr>

            <?php endif; ?>

            <?php foreach ($maintenanceDevicesList as $index => $tb): ?>

                <?php
                $deviceStatus = $tb['device_status'] ?? '';
                $maintenanceStatus = $tb['maintenance_status'] ?? '';
                ?>

                <tr>

                    <td>
                        <?php echo $index + 1; ?>
                    </td>

                    <td>
                        <strong>
                            <?php echo htmlspecialchars($tb['device_code'] ?? ''); ?>
                        </strong>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($tb['device_name'] ?? ''); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($tb['room_id'] ?? ''); ?>
                    </td>

                    <td>

                        <span class="device-status device-<?php echo htmlspecialchars($deviceStatus); ?>">

                            <span class="device-status-dot"></span>

                            <?php echo $statusText[$deviceStatus] ?? $deviceStatus; ?>

                        </span>

                    </td>

                    <td class="maintenance-description">

                        <?php if (!empty($tb['description'])): ?>

                            <?php echo htmlspecialchars($tb['description']); ?>

                        <?php elseif (!empty($tb['content'])): ?>

                            <?php echo htmlspecialchars($tb['content']); ?>

                        <?php else: ?>

                            <span style="color:#999">
                                Chưa có nội dung
                            </span>

                        <?php endif; ?>

                    </td>

                    <td class="maintenance-description">

                        <?php if (!empty($tb['result'])): ?>

                            <?php echo htmlspecialchars($tb['result']); ?>

                        <?php else: ?>

                            <span style="color:#999">
                                Chưa có kết quả
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if ($deviceStatus === 'broken'): ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="batdau_baotri"
                                    value="1"
                                >

                                <input
                                    type="hidden"
                                    name="device_id"
                                    value="<?php echo (int)$tb['device_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="description"
                                    value="Bắt đầu bảo trì thiết bị"
                                >

                                <button
                                    type="submit"
                                    class="primary"
                                >
                                    Bắt đầu
                                </button>

                            </form>

                        <?php elseif ($deviceStatus === 'maintenance' && !empty($tb['maintenance_id'])): ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="capnhat"
                                    value="1"
                                >

                                <input
                                    type="hidden"
                                    name="maintenance_id"
                                    value="<?php echo (int)$tb['maintenance_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="device_id"
                                    value="<?php echo (int)$tb['device_id']; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="status"
                                    value="Hoàn thành"
                                >

                                <input
                                    type="text"
                                    name="result"
                                    placeholder="Nhập kết quả"
                                    required
                                    style="width:180px;margin-bottom:6px"
                                >

                                <button
                                    type="submit"
                                    class="success-btn"
                                >
                                    Hoàn thành
                                </button>

                            </form>

                        <?php elseif ($deviceStatus === 'active'): ?>

                            <span style="color:#198754">
                                Không cần bảo trì
                            </span>

                        <?php else: ?>

                            <span style="color:#888">
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


<div class="card calendar-card">

    <div class="card-header">

        <div>

            <h3>
                Lịch tổng hợp
            </h3>

            <p>
                Lịch đặt phòng và lịch mượn thiết bị
            </p>

        </div>

    </div>

    <div class="calendar">

        <div class="calendar-toolbar">

            <h3 id="calendarTitle"></h3>

            <div class="calendar-nav">

                <button
                    type="button"
                    class="secondary"
                    onclick="doiThang(-1)"
                >
                    ‹
                </button>

                <button
                    type="button"
                    class="secondary"
                    onclick="veHomNay()"
                >
                    Hôm nay
                </button>

                <button
                    type="button"
                    class="secondary"
                    onclick="doiThang(1)"
                >
                    ›
                </button>

            </div>

        </div>

        <div
            class="calendar-grid"
            id="calendarGrid"
        ></div>

        <div class="calendar-legend">

            <div class="legend-item">

                <span class="legend-dot legend-room"></span>

                Lịch đặt phòng

            </div>

            <div class="legend-item">

                <span class="legend-dot legend-borrow"></span>

                Lịch mượn thiết bị

            </div>

        </div>

    </div>

</div>

</div>

</main>

</div>

<script>

const bookingCalendarData =
<?php echo json_encode($bookings, JSON_UNESCAPED_UNICODE); ?>;

const borrowingCalendarData =
<?php echo json_encode($borrowings, JSON_UNESCAPED_UNICODE); ?>;

const calendarRooms =
<?php echo json_encode($rooms, JSON_UNESCAPED_UNICODE); ?>;

const calendarDevices =
<?php echo json_encode($devices, JSON_UNESCAPED_UNICODE); ?>;

let calendarDate = new Date();

function dateKey(value){

    const match = String(value).match(
        /(\d{2})\/(\d{2})\/(\d{4})/
    );

    if(match){
        return `${match[3]}-${match[2]}-${match[1]}`;
    }

    const date = new Date(value);

    if(!isNaN(date.getTime())){
        return `${date.getFullYear()}-${String(
            date.getMonth()+1
        ).padStart(2,'0')}-${String(
            date.getDate()
        ).padStart(2,'0')}`;
    }

    return "";
}

function findRoom(id){

    const item = calendarRooms.find(
        x => Number(x.id) === Number(id)
    );

    if(!item){
        return "";
    }

    return item.room_code || "";
}

function findDevice(id){

    const item = calendarDevices.find(
        x => Number(x.id) === Number(id)
    );

    if(!item){
        return "";
    }

    return item.device_name || "";
}

function taoLichCalendar(){

    const grid =
        document.getElementById("calendarGrid");

    const title =
        document.getElementById("calendarTitle");

    if(!grid || !title){
        return;
    }

    const year =
        calendarDate.getFullYear();

    const month =
        calendarDate.getMonth();

    title.textContent =
        `Tháng ${month + 1} ${year}`;

    const names = [
        "T2",
        "T3",
        "T4",
        "T5",
        "T6",
        "T7",
        "CN"
    ];

    let html = names
        .map(
            x => `<div class="calendar-weekday">${x}</div>`
        )
        .join("");

    let start =
        new Date(year, month, 1).getDay();

    start =
        start === 0
        ? 6
        : start - 1;

    const days =
        new Date(year, month + 1, 0).getDate();

    const prevDays =
        new Date(year, month, 0).getDate();

    const total =
        Math.ceil((start + days) / 7) * 7;

    const now =
        new Date();

    const today =
        `${now.getFullYear()}-${String(
            now.getMonth()+1
        ).padStart(2,"0")}-${String(
            now.getDate()
        ).padStart(2,"0")}`;

    for(let i = 0; i < total; i++){

        let day;
        let cell;

        if(i < start){

            day =
                prevDays - start + i + 1;

            cell =
                new Date(
                    year,
                    month - 1,
                    day
                );

        }else if(i < start + days){

            day =
                i - start + 1;

            cell =
                new Date(
                    year,
                    month,
                    day
                );

        }else{

            day =
                i - start - days + 1;

            cell =
                new Date(
                    year,
                    month + 1,
                    day
                );
        }

        const key =
            `${cell.getFullYear()}-${String(
                cell.getMonth()+1
            ).padStart(2,"0")}-${String(
                cell.getDate()
            ).padStart(2,"0")}`;

        let events = [];

        bookingCalendarData.forEach(item => {

            if(
                item.status !== "rejected" &&
                dateKey(item.start_time) === key
            ){

                events.push({

                    type:"room",

                    text:
                        `${String(item.start_time).split(" ")[0]} ${
                            findRoom(item.room_id)
                        }`,

                    detail:
                        `${item.user_name || ""} • ${
                            item.start_time
                        } - ${
                            item.end_time
                        }`
                });
            }
        });

        borrowingCalendarData.forEach(item => {

            if(
                item.status !== "rejected" &&
                dateKey(item.start_time) === key
            ){

                events.push({

                    type:"borrow",

                    text:
                        `${String(item.start_time).split(" ")[0]} ${
                            findDevice(item.device_id)
                        }`,

                    detail:
                        `${item.user_name || ""} • ${
                            item.start_time
                        } - ${
                            item.end_time
                        }`
                });
            }
        });

        const visible =
            events.slice(0,3);

        const hidden =
            events.length - visible.length;

        const other =
            cell.getMonth() !== month
            ? "other-month"
            : "";

        const current =
            key === today
            ? "today"
            : "";

        html +=
            `<div class="calendar-day ${other} ${current}">
                <div class="calendar-date">${day}</div>
                <div class="calendar-events">`;

        visible.forEach(event => {

            const detail =
                event.detail.replace(
                    /"/g,
                    "&quot;"
                );

            html +=
                `<div
                    class="calendar-event ${event.type}"
                    title="${detail}"
                >
                    ${event.text}
                </div>`;
        });

        if(hidden > 0){

            html +=
                `<div class="calendar-more">
                    +${hidden} lịch khác
                </div>`;
        }

        html +=
            `</div></div>`;
    }

    grid.innerHTML = html;
}

function doiThang(delta){

    calendarDate.setMonth(
        calendarDate.getMonth() + delta
    );

    taoLichCalendar();
}

function veHomNay(){

    calendarDate =
        new Date();

    taoLichCalendar();
}

function moSua(id){

    const row =
        document.getElementById(
            "edit-" + id
        );

    if(row){
        row.classList.add("active");
    }
}

function dongSua(id){

    const row =
        document.getElementById(
            "edit-" + id
        );

    if(row){
        row.classList.remove("active");
    }
}

document.addEventListener(
    "DOMContentLoaded",
    taoLichCalendar
);

</script>

</body>
</html>