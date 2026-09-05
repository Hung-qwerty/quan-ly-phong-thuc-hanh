<?php
namespace App\Repository;

use PDO;

class BookingRepository {
    public function __construct(private PDO $pdo) {}

    public function findUserById(int $userId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function findAvailableRooms(): array {
        $stmt = $this->pdo->query("
            SELECT id, room_code, room_name, capacity, status 
            FROM rooms 
            WHERE status != 'maintenance'
            ORDER BY room_code ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBookingsByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT b.id, r.room_code, r.room_name, b.start_time, b.end_time, b.purpose, b.status
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.user_id = ?
            ORDER BY b.start_time DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isRoomHasConflict(int $roomId, string $startTime, string $endTime): bool {
        $stmt = $this->pdo->prepare("
            SELECT id FROM bookings
            WHERE room_id = ?
              AND status IN ('pending', 'approved')
              AND start_time < ?
              AND end_time > ?
        ");
        $stmt->execute([$roomId, $endTime, $startTime]);
        return (bool) $stmt->fetch();
    }

    public function createBooking(int $userId, int $roomId, string $startTime, string $endTime, string $purpose): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO bookings (user_id, room_id, start_time, end_time, purpose, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([$userId, $roomId, $startTime, $endTime, $purpose]);
    }

    public function cancelBooking(int $bookingId, int $userId): bool {
        $stmt = $this->pdo->prepare("
            DELETE FROM bookings 
            WHERE id = ? AND user_id = ? AND status = 'pending'
        ");
        return $stmt->execute([$bookingId, $userId]);
    }
}