-- Database schema for HUIT Schedule Hub
-- Project: Quản lý và phân lịch giảng dạy đa cơ sở thông minh HUIT

CREATE DATABASE IF NOT EXISTS `huit_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `huit_db`;

-- 1. Bảng branches (Cơ sở)
CREATE TABLE IF NOT EXISTS `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_name` VARCHAR(150) NOT NULL COMMENT 'Tên cơ sở',
    `address` VARCHAR(255) NOT NULL COMMENT 'Địa chỉ cơ sở',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng teachers (Giảng viên)
CREATE TABLE IF NOT EXISTS `teachers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL COMMENT 'Họ và tên giảng viên',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email liên hệ/đăng nhập',
    `phone` VARCHAR(20) NOT NULL COMMENT 'Số điện thoại',
    `expertise` VARCHAR(255) NOT NULL COMMENT 'Chuyên môn chính',
    `password` VARCHAR(255) NOT NULL DEFAULT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' COMMENT 'Mật khẩu (mặc định: password / 123456)',
    `avatar` VARCHAR(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng users (Quản trị viên / Phòng đào tạo)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Tên đăng nhập',
    `full_name` VARCHAR(150) NOT NULL COMMENT 'Họ tên hiển thị',
    `email` VARCHAR(150) NOT NULL UNIQUE COMMENT 'Email',
    `password` VARCHAR(255) NOT NULL COMMENT 'Mật khẩu băm',
    `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'admin' COMMENT 'Vai trò',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng schedules (Lịch dạy)
CREATE TABLE IF NOT EXISTS `schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT NOT NULL COMMENT 'Mã giảng viên',
    `branch_id` INT NOT NULL COMMENT 'Mã cơ sở',
    `subject_name` VARCHAR(150) NOT NULL COMMENT 'Tên học phần',
    `room` VARCHAR(50) NOT NULL COMMENT 'Phòng học',
    `day_of_week` VARCHAR(20) NOT NULL COMMENT 'Thứ trong tuần (Thứ 2, Thứ 3...)',
    `shift_or_periods` VARCHAR(50) NOT NULL COMMENT 'Buổi hoặc Tiết học cụ thể',
    `semester` VARCHAR(50) NOT NULL DEFAULT 'Học kỳ 1' COMMENT 'Học kỳ',
    `academic_year` VARCHAR(50) NOT NULL DEFAULT '2026-2027' COMMENT 'Năm học',
    `notes` TEXT NULL COMMENT 'Ghi chú thêm',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_schedule_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_schedule_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm index để tăng tốc truy vấn lọc và kiểm tra trùng lịch
CREATE INDEX `idx_schedule_teacher_day_shift` ON `schedules` (`teacher_id`, `day_of_week`, `shift_or_periods`);
CREATE INDEX `idx_schedule_branch_room_day_shift` ON `schedules` (`branch_id`, `room`, `day_of_week`, `shift_or_periods`);
