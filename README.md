# HỆ THỐNG QUẢN LÝ PHÒNG THỰC HÀNH VÀ THIẾT BỊ

## 1. Giới thiệu

Hệ thống quản lý phòng thực hành và thiết bị hỗ trợ khoa/phòng thí nghiệm trong việc:

- Quản lý phòng thực hành.
- Quản lý loại thiết bị và thiết bị.
- Đặt phòng theo khoảng thời gian.
- Gửi và duyệt yêu cầu đặt phòng.
- Báo hỏng thiết bị.
- Theo dõi quá trình và lịch sử bảo trì.
- Kiểm tra phòng/thiết bị còn trống qua API JSON.
- Theo dõi tình trạng thiết bị trên Dashboard.

## 2. Vai trò người dùng

### Sinh viên / Giảng viên
- Xem lịch phòng.
- Kiểm tra phòng còn trống.
- Gửi yêu cầu đặt phòng.
- Gửi yêu cầu mượn thiết bị.
- Xem và hủy yêu cầu của mình khi chưa bắt đầu.
- Báo hỏng thiết bị.

### Cán bộ phòng Lab
- Quản lý và cập nhật thiết bị.
- Duyệt hoặc từ chối booking.
- Xử lý báo hỏng.
- Cập nhật trạng thái bảo trì.
- Xem lịch sử bảo trì.

### Admin
- Quản lý tài khoản.
- Quản lý phòng.
- Quản lý loại thiết bị.
- Quản lý thiết bị.
- Quản lý phân quyền.
- Theo dõi Dashboard.

## 3. Chức năng chính

- **Quản lý phòng:** Thêm, sửa, xóa, xem danh sách phòng.
- **Quản lý thiết bị:** Quản lý loại thiết bị, thông tin và trạng thái thiết bị.
- **Quản lý Booking:** Đặt phòng theo ngày và khoảng thời gian, duyệt/từ chối yêu cầu.
- **Báo hỏng:** Người dùng gửi thông tin thiết bị bị lỗi.
- **Bảo trì:** Cán bộ Lab cập nhật kết quả và lịch sử bảo trì.
- **Tìm kiếm & lọc:** Lọc theo phòng, loại thiết bị, trạng thái và thời gian.
- **API JSON:** Kiểm tra phòng hoặc thiết bị còn trống.
- **Dashboard:** Thống kê thiết bị đang hoạt động, bị hỏng và đang bảo trì.

## 4. Quy tắc nghiệp vụ

- Không cho phép hai booking **đã duyệt** trùng thời gian trong cùng một phòng.
- Thiết bị **hỏng** hoặc **đang bảo trì** không được cho mượn.
- Người dùng chỉ được hủy booking của chính mình khi chưa bắt đầu.
- Chỉ cán bộ Lab mới được cập nhật kết quả bảo trì.
- Thời gian kết thúc phải lớn hơn thời gian bắt đầu.

## 5. Các màn hình chính

- Đăng nhập
- Dashboard
- Lịch phòng
- Form đặt phòng
- Yêu cầu của tôi
- Quản lý Booking
- Danh sách thiết bị
- Quản lý phòng
- Báo hỏng thiết bị
- Lịch sử bảo trì
- Quản lý tài khoản và phân quyền

## 6. Công nghệ sử dụng

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** Apache (XAMPP)
- **IDE:** Visual Studio Code
- **API:** JSON

## 7. Cấu trúc thư mục

```text
quanly-phong-lab/
│
├── config/
│   └── database.php
├── admin/
├── lab/
├── user/
├── api/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── database/
│   └── database.sql
├── index.php
├── login.php
└── README.md
```

## 8. Cách cài đặt và chạy

### Bước 1: Khởi động XAMPP

Mở **XAMPP Control Panel** và khởi động:

```text
Apache
MySQL
```

### Bước 2: Đưa project vào XAMPP

Copy thư mục project vào:

```text
C:\xampp\htdocs\
```

Ví dụ:

```text
C:\xampp\htdocs\quanly-phong-lab
```

### Bước 3: Tạo cơ sở dữ liệu

Mở trình duyệt:

```text
http://localhost/phpmyadmin
```

Tạo database:

```text
quanly_phong_lab
```

Sau đó chọn **Import** và import file:

```text
database/database.sql
```

### Bước 4: Cấu hình database

Mở file:

```text
config/database.php
```

Kiểm tra:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "quanly_phong_lab";
```

Nếu MySQL có mật khẩu, thay đổi biến `$password`.

### Bước 5: Chạy project

Mở trình duyệt và truy cập:

```text
http://localhost/quanly-phong-lab/
```

Hoặc:

```text
http://localhost/quanly-phong-lab/login.php
```

## 9. API kiểm tra phòng/thiết bị

API trả về dữ liệu JSON để kiểm tra tình trạng phòng hoặc thiết bị.

Ví dụ:

```text
/api/check_room.php
/api/check_device.php
```

Kết quả mẫu:

```json
{
    "available": true,
    "message": "Phòng còn trống"
}
```

## 10. Lưu ý

- Phải bật **Apache** và **MySQL** trước khi chạy.
- Tên database trong `database.php` phải trùng với database trong phpMyAdmin.
- Project phải nằm trong thư mục `htdocs`.
- Nếu thay đổi cổng Apache hoặc MySQL, cần cập nhật lại cấu hình kết nối.

## 11. Mục tiêu

Hệ thống giúp quản lý tập trung phòng thực hành và thiết bị, hạn chế trùng lịch đặt phòng, kiểm soát thiết bị hỏng/bảo trì và hỗ trợ cán bộ Lab theo dõi quá trình sử dụng thiết bị.
