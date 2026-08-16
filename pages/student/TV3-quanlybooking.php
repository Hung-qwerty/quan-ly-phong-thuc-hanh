<?php
// Khởi tạo session giả lập user_id cho sinh viên (Student)
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 101; // Giả lập sinh viên có ID 101 đang đăng nhập
}

// 1. Mảng dữ liệu giả lập kết quả câu truy vấn SQL:
// SELECT b.*, r.room_name FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.user_id = ?;
$ds_booking = [
    [
        "id" => 1,
        "room_name" => "P101",
        "booking_date" => "2026-08-20",
        "time_slot" => "08:00 - 10:00",
        "status" => "Đã duyệt",
        "created_at" => "2026-08-15 09:00:00"
    ],
    [
        "id" => 2,
        "room_name" => "P203",
        "booking_date" => "2026-08-22",
        "time_slot" => "13:00 - 15:00",
        "status" => "Chờ duyệt",
        "created_at" => "2026-08-16 10:30:00"
    ],
    [
        "id" => 3,
        "room_name" => "P105",
        "booking_date" => "2026-08-25",
        "time_slot" => "15:00 - 17:00",
        "status" => "Đã hủy",
        "created_at" => "2026-08-14 14:20:00"
    ]
];

// 2. Xử lý logic Hủy booking (UPDATE bookings SET status = 'Đã hủy' WHERE id = ? AND user_id = ?)
$message = "";
if (isset($_POST['btn_huy_booking'])) {
    $booking_id_huy = (int)$_POST['booking_id'];
    foreach ($ds_booking as &$item) {
        if ($item['id'] === $booking_id_huy && $item['status'] === 'Chờ duyệt') {
            $item['status'] = 'Đã hủy';
            $message = "Đã hủy thành công đơn đặt phòng #" . $booking_id_huy;
            break;
        }
    }
}

// 3. Hàm hiển thị trạng thái theo chuẩn giao diện
function hienThiTrangThai($trang_thai) {
    if ($trang_thai === "Đã duyệt") {
        return "<span class='badge bg-success'>● Đã duyệt</span>";
    } elseif ($trang_thai === "Chờ duyệt") {
        return "<span class='badge bg-warning text-dark'>● Chờ duyệt</span>";
    } else {
        return "<span class='badge bg-secondary'>● Đã hủy</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV3 - Student: Quản lý Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f9; color: #333; font-family: 'Segoe UI', Arial, sans-serif; }
        :root { --hnmu-blue: #003399; }
        .card-custom { background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h3, h4 { color: var(--hnmu-blue); }
        .table-custom thead { background-color: var(--hnmu-blue); color: white; }
        .btn-primary-custom { background-color: var(--hnmu-blue); border: none; color: white; }
        .btn-primary-custom:hover { background-color: #002266; color: white; }
    </style>
</head>
<body class="py-4">

<div class="container">
    <div class="card card-custom p-4 mb-4">
        <h3 class="fw-bold">TV3 – Student: Quản lý Booking</h3>
        <p class="text-muted mb-0">Mã Sinh viên (Session User ID): <strong><?= $_SESSION['user_id']; ?></strong></p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 9.1 LỊCH SỬ ĐẶT PHÒNG -->
    <div class="card card-custom p-4">
        <h4 class="fw-bold mb-3">Lịch Sử Đặt Phòng</h4>
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
                    <?php foreach ($ds_booking as $item): ?>
                        <tr>
                            <td class="fw-bold"><?= $item['room_name']; ?></td>
                            <td><?= date('d/m/Y', strtotime($item['booking_date'])); ?></td>
                            <td><?= $item['time_slot']; ?></td>
                            <td><?= hienThiTrangThai($item['status']); ?></td>
                            <td>
                                <!-- Nút Xem Chi Tiết Modal -->
                                <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $item['id']; ?>">
                                    Chi tiết
                                </button>

                                <!-- Nút Hủy Booking (Chỉ cho phép khi ở trạng thái 'Chờ duyệt') -->
                                <?php if ($item['status'] === 'Chờ duyệt'): ?>
                                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy booking này?');">
                                        <input type="hidden" name="booking_id" value="<?= $item['id']; ?>">
                                        <button type="submit" name="btn_huy_booking" class="btn btn-sm btn-danger">Hủy</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Hủy</button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <!-- Modal 9.1 Chi tiết booking -->
                        <div class="modal fade" id="modalDetail<?= $item['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Chi Tiết Booking #<?= $item['id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p><strong>Mã User (Sinh viên):</strong> <?= $_SESSION['user_id']; ?></p>
                                        <p><strong>Phòng thực hành:</strong> <?= $item['room_name']; ?></p>
                                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($item['booking_date'])); ?></p>
                                        <p><strong>Khung giờ:</strong> <?= $item['time_slot']; ?></p>
                                        <p><strong>Trạng thái:</strong> <?= hienThiTrangThai($item['status']); ?></p>
                                        <p><strong>Thời gian tạo đơn:</strong> <?= $item['created_at']; ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>