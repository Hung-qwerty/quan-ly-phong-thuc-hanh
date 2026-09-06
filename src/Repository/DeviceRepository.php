<?php

namespace App\Repository;

use PDO;

class DeviceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllDevices(): array
    {
        $sql = "SELECT
                    d.id, d.device_code, d.device_name, d.room_id, d.type_id, d.status,
                    dt.name AS type_name, r.room_code, r.room_name
                FROM devices d
                INNER JOIN device_types dt ON d.type_id = dt.id
                INNER JOIN rooms r ON d.room_id = r.id
                ORDER BY d.id DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countDevices(string $search = '', int $roomId = 0, string $status = ''): int
    {
        $sql = "SELECT COUNT(d.id)
                FROM devices d
                INNER JOIN device_types dt ON d.type_id = dt.id
                INNER JOIN rooms r ON d.room_id = r.id
                WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (d.device_code LIKE :search OR d.device_name LIKE :search OR dt.name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($roomId > 0) {
            $sql .= " AND d.room_id = :room_id";
            $params[':room_id'] = $roomId;
        }

        if ($status !== '') {
            $sql .= " AND d.status = :status";
            $params[':status'] = $status;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getDevicesPaginated(string $search = '', int $roomId = 0, string $status = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT
                    d.id, d.device_code, d.device_name, d.room_id, d.type_id, d.status,
                    dt.name AS type_name, r.room_code, r.room_name
                FROM devices d
                INNER JOIN device_types dt ON d.type_id = dt.id
                INNER JOIN rooms r ON d.room_id = r.id
                WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (d.device_code LIKE :search OR d.device_name LIKE :search OR dt.name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($roomId > 0) {
            $sql .= " AND d.room_id = :room_id";
            $params[':room_id'] = $roomId;
        }

        if ($status !== '') {
            $sql .= " AND d.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY d.id DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeviceTypes(): array
    {
        $sql = "SELECT id, name FROM device_types ORDER BY name ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDeviceType(string $name): bool
    {
        $sql = "INSERT INTO device_types (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':name' => $name]);
    }

    public function getRooms(): array
    {
        $sql = "SELECT id, room_code, room_name, capacity, status FROM rooms ORDER BY room_code ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeviceById(int $id): ?array
    {
        $sql = "SELECT * FROM devices WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $device = $stmt->fetch(PDO::FETCH_ASSOC);
        return $device ?: null;
    }

    public function addDevice(string $deviceCode, string $deviceName, int $typeId, int $roomId, string $status): bool {
        $sql = "INSERT INTO devices (device_code, device_name, type_id, room_id, status)
                VALUES (:device_code, :device_name, :type_id, :room_id, :status)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':device_code' => $deviceCode, ':device_name' => $deviceName,
            ':type_id' => $typeId, ':room_id' => $roomId, ':status' => $status
        ]);
    }

    public function updateDevice(int $id, string $deviceCode, string $deviceName, int $typeId, int $roomId, string $status): bool {
        $sql = "UPDATE devices
                SET device_code = :device_code, device_name = :device_name,
                    type_id = :type_id, room_id = :room_id, status = :status
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id, ':device_code' => $deviceCode, ':device_name' => $deviceName,
            ':type_id' => $typeId, ':room_id' => $roomId, ':status' => $status
        ]);
    }

    public function deleteDevice(int $id): bool
    {
        $sql = "DELETE FROM devices WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getBookings(): array
    {
        $sql = "SELECT
                    b.id, b.user_id, u.full_name AS user_name, b.room_id, r.room_code,
                    r.room_name, b.start_time, b.end_time, b.purpose, b.status, b.created_at
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN rooms r ON b.room_id = r.id
                ORDER BY b.start_time ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countBookings(string $search = '', string $statusFilter = ''): int
    {
        $sql = "SELECT COUNT(b.id) 
                FROM bookings b 
                INNER JOIN users u ON b.user_id = u.id 
                INNER JOIN rooms r ON b.room_id = r.id 
                WHERE 1=1";
        $params = [];

        if ($statusFilter !== '') {
            $sql .= " AND b.status = :status";
            $params[':status'] = $statusFilter;
        }

        if ($search !== '') {
            $sql .= " AND (u.full_name LIKE :search OR u.username LIKE :search OR r.room_code LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getBookingsPaginated(string $search = '', string $statusFilter = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT
                    b.id, b.user_id, u.full_name AS user_name, u.username, b.room_id, r.room_code,
                    r.room_name, b.start_time, b.end_time, b.purpose, b.status, b.created_at
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN rooms r ON b.room_id = r.id
                WHERE 1=1";
        $params = [];

        if ($statusFilter !== '') {
            $sql .= " AND b.status = :status";
            $params[':status'] = $statusFilter;
        }

        if ($search !== '') {
            $sql .= " AND (u.full_name LIKE :search OR u.username LIKE :search OR r.room_code LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY (b.status = 'pending') DESC, b.start_time DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById(int $id): ?array
    {
        $sql = "SELECT * FROM bookings WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    public function updateBookingStatus(int $id, string $status): bool
    {
        $sql = "UPDATE bookings SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }

    public function checkBookingConflict(int $roomId, string $startTime, string $endTime, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) FROM bookings
                WHERE room_id = :room_id AND status = 'approved'
                AND start_time < :end_time AND end_time > :start_time";
        $params = [':room_id' => $roomId, ':start_time' => $startTime, ':end_time' => $endTime];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function rejectConflictingBookings(int $roomId, string $startTime, string $endTime, int $approvedId): void
    {
        $sql = "UPDATE bookings 
                SET status = 'rejected' 
                WHERE room_id = :room_id AND id != :approved_id 
                AND status = 'pending' AND start_time < :end_time AND end_time > :start_time";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId, ':approved_id' => $approvedId,
            ':start_time' => $startTime, ':end_time' => $endTime
        ]);
    }

    public function approveMultipleBookings(array $ids): int
    {
        $successCount = 0;
        foreach ($ids as $id) {
            $booking = $this->getBookingById((int)$id);
            if (!$booking || $booking['status'] !== 'pending') {
                continue;
            }

            if ($this->checkBookingConflict((int)$booking['room_id'], $booking['start_time'], $booking['end_time'], (int)$booking['id'])) {
                continue;
            }

            $this->updateBookingStatus((int)$booking['id'], 'approved');
            $this->rejectConflictingBookings((int)$booking['room_id'], $booking['start_time'], $booking['end_time'], (int)$booking['id']);
            $successCount++;
        }
        return $successCount;
    }

    public function rejectMultipleBookings(array $ids): int
    {
        if (empty($ids)) return 0;
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "UPDATE bookings SET status = 'rejected' WHERE id IN ($in) AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_map('intval', $ids));
        return $stmt->rowCount();
    }
}