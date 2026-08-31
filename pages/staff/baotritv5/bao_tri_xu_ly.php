<?php

$thongbao = "";
$loi = "";


$staff_id = (int)(
    $_SESSION["staff_id"] ?? 0
);


if ($staff_id <= 0) {

    try {

        $stmtStaff = $conn->query(
            "SELECT id
             FROM users
             ORDER BY id ASC
             LIMIT 1"
        );

        $staff_id = (int)(
            $stmtStaff->fetchColumn() ?: 0
        );

    } catch (PDOException $e) {

        $staff_id = 0;
    }
}


if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["batdau_baotri"])
) {

    $device_id = (int)(
        $_POST["device_id"] ?? 0
    );

    $description = trim(
        $_POST["description"] ?? ""
    );


    if ($staff_id <= 0) {

        $loi =
            "Không xác định được nhân viên thực hiện. "
            . "Hãy đăng nhập hoặc tạo ít nhất 1 tài khoản "
            . "trong bảng users.";

    } elseif ($device_id <= 0) {

        $loi = "Thiết bị không hợp lệ.";

    } elseif ($description === "") {

        $loi = "Vui lòng nhập nội dung bảo trì.";

    } else {

        try {

            $conn->beginTransaction();


            /*
             * Lấy thiết bị
             */

            $stmt = $conn->prepare(
                "SELECT id, device_name, status
                 FROM devices
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->execute([
                $device_id
            ]);

            $device = $stmt->fetch();


            if (!$device) {

                throw new Exception(
                    "Không tìm thấy thiết bị."
                );
            }


            /*
             * Kiểm tra phiếu đang bảo trì
             */

            $stmt = $conn->prepare(
                "SELECT id
                 FROM maintenance
                 WHERE device_id = ?
                 AND status IN (
                     'Đang bảo trì',
                     'Đang xử lý',
                     'pending',
                     'in_progress'
                 )
                 ORDER BY id DESC
                 LIMIT 1"
            );

            $stmt->execute([
                $device_id
            ]);


            if ($stmt->fetch()) {

                throw new Exception(
                    "Thiết bị này đang có phiếu bảo trì."
                );
            }


            /*
             * Tạo phiếu bảo trì
             */

            $stmt = $conn->prepare(
                "INSERT INTO maintenance
                (
                    device_id,
                    staff_id,
                    description,
                    status,
                    created_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    'Đang bảo trì',
                    NOW()
                )"
            );


            $stmt->execute([
                $device_id,
                $staff_id,
                $description
            ]);


            $maintenance_id = (int)
                $conn->lastInsertId();


            /*
             * Đổi trạng thái thiết bị
             */

            $stmt = $conn->prepare(
                "UPDATE devices
                 SET status = 'Đang bảo trì'
                 WHERE id = ?"
            );

            $stmt->execute([
                $device_id
            ]);


            /*
             * Tạo lịch sử bảo trì
             */

            $stmt = $conn->prepare(
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
                (
                    ?,
                    ?,
                    ?,
                    CURDATE(),
                    ?,
                    'Đang thực hiện'
                )"
            );


            $stmt->execute([
                $maintenance_id,
                $device_id,
                $staff_id,
                $description
            ]);


            $conn->commit();


            $thongbao =
                "Đã tạo phiếu bảo trì #"
                . $maintenance_id
                . " thành công.";


        } catch (Throwable $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $loi = $e->getMessage();
        }
    }
}


/*
|--------------------------------------------------------------------------
| CẬP NHẬT BẢO TRÌ
|--------------------------------------------------------------------------
*/

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["capnhat"])
) {

    $maintenance_id = (int)(
        $_POST["maintenance_id"] ?? 0
    );

    $device_id = (int)(
        $_POST["device_id"] ?? 0
    );

    $status = trim(
        $_POST["status"] ?? ""
    );

    $result = trim(
        $_POST["result"] ?? ""
    );


    $allowedStatuses = [
        "Đang bảo trì",
        "Hoàn thành"
    ];


    if ($staff_id <= 0) {

        $loi =
            "Không xác định được nhân viên thực hiện.";

    } elseif ($maintenance_id <= 0) {

        $loi =
            "Phiếu bảo trì không hợp lệ.";

    } elseif ($device_id <= 0) {

        $loi =
            "Thiết bị không hợp lệ.";

    } elseif (
        !in_array(
            $status,
            $allowedStatuses,
            true
        )
    ) {

        $loi =
            "Trạng thái bảo trì không hợp lệ.";

    } elseif ($result === "") {

        $loi =
            "Vui lòng nhập kết quả bảo trì.";

    } else {

        try {

            $conn->beginTransaction();


            /*
             * Lấy phiếu bảo trì
             */

            $stmt = $conn->prepare(
                "SELECT id, device_id
                 FROM maintenance
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->execute([
                $maintenance_id
            ]);

            $maintenance =
                $stmt->fetch();


            if (!$maintenance) {

                throw new Exception(
                    "Không tìm thấy phiếu bảo trì."
                );
            }


            if (
                (int)$maintenance["device_id"]
                !== $device_id
            ) {

                throw new Exception(
                    "Phiếu bảo trì không thuộc thiết bị đã chọn."
                );
            }


            /*
             * Cập nhật phiếu
             */

            $stmt = $conn->prepare(
                "UPDATE maintenance
                 SET status = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $status,
                $maintenance_id
            ]);


            /*
             * Cập nhật lịch sử
             */

            $stmt = $conn->prepare(
                "UPDATE maintenance_history
                 SET
                    content = ?,
                    result = ?,
                    maintenance_date = CURDATE()

                 WHERE maintenance_id = ?

                 ORDER BY id DESC

                 LIMIT 1"
            );

            $stmt->execute([
                $result,
                $result,
                $maintenance_id
            ]);


            /*
             * Nếu chưa có lịch sử thì tạo mới
             */

            if ($stmt->rowCount() === 0) {

                $stmt = $conn->prepare(
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
                    (
                        ?,
                        ?,
                        ?,
                        CURDATE(),
                        ?,
                        ?
                    )"
                );


                $stmt->execute([
                    $maintenance_id,
                    $device_id,
                    $staff_id,
                    $result,
                    $result
                ]);
            }


            /*
             * Xác định trạng thái thiết bị
             */

            if ($status === "Hoàn thành") {

                $deviceStatus =
                    "Hoạt động";

            } else {

                $deviceStatus =
                    "Đang bảo trì";
            }


            /*
             * Cập nhật thiết bị
             */

            $stmt = $conn->prepare(
                "UPDATE devices
                 SET status = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $deviceStatus,
                $device_id
            ]);


            $conn->commit();


            if ($status === "Hoàn thành") {

                $thongbao =
                    "Bảo trì hoàn thành. "
                    . "Thiết bị đã chuyển sang Hoạt động.";

            } else {

                $thongbao =
                    "Đã cập nhật phiếu bảo trì.";
            }


        } catch (Throwable $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $loi = $e->getMessage();
        }
    }
}