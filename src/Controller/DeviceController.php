<?php

namespace App\Controller;

use App\Repository\DeviceRepository;
use PDOException;

class DeviceController
{
    private DeviceRepository $deviceRepo;

    public function __construct(DeviceRepository $deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }

    public function index(): void
    {
        if (empty($_SESSION['user_id']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
            http_response_code(403);
            die('LỖI 403: CẢNH BÁO BẢO MẬT! Chỉ Cán bộ Lab mới có quyền truy cập trang này.');
        }

        $devices = $this->deviceRepo->getAllDevices();
        $deviceTypes = $this->deviceRepo->getDeviceTypes();
        $rooms = $this->deviceRepo->getRooms();
        $bookings = $this->deviceRepo->getBookings();

        $message = $_SESSION['device_message'] ?? '';
        $messageType = $_SESSION['device_message_type'] ?? '';

        unset($_SESSION['device_message']);
        unset($_SESSION['device_message_type']);

        $page = $_GET['page'] ?? 'devices';

        if (!in_array($page, ['devices', 'staff_bookings', 'borrowings'], true)) {
            $page = 'devices';
        }

        $devSearch = trim($_GET['dev_search'] ?? '');
        $devRoom = (int)($_GET['dev_room'] ?? 0);
        $devStatus = trim($_GET['dev_status'] ?? '');
        $devPage = max(1, (int)($_GET['p'] ?? 1));
        $devLimit = 10;
        $devOffset = ($devPage - 1) * $devLimit;

        $devTotalCount = $this->deviceRepo->countDevices($devSearch, $devRoom, $devStatus);
        $devTotalPages = ceil($devTotalCount / $devLimit);
        $paginatedDevices = $this->deviceRepo->getDevicesPaginated($devSearch, $devRoom, $devStatus, $devLimit, $devOffset);

        $pendingBookings = 0;
        $processedBookings = 0;
        foreach ($bookings as $booking) {
            if ($booking['status'] === 'pending') {
                $pendingBookings++;
            } else {
                $processedBookings++;
            }
        }

        $bookingSearch = trim($_GET['search'] ?? '');
        $bookingStatusFilter = trim($_GET['status_filter'] ?? '');
        $bookingPage = max(1, (int)($_GET['p'] ?? 1));
        $bookingLimit = 10;
        $bookingOffset = ($bookingPage - 1) * $bookingLimit;

        $bookingTotalCount = $this->deviceRepo->countBookings($bookingSearch, $bookingStatusFilter);
        $bookingTotalPages = ceil($bookingTotalCount / $bookingLimit);
        $paginatedBookings = $this->deviceRepo->getBookingsPaginated($bookingSearch, $bookingStatusFilter, $bookingLimit, $bookingOffset);

        $borrowings = [];
        $pendingBorrowings = 0;
        $processedBorrowings = 0;

        $totalDevices = count($devices);
        $activeDevices = 0;
        $maintenanceDevices = 0;
        $brokenDevices = 0;

        foreach ($devices as $device) {
            if ($device['status'] === 'active') {
                $activeDevices++;
            } elseif ($device['status'] === 'maintenance') {
                $maintenanceDevices++;
            } elseif ($device['status'] === 'broken') {
                $brokenDevices++;
            }
        }

        $staffName = $_SESSION['full_name'] ?? 'Cán bộ Lab';

        require __DIR__ . '/../View/staff/themthietbitv4.php';
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        if (empty($_SESSION['user_id']) || !in_array($_SESSION['role'], ['staff', 'admin'])) {
            http_response_code(403);
            die('LỖI 403: CẢNH BÁO BẢO MẬT!');
        }

        if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            die('LỖI 419: Token bảo mật CSRF không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang!');
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                $this->addDevice();
                break;
            case 'add_type':
                $this->addDeviceType();
                break;
            case 'edit':
                $this->editDevice();
                break;
            case 'delete':
                $this->deleteDevice();
                break;
            case 'booking':
                $this->handleBooking();
                break;
            case 'bulk_booking':
                $this->handleBulkBooking();
                break;
            default:
                $this->index();
                break;
        }
    }

    private function addDeviceType(): void {
        $name = trim($_POST['type_name'] ?? '');
        if ($name === '') { $this->setMessage('Vui lòng nhập tên loại thiết bị.', 'error'); $this->redirect('devices'); }
        try { $this->deviceRepo->addDeviceType($name); $this->setMessage('Thêm loại thiết bị thành công!', 'success'); } catch (PDOException $e) { $this->setMessage('Không thể thêm loại thiết bị.', 'error'); }
        $this->redirect('devices');
    }

    private function addDevice(): void {
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($deviceCode === '' || $deviceName === '' || $typeId <= 0 || $roomId <= 0) {
            $this->setMessage('Vui lòng nhập đầy đủ thông tin thiết bị.', 'error');
            $this->redirect('devices');
        }

        if (!in_array($status, ['active', 'broken', 'maintenance'], true)) { $status = 'active'; }

        try {
            $this->deviceRepo->addDevice($deviceCode, $deviceName, $typeId, $roomId, $status);
            $this->setMessage('Thêm thiết bị thành công!', 'success');
        } catch (PDOException $e) {
            $this->setMessage('Không thể thêm thiết bị. Mã thiết bị có thể đã tồn tại.', 'error');
        }
        $this->redirect('devices');
    }

    private function editDevice(): void {
        $id = (int)($_POST['id'] ?? 0);
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || $deviceCode === '' || $deviceName === '' || $typeId <= 0 || $roomId <= 0) {
            $this->setMessage('Thông tin thiết bị không hợp lệ.', 'error');
            $this->redirect('devices');
        }

        $currentDevice = $this->deviceRepo->getDeviceById($id);
        if ($currentDevice && in_array($currentDevice['status'], ['broken', 'maintenance'], true) && $status === 'active') {
            $this->setMessage('Lỗi: Phải hoàn thành phiếu bảo trì mới được chuyển sang trạng thái Hoạt động.', 'error');
            $this->redirect('devices');
        }

        if (!in_array($status, ['active', 'broken', 'maintenance'], true)) { $status = 'active'; }

        try {
            $this->deviceRepo->updateDevice($id, $deviceCode, $deviceName, $typeId, $roomId, $status);
            $this->setMessage('Cập nhật thiết bị thành công!', 'success');
        } catch (PDOException $e) {
            $this->setMessage('Không thể cập nhật. Mã thiết bị có thể đã tồn tại.', 'error');
        }
        $this->redirect('devices');
    }

    private function deleteDevice(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) { $this->setMessage('Thiết bị không hợp lệ.', 'error'); $this->redirect('devices'); }
        try { $this->deviceRepo->deleteDevice($id); $this->setMessage('Xóa thiết bị thành công!', 'success'); } catch (PDOException $e) { $this->setMessage('Không thể xóa thiết bị. Thiết bị có thể đang được sử dụng.', 'error'); }
        $this->redirect('devices');
    }

    private function handleBooking(): void {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if ($id <= 0 || !in_array($status, ['approved', 'rejected'], true)) { $this->setMessage('Yêu cầu phòng không hợp lệ.', 'error'); $this->redirect('staff_bookings'); }
        $booking = $this->deviceRepo->getBookingById($id);
        if (!$booking || $booking['status'] !== 'pending') { $this->setMessage('Không tìm thấy yêu cầu hoặc yêu cầu đã được xử lý.', 'error'); $this->redirect('staff_bookings'); }

        if ($status === 'approved') {
            $conflict = $this->deviceRepo->checkBookingConflict((int)$booking['room_id'], $booking['start_time'], $booking['end_time'], (int)$booking['id']);
            if ($conflict) { $this->setMessage('Không thể duyệt. Phòng đã có lịch được duyệt trong khoảng thời gian này.', 'error'); $this->redirect('staff_bookings'); }
        }

        try {
            $this->deviceRepo->updateBookingStatus($id, $status);
            if ($status === 'approved') {
                $this->deviceRepo->rejectConflictingBookings((int)$booking['room_id'], $booking['start_time'], $booking['end_time'], $id);
                $this->setMessage('Đã duyệt yêu cầu và tự động từ chối các lịch trùng!', 'success');
            } else {
                $this->setMessage('Đã từ chối yêu cầu đặt phòng!', 'success');
            }
        } catch (PDOException $e) { $this->setMessage('Lỗi hệ thống khi cập nhật đặt phòng.', 'error'); }
        $this->redirect('staff_bookings');
    }

    private function handleBulkBooking(): void {
        $bulkAction = $_POST['bulk_action'] ?? '';
        $bookingIds = $_POST['booking_ids'] ?? [];
        if (empty($bookingIds) || !is_array($bookingIds)) { $this->setMessage('Vui lòng chọn ít nhất một yêu cầu để xử lý.', 'error'); $this->redirect('staff_bookings'); }
        if ($bulkAction === 'approve') {
            $count = $this->deviceRepo->approveMultipleBookings($bookingIds);
            $this->setMessage("Đã phê duyệt thành công {$count} yêu cầu (tự động từ chối các lịch trùng).", 'success');
        } elseif ($bulkAction === 'reject') {
            $count = $this->deviceRepo->rejectMultipleBookings($bookingIds);
            $this->setMessage("Đã từ chối {$count} yêu cầu được chọn.", 'success');
        }
        $this->redirect('staff_bookings');
    }

    private function setMessage(string $message, string $type): void {
        $_SESSION['device_message'] = $message;
        $_SESSION['device_message_type'] = $type;
    }

    private function redirect(string $page = 'devices'): void {
        header('Location: index.php?page=' . urlencode($page));
        exit;
    }
}