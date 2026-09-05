<?php

class DeviceController
{
    private $deviceRepo;

    public function __construct($deviceRepo)
    {
        $this->deviceRepo = $deviceRepo;
    }

    public function index()
    {
        $devices = $this->deviceRepo->getAllDevices();
        $deviceTypes = $this->deviceRepo->getDeviceTypes();
        $rooms = $this->deviceRepo->getRooms();
        $bookings = $this->deviceRepo->getBookings();

        $message = $_SESSION['device_message'] ?? '';
        unset($_SESSION['device_message']);

        require __DIR__ . '/../View/staff/themthietbitv4.php';
    }

    public function handleRequest()
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

    private function addDevice()
    {
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($deviceCode === '' || $deviceName === '' || $typeId <= 0 || $roomId <= 0) {
            $_SESSION['device_message'] = 'Vui lòng nhập đầy đủ thông tin thiết bị.';
            $this->redirect();
            return;
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

            $_SESSION['device_message'] = 'Thêm thiết bị thành công.';
        } catch (PDOException $e) {
            $_SESSION['device_message'] = 'Không thể thêm thiết bị. Mã thiết bị có thể đã tồn tại.';
        }

        $this->redirect();
    }

    private function editDevice()
    {
        $id = (int)($_POST['id'] ?? 0);
        $deviceCode = trim($_POST['device_code'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $typeId = (int)($_POST['type_id'] ?? 0);
        $roomId = (int)($_POST['room_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($id <= 0 || $deviceCode === '' || $deviceName === '' || $typeId <= 0 || $roomId <= 0) {
            $_SESSION['device_message'] = 'Thông tin thiết bị không hợp lệ.';
            $this->redirect();
            return;
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

            $_SESSION['device_message'] = 'Cập nhật thiết bị thành công.';
        } catch (PDOException $e) {
            $_SESSION['device_message'] = 'Không thể cập nhật thiết bị. Mã thiết bị có thể đã tồn tại.';
        }

        $this->redirect();
    }

    private function deleteDevice()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['device_message'] = 'Thiết bị không hợp lệ.';
            $this->redirect();
            return;
        }

        try {
            $this->deviceRepo->deleteDevice($id);
            $_SESSION['device_message'] = 'Xóa thiết bị thành công.';
        } catch (PDOException $e) {
            $_SESSION['device_message'] = 'Không thể xóa thiết bị.';
        }

        $this->redirect();
    }

    private function handleBooking()
    {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id <= 0 || !in_array($status, ['approved', 'rejected'], true)) {
            $_SESSION['device_message'] = 'Yêu cầu phòng không hợp lệ.';
            $this->redirect();
            return;
        }

        $booking = $this->deviceRepo->getBookingById($id);

        if (!$booking) {
            $_SESSION['device_message'] = 'Không tìm thấy yêu cầu đặt phòng.';
            $this->redirect();
            return;
        }

        if ($booking['status'] !== 'pending') {
            $_SESSION['device_message'] = 'Yêu cầu này đã được xử lý.';
            $this->redirect();
            return;
        }

        if ($status === 'approved') {
            $conflict = $this->deviceRepo->checkBookingConflict(
                $booking['room_id'],
                $booking['start_time'],
                $booking['end_time'],
                $booking['id']
            );

            if ($conflict) {
                $_SESSION['device_message'] = 'Không thể duyệt. Phòng đã có lịch được duyệt trong khoảng thời gian này.';
                $this->redirect();
                return;
            }
        }

        $this->deviceRepo->updateBookingStatus($id, $status);

        if ($status === 'approved') {
            $_SESSION['device_message'] = 'Đã duyệt yêu cầu đặt phòng.';
        } else {
            $_SESSION['device_message'] = 'Đã từ chối yêu cầu đặt phòng.';
        }

        $this->redirect();
    }

    private function redirect()
    {
        header('Location: index.php?page=devices');
        exit;
    }
}