# Quản lý Phòng thực hành và Thiết bị MVC Demo

Hệ demo dùng trong học phần Lập trình Web.

## 1. Tên đề tài
* **Hệ thống Quản lý Phòng thực hành và Thiết bị**

## 2. Mục tiêu

Quản lý Phòng thực hành và Thiết bị là dự án được dùng để minh họa:

- MVC / Repository / Front Controller.
- PDO + prepared statement.
- Bài 7: Authentication, password hashing, Authorization, Session, Role, Ownership, CSRF.
- Bài 8: JSON endpoint, Fetch API, async/await, debounce, cập nhật DOM bằng `textContent`.

## 3. Cấu trúc

```text
quan-ly-phong-thuc-hanh/
├── composer.json
├── install.php               ← File cài đặt tự động CSDL
├── config/
│   └── database.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── public/
│   ├── index.php
│   └── assets/
└── src/
    ├── Core/
    │   └── Database.php
    ├── Controller/
    │   ├── ApiDeviceController.php
    │   ├── DeviceController.php
    │   ├── MaintenanceController.php
    │   ├── RoomBookingController.php
    │   └── UserController.php
    ├── Repository/
    │   ├── DeviceRepository.php
    │   ├── MaintenanceRepository.php
    │   ├── RoomBookingRepository.php
    │   └── UserRepository.php
    └── View/
        ├── admin/
        │   └── quan_ly_user.php
        ├── auth/
        │   ├── login.php
        │   └── register.php
        ├── staff/
        │   ├── bao_tri.php
        │   └── themthietbitv4.php
        └── student/
            └── roombooking.php
```

## 4. Các đối tượng dữ liệu chính
* **Users (Người dùng):** Quản lý thông tin tài khoản và phân quyền (Admin, Cán bộ Lab, Sinh viên).
* **Rooms (Phòng thực hành):** Quản lý danh sách và thông tin phòng lab.
* **Device_Types (Loại thiết bị):** Phân nhóm thiết bị.
* **Devices (Thiết bị):** Quản lý chi tiết từng thiết bị và trạng thái.
* **Bookings (Đặt phòng):** Lưu trữ lịch sử đăng ký phòng và mượn thiết bị.
* **Maintenances (Bảo trì):** Ghi nhận lịch sử xử lý thiết bị hỏng.

## 5. Sơ đồ ERD Cơ sở dữ liệu
![Sơ đồ ERD hệ thống](./erd.png)

## 6. Cài đặt nhanh trên WAMP / XAMPP

Hệ thống được tích hợp sẵn file cài đặt tự động (`install.php`), giúp khởi tạo CSDL mà không cần import thủ công qua phpMyAdmin.

1. Chép thư mục dự án vào:

```text
C:\wamp64\www\quan-ly-phong-thuc-hanh
```
*(hoặc `C:\xampp\htdocs\quan-ly-phong-thuc-hanh` nếu dùng XAMPP)*

2. Mở terminal tại thư mục project và cập nhật nạp lớp PSR-4:

```bash
composer dump-autoload
```

3. Mở trình duyệt và chạy công cụ cài đặt tự động:

```text
http://localhost/quan-ly-phong-thuc-hanh/install.php
```
*(Hệ thống sẽ tự động tạo database `quan_ly_phong_thuc_hanh`, nạp các bảng và dữ liệu mẫu tiếng Việt).*

4. Sau khi cài đặt xong, truy cập hệ thống tại:

```text
http://localhost/quan-ly-phong-thuc-hanh/public/index.php
```

*Lưu ý: Nếu mật khẩu MySQL root của WAMP/XAMPP không rỗng, vui lòng sửa thông tin tại `config/database.php` (hoặc `src/Core/Database.php`) trước khi chạy file install.*

## 7. Tài khoản demo

```text
Admin
admin
123

Cán bộ Lab (Staff)
staff
123

Sinh viên (Student)
sv01
123
```

## 8. Demo Bài 7

### 8.0 Tạo người dùng và minh họa password_hash()

Đăng nhập admin rồi mở:

```text
?route=users&tab=internal
```

Chọn **Thêm mới**. Form gửi mật khẩu rõ qua POST trong request hiện tại; Controller không lưu trực tiếp mật khẩu đó mà tạo hash:

```php
$hashed_pass = password_hash($password_raw, PASSWORD_DEFAULT);
```

Sau đó Repository chỉ INSERT `$hashed_pass` vào cột `users.password`.

Demo trên lớp:

1. Mở phpMyAdmin → bảng `users` trước khi tạo user.
2. Tạo user mới với mật khẩu ví dụ `123456`.
3. Refresh bảng `users`: cột `password` chứa chuỗi hash, không chứa `123456`.
4. Logout và đăng nhập bằng user mới. `login.php` dùng:

```php
password_verify($password, $user['password'])
```

Lưu ý thuật ngữ: đây là **băm mật khẩu (hashing)**, không phải mã hóa có thể giải mã ngược.

### 8.1 Authentication

Mở:

```text
?route=login
```

Điểm cần chỉ ra:

- User được lấy bằng username qua prepared statement.
- Mật khẩu được kiểm tra bằng `password_verify()`.
- Sau đăng nhập gọi `session_regenerate_id(true)`.
- Session chỉ lưu `user_id`, `role`, `full_name`, không lưu password/hash.

### 8.2 Authorization theo role

Đăng nhập student rồi nhập trực tiếp URL:

```text
?page=devices
```
hoặc:
```text
?route=users
```

Kết quả mong đợi: HTTP 403.

Sau đó đăng nhập staff hoặc admin và truy cập lại.

### 8.3 CSRF

Mở:

```text
?page=devices
```

Form xóa/sửa/thêm có hidden `csrf_token`:

```html
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

Xóa token trong DevTools rồi submit:
server phải trả 419.

### 8.4 Ownership

Đăng nhập student:

```text
?route=bookings&page=mybookings
```

- Thử gửi request hủy đơn đặt phòng của một user khác. Repository dùng cả `booking_id` và `user_id` nên không cho phép hủy chéo trái quyền.
- Mở `?route=bookings&page=report`, dropdown danh sách thiết bị chỉ hiển thị các máy nằm trong phòng mà sinh viên đã có lịch được duyệt (`status = 'approved'`), không cho phép báo hỏng thiết bị phòng khác.

## 9. Demo Bài 8

### 9.1 JSON endpoint

Mở trực tiếp:

```text
?route=api_devices&q=TB
```

Quan sát:

- `Content-Type: application/json; charset=utf-8`.
- JSON chỉ trả dữ liệu thiết bị cần thiết.
- Không có `password` hay thông tin nhạy cảm.

### 9.2 Fetch API

Vào:

```text
?page=devices
```

Gõ từ khóa vào ô tìm kiếm thiết bị.

Mở DevTools → Network và quan sát request:

```text
index.php?route=api_devices&q=...
```

Điểm cần chỉ ra:

- `fetch()`
- `response.ok`
- `await response.json()`
- debounce 300 ms
- cập nhật DOM bằng `textContent` (chống tấn công XSS)

## 10. Luồng MVC minh họa

```text
Browser
  ↓
public/index.php          ← Front Controller / Router
  ↓
Controller                ← đọc request, kiểm tra quyền (403), kiểm tra CSRF (419), chọn luồng
  ↓
Repository                ← PDO / SQL (Prepared Statements)
  ↓
MySQL
  ↓
Repository                ← fetch / fetchAll
  ↓
Controller                ← chuẩn bị dữ liệu
  ↓
View                      ← HTML

Bài 8:
Browser → Fetch → ApiDeviceController → DeviceRepository → MySQL → JSON
```

## 11. Danh sách thành viên và phân công

* **Nguyễn Việt Hùng (TV1):** Thiết kế Cơ sở dữ liệu, cấu hình kết nối chung (`config/database.php`) và module Quản lý Tài khoản (`pages/admin/quan_ly_user.php`).
* **Vũ Minh Đức (TV2):** Phụ trách phân hệ Quản lý phòng thực hành (`pages/student/TV2-quanlyphongthuchanh.php`).
* **Ôn Ngọc Phi (TV3):** Phụ trách phân hệ Quản lý booking (`pages/student/TV3-quanlybooking.php`).
* **Đặng Đình Thái An (TV4):** Phụ trách phân hệ Quản lý thiết bị (`pages/staff/themthietbitv4.php`).
* **Trương Văn Minh (TV5):** Phụ trách phân hệ Báo hỏng và bảo trì (`pages/staff/formbaotritv5.php`).