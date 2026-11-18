<?php
use App\Helpers\Auth;
$currentUser = Auth::user();
$currentRole = Auth::role();
$currentController = $_GET['controller'] ?? ($currentRole === 'teacher' ? 'teacher' : 'dashboard');
$currentAction = $_GET['action'] ?? ($currentRole === 'teacher' ? 'portal' : 'index');
$flash = Auth::getFlash();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'HUIT Schedule Hub' ?> | Quản lý & Phân lịch Giảng dạy Đa cơ sở</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --huit-blue: #0b3c61;
            --huit-dark: #07253d;
            --huit-orange: #f37021;
            --huit-orange-hover: #d95e13;
            --huit-light: #f4f7fb;
            --huit-border: #e2e8f0;
            --font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--huit-light);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-wrapper {
            flex: 1;
        }

        /* Navbar Styling */
        .navbar-huit {
            background: linear-gradient(135deg, var(--huit-dark) 0%, var(--huit-blue) 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .huit-badge-brand {
            background-color: var(--huit-orange);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 0.85rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.12);
        }

        .nav-link i {
            margin-right: 6px;
        }

        /* Cards & Components */
        .card-custom {
            border: 1px solid var(--huit-border);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-header-custom {
            background-color: #ffffff;
            border-bottom: 1px solid var(--huit-border);
            padding: 1rem 1.25rem;
            font-weight: 700;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }

        .btn-huit-primary {
            background-color: var(--huit-blue);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 1.15rem;
            transition: all 0.2s;
        }

        .btn-huit-primary:hover {
            background-color: var(--huit-dark);
            color: #ffffff;
        }

        .btn-huit-orange {
            background-color: var(--huit-orange);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.5rem 1.15rem;
            transition: all 0.2s;
        }

        .btn-huit-orange:hover {
            background-color: var(--huit-orange-hover);
            color: #ffffff;
        }

        /* Branch Badges */
        .badge-branch-1 { background-color: #0b3c61; color: white; }
        .badge-branch-2 { background-color: #d97706; color: white; }
        .badge-branch-3 { background-color: #7c3aed; color: white; }
        .badge-branch-4 { background-color: #059669; color: white; }
        .badge-branch-default { background-color: #475569; color: white; }

        /* Timetable Matrix Grid */
        .table-matrix th, .table-matrix td {
            vertical-align: middle;
            text-align: center;
        }

        .matrix-cell {
            min-height: 85px;
            padding: 6px;
        }

        .schedule-pill {
            font-size: 0.78rem;
            border-radius: 6px;
            padding: 6px 8px;
            margin-bottom: 4px;
            text-align: left;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .empty-slot-btn {
            font-size: 0.75rem;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 4px;
            width: 100%;
            display: inline-block;
            text-decoration: none;
            transition: all 0.2s;
        }

        .empty-slot-btn:hover {
            background-color: #e2e8f0;
            color: #334155;
            border-color: #94a3b8;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .card-custom {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
</head>
<body>

<!-- Top Notification Bar / Quick Switcher -->
<div class="bg-dark text-white py-1 px-3 d-none d-md-block no-print" style="font-size: 0.8rem; background-color: #051a2c !important;">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <i class="fa-solid fa-graduation-cap text-warning me-1"></i>
            <strong>TRƯỜNG ĐẠI HỌC CÔNG THƯƠNG TP. HỒ CHÍ MINH (HUIT)</strong>
            <span class="text-white-50 ms-2">| Hệ thống Phân lịch Giảng dạy Đa cơ sở Thông minh</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50">Chuyển nhanh:</span>
            <a href="index.php?controller=auth&action=quick_login&role=admin" class="badge text-bg-warning text-decoration-none">
                <i class="fa-solid fa-user-shield me-1"></i> Admin
            </a>
            <a href="index.php?controller=auth&action=quick_login&role=teacher&id=1" class="badge text-bg-info text-decoration-none">
                <i class="fa-solid fa-chalkboard-user me-1"></i> GV: TS. Nguyễn Văn Hùng
            </a>
            <a href="index.php?controller=auth&action=quick_login&role=teacher&id=2" class="badge text-bg-success text-decoration-none">
                <i class="fa-solid fa-chalkboard-user me-1"></i> GV: ThS. Trần Thị Mai
            </a>
        </div>
    </div>
</div>

<!-- Main Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-huit sticky-top no-print">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa-solid fa-calendar-check text-warning fs-4"></i>
            <div>
                <span>HUIT Schedule Hub</span>
                <span class="huit-badge-brand ms-1">Multi-Campus</span>
            </div>
        </a>

        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="fa-solid fa-bars fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <?php if (Auth::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentController === 'dashboard') ? 'active' : '' ?>" href="index.php?controller=dashboard&action=index">
                            <i class="fa-solid fa-chart-pie"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentController === 'schedule' && $currentAction === 'index') ? 'active' : '' ?>" href="index.php?controller=schedule&action=index">
                            <i class="fa-solid fa-list-check"></i> Quản lý Lịch dạy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentController === 'schedule' && $currentAction === 'matrix') ? 'active' : '' ?>" href="index.php?controller=schedule&action=matrix">
                            <i class="fa-solid fa-table-cells"></i> Ma trận TKB & Tiết trống
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentController === 'teacher' && $currentAction === 'index') ? 'active' : '' ?>" href="index.php?controller=teacher&action=index">
                            <i class="fa-solid fa-users"></i> Giảng viên
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentController === 'branch') ? 'active' : '' ?>" href="index.php?controller=branch&action=index">
                            <i class="fa-solid fa-building-columns"></i> Cơ sở Đào tạo
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($currentController === 'teacher' && $currentAction === 'portal') ? 'active' : '' ?>" href="index.php?controller=teacher&action=portal">
                        <i class="fa-solid fa-id-card-clip"></i> Cổng thông tin GV
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center">
                <?php if (Auth::check()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                <?= mb_substr($currentUser['full_name'] ?? 'U', 0, 1, 'UTF-8') ?>
                            </div>
                            <div class="text-start">
                                <div class="text-white fw-semibold" style="font-size: 0.85rem; line-height: 1.2;">
                                    <?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?>
                                </div>
                                <span class="badge text-bg-light" style="font-size: 0.65rem;">
                                    <?= $currentRole === 'admin' ? 'Quản trị viên' : ($currentRole === 'staff' ? 'Phòng Đào tạo' : 'Giảng viên') ?>
                                </span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li class="dropdown-header">
                                Tài khoản: <strong><?= htmlspecialchars($currentUser['email'] ?? $currentUser['username'] ?? '') ?></strong>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=teacher&action=portal">
                                    <i class="fa-solid fa-calendar-day me-2 text-primary"></i> Lịch dạy cá nhân
                                </a>
                            </li>
                            <?php if (Auth::isAdmin()): ?>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=schedule&action=create">
                                    <i class="fa-solid fa-plus-circle me-2 text-success"></i> Thêm lịch mới
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="index.php?controller=auth&action=logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm fw-bold px-3" href="index.php?controller=auth&action=login">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Đăng nhập
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Container Content -->
<main class="main-wrapper py-4">
    <div class="container">
        <!-- Flash Alert Message -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <?php if ($flash['type'] === 'success'): ?>
                        <i class="fa-solid fa-circle-check fs-4 me-2 text-success"></i>
                    <?php elseif ($flash['type'] === 'danger'): ?>
                        <i class="fa-solid fa-triangle-exclamation fs-4 me-2 text-danger"></i>
                    <?php elseif ($flash['type'] === 'warning'): ?>
                        <i class="fa-solid fa-circle-exclamation fs-4 me-2 text-warning"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-circle-info fs-4 me-2 text-info"></i>
                    <?php endif; ?>
                    <div>
                        <?= $flash['message'] ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
