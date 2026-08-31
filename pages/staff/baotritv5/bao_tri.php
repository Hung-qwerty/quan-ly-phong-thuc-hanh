<?php
require_once __DIR__ . "/ketnoi.php";
require_once __DIR__ . "/bao_tri_ham.php";
require_once __DIR__ . "/bao_tri_xu_ly.php";
require_once __DIR__ . "/bao_tri_truy_van.php";

// Lấy danh sách thiết bị
$thietbi = layDanhSachThietBi($conn);

// Thống kê
$tong = count($thietbi);
$hoatdong = 0;
$hong = 0;
$baotri = 0;

foreach ($thietbi as $tb) {
    $status = trim((string)$tb["device_status"]);

    if (in_array($status, ["Hoạt động", "working", "active"], true)) {
        $hoatdong++;
    } elseif (in_array($status, ["Hỏng", "broken"], true)) {
        $hong++;
    } elseif (in_array($status, ["Đang bảo trì", "maintenance"], true)) {
        $baotri++;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quản lý bảo trì thiết bị</title>

    <link rel="stylesheet" href="css/bao_tri.css">
</head>

<body>

<div class="container">

    <h2>PHÒNG BẢO TRÌ THIẾT BỊ</h2>

    <p class="mo-ta">
        Quản lý bảo trì và cập nhật trạng thái thiết bị
    </p>


    <!-- THÔNG BÁO -->

    <?php if ($thongbao !== ""): ?>

        <div class="thong-bao">
            <?= h($thongbao) ?>
        </div>

    <?php endif; ?>


    <?php if ($loi !== ""): ?>

        <div class="thong-bao-loi">
            <?= h($loi) ?>
        </div>

    <?php endif; ?>


    <!-- THỐNG KÊ -->

    <div class="thong-ke">

        <div class="box">
            Tổng thiết bị
            <strong><?= $tong ?></strong>
        </div>

        <div class="box">
            Hoạt động
            <strong><?= $hoatdong ?></strong>
        </div>

        <div class="box">
            Hỏng
            <strong><?= $hong ?></strong>
        </div>

        <div class="box">
            Đang bảo trì
            <strong><?= $baotri ?></strong>
        </div>

    </div>


    <!-- DANH SÁCH THIẾT BỊ -->

    <div class="table-wrap">

        <table>

            <thead>

            <tr>
                <th>STT</th>
                <th>Mã thiết bị</th>
                <th>Thiết bị</th>
                <th>Loại (ID)</th>
                <th>Phòng (ID)</th>
                <th>Trạng thái</th>
                <th>Thông tin bảo trì</th>
                <th>Thao tác</th>
            </tr>

            </thead>


            <tbody>

            <?php if (!empty($thietbi)): ?>

                <?php foreach ($thietbi as $i => $tb): ?>

                    <?php
                    $deviceStatus = (string)$tb["device_status"];
                    $maintenanceId = (int)($tb["maintenance_id"] ?? 0);
                    ?>

                    <tr>

                        <!-- STT -->

                        <td>
                            <?= $i + 1 ?>
                        </td>


                        <!-- MÃ THIẾT BỊ -->

                        <td>
                            <?= h($tb["device_code"] ?? "") ?>
                        </td>


                        <!-- TÊN THIẾT BỊ -->

                        <td>
                            <strong>
                                <?= h($tb["device_name"] ?? "") ?>
                            </strong>
                        </td>


                        <!-- LOẠI -->

                        <td>
                            <?= h($tb["type_id"] ?? "") ?>
                        </td>


                        <!-- PHÒNG -->

                        <td>
                            <?= h($tb["room_id"] ?? "") ?>
                        </td>


                        <!-- TRẠNG THÁI -->

                        <td class="status">

                            <span class="<?= h(
                                trangThaiClass($deviceStatus)
                            ) ?>">

                                <?= h(
                                    trangThaiText($deviceStatus)
                                ) ?>

                            </span>

                        </td>


                        <!-- THÔNG TIN BẢO TRÌ -->

                        <td class="thong-tin-bao-tri">

                            <?php if ($maintenanceId > 0): ?>

                                <strong>
                                    Phiếu #<?= $maintenanceId ?>
                                </strong>

                                <br>

                                <span class="muted">

                                    Trạng thái phiếu:

                                    <?= h(
                                        $tb["maintenance_status"] ?? ""
                                    ) ?>

                                </span>

                                <br>

                                Ngày:

                                <?= h(
                                    $tb["maintenance_date"]
                                    ?? $tb["created_at"]
                                    ?? ""
                                ) ?>

                                <br>

                                Nội dung:

                                <?= h(
                                    $tb["description"]
                                    ?? $tb["content"]
                                    ?? ""
                                ) ?>


                                <?php if (!empty($tb["result"])): ?>

                                    <br>

                                    Kết quả:

                                    <?= h($tb["result"]) ?>

                                <?php endif; ?>


                            <?php else: ?>

                                <span class="muted">
                                    Chưa có lịch sử bảo trì.
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- THAO TÁC -->

                        <td>

                            <?php
                            include __DIR__ . "/bao_tri_thao_tac.php";
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>


            <?php else: ?>

                <tr>

                    <td colspan="8">
                        Chưa có thiết bị trong cơ sở dữ liệu.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


    <!-- QUAY LẠI -->

    <a
        href="thietbi.php"
        class="back"
    >
        ← Quay lại quản lý thiết bị
    </a>

</div>

</body>
</html>