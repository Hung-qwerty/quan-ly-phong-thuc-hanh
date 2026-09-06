<?php
namespace App\Controller;

use App\Repository\DeviceMaintenanceRepository;

class DeviceMaintenanceController {
    public function __construct(private DeviceMaintenanceRepository $repo) {}

    public function indexDevices(): void {
        $message = "";
        $messageType = "";
        $page = $_GET['page'] ?? 'devices';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add') {
                $name = trim($_POST['name'] ?? '');
                $typeId = $_POST['type_id'] ?? '';
                $roomId = $_POST['room_id'] ?? '';

                if (empty($name) || empty($typeId) || empty($roomId)) {
                    $message = "Vui lòng chọn loại thiết bị và phòng học!";
                    $messageType = "error";
                } else {
                    $this->repo->createDevice([
                        'name' => $name,
                        'type_id' => $typeId,
                        'room_id' => $roomId
                    ]);
                    $message = "Thêm thiết bị thành công!";
                    $messageType = "success";
                }
            }

            if ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $this->repo->deleteDevice($id);
                    $message = "Xóa thiết bị thành công!";
                    $messageType = "success";
                }
            }

            if ($action === 'booking') {
                $id = (int)($_POST['id'] ?? 0);
                $status = $_POST['status'] ?? '';
                if ($id > 0 && in_array($status, ['approved', 'rejected'])) {
                    $this->repo->updateBookingStatus($id, $status);
                    $message = ($status === 'approved') ? "Đã duyệt yêu cầu!" : "Đã từ chối yêu cầu!";
                    $messageType = "success";
                }
            }
        }

        $devices = $this->repo->getAllDevices();
        $deviceTypes = $this->repo->getDeviceTypes();
        $rooms = $this->repo->getRooms();
        $bookings = $this->repo->getAllBookings();

        require_once __DIR__ . '/../View/staff/quan_ly_thiet_bi.php';
    }

    public function indexMaintenance(): void {
        $thongbao = "";
        $loi = "";
        $staff_id = (int)($_SESSION['user_id'] ?? 1);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['batdau_baotri'])) {
                $deviceId = (int)($_POST['device_id'] ?? 0);
                $description = trim($_POST['description'] ?? '');

                if ($deviceId <= 0 || empty($description)) {
                    $loi = "Vui lòng nhập đầy đủ mô tả sự cố!";
                } else {
                    try {
                        $this->repo->startMaintenance($deviceId, $staff_id, $description);
                        $thongbao = "Đã cập nhật trạng thái thiết bị sang Bảo trì!";
                    } catch (\Throwable $e) {
                        $loi = $e->getMessage();
                    }
                }
            }

            if (isset($_POST['capnhat'])) {
                $maintenanceId = (int)($_POST['maintenance_id'] ?? 0);
                $deviceId = (int)($_POST['device_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $result = trim($_POST['result'] ?? '');

                if ($maintenanceId <= 0 || $deviceId <= 0) {
                    $loi = "Dữ liệu không hợp lệ!";
                } else {
                    try {
                        $this->repo->updateMaintenance($maintenanceId, $deviceId, $staff_id, $status, $result);
                        $thongbao = "Cập nhật tiến độ bảo trì thành công!";
                    } catch (\Throwable $e) {
                        $loi = $e->getMessage();
                    }
                }
            }
        }

        $thietbi = $this->repo->getDevicesWithMaintenanceInfo();

        require_once __DIR__ . '/../View/staff/quan_ly_bao_tri.php';
    }
}