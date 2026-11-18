<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Helpers\Auth;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Start session
Auth::init();

// Simple routing mechanism
$controllerParam = $_GET['controller'] ?? (Auth::check() ? (Auth::isTeacher() ? 'teacher' : 'dashboard') : 'auth');
$actionParam = $_GET['action'] ?? (Auth::check() ? (Auth::isTeacher() ? 'portal' : 'index') : 'login');

// Map controller names to classes
$controllerMap = [
    'auth' => \App\Controllers\AuthController::class,
    'dashboard' => \App\Controllers\DashboardController::class,
    'schedule' => \App\Controllers\ScheduleController::class,
    'teacher' => \App\Controllers\TeacherController::class,
    'branch' => \App\Controllers\BranchController::class,
    'api' => \App\Controllers\ApiController::class,
];

// Convert action kebab-case to camelCase if needed
$actionName = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $actionParam))));

if (isset($controllerMap[$controllerParam])) {
    $controllerClass = $controllerMap[$controllerParam];
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        if (method_exists($controllerInstance, $actionName)) {
            $controllerInstance->$actionName();
            exit;
        }
    }
}

// 404 fallback
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | HUIT Schedule Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center p-5 bg-white rounded shadow-sm">
        <h1 class="display-1 text-danger fw-bold">404</h1>
        <h3 class="mb-3">Không tìm thấy trang yêu cầu</h3>
        <p class="text-muted mb-4">Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
        <a href="index.php" class="btn btn-primary px-4 py-2">Quay lại Trang Chủ</a>
    </div>
</body>
</html>
