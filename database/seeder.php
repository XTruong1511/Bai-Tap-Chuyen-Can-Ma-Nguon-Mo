<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Faker\Factory as Faker;

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

    // 3. Seed Teachers using Faker
    echo "Seeding Teachers with Faker...\n";
    $faker = Faker::create('vi_VN');

    $expertises = [
        'Công nghệ phần mềm & Lập trình Web PHP/Laravel',
        'Lập trình Web & Di động (React, Flutter, Android)',
        'Mạng máy tính & An toàn thông tin',
        'Cơ sở dữ liệu & Phân tích hệ thống',
        'Trí tuệ nhân tạo & Học máy (AI/ML)',
        'Khoa học dữ liệu & Trực quan hóa dữ liệu',
        'Kiến trúc máy tính & Hệ điều hành',
        'Điện toán đám mây & Quản trị hệ thống Linux',
        'Thiết kế giao diện UI/UX & Đồ họa',
        'Công nghệ thực phẩm & Hóa sinh thực phẩm'
    ];

    $departmentsMap = [
        'Công nghệ phần mềm & Lập trình Web PHP/Laravel' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Công nghệ phần mềm'
        ],
        'Lập trình Web & Di động (React, Flutter, Android)' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Công nghệ phần mềm'
        ],
        'Mạng máy tính & An toàn thông tin' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Mạng máy tính & An ninh mạng'
        ],
        'Điện toán đám mây & Quản trị hệ thống Linux' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Mạng máy tính & An ninh mạng'
        ],
        'Cơ sở dữ liệu & Phân tích hệ thống' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Hệ thống thông tin'
        ],
        'Trí tuệ nhân tạo & Học máy (AI/ML)' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Khoa học dữ liệu & Trí tuệ nhân tạo'
        ],
        'Khoa học dữ liệu & Trực quan hóa dữ liệu' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Khoa học dữ liệu & Trí tuệ nhân tạo'
        ],
        'Kiến trúc máy tính & Hệ điều hành' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Khoa học máy tính'
        ],
        'Thiết kế giao diện UI/UX & Đồ họa' => [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Thiết kế đồ họa & Đa phương tiện'
        ],
        'Công nghệ thực phẩm & Hóa sinh thực phẩm' => [
            'faculty' => 'Khoa Công nghệ Thực phẩm',
            'department' => 'Bộ môn Công nghệ thực phẩm & Hóa sinh'
        ]
    ];

    $defaultTeacherPassword = password_hash('123456', PASSWORD_BCRYPT);
    $stmtTeacher = $pdo->prepare("INSERT INTO teachers (full_name, email, phone, faculty, department, expertise, password) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $teacherIds = [];
    $teachersData = [];

    // Predefine a few notable real-styled HUIT faculty members
    $coreTeachers = [
        ['TS. Nguyễn Văn Hùng', 'hungnv@huit.edu.vn', '0903123456', 'Khoa Công nghệ Thông tin', 'Bộ môn Công nghệ phần mềm', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['ThS. Trần Thị Mai', 'maitt@huit.edu.vn', '0918234567', 'Khoa Công nghệ Thông tin', 'Bộ môn Công nghệ phần mềm', 'Lập trình Web & Di động (React, Flutter, Android)'],
        ['TS. Lê Hoàng Nam', 'namlh@huit.edu.vn', '0937345678', 'Khoa Công nghệ Thông tin', 'Bộ môn Mạng máy tính & An ninh mạng', 'Mạng máy tính & An toàn thông tin'],
        ['ThS. Phạm Quốc Bảo', 'baopq@huit.edu.vn', '0948456789', 'Khoa Công nghệ Thông tin', 'Bộ môn Hệ thống thông tin', 'Cơ sở dữ liệu & Phân tích hệ thống'],
        ['TS. Đặng Thanh Sơn', 'sondt@huit.edu.vn', '0979567890', 'Khoa Công nghệ Thông tin', 'Bộ môn Khoa học dữ liệu & Trí tuệ nhân tạo', 'Trí tuệ nhân tạo & Học máy (AI/ML)'],
        ['ThS. Vũ Thị Bích', 'bichvt@huit.edu.vn', '0988678901', 'Khoa Công nghệ Thông tin', 'Bộ môn Khoa học dữ liệu & Trí tuệ nhân tạo', 'Khoa học dữ liệu & Trực quan hóa dữ liệu'],
    ];

    foreach ($coreTeachers as $t) {
        $stmtTeacher->execute([$t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $defaultTeacherPassword]);
        $teacherIds[] = $pdo->lastInsertId();
        $teachersData[] = ['id' => $pdo->lastInsertId(), 'name' => $t[0], 'expertise' => $t[5]];
    }

    // Generate 10 more teachers with Faker
    for ($i = 0; $i < 10; $i++) {
        $gender = ($i % 2 === 0) ? 'male' : 'female';
        $fullName = ($gender === 'male' ? 'ThS. ' : 'ThS. ') . $faker->lastName . ' ' . $faker->middleName($gender) . ' ' . $faker->firstName($gender);
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', iconv('UTF-8', 'ASCII//IGNORE', $fullName));
        $email = strtolower(substr($cleanName, 0, 8)) . rand(10, 99) . '@huit.edu.vn';
        $phone = '09' . $faker->numerify('########');
        $exp = $expertises[array_rand($expertises)];
        $deptInfo = $departmentsMap[$exp] ?? [
            'faculty' => 'Khoa Công nghệ Thông tin',
            'department' => 'Bộ môn Công nghệ phần mềm'
        ];

        $stmtTeacher->execute([$fullName, $email, $phone, $deptInfo['faculty'], $deptInfo['department'], $exp, $defaultTeacherPassword]);
        $tid = $pdo->lastInsertId();
        $teacherIds[] = $tid;
        $teachersData[] = ['id' => $tid, 'name' => $fullName, 'expertise' => $exp];
    }

    // 4. Seed Schedules
    echo "Seeding Schedules without conflicts...\n";
    $subjects = [
        ['Lập trình mã nguồn mở PHP', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['Phát triển ứng dụng Web', 'Công nghệ phần mềm & Lập trình Web PHP/Laravel'],
        ['Lập trình di động Flutter', 'Lập trình Web & Di động (React, Flutter, Android)'],
        ['An ninh mạng & ATTT', 'Mạng máy tính & An toàn thông tin'],
        ['Quản trị Mạng Cisco', 'Mạng máy tính & An toàn thông tin'],
        ['Hệ quản trị CSDL MySQL', 'Cơ sở dữ liệu & Phân tích hệ thống'],
        ['Trí tuệ nhân tạo ứng dụng', 'Trí tuệ nhân tạo & Học máy (AI/ML)'],
        ['Khai phá dữ liệu Data Mining', 'Khoa học dữ liệu & Trực quan hóa dữ liệu'],
        ['Kiến trúc máy tính', 'Kiến trúc máy tính & Hệ điều hành'],
        ['Điện toán đám mây AWS', 'Điện toán đám mây & Quản trị hệ thống Linux'],
        ['Thiết kế giao diện UI/UX', 'Thiết kế giao diện UI/UX & Đồ họa'],
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

    // Generate schedules with proper teacher expertise matching and no conflict
    foreach ($subjects as $subj) {
        $subjName = $subj[0];
        $subjExp = $subj[1];

        // Find matching teachers
        $matchedTeachers = array_filter($teachersData, function($t) use ($subjExp) {
            return strpos($t['expertise'], explode('&', $subjExp)[0]) !== false;
        });

        if (empty($matchedTeachers)) {
            $matchedTeachers = $teachersData;
        }

        // Create 3 to 4 sections for each subject
        $sections = rand(3, 4);
        for ($s = 0; $s < $sections; $s++) {
            $teacherCandidate = $matchedTeachers[array_rand($matchedTeachers)];
            $tId = $teacherCandidate['id'];

            // Try to find a free day and shift
            $attempts = 0;
            $placed = false;

            while ($attempts < 30 && !$placed) {
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
    echo "- Teachers: " . count($teacherIds) . " (Password: 123456)\n";
    echo "- Schedules: {$scheduleCount} classes assigned without conflict!\n";

} catch (PDOException $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
    exit(1);
}
