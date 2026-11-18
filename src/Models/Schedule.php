<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Schedule
{
    private PDO $db;

    public const SHIFTS = [
        'Sáng (Tiết 1-3)',
        'Sáng (Tiết 4-6)',
        'Chiều (Tiết 7-9)',
        'Chiều (Tiết 10-12)',
        'Tối (Tiết 13-15)'
    ];

    public const DAYS = [
        'Thứ 2',
        'Thứ 3',
        'Thứ 4',
        'Thứ 5',
        'Thứ 6',
        'Thứ 7',
        'Chủ Nhật'
    ];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(array $filters = []): array
    {
        $sql = "
            SELECT s.*, 
                   t.full_name AS teacher_name, t.email AS teacher_email, t.phone AS teacher_phone, t.expertise AS teacher_expertise,
                   b.branch_name, b.address AS branch_address
            FROM schedules s
            JOIN teachers t ON s.teacher_id = t.id
            JOIN branches b ON s.branch_id = b.id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND s.branch_id = ?";
            $params[] = (int) $filters['branch_id'];
        }

        if (!empty($filters['teacher_id'])) {
            $sql .= " AND s.teacher_id = ?";
            $params[] = (int) $filters['teacher_id'];
        }

        if (!empty($filters['day_of_week'])) {
            $sql .= " AND s.day_of_week = ?";
            $params[] = $filters['day_of_week'];
        }

        if (!empty($filters['shift_or_periods'])) {
            $sql .= " AND s.shift_or_periods = ?";
            $params[] = $filters['shift_or_periods'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (s.subject_name LIKE ? OR s.room LIKE ? OR t.full_name LIKE ?)";
            $searchTerm = '%' . trim($filters['search']) . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY 
            FIELD(s.day_of_week, 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'),
            FIELD(s.shift_or_periods, 'Sáng (Tiết 1-3)', 'Sáng (Tiết 4-6)', 'Chiều (Tiết 7-9)', 'Chiều (Tiết 10-12)', 'Tối (Tiết 13-15)'),
            s.room ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $sql = "
            SELECT s.*, 
                   t.full_name AS teacher_name, t.email AS teacher_email, t.expertise AS teacher_expertise,
                   b.branch_name, b.address AS branch_address
            FROM schedules s
            JOIN teachers t ON s.teacher_id = t.id
            JOIN branches b ON s.branch_id = b.id
            WHERE s.id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods, semester, academic_year, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int) $data['teacher_id'],
            (int) $data['branch_id'],
            trim($data['subject_name']),
            trim($data['room']),
            trim($data['day_of_week']),
            trim($data['shift_or_periods']),
            $data['semester'] ?? 'Học kỳ 1',
            $data['academic_year'] ?? '2026-2027',
            $data['notes'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE schedules
            SET teacher_id = ?, branch_id = ?, subject_name = ?, room = ?, day_of_week = ?, shift_or_periods = ?, semester = ?, academic_year = ?, notes = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            (int) $data['teacher_id'],
            (int) $data['branch_id'],
            trim($data['subject_name']),
            trim($data['room']),
            trim($data['day_of_week']),
            trim($data['shift_or_periods']),
            $data['semester'] ?? 'Học kỳ 1',
            $data['academic_year'] ?? '2026-2027',
            $data['notes'] ?? null,
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM schedules WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Kiểm tra xung đột lịch dạy
     * Trả về mảng lỗi nếu có xung đột, hoặc mảng rỗng nếu hợp lệ.
     */
    public function checkConflict(int $teacherId, int $branchId, string $room, string $day, string $shift, ?int $excludeId = null): array
    {
        $conflicts = [];

        // 1. Kiểm tra Giảng viên có bị trùng lịch trong cùng Thứ và Ca học không
        $sqlTeacher = "
            SELECT s.*, b.branch_name, t.full_name AS teacher_name
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            JOIN teachers t ON s.teacher_id = t.id
            WHERE s.teacher_id = ? AND s.day_of_week = ? AND s.shift_or_periods = ?
        ";
        $paramsTeacher = [$teacherId, $day, $shift];
        if ($excludeId !== null) {
            $sqlTeacher .= " AND s.id != ?";
            $paramsTeacher[] = $excludeId;
        }

        $stmtTeacher = $this->db->prepare($sqlTeacher);
        $stmtTeacher->execute($paramsTeacher);
        $teacherConflict = $stmtTeacher->fetch();

        if ($teacherConflict) {
            $conflicts[] = [
                'type' => 'teacher_busy',
                'title' => 'Trùng lịch Giảng viên',
                'message' => "Giảng viên {$teacherConflict['teacher_name']} đã có lịch dạy môn \"{$teacherConflict['subject_name']}\" tại phòng {$teacherConflict['room']} - {$teacherConflict['branch_name']} trong khung giờ này ({$day}, {$shift})."
            ];
        }

        // 2. Kiểm tra Phòng học tại Cơ sở đó có bị trùng trong cùng Thứ và Ca học không
        $sqlRoom = "
            SELECT s.*, b.branch_name, t.full_name AS teacher_name
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            JOIN teachers t ON s.teacher_id = t.id
            WHERE s.branch_id = ? AND s.room = ? AND s.day_of_week = ? AND s.shift_or_periods = ?
        ";
        $paramsRoom = [$branchId, $room, $day, $shift];
        if ($excludeId !== null) {
            $sqlRoom .= " AND s.id != ?";
            $paramsRoom[] = $excludeId;
        }

        $stmtRoom = $this->db->prepare($sqlRoom);
        $stmtRoom->execute($paramsRoom);
        $roomConflict = $stmtRoom->fetch();

        if ($roomConflict) {
            $conflicts[] = [
                'type' => 'room_occupied',
                'title' => 'Trùng Phòng học',
                'message' => "Phòng {$room} ({$roomConflict['branch_name']}) đã được phân cho môn \"{$roomConflict['subject_name']}\" (GV: {$roomConflict['teacher_name']}) vào {$day}, {$shift}."
            ];
        }

        return $conflicts;
    }

    /**
     * Tối ưu hóa di chuyển: Kiểm tra cảnh báo nếu GV có lịch ở cơ sở khác trong cùng ngày
     */
    public function checkTravelWarning(int $teacherId, string $day, string $shift, int $targetBranchId, ?int $excludeId = null): ?array
    {
        $sql = "
            SELECT s.*, b.branch_name, b.address
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            WHERE s.teacher_id = ? AND s.day_of_week = ? AND s.branch_id != ?
        ";
        $params = [$teacherId, $day, $targetBranchId];
        if ($excludeId !== null) {
            $sql .= " AND s.id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $otherBranchSchedules = $stmt->fetchAll();

        if (empty($otherBranchSchedules)) {
            return null;
        }

        // Target branch details
        $stmtBranch = $this->db->prepare("SELECT branch_name, address FROM branches WHERE id = ?");
        $stmtBranch->execute([$targetBranchId]);
        $targetBranch = $stmtBranch->fetch();

        $details = [];
        foreach ($otherBranchSchedules as $obs) {
            $details[] = "Ca: {$obs['shift_or_periods']} tại {$obs['branch_name']} (Môn: {$obs['subject_name']}, Phòng: {$obs['room']})";
        }

        return [
            'type' => 'travel_optimization',
            'title' => 'Lưu ý di chuyển giữa các cơ sở',
            'target_branch' => $targetBranch['branch_name'] ?? 'Cơ sở dự kiến',
            'other_schedules' => $details,
            'message' => "Trong ngày {$day}, giảng viên đã có lịch giảng dạy tại cơ sở khác. Hãy đảm bảo khoảng cách và thời gian di chuyển giữa các ca để tránh trễ giờ lên lớp!"
        ];
    }

    /**
     * Thuật toán Gợi ý Giảng viên Thông minh
     * 1. So khớp chuyên môn (expertise) với tên môn học
     * 2. Kiểm tra xem giảng viên có bị trùng lịch trong ngày & ca đã chọn không
     * 3. Tính toán ưu tiên nếu GV đã có lịch cùng cơ sở trong ngày (tối ưu di chuyển)
     * 4. Cân bằng tải: ưu tiên GV ít tiết hơn trong tuần
     */
    public function getSmartRecommendations(string $subjectName, string $day, string $shift, int $branchId): array
    {
        // Lấy tất cả giảng viên kèm số lịch đã phân trong tuần
        $sqlTeachers = "
            SELECT t.id, t.full_name, t.email, t.phone, t.expertise,
                   COUNT(s.id) AS total_weekly_classes
            FROM teachers t
            LEFT JOIN schedules s ON t.id = s.teacher_id
            GROUP BY t.id
        ";
        $teachers = $this->db->query($sqlTeachers)->fetchAll();

        // Lấy lịch dạy trong cùng ngày & ca để kiểm tra trùng
        $stmtBusy = $this->db->prepare("
            SELECT s.teacher_id, s.subject_name, s.room, b.branch_name
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            WHERE s.day_of_week = ? AND s.shift_or_periods = ?
        ");
        $stmtBusy->execute([$day, $shift]);
        $busyList = [];
        foreach ($stmtBusy->fetchAll() as $row) {
            $busyList[$row['teacher_id']] = $row;
        }

        // Lấy lịch trong cùng ngày của GV tại cơ sở này (để tối ưu di chuyển)
        $stmtSameBranch = $this->db->prepare("
            SELECT DISTINCT teacher_id
            FROM schedules
            WHERE day_of_week = ? AND branch_id = ?
        ");
        $stmtSameBranch->execute([$day, $branchId]);
        $sameBranchTeachers = $stmtSameBranch->fetchAll(PDO::FETCH_COLUMN);
        $sameBranchMap = array_flip($sameBranchTeachers);

        // Lấy lịch trong cùng ngày của GV tại cơ sở KHÁC
        $stmtOtherBranch = $this->db->prepare("
            SELECT DISTINCT teacher_id, b.branch_name
            FROM schedules s
            JOIN branches b ON s.branch_id = b.id
            WHERE s.day_of_week = ? AND s.branch_id != ?
        ");
        $stmtOtherBranch->execute([$day, $branchId]);
        $otherBranchMap = [];
        foreach ($stmtOtherBranch->fetchAll() as $row) {
            $otherBranchMap[$row['teacher_id']] = $row['branch_name'];
        }

        // Chuẩn hóa tên môn học để so khớp
        $normalizedSubject = mb_strtolower($subjectName, 'UTF-8');
        $subjectKeywords = array_filter(explode(' ', preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalizedSubject)));

        $recommendations = [];

        foreach ($teachers as $t) {
            $tid = (int) $t['id'];
            $isBusy = isset($busyList[$tid]);
            $busyInfo = $isBusy ? $busyList[$tid] : null;

            // Tính điểm chuyên môn (Expertise Match Score)
            $normalizedExp = mb_strtolower($t['expertise'], 'UTF-8');
            $score = 0;
            $matchedKeywords = [];

            // 1. Kiểm tra khớp toàn chuỗi hoặc nhóm từ khóa quan trọng
            $domainKeywords = [
                'web' => ['web', 'php', 'html', 'css', 'javascript', 'laravel', 'frontend', 'backend', 'fullstack'],
                'lập trình' => ['lập trình', 'code', 'phần mềm', 'c++', 'java', 'c#', 'python', 'oop'],
                'di động' => ['di động', 'mobile', 'android', 'ios', 'flutter', 'react native'],
                'mạng' => ['mạng', 'network', 'cisco', 'an ninh', 'bảo mật', 'attt', 'security'],
                'cơ sở dữ liệu' => ['cơ sở dữ liệu', 'database', 'sql', 'mysql', 'oracle', 'hệ quản trị', 'dbms'],
                'trí tuệ nhân tạo' => ['trí tuệ nhân tạo', 'ai', 'học máy', 'machine learning', 'deep learning'],
                'khoa học dữ liệu' => ['khoa học dữ liệu', 'data science', 'khai phá', 'data mining', 'analytics', 'thống kê'],
                'kiến trúc máy tính' => ['kiến trúc', 'hệ điều hành', 'linux', 'phần cứng', 'vi xử lý'],
                'đám mây' => ['đám mây', 'cloud', 'aws', 'azure', 'devops'],
                'đồ họa' => ['đồ họa', 'ui', 'ux', 'thiết kế', 'photoshop', 'figma'],
                'thực phẩm' => ['thực phẩm', 'hóa sinh', 'chế biến', 'vi sinh']
            ];

            foreach ($domainKeywords as $category => $kwGroup) {
                $subjectMatchesCategory = false;
                foreach ($kwGroup as $kw) {
                    if (str_contains($normalizedSubject, $kw)) {
                        $subjectMatchesCategory = true;
                        break;
                    }
                }

                if ($subjectMatchesCategory) {
                    foreach ($kwGroup as $kw) {
                        if (str_contains($normalizedExp, $kw)) {
                            $score += 25;
                            $matchedKeywords[] = $kw;
                        }
                    }
                }
            }

            // So sánh từng từ khóa đơn lẻ
            foreach ($subjectKeywords as $kw) {
                if (mb_strlen($kw, 'UTF-8') >= 3 && str_contains($normalizedExp, $kw)) {
                    $score += 15;
                    $matchedKeywords[] = $kw;
                }
            }

            $matchedKeywords = array_unique($matchedKeywords);

            // Tối ưu di chuyển: Nếu GV hôm đó đã có tiết ở CÙNG cơ sở -> cộng điểm
            $travelStatus = 'normal';
            $travelNote = 'Chưa có lịch dạy ở cơ sở nào trong ngày';
            if (isset($sameBranchMap[$tid])) {
                $score += 15; // Ưu tiên xếp cùng cơ sở
                $travelStatus = 'same_branch_bonus';
                $travelNote = 'Đã có lịch tại cơ sở này trong ngày (tối ưu di chuyển, không cần đi xa)';
            } elseif (isset($otherBranchMap[$tid])) {
                $travelStatus = 'other_branch_warning';
                $travelNote = "Đã có lịch dạy tại {$otherBranchMap[$tid]} trong ngày (cần di chuyển giữa các cơ sở)";
            }

            // Cân bằng tải: Giảm nhẹ điểm nếu GV đã dạy quá nhiều tiết trong tuần
            $weeklyLoadPenalty = min(30, (int)$t['total_weekly_classes'] * 3);
            $score -= $weeklyLoadPenalty;

            // Nếu giảng viên bị trùng lịch giờ này -> điểm trừ rất nặng để xuống cuối danh sách
            if ($isBusy) {
                $score -= 1000;
            }

            $recommendations[] = [
                'teacher_id' => $tid,
                'full_name' => $t['full_name'],
                'email' => $t['email'],
                'phone' => $t['phone'],
                'expertise' => $t['expertise'],
                'is_busy' => $isBusy,
                'busy_details' => $busyInfo ? "Đang dạy môn {$busyInfo['subject_name']} tại phòng {$busyInfo['room']} ({$busyInfo['branch_name']})" : null,
                'total_weekly_classes' => (int) $t['total_weekly_classes'],
                'match_score' => max(0, $score),
                'raw_score' => $score,
                'matched_keywords' => array_values($matchedKeywords),
                'travel_status' => $travelStatus,
                'travel_note' => $travelNote,
            ];
        }

        // Sắp xếp theo raw_score giảm dần (Người phù hợp nhất và rảnh lên đầu)
        usort($recommendations, function ($a, $b) {
            return $b['raw_score'] <=> $a['raw_score'];
        });

        return $recommendations;
    }

    /**
     * Lấy ma trận lịch trống và phân bổ thời khóa biểu
     */
    public function getTimetableMatrix(?int $branchId = null, ?int $teacherId = null): array
    {
        $filters = [];
        if ($branchId) $filters['branch_id'] = $branchId;
        if ($teacherId) $filters['teacher_id'] = $teacherId;

        $schedules = $this->all($filters);

        $matrix = [];
        foreach (self::SHIFTS as $shift) {
            $matrix[$shift] = [];
            foreach (self::DAYS as $day) {
                $matrix[$shift][$day] = [];
            }
        }

        foreach ($schedules as $s) {
            $shift = $s['shift_or_periods'];
            $day = $s['day_of_week'];
            if (isset($matrix[$shift][$day])) {
                $matrix[$shift][$day][] = $s;
            }
        }

        return $matrix;
    }

    /**
     * Thống kê tổng quan cho Dashboard & biểu đồ phân bổ thời gian
     */
    public function getStatistics(): array
    {
        // 1. Tổng số
        $totalSchedules = (int) $this->db->query("SELECT COUNT(*) FROM schedules")->fetchColumn();
        $totalTeachers = (int) $this->db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
        $totalBranches = (int) $this->db->query("SELECT COUNT(*) FROM branches")->fetchColumn();

        // 2. Phân bổ theo cơ sở (Branch distribution)
        $branchStats = $this->db->query("
            SELECT b.id, b.branch_name, COUNT(s.id) AS count
            FROM branches b
            LEFT JOIN schedules s ON b.id = s.branch_id
            GROUP BY b.id
            ORDER BY count DESC
        ")->fetchAll();

        // 3. Phân bổ theo Thứ trong tuần (Day distribution)
        $dayStats = $this->db->query("
            SELECT day_of_week, COUNT(*) AS count
            FROM schedules
            GROUP BY day_of_week
            ORDER BY FIELD(day_of_week, 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật')
        ")->fetchAll();

        // 4. Phân bổ theo Ca học (Shift distribution)
        $shiftStats = $this->db->query("
            SELECT shift_or_periods, COUNT(*) AS count
            FROM schedules
            GROUP BY shift_or_periods
            ORDER BY FIELD(shift_or_periods, 'Sáng (Tiết 1-3)', 'Sáng (Tiết 4-6)', 'Chiều (Tiết 7-9)', 'Chiều (Tiết 10-12)', 'Tối (Tiết 13-15)')
        ")->fetchAll();

        // 5. Top giảng viên giảng dạy nhiều tiết nhất
        $topTeachers = $this->db->query("
            SELECT t.full_name, t.expertise, COUNT(s.id) AS schedule_count
            FROM teachers t
            JOIN schedules s ON t.id = s.teacher_id
            GROUP BY t.id
            ORDER BY schedule_count DESC
            LIMIT 5
        ")->fetchAll();

        return [
            'total_schedules' => $totalSchedules,
            'total_teachers' => $totalTeachers,
            'total_branches' => $totalBranches,
            'branch_stats' => $branchStats,
            'day_stats' => $dayStats,
            'shift_stats' => $shiftStats,
            'top_teachers' => $topTeachers,
        ];
    }
}
