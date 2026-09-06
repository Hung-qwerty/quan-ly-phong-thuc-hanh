<?php

$statusText = [
    'active' => 'Hoạt động',
    'maintenance' => 'Đang bảo trì',
    'broken' => 'Hỏng'
];

$page = $page ?? $_GET['page'] ?? 'devices';
$staffName = $staffName ?? 'Cán bộ Lab';

$devices = $devices ?? [];
$deviceTypes = $deviceTypes ?? [];
$rooms = $rooms ?? [];
$bookings = $bookings ?? [];
$borrowings = $borrowings ?? [];

$paginatedDevices = $paginatedDevices ?? $devices;
$devSearch = $devSearch ?? '';
$devRoom = $devRoom ?? 0;
$devStatus = $devStatus ?? '';
$devPage = $devPage ?? 1;
$devTotalPages = $devTotalPages ?? 1;
$devOffset = $devOffset ?? 0;

$paginatedBookings = $paginatedBookings ?? $bookings;
$bookingSearch = $bookingSearch ?? '';
$bookingStatusFilter = $bookingStatusFilter ?? '';
$bookingPage = $bookingPage ?? 1;
$bookingTotalPages = $bookingTotalPages ?? 1;
$bookingOffset = $bookingOffset ?? 0;

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
body{margin:0;font-family:"Segoe UI",Arial,sans-serif;background:#f4f7f9;color:#333}
.header{height:70px;background:#003399;border-bottom:1px solid #002F62;display:flex;align-items:center;justify-content:space-between;padding:0 30px}
.header-title{color:#fff;font-size:15px;font-weight:600}
.staff{display:flex;align-items:center;gap:10px}
.avatar{width:36px;height:36px;border-radius:50%;background:#fff;color:#003B7A;display:flex;align-items:center;justify-content:center;font-weight:700}
.staff-name{font-size:14px;font-weight:600;color:#fff}
.layout{display:flex;min-height:calc(100vh - 70px)}
.sidebar{width:245px;flex-shrink:0;background:#fff;border-right:1px solid #dee2e6;padding:25px 16px}
.logo{padding:0 12px 25px;border-bottom:1px solid #dee2e6;margin-bottom:22px}
.logo h2{margin:0;color:#003399;font-size:20px;line-height:1.35}
.menu-title{padding:0 12px;margin-bottom:8px;color:#999;font-size:11px;font-weight:700;text-transform:uppercase}
.menu a{display:flex;align-items:center;gap:12px;padding:12px;margin-bottom:5px;border-radius:7px;color:#555;text-decoration:none;font-size:14px}
.menu a:hover,.menu a.active{background:#eaf0fa;color:#003399}
.menu a.active{font-weight:600}
.icon{width:22px;text-align:center;font-size:16px}
.main{flex:1;min-width:0;width:calc(100% - 245px)}
.content{width:100%;padding:30px}
.page-title{margin-bottom:25px}
.page-title h1{margin:0;color:#003399;font-size:25px}
.page-title p{margin:6px 0 0;color:#777;font-size:14px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:25px}
.stat{background:#fff;border:1px solid #dee2e6;border-radius:8px;padding:18px 20px}
.stat-label{color:#777;font-size:13px;margin-bottom:8px}
.stat-number{color:#003399;font-size:25px;font-weight:700}
.card{width:100%;background:#fff;border:1px solid #dee2e6;border-radius:8px;overflow:hidden;margin-bottom:20px}
.card-header{padding:18px 20px;border-bottom:1px solid #dee2e6;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.card-header h3{margin:0;color:#003399;font-size:17px}
.card-header p{margin:4px 0 0;color:#888;font-size:13px}
.form-area{padding:20px;background:#fafbfc;border-bottom:1px solid #eee}
.form-grid{display:grid;grid-template-columns:1.2fr 2fr 1fr 1fr 1fr auto;gap:15px;align-items:end}
.form-group label{display:block;margin-bottom:6px;font-size:13px;font-weight:600}
input,select,textarea{width:100%;padding:8px 11px;border:1px solid #d7dce1;border-radius:6px;background:#fff;outline:none;font-family:inherit}
input,select{height:38px}
button{border:0;border-radius:6px;padding:8px 14px;color:#fff;cursor:pointer;font-family:inherit;font-size:13px}
.primary{background:#003399}
.secondary{background:#6c757d}
.danger{background:#dc3545}
.success-btn{background:#198754}
.message{padding:12px 15px;border-radius:6px;margin-bottom:20px;font-size:14px}
.message.success{background:#e8f5ee;color:#198754;border:1px solid #c8e6d3}
.message.error{background:#fdeaea;color:#dc3545;border:1px solid #f5c2c7}
.table-wrapper{width:100%;overflow-x:auto}
table{width:100%;border-collapse:collapse}
th{background:#003399;color:#fff;padding:12px 14px;text-align:left;font-size:13px;font-weight:600}
td{padding:12px 14px;border-bottom:1px solid #eee;font-size:13px;transition:opacity 0.2s;}
tr:hover td{background:#f8fafc}
.actions{display:flex;gap:6px}
.status{display:inline-block;padding:4px 8px;border-radius:12px;font-size:11px;font-weight:600}
.pending{background:#fff3cd;color:#856404}
.approved{background:#e8f5ee;color:#198754}
.rejected{background:#fdeaea;color:#dc3545}
.device-status{display:inline-flex;align-items:center;gap:7px;font-weight:600;font-size:13px}
.device-status-dot{width:10px;height:10px;border-radius:50%;display:inline-block}
.device-active .device-status-dot{background:#198754}
.device-maintenance .device-status-dot{background:#ffc107}
.device-broken .device-status-dot{background:#dc3545}
.request-tabs{display:flex;gap:8px;padding:0 0 15px;margin-bottom:20px;border-bottom:1px solid #dee2e6}
.request-tabs a{padding:10px 15px;border:1px solid #dee2e6;border-radius:6px;color:#555;text-decoration:none;font-size:13px;background:#f8f9fa}
.request-tabs a.active{color:#003399;background:#fff;font-weight:600}
.pagination-wrap{display:flex;justify-content:flex-end;gap:5px;padding:15px 20px}
.pagination-wrap a{padding:6px 12px;border:1px solid #dee2e6;border-radius:4px;text-decoration:none;color:#003399;font-size:13px}
.pagination-wrap a.active{background:#003399;color:#fff;font-weight:bold}
.staff-info{display:flex;align-items:center;gap:15px}
.logout-btn{padding:6px 12px;background:#dc3545;color:white;text-decoration:none;border-radius:6px;font-size:13px}
.edit-area{display:none;background:#f8f9fa;border-bottom:1px solid #eee}
.edit-area.active{display:table-row}
.edit-grid{display:grid;grid-template-columns:1.2fr 2fr 1fr 1fr 1fr auto;gap:12px;align-items:end;padding:15px}
</style>
</head>

<body>

<header class="header">
    <div class="header-title">HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ</div>
    <div class="staff">
        <div class="avatar">S</div>
        <div class="staff-info">
            <div class="staff-name"><?php echo htmlspecialchars($staffName); ?></div>
            <a href="index.php?route=logout" class="logout-btn">Đăng xuất</a>
        </div>
    </div>
</header>

<div class="layout">
<aside class="sidebar">
    <div class="logo"><h2>CÁN BỘ LAB</h2></div>
    <div class="menu-title">DANH MỤC</div>
    <nav class="menu">
        <a href="?page=devices" class="<?php echo $page === 'devices' ? 'active' : ''; ?>">
            <span class="icon">■</span><span>Quản lý thiết bị</span>
        </a>
        <a href="?page=staff_bookings" class="<?php echo in_array($page, ['staff_bookings','borrowings'], true) ? 'active' : ''; ?>">
            <span class="icon">▤</span><span>Duyệt yêu cầu</span>
        </a>
        <a href="?page=maintenance" class="<?php echo $page === 'maintenance' ? 'active' : ''; ?>">
            <span class="icon">⚙</span><span>Bảo trì</span>
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

<!-- TAB QUẢN LÝ THIẾT BỊ -->
<?php if ($page === 'devices'): ?>
<div class="card">
    <div class="card-header">
        <div>
            <h3>Thêm loại thiết bị mới</h3>
            <p>Tạo danh mục loại thiết bị trước khi thêm thiết bị</p>
        </div>
    </div>
    <div class="form-area">
        <form method="POST">
            <input type="hidden" name="action" value="add_type">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div style="display: flex; gap: 15px; align-items: end;">
                <div class="form-group" style="flex: 1; margin: 0;">
                    <label>Tên loại thiết bị</label>
                    <input type="text" name="type_name" placeholder="Ví dụ: Bàn phím, Chuột, Máy tính..." required>
                </div>
                <button type="submit" class="secondary" style="height: 38px;">+ Thêm loại</button>
            </div>
        </form>
    </div>
</div>

<div class="page-title">
    <h1>Quản lý thiết bị</h1>
    <p>Quản lý thông tin, vị trí và trạng thái thiết bị</p>
</div>

<div class="stats">
    <div class="stat"><div class="stat-label">Tổng thiết bị</div><div class="stat-number"><?php echo $totalDevices; ?></div></div>
    <div class="stat"><div class="stat-label">Đang hoạt động</div><div class="stat-number"><?php echo $activeDevices; ?></div></div>
    <div class="stat"><div class="stat-label">Đang bảo trì / Hỏng</div><div class="stat-number"><?php echo $maintenanceDevices + $brokenDevices; ?></div></div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Danh sách thiết bị</h3>
            <p>Quản lý thông tin và trạng thái thiết bị</p>
        </div>

        <!-- BỘ LỌC VÀ TÌM KIẾM (LIVE SEARCH FETCH API) -->
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="hidden" name="page" value="devices">
            <select name="dev_room" style="height: 34px; width: 140px;">
                <option value="0">-- Tất cả phòng --</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['id']; ?>" <?php echo $devRoom === (int)$room['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($room['room_code']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="dev_status" style="height: 34px; width: 150px;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="active" <?php echo $devStatus === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                <option value="broken" <?php echo $devStatus === 'broken' ? 'selected' : ''; ?>>Hỏng</option>
                <option value="maintenance" <?php echo $devStatus === 'maintenance' ? 'selected' : ''; ?>>Đang bảo trì</option>
            </select>

            <!-- Thêm ID liveSearchDevice để JS bắt sự kiện BÀI 8 -->
            <input type="text" id="liveSearchDevice" name="dev_search" placeholder="Mã TB hoặc tên..." value="<?php echo htmlspecialchars($devSearch); ?>" style="height: 34px; width: 180px;">
            <button type="submit" class="primary" style="padding: 7px 14px;">Lọc</button>

            <?php if (!empty($devSearch) || $devRoom > 0 || !empty($devStatus)): ?>
                <a href="index.php?page=devices" style="font-size: 13px; color: #666; text-decoration: none;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="form-area">
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Mã thiết bị</label>
                    <input type="text" name="device_code" placeholder="TB004" required>
                </div>
                <div class="form-group">
                    <label>Tên thiết bị</label>
                    <input type="text" name="device_name" placeholder="Tên thiết bị..." required>
                </div>
                <div class="form-group">
                    <label>Loại thiết bị</label>
                    <select name="type_id" required>
                        <option value="">Chọn loại</option>
                        <?php foreach ($deviceTypes as $type): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phòng</label>
                    <select name="room_id" required>
                        <option value="">Chọn phòng</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['room_code']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" required>
                        <option value="active">Hoạt động</option>
                        <option value="maintenance">Đang bảo trì</option>
                        <option value="broken">Hỏng</option>
                    </select>
                </div>
                <button type="submit" class="primary">+ Thêm</button>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th width="60">STT</th>
                    <th>Mã TB</th>
                    <th>Tên thiết bị</th>
                    <th>Loại</th>
                    <th>Phòng</th>
                    <th width="140">Trạng thái</th>
                    <th width="140">Thao tác</th>
                </tr>
            </thead>
            <!-- Thêm ID cho tbody để JS đổ data BÀI 8 vào đây -->
            <tbody id="deviceTableBody">
                <?php if (empty($paginatedDevices)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#888;padding: 25px;">Không tìm thấy thiết bị nào.</td></tr>
                <?php endif; ?>
                <?php foreach ($paginatedDevices as $index => $device): ?>
                    <tr>
                        <td><?php echo $devOffset + $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($device['device_code']); ?></strong></td>
                        <td><?php echo htmlspecialchars($device['device_name']); ?></td>
                        <td><?php echo htmlspecialchars($device['type_name']); ?></td>
                        <td><?php echo htmlspecialchars($device['room_code']); ?></td>
                        <td>
                            <span class="device-status device-<?php echo htmlspecialchars($device['status']); ?>">
                                <span class="device-status-dot"></span>
                                <?php echo $statusText[$device['status']] ?? ''; ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <button type="button" class="secondary" onclick="moSua(<?php echo $device['id']; ?>)">Sửa</button>
                                <form method="POST" style="display:inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $device['id']; ?>">
                                    <!-- CSRF Token -->
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <button type="submit" class="danger" onclick="return confirm('Bạn có chắc muốn xóa thiết bị này không?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr id="edit-<?php echo $device['id']; ?>" class="edit-area">
                        <td colspan="7">
                            <form method="POST">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?php echo $device['id']; ?>">
                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                
                                <div class="edit-grid">
                                    <div class="form-group">
                                        <label>Mã thiết bị</label>
                                        <input type="text" name="device_code" value="<?php echo htmlspecialchars($device['device_code']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tên thiết bị</label>
                                        <input type="text" name="device_name" value="<?php echo htmlspecialchars($device['device_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Loại thiết bị</label>
                                        <select name="type_id" required>
                                            <?php foreach ($deviceTypes as $type): ?>
                                                <option value="<?php echo $type['id']; ?>" <?php echo (int)$type['id'] === (int)$device['type_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($type['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Phòng</label>
                                        <select name="room_id" required>
                                            <?php foreach ($rooms as $room): ?>
                                                <option value="<?php echo $room['id']; ?>" <?php echo (int)$room['id'] === (int)$device['room_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($room['room_code']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Trạng thái</label>
                                        <select name="status" required>
                                            <option value="active" <?php echo $device['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                            <option value="maintenance" <?php echo $device['status'] === 'maintenance' ? 'selected' : ''; ?>>Đang bảo trì</option>
                                            <option value="broken" <?php echo $device['status'] === 'broken' ? 'selected' : ''; ?>>Hỏng</option>
                                        </select>
                                    </div>
                                    <div class="actions">
                                        <button type="submit" class="primary">Lưu</button>
                                        <button type="button" class="secondary" onclick="dongSua(<?php echo $device['id']; ?>)">Hủy</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($devTotalPages > 1): ?>
        <div class="pagination-wrap" id="devicePagination">
            <?php for ($i = 1; $i <= $devTotalPages; $i++): ?>
                <a href="index.php?page=devices&dev_search=<?php echo urlencode($devSearch); ?>&dev_room=<?php echo $devRoom; ?>&dev_status=<?php echo urlencode($devStatus); ?>&p=<?php echo $i; ?>" class="<?php echo $i === $devPage ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- TAB DUYỆT YÊU CẦU ĐẶT PHÒNG -->
<?php elseif ($page === 'staff_bookings' || $page === 'borrowings'): ?>
<div class="page-title">
    <h1>DUYỆT YÊU CẦU</h1>
</div>

<div class="request-tabs">
    <a href="?page=staff_bookings" class="<?php echo $page === 'staff_bookings' ? 'active' : ''; ?>">Duyệt yêu cầu đặt phòng</a>
    <a href="?page=borrowings" class="<?php echo $page === 'borrowings' ? 'active' : ''; ?>">Duyệt yêu cầu mượn thiết bị</a>
</div>

<?php if ($page === 'staff_bookings'): ?>
<div class="stats">
    <div class="stat"><div class="stat-label">Tổng yêu cầu</div><div class="stat-number"><?php echo count($bookings); ?></div></div>
    <div class="stat"><div class="stat-label">Chờ duyệt</div><div class="stat-number"><?php echo $pendingBookings; ?></div></div>
    <div class="stat"><div class="stat-label">Đã xử lý</div><div class="stat-number"><?php echo $processedBookings; ?></div></div>
</div>

<div class="card">
    <div class="card-header">
        <div><h3>Danh sách yêu cầu đặt phòng</h3></div>
        
        <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="page" value="staff_bookings">
            <select name="status_filter" style="height: 34px; width: 140px;">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" <?php echo $bookingStatusFilter === 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                <option value="approved" <?php echo $bookingStatusFilter === 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                <option value="rejected" <?php echo $bookingStatusFilter === 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
            </select>
            <input type="text" name="search" placeholder="Mã SV, họ tên, phòng..." value="<?php echo htmlspecialchars($bookingSearch); ?>" style="height: 34px; width: 220px;">
            <button type="submit" class="primary" style="padding: 7px 14px;">Lọc</button>
            <?php if (!empty($bookingSearch) || !empty($bookingStatusFilter)): ?>
                <a href="index.php?page=staff_bookings" style="font-size: 13px; color: #666; text-decoration: none;">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <form method="POST" action="index.php?page=staff_bookings">
        <input type="hidden" name="action" value="bulk_booking">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="padding: 12px 20px; background: #fafbfc; border-bottom: 1px solid #eee; display: flex; gap: 10px; align-items: center;">
            <span style="font-size: 13px; font-weight: 600; color: #555;">Thao tác hàng loạt:</span>
            <button type="submit" name="bulk_action" value="approve" class="success-btn" onclick="return confirm('Đồng ý tất cả các yêu cầu đã chọn?')">✓ Phê duyệt đã chọn</button>
            <button type="submit" name="bulk_action" value="reject" class="danger" onclick="return confirm('Từ chối tất cả các yêu cầu đã chọn?')">✕ Từ chối đã chọn</button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="selectAllBookings"></th>
                        <th width="50">STT</th>
                        <th>Người đặt</th>
                        <th>Phòng</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Mục đích</th>
                        <th>Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($paginatedBookings)): ?>
                        <tr><td colspan="9" style="text-align:center;color:#888;padding: 30px;">Không tìm thấy yêu cầu đặt phòng nào.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($paginatedBookings as $index => $booking): ?>
                        <tr>
                            <td>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <input type="checkbox" name="booking_ids[]" value="<?php echo $booking['id']; ?>" class="booking-checkbox">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $bookingOffset + $index + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong>
                                <br><small style="color: #777"><?php echo htmlspecialchars($booking['username'] ?? ''); ?></small>
                            </td>
                            <td><strong><?php echo htmlspecialchars($booking['room_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($booking['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['end_time']); ?></td>
                            <td><?php echo htmlspecialchars($booking['purpose']); ?></td>
                            <td>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <span class="status pending">Chờ duyệt</span>
                                <?php elseif ($booking['status'] === 'approved'): ?>
                                    <span class="status approved">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="status rejected">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <div class="actions">
                                        <button type="submit" form="single-approve-<?php echo $booking['id']; ?>" class="success-btn">Đồng ý</button>
                                        <button type="submit" form="single-reject-<?php echo $booking['id']; ?>" class="danger">Từ chối</button>
                                    </div>
                                <?php else: ?>
                                    <span style="color:#888; font-size: 12px;">Đã hoàn thành</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <?php foreach ($paginatedBookings as $booking): ?>
        <?php if ($booking['status'] === 'pending'): ?>
            <form id="single-approve-<?php echo $booking['id']; ?>" method="POST" action="index.php?page=staff_bookings" style="display:none;">
                <input type="hidden" name="action" value="booking">
                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                <input type="hidden" name="status" value="approved">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            </form>
            <form id="single-reject-<?php echo $booking['id']; ?>" method="POST" action="index.php?page=staff_bookings" style="display:none;">
                <input type="hidden" name="action" value="booking">
                <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                <input type="hidden" name="status" value="rejected">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            </form>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($bookingTotalPages > 1): ?>
        <div class="pagination-wrap">
            <?php for ($i = 1; $i <= $bookingTotalPages; $i++): ?>
                <a href="index.php?page=staff_bookings&search=<?php echo urlencode($bookingSearch); ?>&status_filter=<?php echo urlencode($bookingStatusFilter); ?>&p=<?php echo $i; ?>" class="<?php echo $i === $bookingPage ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="card"><div class="card-header"><h3>Mượn thiết bị</h3></div><div style="padding: 20px; color: #666;">Chức năng đang hoàn thiện dữ liệu.</div></div>
<?php endif; ?>

<!-- TAB BẢO TRÌ -->
<?php elseif ($page === 'maintenance'): ?>
<div class="page-title">
    <h1>Bảo trì thiết bị</h1>
</div>

<div class="card">
    <div class="card-header"><div><h3>Danh sách thiết bị bảo trì</h3></div></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã TB</th>
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
                    <tr><td colspan="8" style="text-align:center;color:#888">Chưa có dữ liệu thiết bị.</td></tr>
                <?php endif; ?>
                <?php foreach ($maintenanceDevicesList as $index => $tb): 
                    $deviceStatus = $tb['device_status'] ?? '';
                ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($tb['device_code'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($tb['device_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($tb['room_id'] ?? ''); ?></td>
                        <td>
                            <span class="device-status device-<?php echo htmlspecialchars($deviceStatus); ?>">
                                <span class="device-status-dot"></span>
                                <?php echo $statusText[$deviceStatus] ?? $deviceStatus; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($tb['description'] ?? ($tb['content'] ?? 'Chưa có')); ?></td>
                        <td><?php echo htmlspecialchars($tb['result'] ?? 'Chưa có kết quả'); ?></td>
                        <td>
                            <?php if ($deviceStatus === 'broken'): ?>
                                <form method="POST">
                                    <input type="hidden" name="batdau_baotri" value="1">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="device_id" value="<?php echo (int)$tb['device_id']; ?>">
                                    <input type="hidden" name="description" value="Bắt đầu bảo trì thiết bị">
                                    <button type="submit" class="primary">Bắt đầu</button>
                                </form>
                            <?php elseif ($deviceStatus === 'maintenance' && !empty($tb['maintenance_id'])): ?>
                                <form method="POST">
                                    <input type="hidden" name="capnhat" value="1">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="maintenance_id" value="<?php echo (int)$tb['maintenance_id']; ?>">
                                    <input type="hidden" name="device_id" value="<?php echo (int)$tb['device_id']; ?>">
                                    <input type="hidden" name="status" value="Hoàn thành">
                                    <input type="text" name="result" placeholder="Nhập kết quả" required style="width:140px;height:32px;display:inline-block">
                                    <button type="submit" class="success-btn">Hoàn thành</button>
                                </form>
                            <?php else: ?>
                                <span style="color:#198754">Không cần bảo trì</span>
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
// Logic Chọn tất cả (CheckBox)
document.getElementById('selectAllBookings')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.booking-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// BÀI 8: TÍNH NĂNG FETCH API - LIVE SEARCH TÌM KIẾM THIẾT BỊ BẰNG JAVASCRIPT
const searchInput = document.getElementById('liveSearchDevice');
const tbody = document.getElementById('deviceTableBody');

if (searchInput && tbody) {
    let timeout = null;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(timeout);
        // Kỹ thuật Debounce (Bài 8): Đợi 300ms sau khi ngừng gõ mới gửi request để tránh nghẽn Server
        timeout = setTimeout(async () => {
            const keyword = e.target.value;
            try {
                tbody.style.opacity = '0.5'; // Hiệu ứng đang load
                
                // Gọi Endpoint JSON bằng Fetch API (Bài 8)
                const res = await fetch('index.php?route=api_devices&q=' + encodeURIComponent(keyword), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('HTTP error');
                
                const json = await res.json();
                tbody.innerHTML = ''; 

                // Tắt phân trang tĩnh của PHP đi khi đang dùng Live Search
                const pagination = document.getElementById('devicePagination');
                if (pagination) pagination.style.display = keyword ? 'none' : 'flex';

                if (json.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#888;padding: 25px;">Không tìm thấy thiết bị nào qua Live Search.</td></tr>';
                } else {
                    json.data.forEach((dev, index) => {
                        const tr = document.createElement('tr');
                        
                        // Cập nhật DOM an toàn chống XSS bằng textContent thay vì innerHTML (Bài 8)
                        const tdStt = document.createElement('td'); tdStt.textContent = index + 1;
                        
                        const tdCode = document.createElement('td'); 
                        const strong = document.createElement('strong'); strong.textContent = dev.device_code;
                        tdCode.appendChild(strong);
                        
                        const tdName = document.createElement('td'); tdName.textContent = dev.device_name;
                        const tdType = document.createElement('td'); tdType.textContent = dev.type_name;
                        const tdRoom = document.createElement('td'); tdRoom.textContent = dev.room_code;
                        
                        const tdStatus = document.createElement('td');
                        const span = document.createElement('span');
                        span.className = 'device-status device-' + dev.status;
                        let statusText = dev.status === 'active' ? 'Hoạt động' : (dev.status === 'broken' ? 'Hỏng' : 'Đang bảo trì');
                        span.innerHTML = '<span class="device-status-dot"></span> ' + statusText;
                        tdStatus.appendChild(span);
                        
                        const tdAction = document.createElement('td');
                        tdAction.innerHTML = '<span style="color:#198754; font-size:12px; font-weight:bold;">(Live Data)</span>';
                        
                        tr.append(tdStt, tdCode, tdName, tdType, tdRoom, tdStatus, tdAction);
                        tbody.appendChild(tr);
                    });
                }
                tbody.style.opacity = '1';
            } catch (err) {
                console.error("Lỗi Fetch API:", err);
                tbody.style.opacity = '1';
            }
        }, 300);
    });
}

function moSua(id){
    const row = document.getElementById("edit-" + id);
    if(row){ row.classList.add("active"); }
}

function dongSua(id){
    const row = document.getElementById("edit-" + id);
    if(row){ row.classList.remove("active"); }
}
</script>
</body>
</html>