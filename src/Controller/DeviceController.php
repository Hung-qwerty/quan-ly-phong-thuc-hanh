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

        $pendingBookings = 0;
        $processedBookings = 0;

        foreach ($bookings as $booking) {
            if ($booking['status'] === 'pending') {
                $pendingBookings++;
            } else {
                $processedBookings++;
            }
        }

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

        require __DIR__ . '/../View/staff/themthietbitv4.php';
    }

    public function handleRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
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

            default:
                $this->index();
                break;
        }
    }

    private function addDeviceType(): void
    {
        $name = trim($_POST['type_name'] ?? '');

        if ($name === '') {
            $this->setMessage(
                'Vui lòng nhập tên loại thiết bị.',
                'error'
            );

            $this->redirect('devices');
        }

        try {
            $this->deviceRepo->addDeviceType($name);

            $this->setMessage(
                'Thêm loại thiết bị thành công!',
                'success'
            );
        } catch (PDOException $e) {
            $this->setMessage(
                'Không thể thêm loại thiết bị.',
                'error'
            );
        }

        $this->redirect('devices');
    }

    private function addDevice(): void
    {
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if (
            $deviceCode === '' ||
            $deviceName === '' ||
            $typeId <= 0 ||
            $roomId <= 0
        ) {
            $this->setMessage(
                'Vui lòng nhập đầy đủ thông tin thiết bị.',
                'error'
            );

            $this->redirect('devices');
        }

        if (!in_array($status, ['active', 'broken', 'maintenance'], true)) {
            $status = 'active';
        }

        try {
            $this->deviceRepo->addDevice(
                $deviceCode,
                $deviceName,
                $typeId,
                $roomId,
                $status
            );

            $this->setMessage(
                'Thêm thiết bị thành công!',
                'success'
            );
        } catch (PDOException $e) {
            $this->setMessage(
                'Không thể thêm thiết bị. Mã thiết bị có thể đã tồn tại.',
                'error'
            );
        }

        $this->redirect('devices');
    }

    private function editDevice(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if (
            $id <= 0 ||
            $deviceCode === '' ||
            $deviceName === '' ||
            $typeId <= 0 ||
            $roomId <= 0
        ) {
            $this->setMessage(
                'Thông tin thiết bị không hợp lệ.',
                'error'
            );

            $this->redirect('devices');
        }

        if (!in_array($status, ['active', 'broken', 'maintenance'], true)) {
            $status = 'active';
        }

        try {
            $this->deviceRepo->updateDevice(
                $id,
                $deviceCode,
                $deviceName,
                $typeId,
                $roomId,
                $status
            );

            $this->setMessage(
                'Cập nhật thiết bị thành công!',
                'success'
            );
        } catch (PDOException $e) {
            $this->setMessage(
                'Không thể cập nhật thiết bị. Mã thiết bị có thể đã tồn tại.',
                'error'
            );
        }

        $this->redirect('devices');
    }

    private function deleteDevice(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->setMessage(
                'Thiết bị không hợp lệ.',
                'error'
            );

            $this->redirect('devices');
        }

        try {
            $this->deviceRepo->deleteDevice($id);

            $this->setMessage(
                'Xóa thiết bị thành công!',
                'success'
            );
        } catch (PDOException $e) {
            $this->setMessage(
                'Không thể xóa thiết bị. Thiết bị có thể đang được sử dụng.',
                'error'
            );
        }

        $this->redirect('devices');
    }

    private function handleBooking(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (
            $id <= 0 ||
            !in_array($status, ['approved', 'rejected'], true)
        ) {
            $this->setMessage(
                'Yêu cầu phòng không hợp lệ.',
                'error'
            );

            $this->redirect('staff_bookings');
        }

        $booking = $this->deviceRepo->getBookingById($id);

        if (!$booking) {
            $this->setMessage(
                'Không tìm thấy yêu cầu đặt phòng.',
                'error'
            );

            $this->redirect('staff_bookings');
        }

        if ($booking['status'] !== 'pending') {
            $this->setMessage(
                'Yêu cầu này đã được xử lý.',
                'error'
            );

            $this->redirect('staff_bookings');
        }

        if ($status === 'approved') {
            $conflict = $this->deviceRepo->checkBookingConflict(
                (int)$booking['room_id'],
                $booking['start_time'],
                $booking['end_time'],
                (int)$booking['id']
            );

            if ($conflict) {
                $this->setMessage(
                    'Không thể duyệt. Phòng đã có lịch được duyệt trong khoảng thời gian này.',
                    'error'
                );

                $this->redirect('staff_bookings');
            }
        }

        try {
            $this->deviceRepo->updateBookingStatus(
                $id,
                $status
            );

            if ($status === 'approved') {
                $this->setMessage(
                    'Đã đồng ý yêu cầu đặt phòng!',
                    'success'
                );
            } else {
                $this->setMessage(
                    'Đã từ chối yêu cầu đặt phòng!',
                    'success'
                );
            }
        } catch (PDOException $e) {
            $this->setMessage(
                'Không thể cập nhật yêu cầu đặt phòng.',
                'error'
            );
        }

        $this->redirect('staff_bookings');
    }

    private function setMessage(
        string $message,
        string $type
    ): void {
        $_SESSION['device_message'] = $message;
        $_SESSION['device_message_type'] = $type;
    }

    private function redirect(string $page = 'devices'): void
    {
        header(
            'Location: index.php?page=' . urlencode($page)
        );

        exit;
    }
}