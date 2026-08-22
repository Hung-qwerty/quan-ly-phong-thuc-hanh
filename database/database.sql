-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 18, 2026 lúc 08:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET foreign_key_checks = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quan_ly_phong_thuc_hanh`
--

-- --------------------------------------------------------

--
-- Xóa bảng cũ theo thứ tự an toàn (tránh lỗi khóa ngoại) trước khi tạo mới
--
DROP TABLE IF EXISTS `maintenance_history`;
DROP TABLE IF EXISTS `maintenance`;
DROP TABLE IF EXISTS `device_reports`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `devices`;
DROP TABLE IF EXISTS `device_types`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------

--
-- 1. TV1: Bảng `users` (Quản lý tài khoản & phân quyền)
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('student','staff','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 2. TV3 (Dùng chung): Bảng `rooms` (Quản lý phòng thực hành)
--
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `room_code` varchar(50) NOT NULL UNIQUE,
  `room_name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` enum('available','booked','maintenance') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 3. TV4: Bảng `device_types` (Loại thiết bị)
--
CREATE TABLE `device_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 4. TV4: Bảng `devices` (Thiết bị trong phòng)
--
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_code` varchar(50) NOT NULL UNIQUE,
  `device_name` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `status` enum('active','broken','maintenance') NOT NULL DEFAULT 'active',
  CONSTRAINT `fk_devices_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_devices_type` FOREIGN KEY (`type_id`) REFERENCES `device_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 5. TV2 & TV3 (Dùng chung): Bảng `bookings` (Quản lý đặt phòng / Lịch tổng quan)
--
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 6. TV2: Bảng `device_reports` (Báo cáo sự cố thiết bị)
--
CREATE TABLE `device_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('reported','processing','resolved') NOT NULL DEFAULT 'reported',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_reports_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 7. TV5: Bảng `maintenance` (Quản lý bảo trì thiết bị)
--
CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('fixing','completed') NOT NULL DEFAULT 'fixing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  CONSTRAINT `fk_maint_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_maint_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 8. TV5: Bảng `maintenance_history` (Lịch sử bảo trì chi tiết)
--
CREATE TABLE `maintenance_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `maintenance_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `maintenance_date` date NOT NULL,
  `content` text NOT NULL,
  `result` text NOT NULL,
  CONSTRAINT `fk_history_maint` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_staff` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Dữ liệu mẫu (Seed Data) ban đầu cho hệ thống
--

INSERT INTO `rooms` (`id`, `room_code`, `room_name`, `capacity`, `status`) VALUES
(1, 'P101', 'Phòng Lab Lập Trình Web', 40, 'available'),
(2, 'P102', 'Phòng Lab Cơ Sở Dữ Liệu', 35, 'available'),
(3, 'P103', 'Phòng Lab Nhúng & IoT', 30, 'maintenance');

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản Trị Viên', 'admin'),
(2, 'staff01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cán Bộ Lab Nguyễn Văn A', 'staff'),
(3, 'student01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sinh Viên Trần Văn B', 'student');

SET foreign_key_checks = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;