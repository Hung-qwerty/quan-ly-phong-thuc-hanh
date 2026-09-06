<?php

namespace App\Controller;

use App\Repository\RoomBookingRepository;

class RoomBookingController
{
    private RoomBookingRepository $roomRepo;

    public function __construct(RoomBookingRepository $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }

    public function index(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($userId <= 0) {
            header('Location: index.php?route=login');
            exit;
        }

        $page = $_GET['page'] ?? 'home';

        $allowedPages = [
            'home',
            'rooms',
            'booking',
            'mybookings',
            'report',
            'myreports'
        ];

        if (!in_array($page, $allowedPages, true)) {
            $page = 'home';
        }

        $errors = [];
        $success = '';

        $user = $this->roomRepo->findUser($userId);

        if (!$user) {
            session_unset();
            session_destroy();
            header('Location: index.php?route=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'booking' || $action === 'create_booking') {
                $page = 'booking';
                [$errors, $success] = $this->handleBooking($userId);
            } elseif ($action === 'cancel_booking') {
                $page = 'mybookings';
                [$errors, $success] = $this->handleCancelBooking($userId);
            } elseif ($action === 'report') {
                $page = 'report';
                [$errors, $success] = $this->handleReport($userId);
            }
        }

        $rooms = $this->roomRepo->findAllRooms();
        $devices = $this->roomRepo->findReportableDevices($userId);

        // Phân trang danh sách đặt phòng của tôi
        $limit = 10;
        $bookingPage = max(1, (int)($_GET['p_b'] ?? 1));
        $bookingOffset = ($bookingPage - 1) * $limit;
        $totalBookings = $this->roomRepo->countBookingsByUser($userId);
        $bookingTotalPages = ceil($totalBookings / $limit);
        $myBookings = $this->roomRepo->findBookingsByUserPaginated($userId, $limit, $bookingOffset);

        // Phân trang danh sách báo hỏng của tôi
        $reportPage = max(1, (int)($_GET['p_r'] ?? 1));
        $reportOffset = ($reportPage - 1) * $limit;
        $totalReports = $this->roomRepo->countReportsByUser($userId);
        $reportTotalPages = ceil($totalReports / $limit);
        $myReports = $this->roomRepo->findReportsByUserPaginated($userId, $limit, $reportOffset);

        require_once __DIR__ . '/../View/student/roombooking.php';
    }

    private function handleBooking(int $userId): array
    {
        $errors = [];
        $success = '';

        $roomId = trim((string) ($_POST['room_id'] ?? ''));
        $date = trim((string) ($_POST['date'] ?? ''));
        $start = trim((string) ($_POST['start'] ?? ''));
        $end = trim((string) ($_POST['end'] ?? ''));
        $purpose = trim((string) ($_POST['purpose'] ?? ''));

        if ($roomId === '') {
            $errors['room_id'] = 'Vui lòng chọn phòng.';
        }
        if ($date === '') {
            $errors['date'] = 'Vui lòng chọn ngày.';
        }
        if ($start === '') {
            $errors['start'] = 'Vui lòng chọn giờ bắt đầu.';
        }
        if ($end === '') {
            $errors['end'] = 'Vui lòng chọn giờ kết thúc.';
        }
        if ($start !== '' && $end !== '' && $end <= $start) {
            $errors['end'] = 'Giờ kết thúc phải lớn hơn giờ bắt đầu.';
        }
        if ($purpose === '') {
            $errors['purpose'] = 'Vui lòng nhập mục đích.';
        }

        if (!empty($errors)) {
            return [$errors, $success];
        }

        $room = $this->roomRepo->findBookableRoom((int) $roomId);

        if (!$room) {
            $errors['room_id'] = 'Phòng không tồn tại hoặc đang bảo trì.';
            return [$errors, $success];
        }

        $startTime = $date . ' ' . $start . ':00';
        $endTime = $date . ' ' . $end . ':00';

        if ($this->roomRepo->hasBookingConflict((int) $roomId, $startTime, $endTime)) {
            $errors['room_id'] = 'Phòng đã có lịch được duyệt trong thời gian này.';
            return [$errors, $success];
        }

        $this->roomRepo->createBooking($userId, (int) $roomId, $startTime, $endTime, $purpose);
        $success = 'Gửi yêu cầu đặt phòng thành công, vui lòng chờ Cán bộ Lab phê duyệt.';

        return [$errors, $success];
    }

    private function handleCancelBooking(int $userId): array
    {
        $errors = [];
        $success = '';
        $bookingId = (int) ($_POST['booking_id'] ?? 0);

        if ($bookingId <= 0) {
            $errors['cancel'] = 'Yêu cầu đặt phòng không hợp lệ.';
            return [$errors, $success];
        }

        $booking = $this->roomRepo->findBookingForUser($bookingId, $userId);

        if (!$booking) {
            $errors['cancel'] = 'Không tìm thấy yêu cầu đặt phòng của bạn.';
            return [$errors, $success];
        }

        $canCancel = $booking['status'] !== 'cancelled' && ($booking['status'] === 'pending' || $booking['start_time'] > date('Y-m-d H:i:s'));

        if (!$canCancel) {
            $errors['cancel'] = 'Không thể hủy yêu cầu này.';
            return [$errors, $success];
        }

        $this->roomRepo->cancelBooking($bookingId, $userId);
        $success = 'Hủy yêu cầu đặt phòng thành công.';

        return [$errors, $success];
    }

    private function handleReport(int $userId): array
    {
        $errors = [];
        $success = '';
        $deviceId = trim((string) ($_POST['device_id'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));

        if ($deviceId === '') {
            $errors['device_id'] = 'Vui lòng chọn thiết bị.';
        }
        if ($description === '') {
            $errors['description'] = 'Vui lòng nhập nội dung báo hỏng.';
        }

        if (!empty($errors)) {
            return [$errors, $success];
        }

        if (!$this->roomRepo->deviceExists((int) $deviceId)) {
            $errors['device_id'] = 'Thiết bị không tồn tại.';
            return [$errors, $success];
        }

        $this->roomRepo->createDeviceReport((int) $deviceId, $userId, $description);
        $success = 'Báo hỏng thiết bị thành công. Cán bộ Lab sẽ kiểm tra và bảo trì sớm.';

        return [$errors, $success];
    }
}