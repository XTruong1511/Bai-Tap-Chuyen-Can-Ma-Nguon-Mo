<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Teacher
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array
    {
        $sql = "
            SELECT t.*, COUNT(s.id) AS schedule_count
            FROM teachers t
            LEFT JOIN schedules s ON t.id = s.teacher_id
            GROUP BY t.id
            ORDER BY t.full_name ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM teachers WHERE email = ?");
        $stmt->execute([trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $password = !empty($data['password']) 
            ? password_hash($data['password'], PASSWORD_BCRYPT) 
            : password_hash('123456', PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            INSERT INTO teachers (full_name, email, phone, faculty, department, expertise, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($data['full_name']),
            trim($data['email']),
            trim($data['phone']),
            trim($data['faculty'] ?? 'Khoa Công nghệ Thông tin'),
            trim($data['department'] ?? 'Bộ môn Công nghệ phần mềm'),
            trim($data['expertise']),
            $password
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE teachers 
                SET full_name = ?, email = ?, phone = ?, faculty = ?, department = ?, expertise = ?, password = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                trim($data['full_name']),
                trim($data['email']),
                trim($data['phone']),
                trim($data['faculty'] ?? 'Khoa Công nghệ Thông tin'),
                trim($data['department'] ?? 'Bộ môn Công nghệ phần mềm'),
                trim($data['expertise']),
                password_hash($data['password'], PASSWORD_BCRYPT),
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE teachers 
                SET full_name = ?, email = ?, phone = ?, faculty = ?, department = ?, expertise = ?
                WHERE id = ?
            ");
            return $stmt->execute([
                trim($data['full_name']),
                trim($data['email']),
                trim($data['phone']),
                trim($data['faculty'] ?? 'Khoa Công nghệ Thông tin'),
                trim($data['department'] ?? 'Bộ môn Công nghệ phần mềm'),
                trim($data['expertise']),
                $id
            ]);
        }
    }

    public const HUIT_FACULTIES = [
        'Khoa Công nghệ Thực phẩm' => [
            'Bộ môn Công nghệ thực phẩm',
            'Bộ môn Đảm bảo chất lượng & An toàn thực phẩm',
            'Bộ môn Hóa sinh & Độc chất thực phẩm',
            'Bộ môn Công nghệ sau thu hoạch'
        ],
        'Khoa Công nghệ Thông tin' => [
            'Bộ môn Công nghệ phần mềm',
            'Bộ môn Mạng máy tính & An ninh mạng',
            'Bộ môn Hệ thống thông tin',
            'Bộ môn Trí tuệ nhân tạo & Khoa học dữ liệu',
            'Bộ môn Khoa học máy tính'
        ],
        'Khoa Quản trị Kinh doanh' => [
            'Bộ môn Quản trị doanh nghiệp',
            'Bộ môn Quản trị Marketing',
            'Bộ môn Quản trị nguồn nhân lực'
        ],
        'Khoa Tài chính - Kế toán' => [
            'Bộ môn Kế toán doanh nghiệp',
            'Bộ môn Tài chính - Ngân hàng',
            'Bộ môn Kiểm toán'
        ],
        'Khoa Du lịch và Ẩm thực' => [
            'Bộ môn Quản trị khách sạn & Nhà hàng',
            'Bộ môn Quản trị du lịch & Lữ hành',
            'Bộ môn Kỹ thuật chế biến món ăn'
        ],
        'Khoa Ngoại ngữ' => [
            'Bộ môn Ngôn ngữ Anh',
            'Bộ môn Ngôn ngữ Trung Quốc'
        ],
        'Khoa Luật' => [
            'Bộ môn Luật kinh tế',
            'Bộ môn Luật dân sự & Quốc tế'
        ],
        'Khoa Công nghệ Điện - Điện tử' => [
            'Bộ môn Kỹ thuật điện & Hệ thống điện',
            'Bộ môn Điện tử - Viễn thông',
            'Bộ môn Tự động hóa & Điều khiển'
        ],
        'Khoa Công nghệ Cơ khí' => [
            'Bộ môn Cơ khí chế tạo máy',
            'Bộ môn Cơ điện tử',
            'Bộ môn Kỹ thuật nhiệt - Điện lạnh'
        ],
        'Khoa Công nghệ Hóa học' => [
            'Bộ môn Kỹ thuật hóa học',
            'Bộ môn Hóa vô cơ & Phân tích',
            'Bộ môn Công nghệ vật liệu'
        ],
        'Khoa Công nghệ May và Thời trang' => [
            'Bộ môn Công nghệ may',
            'Bộ môn Thiết kế thời trang'
        ],
        'Khoa Thương mại' => [
            'Bộ môn Thương mại điện tử',
            'Bộ môn Kinh doanh quốc tế',
            'Bộ môn Logistics & Quản lý chuỗi cung ứng'
        ],
        'Khoa Sinh học và Môi trường' => [
            'Bộ môn Công nghệ sinh học',
            'Bộ môn Kỹ thuật môi trường'
        ],
        'Khoa Kinh tế và Quản lý Công nghiệp' => [
            'Bộ môn Quản lý công nghiệp',
            'Bộ môn Kinh tế công nghiệp'
        ],
        'Khoa Lý luận Chính trị' => [
            'Bộ môn Triết học Mác - Lênin & Tư tưởng HCM',
            'Bộ môn Lịch sử Đảng & Pháp luật đại cương'
        ],
        'Khoa Khoa học ứng dụng' => [
            'Bộ môn Toán ứng dụng',
            'Bộ môn Vật lý kỹ thuật'
        ],
        'Khoa Giáo dục thể chất và Quốc phòng An ninh' => [
            'Bộ môn Giáo dục thể chất',
            'Bộ môn Giáo dục quốc phòng - An ninh'
        ]
    ];

    public function getFacultiesAndDepartments(): array
    {
        $result = self::HUIT_FACULTIES;

        $sql = "SELECT DISTINCT faculty, department FROM teachers WHERE faculty IS NOT NULL AND faculty != ''";
        $rows = $this->db->query($sql)->fetchAll();
        foreach ($rows as $r) {
            $fac = $r['faculty'];
            $dept = $r['department'];
            if (!isset($result[$fac])) {
                $result[$fac] = [];
            }
            if ($dept && !in_array($dept, $result[$fac])) {
                $result[$fac][] = $dept;
            }
        }
        return $result;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM teachers WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getSchedules(int $teacherId): array
    {
        $sql = "
            SELECT s.*, b.branch_name, b.address AS branch_address
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            WHERE s.teacher_id = ?
            ORDER BY 
                FIELD(s.day_of_week, 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'),
                FIELD(s.shift_or_periods, 'Sáng (Tiết 1-3)', 'Sáng (Tiết 4-6)', 'Chiều (Tiết 7-9)', 'Chiều (Tiết 10-12)', 'Tối (Tiết 13-15)')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
}
