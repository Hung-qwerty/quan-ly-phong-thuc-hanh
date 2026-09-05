<?php

namespace App\Repository;

use PDO;

class RoomRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findAllRooms(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, room_code, room_name, capacity, status
             FROM rooms
             ORDER BY room_code'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllDevices(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, device_code, device_name, room_id, status
             FROM devices
             ORDER BY device_code'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBookableRoom(int $roomId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, room_code, room_name, capacity, status
             FROM rooms
             WHERE id = ?
               AND status != 'maintenance'
             LIMIT 1"
        );
        $stmt->execute([$roomId]);

        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        return $room ?: null;
    }

    public function hasBookingConflict(
        int $roomId,
        string $startTime,
        string $endTime
    ): bool {
        $stmt = $this->pdo->prepare(
            "SELECT id
             FROM bookings
             WHERE room_id = ?
               AND status IN ('pending', 'approved')
               AND start_time < ?
               AND end_time > ?
             LIMIT 1"
        );

        $stmt->execute([
            $roomId,
            $endTime,
            $startTime
        ]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createBooking(
        int $userId,
        int $roomId,
        string $startTime,
        string $endTime,
        string $purpose
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bookings
                (user_id, room_id, start_time, end_time, purpose, status)
             VALUES (?, ?, ?, ?, ?, 'pending')"
        );

        return $stmt->execute([
            $userId,
            $roomId,
            $startTime,
            $endTime,
            $purpose
        ]);
    }

    public function createDeviceReport(
        int $deviceId,
        int $userId,
        string $description
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO device_reports
                (device_id, user_id, description, status)
             VALUES (?, ?, ?, 'reported')"
        );

        return $stmt->execute([
            $deviceId,
            $userId,
            $description
        ]);
    }

    public function findBookingsByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                b.id,
                r.room_code,
                r.room_name,
                b.start_time,
                b.end_time,
                b.purpose,
                b.status
             FROM bookings b
             JOIN rooms r ON b.room_id = r.id
             WHERE b.user_id = ?
             ORDER BY b.start_time DESC"
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBookingForUser(
        int $bookingId,
        int $userId
    ): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT
                id,
                room_id,
                start_time,
                end_time,
                purpose,
                status
             FROM bookings
             WHERE id = ?
               AND user_id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $bookingId,
            $userId
        ]);

        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        return $booking ?: null;
    }

    public function cancelBooking(
        int $bookingId,
        int $userId
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE bookings
             SET status = 'cancelled'
             WHERE id = ?
               AND user_id = ?"
        );

        return $stmt->execute([
            $bookingId,
            $userId
        ]);
    }

    public function deviceExists(int $deviceId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id FROM devices WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$deviceId]);

        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
