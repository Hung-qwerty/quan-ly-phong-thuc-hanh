<?php
namespace App\Repository;

use PDO;

class DeviceMaintenanceRepository {
    public function __construct(private PDO $pdo) {}

    // --- QUẢN LÝ THIẾT BỊ & LOẠI / PHÒNG ---
    
    public function getAllDevices(): array {
        $sql = "
            SELECT d.*, 
                   dt.name AS type_name, 
                   r.room_name AS room_display_name
            FROM devices d
            LEFT JOIN device_types dt ON d.type_id = dt.id
            LEFT JOIN rooms r ON d.room_id = r.id
            ORDER BY d.id DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeviceTypes(): array {
        return $this->pdo->query("SELECT * FROM device_types ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRooms(): array {
        return $this->pdo->query("SELECT *, room_name AS display_name FROM rooms ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDevice(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO devices (device_code, device_name, type_id, room_id, status) 
            VALUES (?, ?, ?, ?, 'Hoạt động')
        ");
        return $stmt->execute([
            'DEV' . time(),
            $data['name'],
            $data['type_id'],
            $data['room_id']
        ]);
    }

    public function updateDevice(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE devices 
            SET device_name = ?, type_id = ?, room_id = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['type_id'],
            $data['room_id'],
            $id
        ]);
    }

    public function deleteDevice(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM devices WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // --- DUYỆT ĐẶT PHÒNG ---

    public function getAllBookings(): array {
        $sql = "
            SELECT b.*, u.full_name AS user_name, r.room_name AS room_display_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            ORDER BY b.id DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus(int $bookingId, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $bookingId]);
    }

    // --- PHẦN BẢO TRÌ THIẾT BỊ ---

    public function getDevicesWithMaintenanceInfo(): array {
        $sql = "
            SELECT 
                d.id AS device_id,
                d.device_code,
                d.device_name,
                d.room_id,
                d.type_id,
                d.status AS device_status,
                m.id AS maintenance_id,
                m.description,
                m.status AS maintenance_status,
                m.created_at
            FROM devices d
            LEFT JOIN maintenance m ON m.id = (
                SELECT MAX(m2.id) FROM maintenance m2 WHERE m2.device_id = d.id
            )
            ORDER BY d.id ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function startMaintenance(int $deviceId, int $staffId, string $description): bool {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO maintenance (device_id, staff_id, description, status, created_at)
                VALUES (?, ?, ?, 'Đang bảo trì', NOW())
            ");
            $stmt->execute([$deviceId, $staffId, $description]);

            $stmt = $this->pdo->prepare("UPDATE devices SET status = 'Đang bảo trì' WHERE id = ?");
            $stmt->execute([$deviceId]);

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function updateMaintenance(int $maintenanceId, int $deviceId, int $staffId, string $status, string $result): bool {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("UPDATE maintenance SET status = ? WHERE id = ?");
            $stmt->execute([$status, $maintenanceId]);

            $deviceStatus = ($status === 'Hoàn thành') ? 'Hoạt động' : 'Đang bảo trì';
            $stmt = $this->pdo->prepare("UPDATE devices SET status = ? WHERE id = ?");
            $stmt->execute([$deviceStatus, $deviceId]);

            $this->pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}