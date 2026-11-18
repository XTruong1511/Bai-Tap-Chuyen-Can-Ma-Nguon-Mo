<?php

namespace App\Controllers;

use App\Config\Database;
use App\Helpers\Auth;
use App\Models\Teacher;
use PDO;

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function login(): void
    {
        if (Auth::check()) {
            if (Auth::isTeacher()) {
                header('Location: index.php?controller=teacher&action=portal');
            } else {
                header('Location: index.php?controller=dashboard&action=index');
            }
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $error = 'Vui lòng nhập tên đăng nhập/email và mật khẩu.';
            } else {
                // 1. Check in `users` table (admin/staff)
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    Auth::login([
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'full_name' => $user['full_name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                    ]);
                    Auth::setFlash('success', "Xin chào, {$user['full_name']}! Chúc bạn một ngày làm việc hiệu quả.");
                    header('Location: index.php?controller=dashboard&action=index');
                    exit;
                }

                // 2. Check in `teachers` table (giảng viên tra cứu lịch)
                $stmtTeacher = $this->db->prepare("SELECT * FROM teachers WHERE email = ?");
                $stmtTeacher->execute([$username]);
                $teacher = $stmtTeacher->fetch();

                if ($teacher && password_verify($password, $teacher['password'])) {
                    Auth::login([
                        'id' => $teacher['id'],
                        'teacher_id' => $teacher['id'],
                        'username' => $teacher['email'],
                        'full_name' => $teacher['full_name'],
                        'email' => $teacher['email'],
                        'role' => 'teacher',
                    ]);
                    Auth::setFlash('success', "Xin chào Thầy/Cô {$teacher['full_name']} đến với Cổng thông tin Giảng viên HUIT.");
                    header('Location: index.php?controller=teacher&action=portal');
                    exit;
                }

                $error = 'Tài khoản hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại!';
            }
        }

        // Quick demo login list
        $sampleTeachers = (new Teacher())->all();

        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function quickLogin(): void
    {
        $role = $_GET['role'] ?? 'admin';
        $id = (int) ($_GET['id'] ?? 1);

        if ($role === 'admin') {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            if ($user) {
                Auth::login([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ]);
                Auth::setFlash('success', "Đăng nhập nhanh với quyền Quản trị viên: {$user['full_name']}");
                header('Location: index.php?controller=dashboard&action=index');
                exit;
            }
        } elseif ($role === 'teacher') {
            $stmt = $this->db->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            $teacher = $stmt->fetch();
            if ($teacher) {
                Auth::login([
                    'id' => $teacher['id'],
                    'teacher_id' => $teacher['id'],
                    'username' => $teacher['email'],
                    'full_name' => $teacher['full_name'],
                    'email' => $teacher['email'],
                    'role' => 'teacher',
                ]);
                Auth::setFlash('success', "Đăng nhập nhanh Cổng thông tin Giảng viên: {$teacher['full_name']}");
                header('Location: index.php?controller=teacher&action=portal');
                exit;
            }
        }

        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
