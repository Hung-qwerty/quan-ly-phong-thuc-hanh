<?php

class DeviceRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllDevices()
    {
        $sql = "SELECT 
                    d.id,
                    d.device_code,
                    d.device_name,
                    d.room_id,
                    d.type_id,
                    d.status,
                    dt.name AS type_name,
                    r.room_code,
                    r.room_name
                FROM devices d
                INNER JOIN device_types dt ON d.type_id = dt.id
                INNER JOIN rooms r ON d.room_id = r.id
                ORDER BY d.id DESC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeviceTypes()
    {
        $sql = "SELECT id, name
                FROM device_types
                ORDER BY name ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRooms()
    {
        $sql = "SELECT id, room_code, room_name, capacity, status
                FROM rooms
                ORDER BY room_code ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeviceById($id)
    {
        $sql = "SELECT *
                FROM devices
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addDevice($deviceCode, $deviceName, $typeId, $roomId, $status)
    {
        $sql = "INSERT INTO devices
                (device_code, device_name, type_id, room_id, status)
                VALUES
                (:device_code, :device_name, :type_id, :room_id, :status)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':device_code' => $deviceCode,
            ':device_name' => $deviceName,
            ':type_id' => $typeId,
            ':room_id' => $roomId,
            ':status' => $status
        ]);
    }

    public function updateDevice($id, $deviceCode, $deviceName, $typeId, $roomId, $status)
    {
        $sql = "UPDATE devices
                SET device_code = :device_code,
                    device_name = :device_name,
                    type_id = :type_id,
                    room_id = :room_id,
                    status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':device_code' => $deviceCode,
            ':device_name' => $deviceName,
            ':type_id' => $typeId,
            ':room_id' => $roomId,
            ':status' => $status
        ]);
    }

    public function deleteDevice($id)
    {
        $sql = "DELETE FROM devices
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function getBookings()
    {
        $sql = "SELECT
                    b.id,
                    b.user_id,
                    u.full_name AS user_name,
                    b.room_id,
                    r.room_code,
                    r.room_name,
                    b.start_time,
                    b.end_time,
                    b.purpose,
                    b.status,
                    b.created_at
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN rooms r ON b.room_id = r.id
                ORDER BY b.start_time ASC";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById($id)
    {
        $sql = "SELECT *
                FROM bookings
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBookingStatus($id, $status)
    {
        $sql = "UPDATE bookings
                SET status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }

    public function checkBookingConflict($roomId, $startTime, $endTime, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) 
                FROM bookings
                WHERE room_id = :room_id
                AND status = 'approved'
                AND start_time < :end_time
                AND end_time > :start_time";

        $params = [
            ':room_id' => $roomId,
            ':start_time' => $startTime,
            ':end_time' => $endTime
        ];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }
}