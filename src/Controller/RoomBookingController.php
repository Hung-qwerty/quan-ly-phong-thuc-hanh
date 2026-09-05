<?php

namespace App\Controller;

use App\Repository\RoomBookingRepository;

class RoomBookingController
{
    private RoomBookingRepository $roomRepo;

    public function __construct(RoomBookingRepository $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }

    public function index(): void
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if ($userId <= 0) {
            header('Location: index.php?route=login');
            exit;
        }

        $page = $_GET['page'] ?? 'home';

        $allowedPages = [
            'home',
            'rooms',
            'booking',
            'mybookings',
            'report',
            'myreports'
        ];

        if (!in_array($page, $allowedPages, true)) {
            $page = 'home';
        }

        $errors = [];
        $success = '';

        /*
         * =========================
         * LẤY USER
         * =========================
         */
        $user = $this->roomRepo->findUser($userId);

        if (!$user) {
            session_unset();
            session_destroy();

            header('Location: index.php?route=login');
            exit;
        }

        /*
         * =========================
         * XỬ LÝ POST
         * =========================
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            /*
             * TV3 - ĐẶT PHÒNG
             */
            if (
                $action === 'booking'
                || $action === 'create_booking'
            ) {
                $page = 'booking';

                [$errors, $success]
                    = $this->handleBooking($userId);
            }

            /*
             * TV3 - HỦY BOOKING
             */
            elseif ($action === 'cancel_booking') {

                $page = 'mybookings';

                [$errors, $success]
                    = $this->handleCancelBooking($userId);
            }

            /*
             * TV2 - BÁO HỎNG THIẾT BỊ
             */
            elseif ($action === 'report') {

                $page = 'report';

                [$errors, $success]
                    = $this->handleReport($userId);
            }
        }

        /*
         * =========================
         * LẤY DATA CHO VIEW
         * =========================
         */

        // TV2
        $rooms = $this->roomRepo->findAllRooms();

        // TV2
        $devices = $this->roomRepo->findAllDevices();

        // TV3
        $myBookings = $this->roomRepo->findBookingsByUser($userId);

        // TV2
        $myReports = $this->roomRepo->findReportsByUser($userId);

        /*
         * =========================
         * VIEW
         * =========================
         */

        require_once __DIR__
            . '/../View/student/room_booking/index.php';
    }


    /*
     * =====================================================
     * TV3 - XỬ LÝ ĐẶT PHÒNG
     * =====================================================
     */
    private function handleBooking(int $userId): array
    {
        $errors = [];
        $success = '';

        $roomId = trim(
            (string) ($_POST['room_id'] ?? '')
        );

        $date = trim(
            (string) ($_POST['date'] ?? '')
        );

        $start = trim(
            (string) ($_POST['start'] ?? '')
        );

        $end = trim(
            (string) ($_POST['end'] ?? '')
        );

        $purpose = trim(
            (string) ($_POST['purpose'] ?? '')
        );


        /*
         * VALIDATE
         */

        if ($roomId === '') {
            $errors['room_id']
                = 'Vui lòng chọn phòng.';
        }

        if ($date === '') {
            $errors['date']
                = 'Vui lòng chọn ngày.';
        }

        if ($start === '') {
            $errors['start']
                = 'Vui lòng chọn giờ bắt đầu.';
        }

        if ($end === '') {
            $errors['end']
                = 'Vui lòng chọn giờ kết thúc.';
        }

        if (
            $start !== ''
            && $end !== ''
            && $end <= $start
        ) {
            $errors['end']
                = 'Giờ kết thúc phải lớn hơn giờ bắt đầu.';
        }

        if ($purpose === '') {
            $errors['purpose']
                = 'Vui lòng nhập mục đích.';
        }


        if (!empty($errors)) {
            return [$errors, $success];
        }


        /*
         * KIỂM TRA PHÒNG
         */

        $room = $this->roomRepo->findBookableRoom(
            (int) $roomId
        );

        if (!$room) {

            $errors['room_id']
                = 'Phòng không tồn tại hoặc đang bảo trì.';

            return [$errors, $success];
        }


        /*
         * TẠO DATETIME
         */

        $startTime =
            $date . ' ' . $start . ':00';

        $endTime =
            $date . ' ' . $end . ':00';


        /*
         * KIỂM TRA TRÙNG LỊCH
         *
         * TV3
         */

        if (
            $this->roomRepo->hasBookingConflict(
                (int) $roomId,
                $startTime,
                $endTime
            )
        ) {

            $errors['room_id']
                = 'Phòng đã được đặt trong thời gian này.';

            return [$errors, $success];
        }


        /*
         * INSERT BOOKING
         */

        $this->roomRepo->createBooking(
            $userId,
            (int) $roomId,
            $startTime,
            $endTime,
            $purpose
        );

        $success = 'Đặt phòng thành công.';

        return [$errors, $success];
    }


    /*
     * =====================================================
     * TV3 - HỦY BOOKING
     * =====================================================
     */
    private function handleCancelBooking(
        int $userId
    ): array {

        $errors = [];
        $success = '';

        $bookingId =
            (int) ($_POST['booking_id'] ?? 0);


        if ($bookingId <= 0) {

            $errors['cancel']
                = 'Yêu cầu đặt phòng không hợp lệ.';

            return [$errors, $success];
        }


        /*
         * CHỈ ĐƯỢC HỦY BOOKING CỦA CHÍNH MÌNH
         */

        $booking =
            $this->roomRepo->findBookingForUser(
                $bookingId,
                $userId
            );


        if (!$booking) {

            $errors['cancel']
                = 'Không tìm thấy yêu cầu đặt phòng của bạn.';

            return [$errors, $success];
        }


        /*
         * KIỂM TRA CÓ THỂ HỦY KHÔNG
         */

        $canCancel =
            $booking['status'] !== 'cancelled'
            &&
            (
                $booking['status'] === 'pending'
                ||
                $booking['start_time']
                    > date('Y-m-d H:i:s')
            );


        if (!$canCancel) {

            $errors['cancel']
                = 'Không thể hủy yêu cầu này.';

            return [$errors, $success];
        }


        $this->roomRepo->cancelBooking(
            $bookingId,
            $userId
        );

        $success =
            'Hủy yêu cầu đặt phòng thành công.';

        return [$errors, $success];
    }


    /*
     * =====================================================
     * TV2 - BÁO HỎNG THIẾT BỊ
     * =====================================================
     */
    private function handleReport(
        int $userId
    ): array {

        $errors = [];
        $success = '';

        $deviceId =
            trim(
                (string) ($_POST['device_id'] ?? '')
            );

        $description =
            trim(
                (string) ($_POST['description'] ?? '')
            );


        if ($deviceId === '') {

            $errors['device_id']
                = 'Vui lòng chọn thiết bị.';
        }


        if ($description === '') {

            $errors['description']
                = 'Vui lòng nhập nội dung báo hỏng.';
        }


        if (!empty($errors)) {
            return [$errors, $success];
        }


        /*
         * KIỂM TRA THIẾT BỊ
         */

        if (
            !$this->roomRepo->deviceExists(
                (int) $deviceId
            )
        ) {

            $errors['device_id']
                = 'Thiết bị không tồn tại.';

            return [$errors, $success];
        }


        /*
         * INSERT REPORT
         */

        $this->roomRepo->createDeviceReport(
            (int) $deviceId,
            $userId,
            $description
        );

        $success =
            'Báo hỏng thiết bị thành công.';

        return [$errors, $success];
    }
}