<?php

$thongbao = "";
$loi = "";


/*
|--------------------------------------------------------------------------
| XÁC ĐỊNH NHÂN VIÊN
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| BẮT ĐẦU BẢO TRÌ
|--------------------------------------------------------------------------
*/

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


    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA DỮ LIỆU
    |--------------------------------------------------------------------------
    */

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
            |--------------------------------------------------------------------------
            | LẤY THÔNG TIN THIẾT BỊ
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | KIỂM TRA PHIẾU ĐANG BẢO TRÌ
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | TẠO PHIẾU BẢO TRÌ
            |
            | started_at = thời gian hiện tại
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "INSERT INTO maintenance
                (
                    device_id,
                    staff_id,
                    description,
                    status,
                    created_at,
                    started_at,
                    completed_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    'Đang bảo trì',
                    NOW(),
                    NOW(),
                    NULL
                )"
            );


            $stmt->execute([
                $device_id,
                $staff_id,
                $description
            ]);


            /*
            |--------------------------------------------------------------------------
            | LẤY ID PHIẾU VỪA TẠO
            |--------------------------------------------------------------------------
            */

            $maintenance_id = (int)
                $conn->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | CẬP NHẬT TRẠNG THÁI THIẾT BỊ
            |
            | devices.status:
            | active
            | broken
            | maintenance
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "UPDATE devices
                 SET status = 'maintenance'
                 WHERE id = ?"
            );

            $stmt->execute([
                $device_id
            ]);


            /*
            |--------------------------------------------------------------------------
            | THÊM LỊCH SỬ BẢO TRÌ
            |--------------------------------------------------------------------------
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


            /*
            |--------------------------------------------------------------------------
            | HOÀN TẤT TRANSACTION
            |--------------------------------------------------------------------------
            */

            $conn->commit();


            $thongbao =
                "Đã tạo phiếu bảo trì #"
                . $maintenance_id
                . " thành công. "
                . "Thời gian bắt đầu: "
                . date("d/m/Y H:i");


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
| CẬP NHẬT PHIẾU BẢO TRÌ
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


    /*
    |--------------------------------------------------------------------------
    | TRẠNG THÁI CỦA BẢNG MAINTENANCE
    |--------------------------------------------------------------------------
    */

    $allowedStatuses = [
        "Đang bảo trì",
        "Hoàn thành"
    ];


    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA
    |--------------------------------------------------------------------------
    */

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
            |--------------------------------------------------------------------------
            | LẤY PHIẾU BẢO TRÌ
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "SELECT
                    id,
                    device_id,
                    started_at,
                    completed_at
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


            /*
            |--------------------------------------------------------------------------
            | KIỂM TRA PHIẾU THUỘC ĐÚNG THIẾT BỊ
            |--------------------------------------------------------------------------
            */

            if (
                (int)$maintenance["device_id"]
                !== $device_id
            ) {

                throw new Exception(
                    "Phiếu bảo trì không thuộc thiết bị đã chọn."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CẬP NHẬT PHIẾU BẢO TRÌ
            |
            | Nếu hoàn thành:
            | completed_at = NOW()
            |
            | Nếu vẫn đang bảo trì:
            | completed_at giữ nguyên
            |--------------------------------------------------------------------------
            */

            if ($status === "Hoàn thành") {

                $stmt = $conn->prepare(
                    "UPDATE maintenance
                     SET
                        status = ?,
                        completed_at = NOW()
                     WHERE id = ?"
                );

                $stmt->execute([
                    $status,
                    $maintenance_id
                ]);

            } else {

                $stmt = $conn->prepare(
                    "UPDATE maintenance
                     SET
                        status = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $status,
                    $maintenance_id
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CẬP NHẬT LỊCH SỬ BẢO TRÌ
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | NẾU CHƯA CÓ LỊCH SỬ THÌ TẠO MỚI
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | CẬP NHẬT TRẠNG THÁI THIẾT BỊ
            |--------------------------------------------------------------------------
            */

            if ($status === "Hoàn thành") {

                /*
                 * Bảo trì xong
                 * devices.status = active
                 */

                $deviceStatus = "active";

            } else {

                /*
                 * Vẫn đang bảo trì
                 * devices.status = maintenance
                 */

                $deviceStatus = "maintenance";
            }


            $stmt = $conn->prepare(
                "UPDATE devices
                 SET status = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $deviceStatus,
                $device_id
            ]);


            /*
            |--------------------------------------------------------------------------
            | HOÀN TẤT
            |--------------------------------------------------------------------------
            */

            $conn->commit();


            /*
            |--------------------------------------------------------------------------
            | THÔNG BÁO
            |--------------------------------------------------------------------------
            */

            if ($status === "Hoàn thành") {

                $thongbao =
                    "Bảo trì hoàn thành. "
                    . "Thiết bị đã chuyển sang Đang hoạt động. "
                    . "Thời gian hoàn thành: "
                    . date("d/m/Y H:i");

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