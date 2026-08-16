# HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ

## 1. Tên đề tài
* **Hệ thống Quản lý Phòng thực hành và Thiết bị**

## 2. Danh sách thành viên và phân công
* **Nguyễn Việt Hùng (TV1):** Thiết kế Cơ sở dữ liệu, cấu hình kết nối chung (`config/database.php`) và module Quản lý Tài khoản (`pages/admin/quan_ly_user.php`).
* **Vũ Minh Đức (TV2):** Phụ trách phân hệ Quản lý phòng thực hành (`pages/student/TV2-quanlyphongthuchanh.php`).
* **Th Thành viên 3 (TV3):** Phụ trách phân hệ Quản lý loại thiết bị.
* **Đặng Đình Thái An (TV4):** Phụ trách phân hệ Quản lý thiết bị (`pages/staff/themthietbitv4.php`).
* **Trương Văn Minh (TV5):** Phụ trách phân hệ Báo hỏng và bảo trì (`pages/staff/formbaotritv5.php`).

## 3. Các đối tượng dữ liệu chính
* **Users (Người dùng):** Quản lý thông tin tài khoản và phân quyền (Admin, Cán bộ Lab, Sinh viên).
* **Rooms (Phòng thực hành):** Quản lý danh sách và thông tin phòng lab.
* **Device_Types (Loại thiết bị):** Phân nhóm thiết bị.
* **Devices (Thiết bị):** Quản lý chi tiết từng thiết bị và trạng thái.
* **Bookings (Đặt phòng):** Lưu trữ lịch sử đăng ký phòng và mượn thiết bị.
* **Maintenances (Bảo trì):** Ghi nhận lịch sử xử lý thiết bị hỏng.

## 4. Các chức năng dự kiến
* Quản lý phòng thực hành, loại thiết bị và thiết bị chi tiết.
* Đặt lịch phòng theo khoảng thời gian và duyệt/từ chối yêu cầu.
* Báo hỏng thiết bị và cập nhật quy trình bảo trì.
* Tìm kiếm, lọc dữ liệu và kiểm tra phòng trống thông qua API JSON.
* Phân quyền truy cập hệ thống theo 3 cấp độ (Admin, Cán bộ Lab, Sinh viên).

## 5. Các chức năng đã thực hiện đến hết Buổi 2
* **Kiến trúc hệ thống:** Xây dựng khung thư mục dự án chuẩn và file kết nối cơ sở dữ liệu chung (`config/database.php`).
* **Cơ sở dữ liệu:** Hoàn thiện sơ đồ thiết kế và tạo file `database/database.sql`.
* **Tích hợp bài cá nhân:** Đã đưa các sản phẩm mã nguồn cá nhân của các thành viên vào đúng thư mục phân quyền:
  * Module Quản lý User của TV1 (`pages/admin/quan_ly_user.php`).
  * Module Quản lý phòng của TV2 (`pages/student/TV2-quanlyphongthuchanh.php`).
  * Module Quản lý thiết bị của TV4 (`pages/staff/themthietbitv4.php`).
  * Module Báo hỏng của TV5 (`pages/staff/formbaotritv5.php`).