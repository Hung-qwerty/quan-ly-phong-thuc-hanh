# HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ

## 1. Giới thiệu

Hệ thống quản lý phòng thực hành và thiết bị là một ứng dụng web hỗ trợ khoa, trường học hoặc phòng thí nghiệm trong việc quản lý phòng, thiết bị, đặt phòng, mượn thiết bị, báo hỏng và theo dõi quá trình bảo trì.

Hệ thống hướng đến việc số hóa quy trình quản lý, hạn chế trùng lịch phòng, kiểm soát tình trạng thiết bị và giúp cán bộ phòng Lab xử lý yêu cầu nhanh chóng, chính xác.

---

## 2. Bối cảnh bài toán

Trong quá trình quản lý phòng thực hành, việc sử dụng sổ sách hoặc các file bảng tính riêng lẻ có thể gây ra nhiều vấn đề:

- Khó kiểm tra phòng còn trống.
- Có thể xảy ra trùng lịch đặt phòng.
- Khó quản lý số lượng và tình trạng thiết bị.
- Khó theo dõi thiết bị hỏng hoặc đang bảo trì.
- Khó quản lý lịch sử sửa chữa.
- Việc duyệt yêu cầu còn thủ công.
- Khó thống kê tổng quan tình trạng phòng và thiết bị.

Hệ thống được xây dựng nhằm giải quyết các vấn đề trên bằng một nền tảng quản lý tập trung.

---

## 3. Mục tiêu

Hệ thống có các mục tiêu chính:

1. Quản lý thông tin phòng thực hành.
2. Quản lý loại thiết bị và thiết bị.
3. Hỗ trợ người dùng xem lịch phòng.
4. Kiểm tra phòng còn trống theo ngày và khoảng thời gian.
5. Cho phép gửi yêu cầu đặt phòng.
6. Cho phép cán bộ Lab duyệt hoặc từ chối yêu cầu.
7. Hỗ trợ báo hỏng thiết bị.
8. Theo dõi quá trình bảo trì.
9. Lưu lịch sử bảo trì thiết bị.
10. Cung cấp Dashboard thống kê.
11. Cung cấp Endpoint JSON kiểm tra phòng và thiết bị còn trống.
12. Phân quyền người dùng theo vai trò.

---

# 4. Vai trò người dùng

## 4.1. Sinh viên / Giảng viên

Người dùng có thể:

- Đăng nhập hệ thống.
- Xem lịch phòng.
- Kiểm tra phòng còn trống.
- Gửi yêu cầu đặt phòng.
- Gửi yêu cầu mượn thiết bị.
- Xem các yêu cầu của mình.
- Hủy yêu cầu của mình khi chưa bắt đầu.
- Báo hỏng thiết bị.

## 4.2. Cán bộ phòng Lab

Cán bộ Lab có thể:

- Xem và quản lý yêu cầu đặt phòng.
- Duyệt yêu cầu.
- Từ chối yêu cầu.
- Quản lý phòng.
- Quản lý loại thiết bị.
- Quản lý thiết bị.
- Xử lý báo hỏng.
- Cập nhật trạng thái thiết bị.
- Cập nhật thông tin bảo trì.
- Theo dõi lịch sử bảo trì.

## 4.3. Admin

Admin có quyền:

- Quản lý tài khoản.
- Quản lý phòng.
- Quản lý loại thiết bị.
- Quản lý thiết bị.
- Phân quyền người dùng.
- Xem Dashboard.
- Quản lý toàn bộ dữ liệu hệ thống.

---

# 5. User Story

### User Story 01
Là giảng viên, tôi muốn xem phòng còn trống theo ngày để lựa chọn phòng phù hợp cho buổi học.

### User Story 02
Là người dùng, tôi muốn gửi yêu cầu đặt phòng để sử dụng phòng thực hành.

### User Story 03
Là cán bộ Lab, tôi muốn duyệt hoặc từ chối yêu cầu để kiểm soát việc sử dụng phòng.

### User Story 04
Là người dùng, tôi muốn báo hỏng thiết bị để cán bộ Lab có thể xử lý kịp thời.

### User Story 05
Là cán bộ Lab, tôi muốn theo dõi lịch sử bảo trì để biết tình trạng sửa chữa của thiết bị.

---

# 6. Chức năng chính

## 6.1. CRUD phòng

Hệ thống hỗ trợ:

- Thêm phòng.
- Xem phòng.
- Sửa phòng.
- Xóa phòng.
- Tìm kiếm phòng.
- Lọc phòng.

Thông tin phòng gồm:

- Mã phòng.
- Tên phòng.
- Vị trí.
- Sức chứa.
- Mô tả.
- Trạng thái.

## 6.2. CRUD loại thiết bị

Hệ thống cho phép:

- Thêm loại thiết bị.
- Xem loại thiết bị.
- Sửa loại thiết bị.
- Xóa loại thiết bị.
- Tìm kiếm loại thiết bị.

Ví dụ:

- Máy tính.
- Máy chiếu.
- Máy in.
- Router.
- Switch.
- Bàn phím.
- Chuột.
- Camera.
- Micro.

## 6.3. CRUD thiết bị

Thông tin thiết bị gồm:

- Mã thiết bị.
- Tên thiết bị.
- Loại thiết bị.
- Phòng.
- Serial Number.
- Ngày nhập.
- Mô tả.
- Trạng thái.

Các trạng thái:

- Hoạt động.
- Hỏng.
- Đang bảo trì.

## 6.4. Đặt phòng

Người dùng có thể đặt phòng theo khoảng thời gian.

Thông tin yêu cầu:

- Phòng.
- Ngày sử dụng.
- Thời gian bắt đầu.
- Thời gian kết thúc.
- Mục đích sử dụng.
- Ghi chú.

Trạng thái yêu cầu:

- Chờ duyệt.
- Đã duyệt.
- Đã từ chối.
- Đã hủy.
- Đã hoàn thành.

## 6.5. Quản lý booking

Cán bộ Lab có thể:

- Xem danh sách booking.
- Tìm kiếm booking.
- Lọc theo phòng.
- Lọc theo người đặt.
- Lọc theo trạng thái.
- Lọc theo thời gian.
- Xem chi tiết.
- Duyệt booking.
- Từ chối booking.

## 6.6. Yêu cầu của tôi

Người dùng có thể xem:

- Phòng đã đặt.
- Thời gian sử dụng.
- Mục đích.
- Trạng thái.
- Ngày tạo.
- Thao tác xem/hủy.

## 6.7. Báo hỏng

Người dùng có thể báo hỏng thiết bị bằng cách:

1. Chọn thiết bị.
2. Nhập mô tả lỗi.
3. Nhập ghi chú nếu cần.
4. Gửi báo hỏng.

Trạng thái báo hỏng:

- Chờ xử lý.
- Đang xử lý.
- Đã xử lý.

## 6.8. Bảo trì thiết bị

Cán bộ Lab có thể cập nhật:

- Thiết bị.
- Ngày bắt đầu.
- Ngày kết thúc.
- Nội dung bảo trì.
- Người thực hiện.
- Chi phí.
- Kết quả.
- Ghi chú.

## 6.9. Lịch sử bảo trì

Hệ thống lưu lịch sử:

- Thiết bị.
- Ngày bảo trì.
- Nội dung.
- Kết quả.
- Người thực hiện.
- Chi phí.
- Ghi chú.

## 6.10. Lọc dữ liệu

Hệ thống hỗ trợ lọc theo:

- Phòng.
- Loại thiết bị.
- Trạng thái.
- Thời gian.
- Người đặt.
- Trạng thái booking.

---

# 7. Dashboard

Dashboard dành cho Admin và cán bộ Lab, hiển thị:

- Tổng số phòng.
- Tổng số thiết bị.
- Số thiết bị đang hoạt động.
- Số thiết bị hỏng.
- Số thiết bị đang bảo trì.
- Số booking chờ duyệt.
- Số booking đã duyệt.
- Số báo hỏng.

Ví dụ:

```text
Tổng thiết bị       : 120
Đang hoạt động      : 100
Đang hỏng           : 12
Đang bảo trì        : 8
Booking chờ duyệt   : 5
```

---

# 8. Endpoint JSON

## 8.1. Kiểm tra phòng còn trống

Endpoint:

```http
GET /api/check_room.php
```

Tham số:

```text
room_id
start
end
```

Ví dụ:

```text
/api/check_room.php?room_id=1&start=2026-08-15%2008:00&end=2026-08-15%2010:00
```

Phòng còn trống:

```json
{
    "available": true,
    "room_id": 1,
    "message": "Phòng còn trống"
}
```

Phòng không còn trống:

```json
{
    "available": false,
    "room_id": 1,
    "message": "Phòng đã được đặt trong khoảng thời gian này"
}
```

## 8.2. Kiểm tra thiết bị

Endpoint:

```http
GET /api/check_device.php
```

Ví dụ:

```text
/api/check_device.php?id=15
```

Thiết bị khả dụng:

```json
{
    "available": true,
    "device_id": 15,
    "message": "Thiết bị có thể cho mượn"
}
```

Thiết bị không khả dụng:

```json
{
    "available": false,
    "device_id": 15,
    "message": "Thiết bị đang hỏng hoặc bảo trì"
}
```

---

# 9. Quy tắc nghiệp vụ

## BR01 - Không trùng booking

Không cho phép hai booking đã được duyệt trùng thời gian trong cùng một phòng.

Ví dụ:

```text
Booking A: 08:00 - 10:00
Booking B: 09:00 - 11:00
```

Nếu cùng phòng và Booking A đã được duyệt thì Booking B không được phép duyệt.

## BR02 - Thiết bị không được cho mượn

Thiết bị có trạng thái:

```text
Hỏng
Đang bảo trì
```

không được cho mượn.

## BR03 - Hủy yêu cầu

Người dùng chỉ được hủy yêu cầu do chính mình tạo.

## BR04 - Thời điểm hủy

Người dùng chỉ được hủy yêu cầu khi thời gian sử dụng chưa bắt đầu.

## BR05 - Quyền cập nhật bảo trì

Chỉ cán bộ Lab mới được cập nhật kết quả bảo trì.

## BR06 - Kiểm tra thời gian

Thời gian kết thúc phải lớn hơn thời gian bắt đầu:

```text
end_time > start_time
```

## BR07 - Kiểm tra phòng trước khi đặt

Hệ thống phải kiểm tra tình trạng phòng trước khi tạo hoặc duyệt booking.

---

# 10. Danh sách màn hình

## Người dùng

1. Đăng nhập.
2. Lịch phòng.
3. Form đặt phòng.
4. Yêu cầu của tôi.
5. Danh sách thiết bị.
6. Báo hỏng.

## Cán bộ Lab

7. Dashboard.
8. Quản lý booking.
9. Danh sách thiết bị.
10. Quản lý phòng.
11. Báo hỏng.
12. Lịch sử bảo trì.

## Admin

13. Dashboard.
14. Quản lý tài khoản.
15. Phân quyền.
16. Quản lý phòng.
17. Quản lý loại thiết bị.
18. Quản lý thiết bị.

---

# 11. Phân quyền

| Chức năng | Sinh viên/Giảng viên | Cán bộ Lab | Admin |
|---|:---:|:---:|:---:|
| Xem lịch phòng | ✅ | ✅ | ✅ |
| Đặt phòng | ✅ | ✅ | ✅ |
| Hủy booking của mình | ✅ | ✅ | ✅ |
| Duyệt booking | ❌ | ✅ | ✅ |
| Từ chối booking | ❌ | ✅ | ✅ |
| Quản lý phòng | ❌ | ✅ | ✅ |
| Quản lý loại thiết bị | ❌ | ✅ | ✅ |
| Quản lý thiết bị | ❌ | ✅ | ✅ |
| Báo hỏng | ✅ | ✅ | ✅ |
| Xử lý báo hỏng | ❌ | ✅ | ✅ |
| Cập nhật bảo trì | ❌ | ✅ | ✅ |
| Xem lịch sử bảo trì | ❌ | ✅ | ✅ |
| Quản lý tài khoản | ❌ | ❌ | ✅ |
| Phân quyền | ❌ | ❌ | ✅ |
| Dashboard | ❌ | ✅ | ✅ |

---

# 12. Cơ sở dữ liệu

Hệ thống dự kiến gồm các bảng:

```text
roles
users
rooms
device_types
devices
bookings
damage_reports
maintenance_history
```

## Quan hệ tổng quát

```text
ROLES
  |
  +---- USERS
          |
          +---- BOOKINGS -------- ROOMS
          |
          +---- DAMAGE_REPORTS -- DEVICES
                                   |
                                   +---- DEVICE_TYPES
                                   |
                                   +---- MAINTENANCE_HISTORY
```

---

# 13. Công nghệ sử dụng

## Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap
- Font Awesome

## Backend

- PHP
- PHP Session
- REST API / JSON

## Database

- MySQL
- phpMyAdmin

## Môi trường

- Visual Studio Code
- XAMPP
- Apache
- MySQL
- Git
- GitHub

---

# 14. Cấu trúc thư mục

```text
quanly-phong-lab/
│
├── config/
│   └── database.php
│
├── public/
│   ├── index.php
│   ├── login.php
│   └── logout.php
│
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── rooms.php
│   ├── device_types.php
│   └── devices.php
│
├── lab/
│   ├── dashboard.php
│   ├── bookings.php
│   ├── damage_reports.php
│   └── maintenance.php
│
├── user/
│   ├── room_schedule.php
│   ├── booking.php
│   ├── my_requests.php
│   └── report_damage.php
│
├── api/
│   ├── check_room.php
│   └── check_device.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│
├── database/
│   └── database.sql
│
├── .gitignore
└── README.md
```

---

# 15. Hướng dẫn cài đặt

## Bước 1: Cài đặt XAMPP

Cài XAMPP và khởi động:

```text
Apache
MySQL
```

## Bước 2: Đưa project vào XAMPP

Copy project vào:

```text
C:\xampp\htdocs\
```

Ví dụ:

```text
C:\xampp\htdocs\quanly-phong-lab
```

## Bước 3: Tạo database

Truy cập:

```text
http://localhost/phpmyadmin
```

Tạo database:

```text
quanly_phong_lab
```

Sau đó import:

```text
database/database.sql
```

## Bước 4: Cấu hình kết nối

Mở:

```text
config/database.php
```

Ví dụ:

```php
<?php

$host = "localhost";
$dbname = "quanly_phong_lab";
$username = "root";
$password = "";

$conn = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password
);

$conn->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);
```

Nếu MySQL có mật khẩu, thay đổi:

```php
$password = "your_password";
```

## Bước 5: Chạy hệ thống

Mở trình duyệt:

```text
http://localhost/quanly-phong-lab/
```

---

# 16. Tài khoản demo

Có thể tạo các tài khoản mẫu:

### Admin

```text
Username: admin
Password: admin123
Role: Admin
```

### Cán bộ Lab

```text
Username: lab01
Password: lab123
Role: Cán bộ Lab
```

### Giảng viên

```text
Username: giangvien01
Password: 123456
Role: Giảng viên
```

### Sinh viên

```text
Username: sinhvien01
Password: 123456
Role: Sinh viên
```

Trong môi trường thực tế, mật khẩu phải được mã hóa bằng `password_hash()` và kiểm tra bằng `password_verify()`.

---

# 17. Kiểm thử

## Test Case 01 - Đăng nhập

**Input:**

```text
admin / admin123
```

**Kết quả mong đợi:**

```text
Đăng nhập thành công
Chuyển đến Dashboard
```

## Test Case 02 - Đặt phòng

Người dùng chọn:

```text
Phòng: P101
Thời gian: 08:00 - 10:00
```

Nếu phòng còn trống:

```text
Tạo booking thành công
Trạng thái: Chờ duyệt
```

## Test Case 03 - Trùng phòng

Đã có:

```text
P101
08:00 - 10:00
Đã duyệt
```

Tạo yêu cầu:

```text
P101
09:00 - 11:00
```

Kết quả:

```text
Không thể đặt phòng.
Phòng đã có lịch trong khoảng thời gian này.
```

## Test Case 04 - Thiết bị hỏng

Thiết bị:

```text
Máy chiếu P01
Trạng thái: Hỏng
```

Kết quả:

```text
Không thể mượn thiết bị.
Thiết bị hiện không khả dụng.
```

## Test Case 05 - Hủy booking

Người dùng hủy booking của chính mình trước thời gian bắt đầu.

Kết quả:

```text
Hủy booking thành công.
```

## Test Case 06 - Bảo trì

Cán bộ Lab cập nhật:

```text
Thiết bị: Máy chiếu P01
Kết quả: Đã sửa xong
Trạng thái: Hoạt động
```

Kết quả:

```text
Cập nhật thành công.
Lịch sử bảo trì được lưu.
```

---

# 18. Bảo mật

Hệ thống cần đảm bảo:

- Sử dụng Session để quản lý đăng nhập.
- Phân quyền theo Role.
- Kiểm tra quyền truy cập từng trang.
- Sử dụng PDO/Prepared Statement.
- Không lưu mật khẩu dạng văn bản thuần.
- Sử dụng `password_hash()` để mã hóa mật khẩu.
- Kiểm tra dữ liệu đầu vào.
- Kiểm tra quyền trước khi sửa/xóa.
- Kiểm tra trùng booking ở phía server.
- Kiểm tra thời gian bắt đầu/kết thúc ở phía server.

---

# 19. Kiến trúc hệ thống

```text
                   NGƯỜI DÙNG
                       |
                       v
              +------------------+
              |    FRONTEND      |
              | HTML/CSS/JS      |
              +--------+---------+
                       |
                       v
              +------------------+
              |      PHP         |
              |     BACKEND      |
              +--------+---------+
                       |
             +---------+---------+
             |                   |
             v                   v
      +-------------+      +-------------+
      | JSON API    |      |   SESSION   |
      +------+------+      +-------------+
             |
             v
      +-------------+
      |    MySQL    |
      +-------------+
```

---

# 20. Các yêu cầu bắt buộc đã đáp ứng

| Yêu cầu | Trạng thái |
|---|:---:|
| CRUD phòng | ✅ |
| CRUD loại thiết bị | ✅ |
| CRUD thiết bị | ✅ |
| Đặt phòng theo khoảng thời gian | ✅ |
| Duyệt yêu cầu | ✅ |
| Từ chối yêu cầu | ✅ |
| Báo hỏng | ✅ |
| Cập nhật trạng thái bảo trì | ✅ |
| Lưu lịch sử bảo trì | ✅ |
| Lọc theo phòng | ✅ |
| Lọc theo loại thiết bị | ✅ |
| Lọc theo trạng thái | ✅ |
| Lọc theo thời gian | ✅ |
| Endpoint JSON kiểm tra phòng | ✅ |
| Endpoint JSON kiểm tra thiết bị | ✅ |
| Dashboard | ✅ |
| Phân quyền | ✅ |
| Lịch phòng | ✅ |
| Form đặt phòng | ✅ |
| Yêu cầu của tôi | ✅ |
| Quản lý booking | ✅ |
| Danh sách thiết bị | ✅ |
| Báo hỏng | ✅ |
| Lịch sử bảo trì | ✅ |

---

# 21. Hướng phát triển

Trong tương lai có thể mở rộng:

- Quản lý mượn và trả thiết bị.
- Gửi email khi booking được duyệt.
- Gửi thông báo khi thiết bị sửa xong.
- QR Code cho từng thiết bị.
- Quét QR Code để xem thông tin thiết bị.
- Upload hình ảnh thiết bị hỏng.
- Upload biên bản bảo trì.
- Xuất báo cáo Excel/PDF.
- Thống kê chi phí bảo trì.
- Quản lý chi tiết lịch sử mượn thiết bị.
- Responsive trên điện thoại.
- Triển khai hệ thống lên Internet.
- Tích hợp biểu đồ thống kê nâng cao.

---

# 22. Thông tin dự án

**Tên đề tài:** Hệ thống quản lý phòng thực hành và thiết bị

**Loại:** Web Application

**Lĩnh vực:** Quản lý phòng thực hành và thiết bị

**Frontend:** HTML5, CSS3, JavaScript, Bootstrap

**Backend:** PHP

**Database:** MySQL

**Web Server:** Apache / XAMPP

**IDE:** Visual Studio Code

**Mục đích:** Học tập, nghiên cứu và xây dựng hệ thống quản lý phòng Lab.

---

# 23. License

Project được xây dựng với mục đích học tập và nghiên cứu.

Có thể sử dụng, chỉnh sửa và mở rộng cho mục đích học tập cá nhân.
