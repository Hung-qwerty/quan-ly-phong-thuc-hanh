<?php

namespace App\Repository;

use PDO;
use Exception;

class MaintenanceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllDevicesWithMaintenance(): array
    {
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
                m.created_at,
                mh.maintenance_date,
                mh.content,
                mh.result
            FROM devices d
            LEFT JOIN maintenance m
                ON m.id = (
                    SELECT MAX(m2.id)
                    FROM maintenance m2
                    WHERE m2.device_id = d.id
                )
            LEFT JOIN maintenance_history mh
                ON mh.id = (
                    SELECT MAX(h.id)
                    FROM maintenance_history h
                    WHERE h.maintenance_id = m.id
                )
            ORDER BY d.id ASC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFallbackStaffId(): int
    {
        $stmt = $this->pdo->query(
            "SELECT id
             FROM users
             WHERE role = 'staff'
             ORDER BY id ASC
             LIMIT 1"
        );

        return (int)($stmt->fetchColumn() ?: 0);
    }

    public function startMaintenance(
        int $deviceId,
        int $staffId,
        string $description
    ): int {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, status
                 FROM devices
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->execute([$deviceId]);

            $device = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$device) {
                throw new Exception('Không tìm thấy thiết bị.');
            }

            if ($device['status'] !== 'broken') {
                throw new Exception(
                    'Chỉ thiết bị đang hỏng mới có thể bắt đầu bảo trì.'
                );
            }

            $stmt = $this->pdo->prepare(
                "SELECT id
                 FROM maintenance
                 WHERE device_id = ?
                 AND status IN ('Đang bảo trì', 'pending', 'in_progress')
                 ORDER BY id DESC
                 LIMIT 1"
            );

            $stmt->execute([$deviceId]);

            if ($stmt->fetch()) {
                throw new Exception(
                    'Thiết bị này đang có phiếu bảo trì.'
                );
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO maintenance
                (
                    device_id,
                    staff_id,
                    description,
                    status,
                    created_at
                )
                VALUES
                (?, ?, ?, 'Đang bảo trì', NOW())"
            );

            $stmt->execute([
                $deviceId,
                $staffId,
                $description
            ]);

            $maintenanceId = (int)$this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare(
                "UPDATE devices
                 SET status = 'maintenance'
                 WHERE id = ?"
            );

            $stmt->execute([$deviceId]);

            $stmt = $this->pdo->prepare(
                "INSERT INTO maintenance_history
                (
                    maintenance_id,
                    device_id,
                    staff_id,
                    maintenance_date,
                    content,
                    result
                )
                VALUES
                (?, ?, ?, CURDATE(), ?, 'Đang thực hiện')"
            );

            $stmt->execute([
                $maintenanceId,
                $deviceId,
                $staffId,
                $description
            ]);

            $this->pdo->commit();

            return $maintenanceId;

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function updateMaintenance(
        int $maintenanceId,
        int $deviceId,
        int $staffId,
        string $status,
        string $result
    ): void {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, device_id
                 FROM maintenance
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->execute([$maintenanceId]);

            $maintenance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$maintenance) {
                throw new Exception(
                    'Không tìm thấy phiếu bảo trì.'
                );
            }

            if ((int)$maintenance['device_id'] !== $deviceId) {
                throw new Exception(
                    'Phiếu bảo trì không thuộc thiết bị đã chọn.'
                );
            }

            $stmt = $this->pdo->prepare(
                "UPDATE maintenance
                 SET status = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $status,
                $maintenanceId
            ]);

            $stmt = $this->pdo->prepare(
                "UPDATE maintenance_history
                 SET content = ?,
                     result = ?,
                     maintenance_date = CURDATE()
                 WHERE maintenance_id = ?
                 ORDER BY id DESC
                 LIMIT 1"
            );

            $stmt->execute([
                $result,
                $result,
                $maintenanceId
            ]);

            if ($stmt->rowCount() === 0) {

                $stmt = $this->pdo->prepare(
                    "INSERT INTO maintenance_history
                    (
                        maintenance_id,
                        device_id,
                        staff_id,
                        maintenance_date,
                        content,
                        result
                    )
                    VALUES
                    (?, ?, ?, CURDATE(), ?, ?)"
                );

                $stmt->execute([
                    $maintenanceId,
                    $deviceId,
                    $staffId,
                    $result,
                    $result
                ]);
            }

            $deviceStatus = $status === 'Hoàn thành'
                ? 'active'
                : 'maintenance';

            $stmt = $this->pdo->prepare(
                "UPDATE devices
                 SET status = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $deviceStatus,
                $deviceId
            ]);

            $this->pdo->commit();

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}