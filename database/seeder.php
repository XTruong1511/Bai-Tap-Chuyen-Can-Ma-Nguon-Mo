<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Faker\Factory as Faker;
use App\Models\Teacher;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$dbname = $_ENV['DB_NAME'] ?? 'huit_db';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "=== HUIT Schedule Hub Seeder ===\n";
    echo "Clearing old data...\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE schedules;");
    $pdo->exec("TRUNCATE TABLE teachers;");
    $pdo->exec("TRUNCATE TABLE branches;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 1. Seed Admin User
    echo "Seeding Admin user...\n";
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'Quản Trị Viên HUIT', 'admin@huit.edu.vn', $adminPassword, 'admin']);
    $stmt->execute(['daotao', 'Phòng Đào Tạo HUIT', 'daotao@huit.edu.vn', $adminPassword, 'staff']);

    // 2. Seed Real HUIT Branches
    echo "Seeding Branches...\n";
    $branches = [
        ['Cơ sở chính (Lê Trọng Tấn)', '140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP.HCM'],
        ['Cơ sở Tân Kỳ Tân Quý', '93 Tân Kỳ Tân Quý, P. Tân Sơn Nhì, Q. Tân Phú, TP.HCM'],
        ['Cơ sở Nguyễn Đỗ Cung', '73/1 Nguyễn Đỗ Cung, P. Tây Thạnh, Q. Tân Phú, TP.HCM'],
        ['Trung tâm TN - TH (Chế Lan Viên)', '31 Chế Lan Viên, P. Tây Thạnh, Q. Tân Phú, TP.HCM']
    ];

    $branchIds = [];
    $stmtBranch = $pdo->prepare("INSERT INTO branches (branch_name, address) VALUES (?, ?)");
    foreach ($branches as $b) {
        $stmtBranch->execute([$b[0], $b[1]]);
        $branchIds[] = $pdo->lastInsertId();
    }

    // 3. Seed Teachers across ALL 17 HUIT faculties
    echo "Seeding Teachers across all 17 HUIT Faculties...\n";
    $faker = Faker::create('vi_VN');
    $defaultTeacherPassword = password_hash('123456', PASSWORD_BCRYPT);

    $stmtTeacher = $pdo->prepare("
        INSERT INTO teachers (full_name, email, phone, faculty, department, expertise, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $teacherIds = [];
    $teachersData = [];

    // Predefined Core Faculty Members representing the official faculties of HUIT
    $huitTeachers = [
        // 1. Khoa Công nghệ Thực phẩm (Khoa truyền thống mũi nhọn của HUIT)
        ['TS. Lê Thị Hồng Ánh', 'anhleth@huit.edu.vn', '0903112233', 'Khoa Công nghệ Thực phẩm', 'Bộ môn Công nghệ thực phẩm', 'Công nghệ chế biến lương thực, thực phẩm & đồ uống'],
        ['ThS. Châu Thảo Xuân', 'xuanct@huit.edu.vn', '0918223344', 'Khoa Công nghệ Thực phẩm', 'Bộ môn Hóa sinh & Độc chất thực phẩm', 'Hóa sinh thực phẩm & Phân tích vi sinh thực phẩm'],
        ['TS. Nguyễn Thị Trúc Loan', 'loannt@huit.edu.vn', '0937334455', 'Khoa Công nghệ Thực phẩm', 'Bộ môn Đảm bảo chất lượng & An toàn thực phẩm', 'Quản lý chất lượng & Tiêu chuẩn HACCP/ISO 22000'],
        ['ThS. Đỗ Vĩnh Long', 'longdv@huit.edu.vn', '0948445566', 'Khoa Công nghệ Thực phẩm', 'Bộ môn Công nghệ sau thu hoạch', 'Công nghệ bảo quản & chế biến nông sản sau thu hoạch'],

        // 2. Khoa Công nghệ Thông tin
        ['TS. Nguyễn Văn Hùng', 'hungnv@huit.edu.vn', '0903123456', 'Khoa Công nghệ Thông tin', 'Bộ môn Công nghệ phần mềm', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['ThS. Trần Thị Mai', 'maitt@huit.edu.vn', '0918234567', 'Khoa Công nghệ Thông tin', 'Bộ môn Công nghệ phần mềm', 'Lập trình Web & Di động (React, Flutter, Android)'],
        ['TS. Lê Hoàng Nam', 'namlh@huit.edu.vn', '0937345678', 'Khoa Công nghệ Thông tin', 'Bộ môn Mạng máy tính & An ninh mạng', 'Mạng máy tính & An toàn thông tin'],
        ['ThS. Phạm Quốc Bảo', 'baopq@huit.edu.vn', '0948456789', 'Khoa Công nghệ Thông tin', 'Bộ môn Hệ thống thông tin', 'Cơ sở dữ liệu & Phân tích hệ thống'],
        ['TS. Đặng Thanh Sơn', 'sondt@huit.edu.vn', '0979567890', 'Khoa Công nghệ Thông tin', 'Bộ môn Trí tuệ nhân tạo & Khoa học dữ liệu', 'Trí tuệ nhân tạo & Học máy (AI/ML)'],
        ['ThS. Vũ Thị Bích', 'bichvt@huit.edu.vn', '0988678901', 'Khoa Công nghệ Thông tin', 'Bộ môn Trí tuệ nhân tạo & Khoa học dữ liệu', 'Khoa học dữ liệu & Trực quan hóa dữ liệu'],

        // 3. Khoa Quản trị Kinh doanh
        ['TS. Trần Đình Hoàng', 'hoangtd@huit.edu.vn', '0909121212', 'Khoa Quản trị Kinh doanh', 'Bộ môn Quản trị doanh nghiệp', 'Quản trị học đại cương & Quản trị chiến lược'],
        ['ThS. Nguyễn Thị Thu Trang', 'trangnt@huit.edu.vn', '0913141516', 'Khoa Quản trị Kinh doanh', 'Bộ môn Quản trị Marketing', 'Marketing căn bản & Hành vi người tiêu dùng'],

        // 4. Khoa Tài chính - Kế toán
        ['TS. Võ Minh Trí', 'trivm@huit.edu.vn', '0908181818', 'Khoa Tài chính - Kế toán', 'Bộ môn Kế toán doanh nghiệp', 'Nguyên lý kế toán & Kế toán tài chính'],
        ['ThS. Bùi Thị Thanh Tuyền', 'tuyenbt@huit.edu.vn', '0919191919', 'Khoa Tài chính - Kế toán', 'Bộ môn Tài chính - Ngân hàng', 'Tài chính doanh nghiệp & Thị trường chứng khoán'],

        // 5. Khoa Du lịch và Ẩm thực
        ['ThS. Phan Đình Dũng', 'dungpd@huit.edu.vn', '0907202020', 'Khoa Du lịch và Ẩm thực', 'Bộ môn Quản trị du lịch & Lữ hành', 'Quản trị dịch vụ du lịch & Nghiệp vụ hướng dẫn lữ hành'],
        ['ThS. Lê Thanh Hải', 'hailt@huit.edu.vn', '0938212121', 'Khoa Du lịch và Ẩm thực', 'Bộ môn Kỹ thuật chế biến món ăn', 'Kỹ thuật chế biến món Á - Âu & Dinh dưỡng ẩm thực'],

        // 6. Khoa Ngoại ngữ
        ['ThS. Đỗ Thị Phương Thảo', 'thaodtp@huit.edu.vn', '0977222222', 'Khoa Ngoại ngữ', 'Bộ môn Ngôn ngữ Anh', 'Tiếng Anh thương mại & Kỹ năng giao tiếp học thuật'],
        ['ThS. Vương Khải Hưng', 'hungvk@huit.edu.vn', '0989232323', 'Khoa Ngoại ngữ', 'Bộ môn Ngôn ngữ Trung Quốc', 'Tiếng Trung thương mại & Biên phiên dịch'],

        // 7. Khoa Luật
        ['TS. Huỳnh Tấn Đạt', 'datht@huit.edu.vn', '0906242424', 'Khoa Luật', 'Bộ môn Luật kinh tế', 'Pháp luật đại cương & Luật thương mại quốc tế'],

        // 8. Khoa Công nghệ Điện - Điện tử
        ['TS. Nguyễn Minh Tâm', 'tamnm@huit.edu.vn', '0912252525', 'Khoa Công nghệ Điện - Điện tử', 'Bộ môn Kỹ thuật điện & Hệ thống điện', 'Mạch điện tử & Lập trình vi điều khiển PLC'],

        // 9. Khoa Công nghệ Cơ khí
        ['TS. Hoàng Quốc Tuấn', 'tuanhq@huit.edu.vn', '0933262626', 'Khoa Công nghệ Cơ khí', 'Bộ môn Cơ khí chế tạo máy', 'Nguyên lý chi tiết máy & Kỹ thuật gia công CNC'],

        // 10. Khoa Công nghệ Hóa học
        ['TS. Phan Thị Thùy Dung', 'dungpt@huit.edu.vn', '0978272727', 'Khoa Công nghệ Hóa học', 'Bộ môn Kỹ thuật hóa học', 'Hóa vô cơ, hữu cơ & Phân tích kiểm nghiệm'],

        // 11. Khoa Công nghệ May và Thời trang
        ['ThS. Trương Ngọc Diễm', 'diemtn@huit.edu.vn', '0987282828', 'Khoa Công nghệ May và Thời trang', 'Bộ môn Công nghệ may', 'Kỹ thuật may công nghiệp & Thiết kế rập thời trang'],

        // 12. Khoa Thương mại
        ['TS. Lương Minh Trí', 'trilm@huit.edu.vn', '0902292929', 'Khoa Thương mại', 'Bộ môn Logistics & Quản lý chuỗi cung ứng', 'Logistics, quản lý chuỗi cung ứng & Thương mại điện tử'],

        // 13. Khoa Sinh học và Môi trường
        ['TS. Lâm Thị Kim Phượng', 'phuonglt@huit.edu.vn', '0917303030', 'Khoa Sinh học và Môi trường', 'Bộ môn Công nghệ sinh học', 'Công nghệ sinh học ứng dụng & Xử lý nước cấp, nước thải'],

        // 14. Khoa Kinh tế và Quản lý Công nghiệp
        ['ThS. Hà Duy Quang', 'quanghd@huit.edu.vn', '0932313131', 'Khoa Kinh tế và Quản lý Công nghiệp', 'Bộ môn Quản lý công nghiệp', 'Quản lý dự án công nghiệp & Tối ưu hóa sản xuất'],

        // 15. Khoa Lý luận Chính trị
        ['TS. Đoàn Văn Tiến', 'tiendv@huit.edu.vn', '0976323232', 'Khoa Lý luận Chính trị', 'Bộ môn Triết học Mác - Lênin & Tư tưởng HCM', 'Triết học Mác - Lênin, Tư tưởng Hồ Chí Minh & Lịch sử Đảng'],

        // 16. Khoa Khoa học ứng dụng
        ['ThS. Nguyễn Đức Toàn', 'toannd@huit.edu.vn', '0985333333', 'Khoa Khoa học ứng dụng', 'Bộ môn Toán ứng dụng', 'Toán cao cấp, Giải tích & Xác suất thống kê ứng dụng'],

        // 17. Khoa Giáo dục thể chất và Quốc phòng An ninh
        ['ThS. Trần Công Vinh', 'vinhtc@huit.edu.vn', '0901343434', 'Khoa Giáo dục thể chất và Quốc phòng An ninh', 'Bộ môn Giáo dục thể chất', 'Giáo dục thể chất (Bóng đá, Bóng chuyền) & GD Quốc phòng']
    ];

    foreach ($huitTeachers as $t) {
        $stmtTeacher->execute([$t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $defaultTeacherPassword]);
        $tid = $pdo->lastInsertId();
        $teacherIds[] = $tid;
        $teachersData[] = [
            'id' => $tid, 
            'name' => $t[0], 
            'faculty' => $t[3], 
            'department' => $t[4], 
            'expertise' => $t[5]
        ];
    }

    // 4. Seed Schedules across all major faculties of HUIT
    echo "Seeding Schedules across various HUIT disciplines without conflicts...\n";
    $subjects = [
        // Công nghệ Thực phẩm
        ['Hóa sinh thực phẩm', 'Khoa Công nghệ Thực phẩm', 'Công nghệ chế biến lương thực, thực phẩm & đồ uống'],
        ['Công nghệ chế biến sữa & đồ uống', 'Khoa Công nghệ Thực phẩm', 'Công nghệ chế biến lương thực, thực phẩm & đồ uống'],
        ['Đảm bảo chất lượng & HACCP', 'Khoa Công nghệ Thực phẩm', 'Quản lý chất lượng & Tiêu chuẩn HACCP/ISO 22000'],
        ['Phân tích vi sinh thực phẩm', 'Khoa Công nghệ Thực phẩm', 'Hóa sinh thực phẩm & Phân tích vi sinh thực phẩm'],

        // Công nghệ Thông tin
        ['Lập trình mã nguồn mở PHP', 'Khoa Công nghệ Thông tin', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['Phát triển ứng dụng Web', 'Khoa Công nghệ Thông tin', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['Lập trình di động Flutter', 'Khoa Công nghệ Thông tin', 'Lập trình Web & Di động (React, Flutter, Android)'],
        ['An ninh mạng & ATTT', 'Khoa Công nghệ Thông tin', 'Mạng máy tính & An toàn thông tin'],
        ['Quản trị Mạng Cisco', 'Khoa Công nghệ Thông tin', 'Mạng máy tính & An toàn thông tin'],
        ['Hệ quản trị CSDL MySQL', 'Khoa Công nghệ Thông tin', 'Cơ sở dữ liệu & Phân tích hệ thống'],
        ['Trí tuệ nhân tạo ứng dụng', 'Khoa Công nghệ Thông tin', 'Trí tuệ nhân tạo & Học máy (AI/ML)'],
        ['Khai phá dữ liệu Data Mining', 'Khoa Công nghệ Thông tin', 'Khoa học dữ liệu & Trực quan hóa dữ liệu'],

        // Quản trị Kinh doanh
        ['Quản trị học đại cương', 'Khoa Quản trị Kinh doanh', 'Quản trị học đại cương & Quản trị chiến lược'],
        ['Marketing căn bản & Khách hàng', 'Khoa Quản trị Kinh doanh', 'Marketing căn bản & Hành vi người tiêu dùng'],

        // Tài chính - Kế toán
        ['Nguyên lý kế toán', 'Khoa Tài chính - Kế toán', 'Nguyên lý kế toán & Kế toán tài chính'],
        ['Tài chính doanh nghiệp', 'Khoa Tài chính - Kế toán', 'Tài chính doanh nghiệp & Thị trường chứng khoán'],

        // Du lịch & Ẩm thực
        ['Quản trị dịch vụ lữ hành', 'Khoa Du lịch và Ẩm thực', 'Quản trị dịch vụ du lịch & Nghiệp vụ hướng dẫn lữ hành'],
        ['Kỹ thuật chế biến món Á', 'Khoa Du lịch và Ẩm thực', 'Kỹ thuật chế biến món Á - Âu & Dinh dưỡng ẩm thực'],

        // Ngoại ngữ
        ['Tiếng Anh thương mại', 'Khoa Ngoại ngữ', 'Tiếng Anh thương mại & Kỹ năng giao tiếp học thuật'],
        ['Tiếng Trung giao tiếp', 'Khoa Ngoại ngữ', 'Tiếng Trung thương mại & Biên phiên dịch'],

        // Luật
        ['Pháp luật đại cương', 'Khoa Luật', 'Pháp luật đại cương & Luật thương mại quốc tế'],

        // Điện - Điện tử & Cơ khí
        ['Mạch điện tử và PLC', 'Khoa Công nghệ Điện - Điện tử', 'Mạch điện tử & Lập trình vi điều khiển PLC'],
        ['Gia công cơ khí CNC', 'Khoa Công nghệ Cơ khí', 'Nguyên lý chi tiết máy & Kỹ thuật gia công CNC'],

        // Thương mại
        ['Logistics & Chuỗi cung ứng', 'Khoa Thương mại', 'Logistics, quản lý chuỗi cung ứng & Thương mại điện tử'],

        // Lý luận Chính trị & Toán
        ['Triết học Mác - Lênin', 'Khoa Lý luận Chính trị', 'Triết học Mác - Lênin, Tư tưởng Hồ Chí Minh & Lịch sử Đảng'],
        ['Xác suất thống kê ứng dụng', 'Khoa Khoa học ứng dụng', 'Toán cao cấp, Giải tích & Xác suất thống kê ứng dụng']
    ];

    $days = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
    $shifts = [
        'Sáng (Tiết 1-3)',
        'Sáng (Tiết 4-6)',
        'Chiều (Tiết 7-9)',
        'Chiều (Tiết 10-12)',
        'Tối (Tiết 13-15)'
    ];

    $rooms = [
        'A1.01', 'A1.02', 'B2.01', 'B2.02', 'B3.05', 
        'LAB-01', 'LAB-02', 'LAB-PM1', 'LAB-PM2', 'C1.04'
    ];

    $stmtSchedule = $pdo->prepare("
        INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods, semester, academic_year, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $occupiedTeacherSlots = []; // teacher_id . '-' . day . '-' . shift => true
    $occupiedRoomSlots = [];    // branch_id . '-' . room . '-' . day . '-' . shift => true
    $scheduleCount = 0;

    foreach ($subjects as $subj) {
        $subjName = $subj[0];
        $subjFaculty = $subj[1];
        $subjExp = $subj[2];

        // Find matching teachers by Faculty and expertise
        $matchedTeachers = array_values(array_filter($teachersData, function($t) use ($subjFaculty, $subjExp) {
            return $t['faculty'] === $subjFaculty || strpos($t['expertise'], explode('&', $subjExp)[0]) !== false;
        }));

        if (empty($matchedTeachers)) {
            $matchedTeachers = $teachersData;
        }

        // Create 2 sections for each subject
        $sections = 2;
        for ($s = 0; $s < $sections; $s++) {
            $teacherCandidate = $matchedTeachers[array_rand($matchedTeachers)];
            $tId = $teacherCandidate['id'];

            // Find free day and shift
            $attempts = 0;
            $placed = false;

            while ($attempts < 35 && !$placed) {
                $attempts++;
                $day = $days[array_rand($days)];
                $shift = $shifts[array_rand($shifts)];
                $branchId = $branchIds[array_rand($branchIds)];
                $room = $rooms[array_rand($rooms)];

                $tKey = "{$tId}-{$day}-{$shift}";
                $rKey = "{$branchId}-{$room}-{$day}-{$shift}";

                if (!isset($occupiedTeacherSlots[$tKey]) && !isset($occupiedRoomSlots[$rKey])) {
                    $occupiedTeacherSlots[$tKey] = true;
                    $occupiedRoomSlots[$rKey] = true;

                    $notes = "Lớp học phần nhóm " . str_pad($s + 1, 2, '0', STR_PAD_LEFT);
                    $stmtSchedule->execute([
                        $tId,
                        $branchId,
                        $subjName,
                        $room,
                        $day,
                        $shift,
                        'Học kỳ 1',
                        '2026-2027',
                        $notes
                    ]);
                    $scheduleCount++;
                    $placed = true;
                }
            }
        }
    }

    echo "Seeding completed successfully!\n";
    echo "- Users: 2 (Admin: admin/admin123, Staff: daotao/admin123)\n";
    echo "- Branches: " . count($branches) . "\n";
    echo "- Teachers: " . count($teacherIds) . " across all 17 faculties of HUIT!\n";
    echo "- Schedules: {$scheduleCount} classes assigned across all faculties without conflict!\n";

} catch (PDOException $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
    exit(1);
}
