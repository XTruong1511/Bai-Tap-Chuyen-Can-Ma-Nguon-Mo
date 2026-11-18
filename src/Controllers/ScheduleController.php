<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Schedule;

class ScheduleController
{
    private Schedule $scheduleModel;
    private Teacher $teacherModel;
    private Branch $branchModel;

    public function __construct()
    {
        Auth::requireLogin();
        $this->scheduleModel = new Schedule();
        $this->teacherModel = new Teacher();
        $this->branchModel = new Branch();
    }

    public function index(): void
    {
        $filters = [
            'branch_id' => $_GET['branch_id'] ?? '',
            'teacher_id' => $_GET['teacher_id'] ?? '',
            'day_of_week' => $_GET['day_of_week'] ?? '',
            'shift_or_periods' => $_GET['shift_or_periods'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        $schedules = $this->scheduleModel->all($filters);
        $branches = $this->branchModel->all();
        $teachers = $this->teacherModel->all();

        require_once __DIR__ . '/../../views/schedules/index.php';
    }

    public function matrix(): void
    {
        $branchId = isset($_GET['branch_id']) && $_GET['branch_id'] !== '' ? (int)$_GET['branch_id'] : null;
        $teacherId = isset($_GET['teacher_id']) && $_GET['teacher_id'] !== '' ? (int)$_GET['teacher_id'] : null;

        $matrix = $this->scheduleModel->getTimetableMatrix($branchId, $teacherId);
        $branches = $this->branchModel->all();
        $teachers = $this->teacherModel->all();

        require_once __DIR__ . '/../../views/schedules/matrix.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $errors = [];
        $branches = $this->branchModel->all();
        $teachers = $this->teacherModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teacherId = (int) ($_POST['teacher_id'] ?? 0);
            $branchId = (int) ($_POST['branch_id'] ?? 0);
            $subjectName = trim($_POST['subject_name'] ?? '');
            $room = trim($_POST['room'] ?? '');
            $dayOfWeek = trim($_POST['day_of_week'] ?? '');
            $shiftOrPeriods = trim($_POST['shift_or_periods'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            // Validation
            if (!$teacherId) $errors[] = 'Vui lòng chọn giảng viên.';
            if (!$branchId) $errors[] = 'Vui lòng chọn cơ sở đào tạo.';
            if (empty($subjectName)) $errors[] = 'Vui lòng nhập tên học phần.';
            if (empty($room)) $errors[] = 'Vui lòng nhập phòng học.';
            if (empty($dayOfWeek)) $errors[] = 'Vui lòng chọn thứ trong tuần.';
            if (empty($shiftOrPeriods)) $errors[] = 'Vui lòng chọn ca/tiết học.';

            if (empty($errors)) {
                // Kiểm tra xung đột lịch dạy
                $conflicts = $this->scheduleModel->checkConflict($teacherId, $branchId, $room, $dayOfWeek, $shiftOrPeriods);

                if (!empty($conflicts)) {
                    foreach ($conflicts as $c) {
                        $errors[] = "<strong>{$c['title']}:</strong> {$c['message']}";
                    }
                } else {
                    // Tối ưu hóa di chuyển: kiểm tra cảnh báo
                    $travelWarning = $this->scheduleModel->checkTravelWarning($teacherId, $dayOfWeek, $shiftOrPeriods, $branchId);

                    $this->scheduleModel->create([
                        'teacher_id' => $teacherId,
                        'branch_id' => $branchId,
                        'subject_name' => $subjectName,
                        'room' => $room,
                        'day_of_week' => $dayOfWeek,
                        'shift_or_periods' => $shiftOrPeriods,
                        'notes' => $notes
                    ]);

                    if ($travelWarning) {
                        Auth::setFlash('warning', "Đã thêm lịch thành công! <strong>Lưu ý:</strong> {$travelWarning['message']}");
                    } else {
                        Auth::setFlash('success', 'Thêm lịch giảng dạy mới thành công và đã được kiểm tra không trùng lịch!');
                    }

                    header('Location: index.php?controller=schedule&action=index');
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../views/schedules/create.php';
    }

    public function edit(): void
    {
        Auth::requireAdmin();

        $id = (int) ($_GET['id'] ?? 0);
        $schedule = $this->scheduleModel->find($id);

        if (!$schedule) {
            Auth::setFlash('danger', 'Không tìm thấy lịch giảng dạy yêu cầu.');
            header('Location: index.php?controller=schedule&action=index');
            exit;
        }

        $errors = [];
        $branches = $this->branchModel->all();
        $teachers = $this->teacherModel->all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $teacherId = (int) ($_POST['teacher_id'] ?? 0);
            $branchId = (int) ($_POST['branch_id'] ?? 0);
            $subjectName = trim($_POST['subject_name'] ?? '');
            $room = trim($_POST['room'] ?? '');
            $dayOfWeek = trim($_POST['day_of_week'] ?? '');
            $shiftOrPeriods = trim($_POST['shift_or_periods'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            if (!$teacherId) $errors[] = 'Vui lòng chọn giảng viên.';
            if (!$branchId) $errors[] = 'Vui lòng chọn cơ sở đào tạo.';
            if (empty($subjectName)) $errors[] = 'Vui lòng nhập tên học phần.';
            if (empty($room)) $errors[] = 'Vui lòng nhập phòng học.';
            if (empty($dayOfWeek)) $errors[] = 'Vui lòng chọn thứ trong tuần.';
            if (empty($shiftOrPeriods)) $errors[] = 'Vui lòng chọn ca/tiết học.';

            if (empty($errors)) {
                // Kiểm tra xung đột trừ lịch hiện tại
                $conflicts = $this->scheduleModel->checkConflict($teacherId, $branchId, $room, $dayOfWeek, $shiftOrPeriods, $id);

                if (!empty($conflicts)) {
                    foreach ($conflicts as $c) {
                        $errors[] = "<strong>{$c['title']}:</strong> {$c['message']}";
                    }
                } else {
                    $travelWarning = $this->scheduleModel->checkTravelWarning($teacherId, $dayOfWeek, $shiftOrPeriods, $branchId, $id);

                    $this->scheduleModel->update($id, [
                        'teacher_id' => $teacherId,
                        'branch_id' => $branchId,
                        'subject_name' => $subjectName,
                        'room' => $room,
                        'day_of_week' => $dayOfWeek,
                        'shift_or_periods' => $shiftOrPeriods,
                        'notes' => $notes
                    ]);

                    if ($travelWarning) {
                        Auth::setFlash('warning', "Cập nhật lịch thành công! <strong>Lưu ý:</strong> {$travelWarning['message']}");
                    } else {
                        Auth::setFlash('success', 'Cập nhật lịch giảng dạy thành công!');
                    }

                    header('Location: index.php?controller=schedule&action=index');
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../views/schedules/edit.php';
    }

    public function delete(): void
    {
        Auth::requireAdmin();

        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->scheduleModel->delete($id);
            Auth::setFlash('success', 'Đã xóa lịch giảng dạy thành công.');
        }

        header('Location: index.php?controller=schedule&action=index');
        exit;
    }
}
