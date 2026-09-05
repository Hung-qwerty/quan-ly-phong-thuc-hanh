<?php
namespace App\Controller;

use App\Repository\MaintenanceRepository;
use Exception;

class MaintenanceController {
    public function __construct(private MaintenanceRepository $maintenanceRepo) {}

    public function index(): void {
        $thongbao = "";
        $loi = "";

        // Lấy staff_id từ session (Xử lý đồng bộ session với các thành viên khác)
        $staff_id = (int)($_SESSION["user_id"] ?? $_SESSION["staff_id"] ?? 0);
        if ($staff_id <= 0) {
            $staff_id = $this->maintenanceRepo->getFallbackStaffId();
        }

        // Xử lý POST request
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["batdau_baotri"])) {
                $device_id = (int)($_POST["device_id"] ?? 0);
                $description = trim($_POST["description"] ?? "");

                if ($staff_id <= 0) $loi = "Không xác định được nhân viên thực hiện.";
                elseif ($device_id <= 0) $loi = "Thiết bị không hợp lệ.";
                elseif ($description === "") $loi = "Vui lòng nhập nội dung bảo trì.";
                else {
                    try {
                        $mId = $this->maintenanceRepo->startMaintenance($device_id, $staff_id, $description);
                        $thongbao = "Đã tạo phiếu bảo trì #$mId thành công.";
                    } catch (Exception $e) {
                        $loi = $e->getMessage();
                    }
                }
            } elseif (isset($_POST["capnhat"])) {
                $maintenance_id = (int)($_POST["maintenance_id"] ?? 0);
                $device_id = (int)($_POST["device_id"] ?? 0);
                $status = trim($_POST["status"] ?? "");
                $result = trim($_POST["result"] ?? "");

                if ($staff_id <= 0) $loi = "Không xác định được nhân viên thực hiện.";
                elseif ($maintenance_id <= 0) $loi = "Phiếu bảo trì không hợp lệ.";
                elseif ($device_id <= 0) $loi = "Thiết bị không hợp lệ.";
                elseif (!in_array($status, ["Đang bảo trì", "Hoàn thành"], true)) $loi = "Trạng thái bảo trì không hợp lệ.";
                elseif ($result === "") $loi = "Vui lòng nhập kết quả bảo trì.";
                else {
                    try {
                        $this->maintenanceRepo->updateMaintenance($maintenance_id, $device_id, $staff_id, $status, $result);
                        if ($status === "Hoàn thành") {
                            $thongbao = "Bảo trì hoàn thành. Thiết bị đã chuyển sang Hoạt động.";
                        } else {
                            $thongbao = "Đã cập nhật phiếu bảo trì.";
                        }
                    } catch (Exception $e) {
                        $loi = $e->getMessage();
                    }
                }
            }
        }

        // Lấy dữ liệu cho View
        $thietbi = $this->maintenanceRepo->getAllDevicesWithMaintenance();
        
        // Thống kê
        $stats = ['tong' => count($thietbi), 'hoatdong' => 0, 'hong' => 0, 'baotri' => 0];
        foreach ($thietbi as $tb) {
            $status = trim((string)$tb["device_status"]);
            if (in_array($status, ["Hoạt động", "working", "active"])) $stats['hoatdong']++;
            elseif (in_array($status, ["Hỏng", "broken"])) $stats['hong']++;
            elseif (in_array($status, ["Đang bảo trì", "maintenance"])) $stats['baotri']++;
        }

        // Gọi View hiển thị
        require_once __DIR__ . '/../View/staff/bao_tri.php';
    }
}