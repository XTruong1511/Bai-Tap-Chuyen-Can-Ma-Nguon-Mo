<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Branch;

class ApiController
{
    private Schedule $scheduleModel;
    private Teacher $teacherModel;
    private Branch $branchModel;

    public function __construct()
    {
        $this->scheduleModel = new Schedule();
        $this->teacherModel = new Teacher();
        $this->branchModel = new Branch();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * API: Gợi ý giảng viên thông minh theo chuyên môn & độ rảnh
     */
    public function recommendTeachers(): void
    {
        $subjectName = trim($_GET['subject'] ?? '');
        $day = trim($_GET['day'] ?? 'Thứ 2');
        $shift = trim($_GET['shift'] ?? 'Sáng (Tiết 1-3)');
        $branchId = (int) ($_GET['branch_id'] ?? 1);

        if (empty($subjectName)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Vui lòng nhập tên học phần để hệ thống gợi ý chuyên môn.'
            ], 400);
        }

        $recommendations = $this->scheduleModel->getSmartRecommendations($subjectName, $day, $shift, $branchId);

        $this->jsonResponse([
            'success' => true,
            'query' => [
                'subject' => $subjectName,
                'day' => $day,
                'shift' => $shift,
                'branch_id' => $branchId
            ],
            'count' => count($recommendations),
            'data' => $recommendations
        ]);
    }

    /**
     * API: Kiểm tra trực tiếp trùng lịch và cảnh báo di chuyển đa cơ sở
     */
    public function checkConflict(): void
    {
        $teacherId = (int) ($_GET['teacher_id'] ?? 0);
        $branchId = (int) ($_GET['branch_id'] ?? 0);
        $room = trim($_GET['room'] ?? '');
        $day = trim($_GET['day'] ?? '');
        $shift = trim($_GET['shift'] ?? '');
        $excludeId = isset($_GET['exclude_id']) && $_GET['exclude_id'] !== '' ? (int)$_GET['exclude_id'] : null;

        if (!$teacherId || !$branchId || empty($room) || empty($day) || empty($shift)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Thiếu thông số kiểm tra.'
            ], 400);
        }

        $conflicts = $this->scheduleModel->checkConflict($teacherId, $branchId, $room, $day, $shift, $excludeId);
        $travelWarning = $this->scheduleModel->checkTravelWarning($teacherId, $day, $shift, $branchId, $excludeId);

        $this->jsonResponse([
            'success' => true,
            'has_conflict' => !empty($conflicts),
            'conflicts' => $conflicts,
            'has_travel_warning' => $travelWarning !== null,
            'travel_warning' => $travelWarning
        ]);
    }
}
