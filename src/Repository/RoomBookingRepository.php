<?php

namespace App\Repository;

use PDO;

class RoomBookingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, full_name, role FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findAllRooms(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id, room_code, room_name, capacity, status FROM rooms ORDER BY room_code"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllDevices(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id, device_code, device_name, room_id, status FROM devices ORDER BY device_code"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBookableRoom(int $roomId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, room_code, room_name, capacity, status 
             FROM rooms 
             WHERE id = ? AND status = 'available' 
             LIMIT 1"
        );
        $stmt->execute([$roomId]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        return $room ?: null;
    }

    public function hasBookingConflict(int $roomId, string $startTime, string $endTime): bool 
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM bookings 
             WHERE room_id = ? 
             AND status = 'approved' 
             AND start_time < ? 
             AND end_time > ? 
             LIMIT 1"
        );
        $stmt->execute([$roomId, $endTime, $startTime]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findReportableDevices(int $userId): array 
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT d.id, d.device_code, d.device_name, d.room_id, d.status
             FROM devices d
             INNER JOIN bookings b ON d.room_id = b.room_id
             WHERE b.user_id = ? AND b.status = 'approved'
             ORDER BY d.device_code"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBooking(int $userId, int $roomId, string $startTime, string $endTime, string $purpose): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bookings (user_id, room_id, start_time, end_time, purpose, status)
             VALUES (?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->execute([$userId, $roomId, $startTime, $endTime, $purpose]);
    }

    public function countBookingsByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(id) FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function findBookingsByUser(int $userId): array
    {
        return $this->findBookingsByUserPaginated($userId, 100, 0);
    }

    public function findBookingsByUserPaginated(int $userId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT b.id, r.room_code, r.room_name, b.start_time, b.end_time, b.purpose, b.status
             FROM bookings b
             JOIN rooms r ON b.room_id = r.id
             WHERE b.user_id = ?
             ORDER BY b.start_time DESC
             LIMIT $limit OFFSET $offset"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBookingForUser(int $bookingId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, start_time, status FROM bookings WHERE id = ? AND user_id = ? LIMIT 1"
        );
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        return $booking ?: null;
    }

    public function cancelBooking(int $bookingId, int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$bookingId, $userId]);
    }

    public function deviceExists(int $deviceId): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM devices WHERE id = ? LIMIT 1");
        $stmt->execute([$deviceId]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createDeviceReport(int $deviceId, int $userId, string $description): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO device_reports (device_id, user_id, description, status)
             VALUES (?, ?, ?, 'reported')"
        );
        $stmt->execute([$deviceId, $userId, $description]);

        $stmtUpdate = $this->pdo->prepare("UPDATE devices SET status = 'broken' WHERE id = ?");
        $stmtUpdate->execute([$deviceId]);
    }

    public function countReportsByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(id) FROM device_reports WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function findReportsByUser(int $userId): array
    {
        return $this->findReportsByUserPaginated($userId, 100, 0);
    }

    public function findReportsByUserPaginated(int $userId, int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT dr.id, d.device_code, d.device_name, dr.description, dr.status, dr.created_at
             FROM device_reports dr
             JOIN devices d ON dr.device_id = d.id
             WHERE dr.user_id = ?
             ORDER BY dr.created_at DESC
             LIMIT $limit OFFSET $offset"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}