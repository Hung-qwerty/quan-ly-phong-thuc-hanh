<?php

function e($value): string
{
    return htmlspecialchars(
        $value ?? '',
        ENT_QUOTES,
        'UTF-8'
    );
}


function roomStatusClass(
    string $status
): string {

    return match ($status) {

        'available'
            => 'available',

        'booked'
            => 'busy',

        default
            => 'maintenance'
    };
}


function roomStatusText(
    string $status
): string {

    return match ($status) {

        'available'
            => 'Trống',

        'booked'
            => 'Đang sử dụng',

        default
            => 'Bảo trì'
    };
}


function bookingStatusText(
    string $status
): string {

    return match ($status) {

        'pending'
            => 'Chờ duyệt',

        'approved'
            => 'Đã duyệt',

        'rejected'
            => 'Từ chối',

        'cancelled'
            => 'Đã hủy',

        default
            => $status
    };
}


function reportStatusText(
    string $status
): string {

    return match ($status) {

        'reported'
            => 'Đã báo',

        'processing'
            => 'Đang xử lý',

        'resolved'
            => 'Đã xử lý',

        default
            => $status
    };
}

?>

<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Quản lý phòng thực hành
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;

            font-family:
                Arial,
                sans-serif;

            background: #f4f6f9;

            color: #222;
        }


        a {
            color: inherit;
            text-decoration: none;
        }


        /* SIDEBAR */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 240px;

            height: 100vh;

            padding: 20px 15px;

            background: #003399;

            color: white;
        }


        .logo {

            font-size: 20px;

            font-weight: 700;

            padding:
                10px
                12px
                25px;
        }


        .menu a {

            display: block;

            padding:
                12px
                14px;

            margin: 5px 0;

            border-radius: 6px;
        }


        .menu a:hover,
        .menu a.active {

            background:
                rgba(255,255,255,.16);
        }


        .logout-link {

            margin-top: 25px !important;

            background: #b42318;
        }


        /* MAIN */

        .main {

            margin-left: 240px;

            min-height: 100vh;
        }


        .header {

            min-height: 65px;

            padding:
                0
                30px;

            background: white;

            border-bottom:
                1px solid #ddd;

            display: flex;

            align-items: center;

            justify-content:
                space-between;
        }


        .content {

            padding: 30px;
        }


        h1 {

            margin-top: 0;

            color: #003399;
        }


        /* CARDS */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(220px, 1fr)
                );

            gap: 18px;
        }


        .rooms {

            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(220px, 1fr)
                );

            gap: 18px;
        }


        .card {

            background: white;

            border:
                1px solid #ddd;

            border-radius: 8px;

            padding: 20px;

            box-shadow:
                0 2px 7px
                rgba(0,0,0,.04);
        }


        .card h3 {

            margin-top: 0;
        }


        .link {

            display: inline-block;

            margin-top: 10px;

            color: #003399;

            font-weight: 600;
        }


        /* STATUS */

        .status {

            display: inline-block;

            padding:
                5px
                10px;

            border-radius: 5px;

            font-size: 13px;

            font-weight: 600;
        }


        .available {

            background: #d1fae5;

            color: #065f46;
        }


        .busy {

            background: #fee2e2;

            color: #991b1b;
        }


        .maintenance {

            background: #fef3c7;

            color: #92400e;
        }


        /* FORM */

        .form {

            max-width: 700px;

            background: white;

            border:
                1px solid #ddd;

            border-radius: 8px;

            padding: 20px;

            box-shadow:
                0 2px 7px
                rgba(0,0,0,.04);
        }


        .group {

            margin-bottom: 16px;
        }


        label {

            display: block;

            margin-bottom: 7px;

            font-weight: 600;
        }


        input,
        select,
        textarea {

            width: 100%;

            padding:
                10px
                12px;

            border:
                1px solid #ccc;

            border-radius: 5px;

            font: inherit;
        }


        textarea {

            min-height: 110px;

            resize: vertical;
        }


        button {

            padding:
                10px
                18px;

            border: 0;

            border-radius: 5px;

            background: #003399;

            color: white;

            cursor: pointer;
        }


        button:hover {

            background: #002266;
        }


        button.danger {

            background: #b42318;
        }


        /* MESSAGE */

        .success,
        .error {

            padding:
                12px
                15px;

            border-radius: 5px;

            margin-bottom: 15px;
        }


        .success {

            background: #d1fae5;

            color: #065f46;
        }


        .error {

            background: #fee2e2;

            color: #991b1b;
        }


        .field-error {

            margin-top: 6px;

            color: #b42318;

            font-size: 14px;
        }


        /* TABLE */

        .table-wrap {

            overflow-x: auto;

            background: white;

            border:
                1px solid #ddd;

            border-radius: 8px;
        }


        table {

            width: 100%;

            border-collapse:
                collapse;
        }


        th,
        td {

            padding: 11px;

            border:
                1px solid #ddd;

            text-align: left;

            vertical-align:
                top;
        }


        th {

            background: #003399;

            color: white;
        }


        @media (
            max-width: 800px
        ) {

            .sidebar {

                width: 180px;
            }


            .main {

                margin-left: 180px;
            }


            .content {

                padding: 18px;
            }
        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
     ===================================================== -->

<aside class="sidebar">

    <div class="logo">

        LAB MANAGEMENT

    </div>


    <nav class="menu">


        <!-- TRANG CHỦ -->

        <a
            href="index.php?route=room_booking&page=home"
            class="<?= $page === 'home'
                ? 'active'
                : '' ?>"
        >
            Trang chủ
        </a>


        <!-- TV2 -->

        <a
            href="index.php?route=room_booking&page=rooms"
            class="<?= $page === 'rooms'
                ? 'active'
                : '' ?>"
        >
            Phòng thực hành
        </a>


        <!-- TV3 -->

        <a
            href="index.php?route=room_booking&page=booking"
            class="<?= $page === 'booking'
                ? 'active'
                : '' ?>"
        >
            Đặt phòng
        </a>


        <!-- TV3 -->

        <a
            href="index.php?route=room_booking&page=mybookings"
            class="<?= $page === 'mybookings'
                ? 'active'
                : '' ?>"
        >
            Yêu cầu đặt phòng
        </a>


        <!-- TV2 -->

        <a
            href="index.php?route=room_booking&page=report"
            class="<?= $page === 'report'
                ? 'active'
                : '' ?>"
        >
            Báo hỏng thiết bị
        </a>


        <!-- TV2 -->

        <a
            href="index.php?route=room_booking&page=myreports"
            class="<?= $page === 'myreports'
                ? 'active'
                : '' ?>"
        >
            Báo hỏng của tôi
        </a>


        <!-- LOGOUT -->

        <a
            href="index.php?route=logout"
            class="logout-link"
        >
            Đăng xuất
        </a>

    </nav>

</aside>



<!-- =====================================================
     MAIN
     ===================================================== -->

<main class="main">


    <header class="header">

        <span>
            Student Portal
        </span>

        <strong>
            <?= e($user['full_name']) ?>
        </strong>

    </header>


    <div class="content">


        <!-- SUCCESS -->

        <?php if ($success): ?>

            <div class="success">

                <?= e($success) ?>

            </div>

        <?php endif; ?>


        <!-- ERROR HỦY -->

        <?php if (isset($errors['cancel'])): ?>

            <div class="error">

                <?= e($errors['cancel']) ?>

            </div>

        <?php endif; ?>



        <!-- =================================================
             HOME
             ================================================= -->

        <?php if ($page === 'home'): ?>


            <h1>
                Trang chủ
            </h1>


            <p>

                Xin chào

                <strong>
                    <?= e($user['full_name']) ?>
                </strong>

            </p>


            <div class="cards">


                <!-- TV2 -->

                <div class="card">

                    <h3>
                        Phòng thực hành
                    </h3>

                    <p>
                        Xem danh sách phòng
                        và trạng thái phòng.
                    </p>

                    <a
                        class="link"
                        href="index.php?route=room_booking&page=rooms"
                    >
                        Xem phòng →
                    </a>

                </div>


                <!-- TV3 -->

                <div class="card">

                    <h3>
                        Đặt phòng
                    </h3>

                    <p>
                        Gửi yêu cầu sử dụng
                        phòng thực hành.
                    </p>

                    <a
                        class="link"
                        href="index.php?route=room_booking&page=booking"
                    >
                        Đặt phòng →
                    </a>

                </div>


                <!-- TV3 -->

                <div class="card">

                    <h3>
                        Booking của tôi
                    </h3>

                    <p>
                        Xem lịch sử và
                        trạng thái booking.
                    </p>

                    <a
                        class="link"
                        href="index.php?route=room_booking&page=mybookings"
                    >
                        Xem booking →
                    </a>

                </div>


                <!-- TV2 -->

                <div class="card">

                    <h3>
                        Báo hỏng
                    </h3>

                    <p>
                        Báo thiết bị
                        gặp sự cố.
                    </p>

                    <a
                        class="link"
                        href="index.php?route=room_booking&page=report"
                    >
                        Báo hỏng →
                    </a>

                </div>

            </div>



        <!-- =================================================
             TV2 - ROOMS
             ================================================= -->

        <?php elseif ($page === 'rooms'): ?>


            <h1>
                Phòng thực hành
            </h1>


            <div class="rooms">


                <?php foreach ($rooms as $room): ?>


                    <div class="card">


                        <h3>

                            <?= e(
                                $room['room_code']
                            ) ?>

                        </h3>


                        <p>

                            <?= e(
                                $room['room_name']
                            ) ?>

                        </p>


                        <p>

                            Sức chứa:

                            <?= e(
                                $room['capacity']
                            ) ?>

                            người

                        </p>


                        <span
                            class="status
                                <?= roomStatusClass(
                                    $room['status']
                                ) ?>"
                        >

                            <?= e(
                                roomStatusText(
                                    $room['status']
                                )
                            ) ?>

                        </span>


                        <?php
                        if (
                            $room['status']
                            === 'available'
                        ):
                        ?>

                            <p>

                                <a
                                    class="link"
                                    href="index.php?route=room_booking&page=booking"
                                >
                                    Đặt phòng →
                                </a>

                            </p>

                        <?php endif; ?>


                    </div>


                <?php endforeach; ?>


            </div>



        <!-- =================================================
             TV3 - BOOKING
             ================================================= -->

        <?php elseif ($page === 'booking'): ?>


            <h1>
                Đặt phòng
            </h1>


            <div class="form">


                <form
                    method="POST"
                    action="index.php?route=room_booking&page=booking"
                >


                    <input
                        type="hidden"
                        name="action"
                        value="create_booking"
                    >


                    <!-- ROOM -->

                    <div class="group">

                        <label>
                            Phòng
                        </label>


                        <select
                            name="room_id"
                        >

                            <option value="">
                                -- Chọn phòng --
                            </option>


                            <?php
                            foreach ($rooms as $room):

                                if (
                                    $room['status']
                                    !== 'available'
                                ) {
                                    continue;
                                }
                            ?>

                                <option
                                    value="<?= e(
                                        $room['id']
                                    ) ?>"
                                >

                                    <?= e(
                                        $room['room_code']
                                    ) ?>

                                    -

                                    <?= e(
                                        $room['room_name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <?php
                        if (
                            isset(
                                $errors['room_id']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['room_id']
                                ) ?>

                            </div>

                        <?php endif; ?>


                    </div>


                    <!-- DATE -->

                    <div class="group">

                        <label>
                            Ngày
                        </label>


                        <input
                            type="date"
                            name="date"
                            value="<?= e(
                                $_POST['date']
                                ?? ''
                            ) ?>"
                        >


                        <?php
                        if (
                            isset(
                                $errors['date']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['date']
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- START -->

                    <div class="group">

                        <label>
                            Giờ bắt đầu
                        </label>


                        <input
                            type="time"
                            name="start"
                            value="<?= e(
                                $_POST['start']
                                ?? ''
                            ) ?>"
                        >


                        <?php
                        if (
                            isset(
                                $errors['start']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['start']
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- END -->

                    <div class="group">

                        <label>
                            Giờ kết thúc
                        </label>


                        <input
                            type="time"
                            name="end"
                            value="<?= e(
                                $_POST['end']
                                ?? ''
                            ) ?>"
                        >


                        <?php
                        if (
                            isset(
                                $errors['end']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['end']
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- PURPOSE -->

                    <div class="group">

                        <label>
                            Mục đích
                        </label>


                        <textarea
                            name="purpose"
                        ><?= e(
                            $_POST['purpose']
                            ?? ''
                        ) ?></textarea>


                        <?php
                        if (
                            isset(
                                $errors['purpose']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['purpose']
                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <button type="submit">

                        Gửi yêu cầu

                    </button>


                </form>


            </div>



        <!-- =================================================
             TV3 - MY BOOKINGS
             ================================================= -->

        <?php elseif ($page === 'mybookings'): ?>


            <h1>
                Yêu cầu đặt phòng của tôi
            </h1>


            <div class="table-wrap">


                <table>

                    <thead>

                        <tr>

                            <th>
                                Phòng
                            </th>

                            <th>
                                Bắt đầu
                            </th>

                            <th>
                                Kết thúc
                            </th>

                            <th>
                                Mục đích
                            </th>

                            <th>
                                Trạng thái
                            </th>

                            <th>
                                Thao tác
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php
                        foreach (
                            $myBookings
                            as $booking
                        ):
                        ?>

                            <tr>

                                <td>

                                    <?= e(
                                        $booking['room_code']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $booking['start_time']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $booking['end_time']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $booking['purpose']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        bookingStatusText(
                                            $booking['status']
                                        )
                                    ) ?>

                                </td>


                                <td>


                                    <?php

                                    $canCancel =
                                        $booking['status']
                                        !== 'cancelled'

                                        &&

                                        (
                                            $booking['status']
                                            === 'pending'

                                            ||

                                            $booking['start_time']
                                            >
                                            date(
                                                'Y-m-d H:i:s'
                                            )
                                        );

                                    ?>


                                    <?php
                                    if ($canCancel):
                                    ?>


                                        <form
                                            method="POST"
                                            action="index.php?route=room_booking&page=mybookings"
                                            onsubmit="
                                                return confirm(
                                                    'Bạn có chắc muốn hủy yêu cầu này?'
                                                );
                                            "
                                        >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="cancel_booking"
                                            >


                                            <input
                                                type="hidden"
                                                name="booking_id"
                                                value="<?= e(
                                                    $booking['id']
                                                ) ?>"
                                            >


                                            <button
                                                type="submit"
                                                class="danger"
                                            >
                                                Hủy
                                            </button>

                                        </form>


                                    <?php else: ?>

                                        —

                                    <?php endif; ?>


                                </td>

                            </tr>


                        <?php endforeach; ?>


                        <?php
                        if (
                            empty(
                                $myBookings
                            )
                        ):
                        ?>

                            <tr>

                                <td
                                    colspan="6"
                                >
                                    Bạn chưa có
                                    yêu cầu đặt phòng.
                                </td>

                            </tr>

                        <?php endif; ?>


                    </tbody>

                </table>


            </div>



        <!-- =================================================
             TV2 - REPORT
             ================================================= -->

        <?php elseif ($page === 'report'): ?>


            <h1>
                Báo hỏng thiết bị
            </h1>


            <div class="form">


                <form
                    method="POST"
                    action="index.php?route=room_booking&page=report"
                >


                    <input
                        type="hidden"
                        name="action"
                        value="report"
                    >


                    <div class="group">

                        <label>
                            Thiết bị
                        </label>


                        <select
                            name="device_id"
                        >

                            <option value="">
                                -- Chọn thiết bị --
                            </option>


                            <?php
                            foreach (
                                $devices
                                as $device
                            ):
                            ?>

                                <option
                                    value="<?= e(
                                        $device['id']
                                    ) ?>"
                                >

                                    <?= e(
                                        $device['device_code']
                                    ) ?>

                                    -

                                    <?= e(
                                        $device['device_name']
                                    ) ?>

                                </option>

                            <?php endforeach; ?>


                        </select>


                        <?php
                        if (
                            isset(
                                $errors['device_id']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['device_id']
                                ) ?>

                            </div>

                        <?php endif; ?>


                    </div>


                    <div class="group">

                        <label>
                            Nội dung báo hỏng
                        </label>


                        <textarea
                            name="description"
                            placeholder="Mô tả tình trạng thiết bị..."
                        ><?= e(
                            $_POST['description']
                            ?? ''
                        ) ?></textarea>


                        <?php
                        if (
                            isset(
                                $errors['description']
                            )
                        ):
                        ?>

                            <div class="field-error">

                                <?= e(
                                    $errors['description']
                                ) ?>

                            </div>

                        <?php endif; ?>


                    </div>


                    <button type="submit">

                        Gửi báo hỏng

                    </button>


                </form>


            </div>



        <!-- =================================================
             TV2 - MY REPORTS
             ================================================= -->

        <?php elseif ($page === 'myreports'): ?>


            <h1>
                Báo hỏng của tôi
            </h1>


            <div class="table-wrap">


                <table>

                    <thead>

                        <tr>

                            <th>
                                Thiết bị
                            </th>

                            <th>
                                Nội dung
                            </th>

                            <th>
                                Trạng thái
                            </th>

                            <th>
                                Thời gian
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php
                        foreach (
                            $myReports
                            as $report
                        ):
                        ?>

                            <tr>

                                <td>

                                    <?= e(
                                        $report['device_code']
                                    ) ?>

                                    -

                                    <?= e(
                                        $report['device_name']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $report['description']
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        reportStatusText(
                                            $report['status']
                                        )
                                    ) ?>

                                </td>


                                <td>

                                    <?= e(
                                        $report['created_at']
                                    ) ?>

                                </td>

                            </tr>


                        <?php endforeach; ?>


                        <?php
                        if (
                            empty(
                                $myReports
                            )
                        ):
                        ?>

                            <tr>

                                <td
                                    colspan="4"
                                >
                                    Bạn chưa có
                                    báo hỏng thiết bị.
                                </td>

                            </tr>

                        <?php endif; ?>


                    </tbody>

                </table>


            </div>


        <?php endif; ?>


    </div>

</main>


</body>

</html>