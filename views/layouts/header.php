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
    <title><?= $pageTitle ?? 'HUIT Schedule Hub' ?> | Quản lý Lịch Giảng Dạy</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --accent: #f97316;
            --accent-hover: #ea580c;
            --bg-page: #f8fafc;
            --border-color: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.015em;
            font-size: 0.875rem;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .main-wrapper {
            flex: 1;
        }

        /* Modern Glassmorphic Navbar */
        .navbar-modern {
            background-color: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 0.65rem 0;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .brand-logo-box {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover .brand-logo-box {
            transform: scale(1.05);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.05rem;
            color: #0f172a !important;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-link-modern {
            color: #475569 !important;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.45rem 0.8rem !important;
            border-radius: 8px;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-modern:hover {
            color: #0f172a !important;
            background-color: #f1f5f9;
        }

        .nav-link-modern.active {
            color: var(--primary) !important;
            background-color: var(--primary-light);
            font-weight: 600;
        }

        /* Modern Cards */
        .card-custom {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03), 0 1px 2px -1px rgba(15, 23, 42, 0.02);
            background-color: #ffffff;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-header-custom {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.15rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e293b;
        }

        /* Modern KPI Cards */
        .kpi-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.1rem 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .kpi-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* Modern Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.85rem;
            padding: 0.48rem 0.8rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        /* Modern Buttons */
        .btn-modern-primary {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            font-weight: 500;
            border-radius: 8px;
            padding: 0.45rem 0.95rem;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-modern-primary:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.28);
        }

        .btn-modern-outline {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid var(--border-color);
            font-weight: 500;
            border-radius: 8px;
            padding: 0.45rem 0.9rem;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-modern-outline:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        /* Teacher Avatar circles */
        .teacher-avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        /* Modern Pastel Branch Badges */
        .badge-branch-1 {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
            font-weight: 500;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 0.72rem;
            display: inline-block;
        }

        .badge-branch-2 {
            background-color: #fff7ed !important;
            color: #c2410c !important;
            border: 1px solid #fed7aa !important;
            font-weight: 500;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 0.72rem;
            display: inline-block;
        }

        .badge-branch-3 {
            background-color: #faf5ff !important;
            color: #7e22ce !important;
            border: 1px solid #e9d5ff !important;
            font-weight: 500;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 0.72rem;
            display: inline-block;
        }

        .badge-branch-4 {
            background-color: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
            font-weight: 500;
            border-radius: 9999px;
            padding: 2px 8px;
            font-size: 0.72rem;
            display: inline-block;
        }

        /* Modern Linear Segmented Control for Campus Tabs */
        .campus-segmented-control {
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 11px;
            display: inline-flex;
            flex-wrap: wrap;
            gap: 4px;
            border: 1px solid #e2e8f0;
        }

        .campus-segment-item {
            font-size: 0.8rem;
            font-weight: 500;
            color: #64748b;
            padding: 6px 13px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        .campus-segment-item:hover {
            color: #0f172a;
            background-color: rgba(255, 255, 255, 0.75);
        }

        .campus-segment-item.active {
            color: #0f172a;
            background-color: #ffffff;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        }

        /* Modern Table Styling */
        .table-modern {
            margin-bottom: 0;
            font-size: 0.84rem;
        }

        .table-modern thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .table-modern tbody td {
            padding: 0.75rem 1rem;
            white-space: nowrap !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Modern Spacious Timetable Matrix */
        .table-matrix-spacious {
            min-width: 1250px;
            table-layout: fixed;
            background-color: #ffffff;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-matrix-spacious thead th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 0.82rem;
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            text-align: center;
            padding: 0.85rem 0.5rem;
        }

        .table-matrix-spacious thead th:last-child {
            border-right: none;
        }

        .table-matrix-spacious td {
            border-bottom: 1px solid var(--border-color);
            border-right: 1px solid var(--border-color);
            vertical-align: top !important;
            padding: 8px !important;
            background-color: #ffffff;
        }

        .table-matrix-spacious td:last-child {
            border-right: none;
        }

        .matrix-cell-spacious {
            min-height: 85px;
        }

        .matrix-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            padding: 8px 10px;
            margin-bottom: 6px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: left;
        }

        .matrix-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px -2px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .matrix-card-cs1 { 
            border-left: 3.5px solid #2563eb !important; 
            background: linear-gradient(to right, #f8faff 0%, #ffffff 40%);
        }
        .matrix-card-cs2 { 
            border-left: 3.5px solid #f97316 !important; 
            background: linear-gradient(to right, #fffaf5 0%, #ffffff 40%);
        }
        .matrix-card-cs3 { 
            border-left: 3.5px solid #9333ea !important; 
            background: linear-gradient(to right, #faf5ff 0%, #ffffff 40%);
        }
        .matrix-card-cs4 { 
            border-left: 3.5px solid #16a34a !important; 
            background: linear-gradient(to right, #f7fdf9 0%, #ffffff 40%);
        }

        .matrix-empty-slot {
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px dashed #e2e8f0;
            border-radius: 9px;
            color: #94a3b8;
            font-size: 0.78rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            background-color: #fafbfc;
        }

        .matrix-empty-slot:hover {
            border-color: #93c5fd;
            background-color: #eff6ff;
            color: #2563eb;
            transform: translateY(-1px);
        }

        .hover-text-primary:hover {
            color: var(--primary) !important;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card-custom { box-shadow: none !important; border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body>

<!-- Modern Minimal Navbar -->
<nav class="navbar navbar-expand-lg navbar-modern sticky-top no-print">
    <div class="container">
        <a class="navbar-brand text-nowrap py-0" href="index.php">
            <div class="brand-logo-box">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark">HUIT Schedule Hub</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-sm-inline-block fw-semibold" style="font-size: 0.65rem; padding: 2px 6px;">v2.0</span>
            </div>
        </a>

        <button class="navbar-toggler border-0 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <?php if (Auth::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link-modern <?= ($currentController === 'dashboard') ? 'active' : '' ?>" href="index.php?controller=dashboard&action=index">
                            <i class="fa-solid fa-chart-pie"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern <?= ($currentController === 'schedule' && $currentAction === 'index') ? 'active' : '' ?>" href="index.php?controller=schedule&action=index">
                            <i class="fa-solid fa-list-check"></i> Lịch dạy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern <?= ($currentController === 'schedule' && $currentAction === 'matrix') ? 'active' : '' ?>" href="index.php?controller=schedule&action=matrix">
                            <i class="fa-solid fa-table-cells"></i> Ma trận TKB
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern <?= ($currentController === 'teacher' && $currentAction === 'index') ? 'active' : '' ?>" href="index.php?controller=teacher&action=index">
                            <i class="fa-solid fa-users"></i> Giảng viên
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-modern <?= ($currentController === 'branch') ? 'active' : '' ?>" href="index.php?controller=branch&action=index">
                            <i class="fa-solid fa-building-columns"></i> Cơ sở
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link-modern <?= ($currentController === 'teacher' && $currentAction === 'portal') ? 'active' : '' ?>" href="index.php?controller=teacher&action=portal">
                        <i class="fa-solid fa-id-card-clip"></i> Cổng thông tin GV
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center">
                <?php if (Auth::check()): ?>
                    <li class="nav-item dropdown">
                        <a class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle py-1 px-2 rounded" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 30px; height: 30px; font-size: 0.78rem;">
                                <?= mb_substr($currentUser['full_name'] ?? 'U', 0, 1, 'UTF-8') ?>
                            </div>
                            <div class="text-start text-nowrap">
                                <span class="fw-semibold text-dark" style="font-size: 0.82rem;">
                                    <?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?>
                                </span>
                                <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.65rem;">
                                    <?= $currentRole === 'admin' ? 'Admin' : ($currentRole === 'staff' ? 'Phòng ĐT' : 'Giảng viên') ?>
                                </span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-1">
                            <li>
                                <a class="dropdown-item small" href="index.php?controller=teacher&action=portal">
                                    <i class="fa-solid fa-calendar-day me-2 text-primary"></i> Lịch dạy cá nhân
                                </a>
                            </li>
                            <?php if (Auth::isAdmin()): ?>
                            <li>
                                <a class="dropdown-item small" href="index.php?controller=schedule&action=create">
                                    <i class="fa-solid fa-plus me-2 text-success"></i> Thêm lịch mới
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item small text-danger" href="index.php?controller=auth&action=logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn-modern-primary" href="index.php?controller=auth&action=login">
                            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Container Content -->
<main class="main-wrapper py-3">
    <div class="<?= !empty($isFluid) ? 'container-fluid px-3 px-xl-4' : 'container' ?>">
        <!-- Flash Alert Message -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?> alert-dismissible fade show border py-2 px-3 mb-3" role="alert">
                <div class="d-flex align-items-center small">
                    <i class="fa-solid fa-circle-info me-2 fs-6"></i>
                    <div><?= $flash['message'] ?></div>
                </div>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
