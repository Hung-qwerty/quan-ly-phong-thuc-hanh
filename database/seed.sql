USE quan_ly_phong_thuc_hanh;
INSERT INTO users (id, username, password, full_name, role, status) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị viên hệ thống', 'admin', 'active'),
(2, 'staff01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cán bộ phòng Lab', 'staff', 'active'),
(3, 'student01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Sinh Viên', 'student', 'active');

INSERT INTO rooms (id, room_code, room_name, capacity, status) VALUES
(1, 'LAB201', 'Phòng Thực hành Mạng Máy Tính', 30, 'available'),
(2, 'LAB302', 'Phòng Thực hành Lập trình nhúng', 25, 'available');

INSERT INTO device_types (id, name) VALUES
(1, 'Máy tính để bàn (PC)'),
(2, 'Máy chiếu (Projector)');

INSERT INTO devices (id, device_code, device_name, room_id, type_id, status) VALUES
(1, 'PC01', 'Máy tính Dell Core i5', 1, 1, 'active'),
(2, 'PJ01', 'Máy chiếu Sony 4K', 2, 2, 'active');

INSERT INTO bookings (user_id, room_id, start_time, end_time, purpose, status) VALUES
(3, 1, '2026-06-01 08:00:00', '2026-06-01 10:00:00', 'Làm bài tập lớn môn Mạng máy tính', 'approved');