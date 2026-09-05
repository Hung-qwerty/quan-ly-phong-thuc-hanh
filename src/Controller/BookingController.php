<?php
namespace App\Controller;

use App\Repository\BookingRepository;

class BookingController {
    public function __construct(private BookingRepository $bookingRepo) {}

    public function index(): void {
        $user_id = $_SESSION["user_id"] ?? 3;
        $page = $_GET["page"] ?? "list";

        $errors = [];
        $success = "";

        $user = $this->bookingRepo->findUserById($user_id);
        if (!$user) {
            die("Không tìm thấy thông tin người dùng.");
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $action = $_POST["action"] ?? "";

            if ($action === "create_booking") {
                $page = "create";
                $room_id = (int)($_POST["room_id"] ?? 0);
                $date = $_POST["date"] ?? "";
                $start = $_POST["start"] ?? "";
                $end = $_POST["end"] ?? "";
                $purpose = trim($_POST["purpose"] ?? "");

                if (!$room_id) $errors["room_id"] = "Vui lòng chọn phòng thực hành.";
                if (empty($date)) $errors["date"] = "Vui lòng chọn ngày.";
                if (empty($start)) $errors["start"] = "Vui lòng chọn giờ bắt đầu.";
                if (empty($end)) $errors["end"] = "Vui lòng chọn giờ kết thúc.";
                if (!empty($start) && !empty($end) && $end <= $start) {
                    $errors["end"] = "Giờ kết thúc phải sau giờ bắt đầu.";
                }
                if (empty($purpose)) $errors["purpose"] = "Vui lòng nhập mục đích đặt phòng.";

                if (empty($errors)) {
                    $start_time = $date . " " . $start . ":00";
                    $end_time = $date . " " . $end . ":00";

                    if ($this->bookingRepo->isRoomHasConflict($room_id, $start_time, $end_time)) {
                        $errors["room_id"] = "Phòng đã có lịch đăng ký trong khung giờ này.";
                    }
                }

                if (empty($errors)) {
                    $this->bookingRepo->createBooking($user_id, $room_id, $start_time, $end_time, $purpose);
                    $success = "Gửi yêu cầu đặt phòng thành công! Vui lòng chờ phê duyệt.";
                    $page = "list";
                }
            }

            if ($action === "cancel_booking") {
                $booking_id = (int)($_POST["booking_id"] ?? 0);
                if ($booking_id > 0) {
                    if ($this->bookingRepo->cancelBooking($booking_id, $user_id)) {
                        $success = "Đã hủy yêu cầu đặt phòng thành công.";
                    } else {
                        $errors["general"] = "Không thể hủy yêu cầu này (chỉ được hủy yêu cầu đang chờ duyệt).";
                    }
                }
            }
        }

        $rooms = $this->bookingRepo->findAvailableRooms();
        $my_bookings = $this->bookingRepo->findBookingsByUserId($user_id);

        require_once __DIR__ . '/../View/student/TV3-quanlybooking.php';
    }
}