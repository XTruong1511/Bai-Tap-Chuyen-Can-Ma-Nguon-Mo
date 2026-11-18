# HUIT Schedule Hub (Hệ thống Quản lý và Phân lịch Giảng dạy Đa cơ sở Thông minh)

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![CI](https://github.com/huit-opensource/huit-schedule-hub/actions/workflows/php-lint.yml/badge.svg)](.github/workflows/php-lint.yml)
[![Database](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://www.mysql.com/)
[![Framework](https://img.shields.io/badge/Architecture-Custom%20MVC-success.svg)](#kiến-trúc-dự-án)

Ứng dụng web mã nguồn mở được phát triển dành cho **Trường Đại học Công Thương TP. Hồ Chí Minh (HUIT)** nhằm giải quyết bài toán phân công lịch giảng dạy đa cơ sở, tối ưu hóa thời gian di chuyển của giảng viên giữa các cơ sở, phát hiện xung đột lịch tự động và cung cấp cổng thông tin tra cứu lịch biểu cá nhân trực tuyến.

---

## 📌 Mục Tiêu Bài Toán & Điểm Nổi Bật

Trong môi trường giáo dục đại học đa cơ sở, giảng viên thường xuyên phải di chuyển giảng dạy giữa nhiều địa điểm khác nhau (Cơ sở chính Lê Trọng Tấn, Cơ sở Tân Kỳ Tân Quý, Cơ sở Nguyễn Đỗ Cung, Trung tâm Thí nghiệm - Thực hành Chế Lan Viên). **HUIT Schedule Hub** mang lại:

1. **Phân lịch theo tiết trống & tối ưu hóa di chuyển:**
   - Ma trận thời khóa biểu tuần trực quan (Grid View) giúp phát hiện ngay các "tiết trống" và "khoảng trống chết" giữa các ca học.
   - Thuật toán tự động phát hiện và cảnh báo khi giảng viên có lịch dạy ở nhiều cơ sở khác nhau trong cùng một ngày, giúp người quản trị phòng đào tạo sắp xếp khoảng cách thời gian hợp lý tránh tình trạng trễ giờ lên lớp.
2. **Gợi ý Giảng viên Thông minh (Smart Advisor):**
   - Khi tạo mới một lịch học phần, hệ thống phân tích tên môn học và so khớp từ khóa với lĩnh vực chuyên môn (`expertise`) của từng giảng viên.
   - Tự động kiểm tra thời gian thực để lọc bỏ các giảng viên đã bận giờ đó.
   - Ưu tiên các giảng viên đã có lịch cùng cơ sở trong ngày (tối ưu đi lại) và cân bằng tải số tiết trong tuần.
3. **Kiểm tra Xung đột Thời gian thực (Real-time Conflict Detection):**
   - Ngăn chặn triệt để tình trạng 1 giảng viên bị xếp trùng 2 lớp cùng 1 thời điểm.
   - Ngăn chặn tình trạng 1 phòng học bị xếp cho 2 lớp cùng ca.
4. **Cổng thông tin Giảng viên (Teacher Portal):**
   - Giảng viên có tài khoản cá nhân đăng nhập để tra cứu lịch dạy theo tuần/tháng.
   - Thẻ chỉ dẫn cơ sở trực quan: Nắm bắt chính xác hôm nay mình cần đến cơ sở nào, địa chỉ ở đâu, phòng học số mấy.
   - Hỗ trợ xem trên thiết bị di động và in thời khóa biểu (Print View) định dạng A4 sạch sẽ.
5. **Thống kê & Biểu đồ Phân bổ (Interactive Analytics):**
   - Biểu đồ Doughnut tỉ lệ ca học theo từng cơ sở (Chart.js).
   - Biểu đồ Bar phân bổ mật độ tiết theo từng thứ trong tuần (Thứ 2 đến Chủ Nhật).
   - Bộ lọc nhanh theo Cơ sở hoặc Giảng viên.

---

## 🛠 Công Nghệ Sử Dụng

- **Ngôn ngữ:** PHP 8.0+ (PHP 8.2 / 8.5 tương thích hoàn toàn, chuẩn PSR-4, PDO Prepared Statements).
- **Cơ sở dữ liệu:** MySQL (InnoDB, UTF-8 Multilingual `utf8mb4_unicode_ci`).
- **Giao diện:** Bootstrap 5.3.3, FontAwesome 6, Chart.js, SweetAlert2.
- **Thư viện bên ngoài (Composer / Packagist):**
  - `vlucas/phpdotenv` (^5.8): Quản lý an toàn biến môi trường và kết nối cơ sở dữ liệu.
  - `fakerphp/faker` (^1.24): Giả lập dữ liệu mẫu chuẩn tiếng Việt cho giảng viên, cơ sở và lịch học.
- **CI/CD:** GitHub Actions tự động kiểm tra cú pháp mã nguồn (`php -l`) mỗi khi push mã.

---

## 📂 Cấu Trúc Mã Nguồn

```
huit-schedule/
├── .github/
│   └── workflows/
│       └── php-lint.yml        # CI/CD GitHub Actions kiểm tra cú pháp PHP tự động
├── config/                     # Thiết lập ứng dụng
├── database/
│   ├── schema.sql              # Cấu trúc CSDL chuẩn MySQL
│   ├── seed.sql                # Dữ liệu mẫu dạng SQL dump có sẵn
│   ├── migrate.php             # Script tự động tạo CSDL từ schema.sql
│   └── seeder.php              # Script sinh dữ liệu phong phú bằng Faker
├── src/
│   ├── Config/
│   │   └── Database.php        # Kết nối PDO Singleton an toàn với .env
│   ├── Controllers/
│   │   ├── ApiController.php   # API gợi ý thông minh & kiểm tra xung đột thời gian thực
│   │   ├── AuthController.php  # Xác thực đăng nhập Admin/Staff & Giảng viên
│   │   ├── BranchController.php# Quản lý cơ sở đào tạo HUIT
│   │   ├── DashboardController.php # Thống kê biểu đồ & phân tích đa cơ sở
│   │   ├── ScheduleController.php  # CRUD lịch dạy & ma trận thời khóa biểu
│   │   └── TeacherController.php   # Quản lý giảng viên & Cổng thông tin GV
│   ├── Helpers/
│   │   └── Auth.php            # Quản lý Session, Flash Message & Phân quyền
│   └── Models/
│       ├── Branch.php          # Mô hình dữ liệu Cơ sở
│       ├── Schedule.php        # Mô hình Lịch dạy & Thuật toán gợi ý/xung đột
│       └── Teacher.php         # Mô hình dữ liệu Giảng viên
├── tests/
│   └── test_logic.php          # Kiểm thử tự động thuật toán gợi ý & kiểm tra trùng lịch
├── views/
│   ├── auth/login.php          # Giao diện đăng nhập hệ thống
│   ├── branches/               # Danh sách, thêm, sửa cơ sở
│   ├── dashboard/index.php     # Dashboard tổng quan & biểu đồ Chart.js
│   ├── layouts/
│   │   ├── header.php          # Header, Navbar & Flash alerts
│   │   └── footer.php          # Footer, Chart.js & SweetAlert2
│   ├── schedules/
│   │   ├── index.php           # Quản lý danh sách lịch giảng dạy
│   │   ├── create.php          # Thêm lịch với AI Advisor & Kiểm tra trùng
│   │   ├── edit.php            # Sửa thông tin lịch dạy
│   │   └── matrix.php          # Ma trận Thời khóa biểu & Tiết trống trực quan
│   └── teachers/
│       ├── index.php           # Quản lý danh sách giảng viên
│       ├── create.php          # Thêm giảng viên
│       ├── edit.php            # Sửa thông tin giảng viên
│       └── portal.php          # Cổng thông tin tra cứu lịch cá nhân cho Giảng viên
├── .env                        # Biến môi trường kết nối CSDL thực tế (bị ignore)
├── .env.example                # File mẫu biến môi trường
├── .gitignore                  # Bỏ qua vendor/, .env, cache, log
├── composer.json               # Cấu hình phụ thuộc & PSR-4 Autoload
├── composer.lock
├── index.php                   # Router điều hướng chính của hệ thống
├── LICENSE                     # Giấy phép mã nguồn mở MIT
└── README.md                   # Hướng dẫn chi tiết dự án
```

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án

### 1. Chuẩn bị Môi trường
- Cài đặt **Laragon** (hoặc XAMPP, Herd, Docker) với PHP >= 8.0 và MySQL.
- Thư mục dự án đặt tại: `C:\laragon\www\huit-schedule`.

### 2. Cài đặt Thư viện qua Composer
Mở terminal tại thư mục dự án và chạy lệnh:
```bash
composer install
```

### 3. Cấu hình Biến Môi trường (.env)
Sao chép file `.env.example` thành `.env`:
```bash
cp .env.example .env
```
Nội dung file `.env`:
```env
APP_NAME="HUIT Schedule Hub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://huit-schedule.test

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=huit_db
DB_USER=root
DB_PASS=
```

### 4. Khởi tạo Cơ sở Dữ liệu & Dữ liệu Mẫu

Bạn có thể chọn **1 trong 2 cách** sau:

#### Cách A: Chạy tự động qua dòng lệnh (Khuyên dùng)
```bash
# 1. Tạo Database và các bảng cấu trúc:
php database/migrate.php

# 2. Sinh dữ liệu mẫu bằng Faker (Giảng viên, 4 Cơ sở HUIT, 35+ lịch dạy không trùng):
php database/seeder.php
```

#### Cách B: Import trực tiếp qua HeidiSQL / phpMyAdmin
1. Mở HeidiSQL hoặc phpMyAdmin kết nối vào MySQL localhost.
2. Mở file `database/schema.sql` và thực thi (Run) để tạo database `huit_db` và các bảng.
3. Mở file `database/seed.sql` và thực thi để nạp dữ liệu mẫu có sẵn.

### 5. Khởi chạy Ứng dụng

- **Cách 1 (Laragon Virtual Host):**
  - Mở Laragon -> Bấm **Reload** hoặc khởi động lại Apache/Nginx.
  - Truy cập: **[http://huit-schedule.test](http://huit-schedule.test)**

- **Cách 2 (PHP Built-in Server):**
  ```bash
  php -S localhost:8000
  ```
  - Truy cập: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 Tài Khoản Trải Nghiệm & Đăng Nhập

Hệ thống có sẵn các nút **Đăng nhập 1-Click** ngay trên trang Login để hội đồng/người chấm bài trải nghiệm tức thì:

| Vai trò | Tên đăng nhập / Email | Mật khẩu | Mục đích kiểm tra |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `admin123` | Toàn quyền CRUD lịch dạy, xem ma trận, xếp lịch thông minh |
| **Phòng Đào Tạo** | `daotao` | `admin123` | Phân bổ lịch giảng dạy, kiểm tra tiết trống |
| **Giảng viên 1** | `hungnv@huit.edu.vn` | `123456` | Cổng thông tin GV: TS. Nguyễn Văn Hùng (CNPM, Web PHP) |
| **Giảng viên 2** | `maitt@huit.edu.vn` | `123456` | Cổng thông tin GV: ThS. Trần Thị Mai (Mobile, Web) |

---

## 🧪 Kiểm Thử Tự Động (Tests & CI/CD)

### 1. Chạy Unit Test kiểm tra Core Logic:
```bash
php tests/test_logic.php
```
Kết quả kiểm tra:
- Khởi tạo & nạp dữ liệu từ 4 cơ sở HUIT.
- Thuật toán Smart Advisor phân tích đúng chuyên môn môn học (`Lập trình mã nguồn mở PHP` -> đề xuất TS. Nguyễn Văn Hùng điểm cao nhất).
- Phát hiện xung đột trùng giờ GV và trùng phòng học.
- Phát hiện cảnh báo di chuyển đa cơ sở trong ngày.

### 2. Kiểm tra Cú pháp toàn bộ file PHP (PHP Lint):
```bash
# Trên Windows PowerShell:
Get-ChildItem -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch "vendor" } | ForEach-Object { php -l $_.FullName }

# Trên Linux / macOS:
find . -name "*.php" ! -path "./vendor/*" -exec php -l {} +
```

### 3. Tự động hóa CI/CD với GitHub Actions:
Workflow `.github/workflows/php-lint.yml` tự động chạy mỗi khi có lệnh `git push` hoặc `pull_request` lên nhánh `main`/`master` để bảo đảm không có lỗi cú pháp trước khi triển khai.

---

## 📜 Giấy Phép (License)

Dự án được phát hành theo giấy phép mã nguồn mở **[MIT License](LICENSE)**.
Toàn bộ mã nguồn mở tự do phục vụ mục đích nghiên cứu, học tập và triển khai thực tế.
