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
    <title>Quản lý Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f9;
            color: #333;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        .card-custom {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        :root {
            --hnmu-blue: #003399;
        }
        
        .btn-primary-custom {
            background-color: var(--hnmu-blue);
            border: none;
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: #002266;
            color: white;
        }
        
        h3, h4 { color: var(--hnmu-blue); }
        
        .table-custom thead {
            background-color: var(--hnmu-blue);
            color: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--hnmu-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 153, 0.15);
        }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="card card-custom p-4 mb-4">
        <h3 class="fw-bold">Quản lý Booking</h3>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom p-4 mb-4">
        <h4 class="fw-bold mb-3">Kiểm Tra Phòng Trống Theo Ngày/Giờ</h4>
        <form action="" method="GET" class="row g-3">
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
                <button type="submit" class="btn btn-primary-custom w-100">Kiểm tra</button>
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

    <div class="card card-custom p-4 mb-4">
        <h4 class="fw-bold mb-3">Xem Danh Sách & Lịch Phòng Tổng Quan</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle table-custom mb-0 text-center">
                <thead>
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

    <div class="card card-custom p-4">
        <h4 class="fw-bold mb-3">Các Yêu Cầu Đặt Phòng Của Tôi</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle table-custom mb-0 text-center">
                <thead>
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