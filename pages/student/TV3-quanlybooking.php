<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 101;
}

$ds_phong = [
    ["id" => "P101", "ten" => "Phòng Thực Hành 101", "suc_chua" => 40],
    ["id" => "P105", "ten" => "Phòng Thực Hành 105", "suc_chua" => 30],
    ["id" => "P203", "ten" => "Phòng Máy Tính 203", "suc_chua" => 50],
];

if (!isset($_SESSION['ds_booking'])) {
    $_SESSION['ds_booking'] = [
        [
            "id" => 1,
            "user_id" => 101,
            "room_name" => "P101",
            "booking_date" => "2026-08-20",
            "time_slot" => "08:00 - 10:00",
            "status" => "Đã duyệt",
            "created_at" => "2026-08-15 09:00:00"
        ],
        [
            "id" => 2,
            "user_id" => 101,
            "room_name" => "P203",
            "booking_date" => "2026-08-22",
            "time_slot" => "13:00 - 15:00",
            "status" => "Chờ duyệt",
            "created_at" => "2026-08-16 10:30:00"
        ],
        [
            "id" => 3,
            "user_id" => 102,
            "room_name" => "P105",
            "booking_date" => "2026-08-20",
            "time_slot" => "08:00 - 10:00",
            "status" => "Đã duyệt",
            "created_at" => "2026-08-14 14:20:00"
        ]
    ];
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_huy_booking'])) {
    $booking_id_huy = (int)$_POST['booking_id'];
    $today = date('Y-m-d');

    foreach ($_SESSION['ds_booking'] as &$item) {
        if ($item['id'] === $booking_id_huy && $item['user_id'] === $_SESSION['user_id']) {
            if ($item['status'] === 'Chờ duyệt' || $item['booking_date'] >= $today) {
                $item['status'] = 'Đã hủy';
                $message = "Đã hủy thành công đơn đặt phòng #" . $booking_id_huy;
            } else {
                $message = "Không thể hủy do yêu cầu này đã/đang thực hiện!";
            }
            break;
        }
    }
    unset($item);
}

$page = $_GET['page'] ?? 'home';
$search_date = $_GET['search_date'] ?? '';
$search_time = $_GET['search_time'] ?? '';

$phong_trong = [];
if (!empty($search_date) && !empty($search_time)) {
    foreach ($ds_phong as $phong) {
        $is_busy = false;
        foreach ($_SESSION['ds_booking'] as $b) {
            if ($b['room_name'] === $phong['id'] && 
                $b['booking_date'] === $search_date && 
                $b['time_slot'] === $search_time && 
                $b['status'] !== 'Đã hủy') {
                $is_busy = true;
                break;
            }
        }
        if (!$is_busy) {
            $phong_trong[] = $phong;
        }
    }
}

function hienThiTrangThai($trang_thai) {
    switch ($trang_thai) {
        case "Đã duyệt":
            return "<span class='badge bg-success'>● Đã duyệt</span>";
        case "Chờ duyệt":
            return "<span class='badge bg-warning text-dark'>● Chờ duyệt</span>";
        default:
            return "<span class='badge bg-secondary'>● Đã hủy</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý Phòng Thực Hành</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-blue: #003399;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 1.5rem 1rem;
            z-index: 100;
        }

        .sidebar-brand {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
            text-decoration: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar-menu a:hover {
            background-color: #f3f4f6;
            color: var(--primary-blue);
        }

        .sidebar-menu a.active {
            background-color: #e8f0fe;
            color: var(--primary-blue);
            font-weight: 600;
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            height: 60px;
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        .content-area {
            padding: 2.5rem;
            flex-grow: 1;
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .feature-card h5 {
            color: #0d2838;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .feature-card a {
            font-weight: 600;
            text-decoration: none;
            color: var(--primary-blue);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="?page=home" class="sidebar-brand">
        🏫 LAB MANAGEMENT
    </a>
    <ul class="sidebar-menu">
        <li>
            <a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>">
                🏠 Trang chủ
            </a>
        </li>
        <li>
            <a href="?page=rooms" class="<?= $page === 'rooms' ? 'active' : '' ?>">
                🏫 Phòng thực hành
            </a>
        </li>
        <li>
            <a href="?page=booking" class="<?= $page === 'booking' ? 'active' : '' ?>">
                📅 Đặt phòng
            </a>
        </li>
        <li>
            <a href="?page=my_bookings" class="<?= $page === 'my_bookings' ? 'active' : '' ?>">
                📋 Yêu cầu của tôi
            </a>
        </li>
        <li>
            <a href="?page=schedule" class="<?= $page === 'schedule' ? 'active' : '' ?>">
                📊 Lịch tổng quan
            </a>
        </li>
        <li>
            <a href="?page=report" class="<?= $page === 'report' ? 'active' : '' ?>">
                🔧 Báo hỏng
            </a>
        </li>
    </ul>
</div>

<div class="main-wrapper">
    <header class="top-navbar">
        <div class="text-muted small fw-semibold">Student Portal</div>
        <div class="fw-bold text-dark">
            👤 Sinh viên (ID: <?= $_SESSION['user_id']; ?>)
        </div>
    </header>

    <main class="content-area">

        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <?= htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($page === 'home'): ?>
            <div class="mb-4">
                <h2 class="fw-bold mb-1" style="color: #0d2838;">Xin chào, Sinh viên! 👋</h2>
                <p class="text-secondary">Chào mừng bạn đến với hệ thống quản lý phòng thực hành.</p>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div>
                            <h5>🏫 Phòng thực hành</h5>
                            <p>Xem thông tin phòng thực hành.</p>
                        </div>
                        <a href="?page=rooms">Xem ngay →</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div>
                            <h5>📅 Đặt phòng</h5>
                            <p>Tạo yêu cầu đặt phòng mới.</p>
                        </div>
                        <a href="?page=booking">Đặt phòng →</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div>
                            <h5>📋 Yêu cầu của tôi</h5>
                            <p>Quản lý các yêu cầu đặt phòng cá nhân.</p>
                        </div>
                        <a href="?page=my_bookings">Quản lý →</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div>
                            <h5>📊 Lịch tổng quan</h5>
                            <p>Tra cứu phòng trống và xem lịch toàn hệ thống.</p>
                        </div>
                        <a href="?page=schedule">Xem lịch →</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div>
                            <h5>🔧 Báo hỏng</h5>
                            <p>Gửi báo cáo thiết bị gặp sự cố.</p>
                        </div>
                        <a href="?page=report">Báo hỏng →</a>
                    </div>
                </div>
            </div>

        <?php elseif ($page === 'rooms'): ?>
            <h3 class="fw-bold mb-4" style="color: #0d2838;">Danh Sách Phòng Thực Hành</h3>
            <div class="card card-custom p-5 text-center text-muted">
                <p class="mb-0">Nội dung trang Phòng thực hành đang được cập nhật...</p>
            </div>

        <?php elseif ($page === 'booking'): ?>
            <h3 class="fw-bold mb-4" style="color: #0d2838;">Đặt Phòng Thực Hành</h3>
            <div class="card card-custom p-5 text-center text-muted">
                <p class="mb-0">Nội dung trang Đặt phòng đang được cập nhật...</p>
            </div>

        <?php elseif ($page === 'my_bookings'): ?>
            <h3 class="fw-bold mb-4" style="color: #0d2838;">Các Yêu Cầu Đặt Phòng Của Tôi</h3>

            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $my_id = $_SESSION['user_id'];
                            $today = date('Y-m-d');
                            $has_item = false;

                            foreach ($_SESSION['ds_booking'] as $item): 
                                if ($item['user_id'] === $my_id):
                                    $has_item = true;
                                    $can_cancel = ($item['status'] === 'Chờ duyệt' || ($item['status'] === 'Đã duyệt' && $item['booking_date'] >= $today));
                            ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($item['room_name']); ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['booking_date'])); ?></td>
                                    <td><?= htmlspecialchars($item['time_slot']); ?></td>
                                    <td><?= hienThiTrangThai($item['status']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $item['id']; ?>">
                                            Chi tiết
                                        </button>

                                        <?php if ($can_cancel): ?>
                                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy yêu cầu này?');">
                                                <input type="hidden" name="booking_id" value="<?= $item['id']; ?>">
                                                <button type="submit" name="btn_huy_booking" class="btn btn-sm btn-danger">Hủy</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Hủy</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            endforeach; 

                            if (!$has_item):
                            ?>
                                <tr>
                                    <td colspan="5" class="text-muted">Bạn chưa gửi yêu cầu đặt phòng nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'schedule'): ?>
            <h3 class="fw-bold mb-4" style="color: #0d2838;">Lịch Phòng & Tra Cứu</h3>

            <div class="card card-custom p-4 mb-4">
                <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">Kiểm Tra Phòng Trống Theo Ngày/Giờ</h4>
                <form action="" method="GET" class="row g-3">
                    <input type="hidden" name="page" value="schedule">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Chọn Ngày</label>
                        <input type="date" name="search_date" class="form-control" value="<?= htmlspecialchars($search_date); ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Chọn Khung Giờ</label>
                        <select name="search_time" class="form-select" required>
                            <option value="">-- Chọn giờ --</option>
                            <option value="08:00 - 10:00" <?= $search_time === '08:00 - 10:00' ? 'selected' : ''; ?>>08:00 - 10:00</option>
                            <option value="10:00 - 12:00" <?= $search_time === '10:00 - 12:00' ? 'selected' : ''; ?>>10:00 - 12:00</option>
                            <option value="13:00 - 15:00" <?= $search_time === '13:00 - 15:00' ? 'selected' : ''; ?>>13:00 - 15:00</option>
                            <option value="15:00 - 17:00" <?= $search_time === '15:00 - 17:00' ? 'selected' : ''; ?>>15:00 - 17:00</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" style="background-color: var(--primary-blue);">Kiểm tra</button>
                    </div>
                </form>

                <?php if (!empty($search_date) && !empty($search_time)): ?>
                    <div class="mt-4">
                        <h6 class="fw-bold">Kết quả phòng trống ngày <?= date('d/m/Y', strtotime($search_date)); ?> (<?= htmlspecialchars($search_time); ?>):</h6>
                        <?php if (count($phong_trong) > 0): ?>
                            <ul class="list-group mt-2">
                                <?php foreach ($phong_trong as $p): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><strong><?= htmlspecialchars($p['id']); ?></strong> - <?= htmlspecialchars($p['ten']); ?> (Sức chứa: <?= $p['suc_chua']; ?> người)</span>
                                        <span class="badge bg-success">Còn trống</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-danger mt-2 mb-0">Khung giờ này hiện đã kín phòng.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">Lịch Phòng Tổng Quan</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Phòng</th>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['ds_booking'] as $item): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($item['room_name']); ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['booking_date'])); ?></td>
                                    <td><?= htmlspecialchars($item['time_slot']); ?></td>
                                    <td><?= hienThiTrangThai($item['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'report'): ?>
            <h3 class="fw-bold mb-4" style="color: #0d2838;">Báo Hỏng Thiết Bị</h3>
            <div class="card card-custom p-5 text-center text-muted">
                <p class="mb-0">Nội dung trang Báo hỏng đang được cập nhật...</p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php foreach ($_SESSION['ds_booking'] as $item): ?>
    <?php if ($item['user_id'] === $_SESSION['user_id']): ?>
        <div class="modal fade" id="modalDetail<?= $item['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Chi Tiết Booking #<?= $item['id']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p><strong>Mã User (Sinh viên):</strong> <?= htmlspecialchars($_SESSION['user_id']); ?></p>
                        <p><strong>Phòng thực hành:</strong> <?= htmlspecialchars($item['room_name']); ?></p>
                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($item['booking_date'])); ?></p>
                        <p><strong>Khung giờ:</strong> <?= htmlspecialchars($item['time_slot']); ?></p>
                        <p><strong>Trạng thái:</strong> <?= hienThiTrangThai($item['status']); ?></p>
                        <p><strong>Thời gian tạo đơn:</strong> <?= htmlspecialchars($item['created_at']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>