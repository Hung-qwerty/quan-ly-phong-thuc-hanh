<?php

namespace App\Controller;

use App\Repository\MaintenanceRepository;
use Exception;

class MaintenanceController
{
    private MaintenanceRepository $maintenanceRepo;

    public function __construct(MaintenanceRepository $maintenanceRepo)
    {
        $this->maintenanceRepo = $maintenanceRepo;
    }

    public function index(): void
    {
        $thongbao = '';
        $loi = '';

        $staffId = (int)($_SESSION['user_id'] ?? 0);

        if ($staffId <= 0) {
            $staffId = $this->maintenanceRepo->getFallbackStaffId();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['batdau_baotri'])) {

                $deviceId = (int)($_POST['device_id'] ?? 0);
                $description = trim($_POST['description'] ?? '');

                if ($staffId <= 0) {
                    $loi = 'Không xác định được nhân viên thực hiện.';
                } elseif ($deviceId <= 0) {
                    $loi = 'Thiết bị không hợp lệ.';
                } elseif ($description === '') {
                    $loi = 'Vui lòng nhập nội dung bảo trì.';
                } else {
                    try {
                        $maintenanceId = $this->maintenanceRepo->startMaintenance(
                            $deviceId,
                            $staffId,
                            $description
                        );

                        $thongbao = "Đã tạo phiếu bảo trì #{$maintenanceId} thành công.";
                    } catch (Exception $e) {
                        $loi = $e->getMessage();
                    }
                }
            }

            if (isset($_POST['capnhat'])) {

                $maintenanceId = (int)($_POST['maintenance_id'] ?? 0);
                $deviceId = (int)($_POST['device_id'] ?? 0);
                $status = trim($_POST['status'] ?? '');
                $result = trim($_POST['result'] ?? '');

                if ($staffId <= 0) {
                    $loi = 'Không xác định được nhân viên thực hiện.';
                } elseif ($maintenanceId <= 0) {
                    $loi = 'Phiếu bảo trì không hợp lệ.';
                } elseif ($deviceId <= 0) {
                    $loi = 'Thiết bị không hợp lệ.';
                } elseif (!in_array($status, ['Đang bảo trì', 'Hoàn thành'], true)) {
                    $loi = 'Trạng thái bảo trì không hợp lệ.';
                } elseif ($result === '') {
                    $loi = 'Vui lòng nhập kết quả bảo trì.';
                } else {
                    try {
                        $this->maintenanceRepo->updateMaintenance(
                            $maintenanceId,
                            $deviceId,
                            $staffId,
                            $status,
                            $result
                        );

                        if ($status === 'Hoàn thành') {
                            $thongbao = 'Bảo trì hoàn thành. Thiết bị đã chuyển sang Hoạt động.';
                        } else {
                            $thongbao = 'Đã cập nhật phiếu bảo trì.';
                        }
                    } catch (Exception $e) {
                        $loi = $e->getMessage();
                    }
                }
            }
        }

        $maintenanceDevices = $this->maintenanceRepo->getAllDevicesWithMaintenance();

        $stats = [
            'tong' => count($maintenanceDevices),
            'hoatdong' => 0,
            'hong' => 0,
            'baotri' => 0
        ];

        foreach ($maintenanceDevices as $device) {

            $status = trim((string)($device['device_status'] ?? ''));

            if ($status === 'active') {
                $stats['hoatdong']++;
            } elseif ($status === 'broken') {
                $stats['hong']++;
            } elseif ($status === 'maintenance') {
                $stats['baotri']++;
            }
        }

        $page = 'maintenance';

        require __DIR__ . '/../View/staff/themthietbitv4.php';
    }
}