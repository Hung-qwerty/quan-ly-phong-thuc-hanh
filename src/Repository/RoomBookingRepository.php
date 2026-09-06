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


    /*
     * =====================================================
     * USER
     * =====================================================
     */

    public function findUser(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "
            SELECT
                id,
                full_name,
               
                role
            FROM users
            WHERE id = ?
            LIMIT 1
            "
        );

        $stmt->execute([$userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }


    /*
     * =====================================================
     * TV2 - ROOMS
     * =====================================================
     */

    public function findAllRooms(): array
    {
        $stmt = $this->pdo->query(
            "
            SELECT
                id,
                room_code,
                room_name,
                capacity,
                status
            FROM rooms
            ORDER BY room_code
            "
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
     * =====================================================
     * TV2 - DEVICES
     * =====================================================
     */

    public function findAllDevices(): array
    {
        $stmt = $this->pdo->query(
            "
            SELECT
                id,
                device_code,
                device_name,
                room_id,
                status
            FROM devices
            ORDER BY device_code
            "
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /*
     * =====================================================
     * TV3 - KIỂM TRA PHÒNG CÓ THỂ ĐẶT
     * =====================================================
     */

    public function findBookableRoom(
        int $roomId
    ): ?array {

        $stmt = $this->pdo->prepare(
            "
            SELECT
                id,
                room_code,
                room_name,
                capacity,
                status
            FROM rooms
            WHERE id = ?
            AND status = 'available'
            LIMIT 1
            "
        );

        $stmt->execute([$roomId]);

        $room =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return $room ?: null;
    }


    /*
     * =====================================================
     * TV3 - KIỂM TRA BOOKING TRÙNG
     * =====================================================
     *
     * Hai khoảng thời gian giao nhau khi:
     *
     * start_time < end_time_mới
     * AND
     * end_time > start_time_mới
     *
     */

    public function hasBookingConflict(int $roomId, string $startTime, string $endTime): bool 
    {
        // VÁ LỖI DEADLOCK: Chỉ chặn khi phòng đã có người được DUYỆT (approved)
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

    // VÁ LỖI NGHIỆP VỤ: Chỉ cho phép sinh viên báo hỏng thiết bị ở các phòng họ đã được duyệt mượn
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


    /*
     * =====================================================
     * TV3 - TẠO BOOKING
     * =====================================================
     */

    public function createBooking(
        int $userId,
        int $roomId,
        string $startTime,
        string $endTime,
        string $purpose
    ): void {

        $stmt = $this->pdo->prepare(
            "
            INSERT INTO bookings
            (
                user_id,
                room_id,
                start_time,
                end_time,
                purpose,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                'pending'
            )
            "
        );

        $stmt->execute([
            $userId,
            $roomId,
            $startTime,
            $endTime,
            $purpose
        ]);
    }


    /*
     * =====================================================
     * TV3 - LẤY BOOKING CỦA USER
     * =====================================================
     */

    public function findBookingsByUser(
        int $userId
    ): array {

        $stmt = $this->pdo->prepare(
            "
            SELECT
                b.id,
                r.room_code,
                r.room_name,
                b.start_time,
                b.end_time,
                b.purpose,
                b.status

            FROM bookings b

            JOIN rooms r
                ON b.room_id = r.id

            WHERE b.user_id = ?

            ORDER BY
                b.start_time DESC
            "
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }


    /*
     * =====================================================
     * TV3 - TÌM BOOKING CỦA USER
     * =====================================================
     */

    public function findBookingForUser(
        int $bookingId,
        int $userId
    ): ?array {

        $stmt = $this->pdo->prepare(
            "
            SELECT
                id,
                start_time,
                status
            FROM bookings
            WHERE id = ?
            AND user_id = ?
            LIMIT 1
            "
        );

        $stmt->execute([
            $bookingId,
            $userId
        ]);

        $booking =
            $stmt->fetch(PDO::FETCH_ASSOC);

        return $booking ?: null;
    }


    /*
     * =====================================================
     * TV3 - HỦY BOOKING
     * =====================================================
     */

    public function cancelBooking(
        int $bookingId,
        int $userId
    ): void {

        $stmt = $this->pdo->prepare(
            "
            UPDATE bookings
            SET status = 'cancelled'
            WHERE id = ?
            AND user_id = ?
            "
        );

        $stmt->execute([
            $bookingId,
            $userId
        ]);
    }


    /*
     * =====================================================
     * TV2 - KIỂM TRA DEVICE
     * =====================================================
     */

    public function deviceExists(
        int $deviceId
    ): bool {

        $stmt = $this->pdo->prepare(
            "
            SELECT id
            FROM devices
            WHERE id = ?
            LIMIT 1
            "
        );

        $stmt->execute([$deviceId]);

        return (bool)
            $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /*
     * =====================================================
     * TV2 - TẠO BÁO HỎNG
     * =====================================================
     */

    public function createDeviceReport(
        int $deviceId,
        int $userId,
        string $description
    ): void {
        // 1. Lưu phiếu báo hỏng
        $stmt = $this->pdo->prepare(
            "
            INSERT INTO device_reports
            (
                device_id,
                user_id,
                description,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'reported'
            )
            "
        );

        $stmt->execute([
            $deviceId,
            $userId,
            $description
        ]);

        // 2. Cập nhật trạng thái thiết bị thành hỏng để Staff thấy và bảo trì
        $stmtUpdate = $this->pdo->prepare(
            "UPDATE devices SET status = 'broken' WHERE id = ?"
        );
        $stmtUpdate->execute([$deviceId]);
    }


    /*
     * =====================================================
     * TV2 - LẤY BÁO HỎNG CỦA USER
     * =====================================================
     */

    public function findReportsByUser(
        int $userId
    ): array {

        $stmt = $this->pdo->prepare(
            "
            SELECT
                dr.id,
                d.device_code,
                d.device_name,
                dr.description,
                dr.status,
                dr.created_at

            FROM device_reports dr

            JOIN devices d
                ON dr.device_id = d.id

            WHERE dr.user_id = ?

            ORDER BY
                dr.created_at DESC
            "
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}