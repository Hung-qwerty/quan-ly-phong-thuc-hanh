<?php

function layDanhSachThietBi(PDO $conn): array
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


    try {

        $stmt = $conn->query($sql);

        return $stmt->fetchAll();

    } catch (Throwable $e) {

        throw new Exception(
            "Không thể đọc dữ liệu thiết bị: "
            . $e->getMessage()
        );
    }
}