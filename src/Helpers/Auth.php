<?php

namespace App\Helpers;

class Auth
{
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::init();
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u['id'] ?? null;
    }

    public static function role(): ?string
    {
        $u = self::user();
        return $u['role'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin' || self::role() === 'staff';
    }

    public static function isTeacher(): bool
    {
        return self::role() === 'teacher';
    }

    public static function login(array $user): void
    {
        self::init();
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        self::init();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            self::setFlash('warning', 'Vui lòng đăng nhập để truy cập hệ thống.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            self::setFlash('danger', 'Bạn không có quyền quản trị viên để thực hiện thao tác này.');
            header('Location: index.php?controller=teacher&action=portal');
            exit;
        }
    }

    public static function setFlash(string $type, string $message): void
    {
        self::init();
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function getFlash(): ?array
    {
        self::init();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
