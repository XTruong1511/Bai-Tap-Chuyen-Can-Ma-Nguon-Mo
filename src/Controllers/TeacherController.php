<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Branch;

class TeacherController
{
    private Teacher $teacherModel;
    private Schedule $scheduleModel;
    private Branch $branchModel;

    public function __construct()
    {
        Auth::requireLogin();
        $this->teacherModel = new Teacher();
        $this->scheduleModel = new Schedule();
        $this->branchModel = new Branch();
    }

    public function index(): void
    {
        Auth::requireAdmin();
        $teachers = $this->teacherModel->all();
        require_once __DIR__ . '/../../views/teachers/index.php';
    }

    public function create(): void
    {
        Auth::requireAdmin();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $expertise = trim($_POST['expertise'] ?? '');
            $password = trim($_POST['password'] ?? '123456');

            if (empty($fullName)) $errors[] = 'Vui lòng nhập họ và tên.';
            if (empty($email)) $errors[] = 'Vui lòng nhập email.';
            if (empty($phone)) $errors[] = 'Vui lòng nhập số điện thoại.';
            if (empty($expertise)) $errors[] = 'Vui lòng nhập chuyên môn chính.';

            if (empty($errors)) {
                $existing = $this->teacherModel->findByEmail($email);
                if ($existing) {
                    $errors[] = 'Email này đã tồn tại trong hệ thống.';
                } else {
                    $this->teacherModel->create([
                        'full_name' => $fullName,
                        'email' => $email,
                        'phone' => $phone,
                        'expertise' => $expertise,
                        'password' => $password
                    ]);
                    Auth::setFlash('success', "Thêm giảng viên {$fullName} thành công!");
                    header('Location: index.php?controller=teacher&action=index');
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../views/teachers/create.php';
    }

    public function edit(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_GET['id'] ?? 0);
        $teacher = $this->teacherModel->find($id);

        if (!$teacher) {
            Auth::setFlash('danger', 'Không tìm thấy giảng viên.');
            header('Location: index.php?controller=teacher&action=index');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $expertise = trim($_POST['expertise'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($fullName)) $errors[] = 'Vui lòng nhập họ và tên.';
            if (empty($email)) $errors[] = 'Vui lòng nhập email.';
            if (empty($phone)) $errors[] = 'Vui lòng nhập số điện thoại.';
            if (empty($expertise)) $errors[] = 'Vui lòng nhập chuyên môn chính.';

            if (empty($errors)) {
                $existing = $this->teacherModel->findByEmail($email);
                if ($existing && $existing['id'] != $id) {
                    $errors[] = 'Email này đã được sử dụng bởi giảng viên khác.';
                } else {
                    $data = [
                        'full_name' => $fullName,
                        'email' => $email,
                        'phone' => $phone,
                        'expertise' => $expertise
                    ];
                    if (!empty($password)) {
                        $data['password'] = $password;
                    }
                    $this->teacherModel->update($id, $data);
                    Auth::setFlash('success', "Cập nhật thông tin giảng viên {$fullName} thành công!");
                    header('Location: index.php?controller=teacher&action=index');
                    exit;
                }
            }
        }

        require_once __DIR__ . '/../../views/teachers/edit.php';
    }

    public function delete(): void
    {
        Auth::requireAdmin();
        $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            $this->teacherModel->delete($id);
            Auth::setFlash('success', 'Đã xóa giảng viên thành công.');
        }
        header('Location: index.php?controller=teacher&action=index');
        exit;
    }

    /**
     * Cổng thông tin Giảng viên (Teacher Portal)
     * Tra cứu và hiển thị lịch giảng dạy cá nhân trực quan
     */
    public function portal(): void
    {
        $currentTeacherId = null;

        if (Auth::isTeacher()) {
            $currentTeacherId = Auth::user()['teacher_id'] ?? Auth::id();
        } else {
            // Admin can preview any teacher's portal
            $currentTeacherId = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 1;
        }

        $teacher = $this->teacherModel->find($currentTeacherId);
        if (!$teacher) {
            $allTeachers = $this->teacherModel->all();
            if (!empty($allTeachers)) {
                $teacher = $allTeachers[0];
                $currentTeacherId = $teacher['id'];
            }
        }

        $schedules = $teacher ? $this->teacherModel->getSchedules($currentTeacherId) : [];
        $timetableMatrix = $this->scheduleModel->getTimetableMatrix(null, $currentTeacherId);
        $allTeachers = $this->teacherModel->all();

        // Group schedules by campus/branch to clearly notify teacher where to go
        $branchGrouped = [];
        foreach ($schedules as $s) {
            $branchGrouped[$s['branch_name']][] = $s;
        }

        require_once __DIR__ . '/../../views/teachers/portal.php';
    }
}
