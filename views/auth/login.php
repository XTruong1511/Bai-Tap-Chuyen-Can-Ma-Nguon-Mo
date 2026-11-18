<?php
$pageTitle = 'Đăng nhập';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-4">
        <div class="card card-custom p-3 p-md-4 shadow-sm">
            <div class="text-center mb-4">
                <div class="brand-logo-box mx-auto mb-2" style="width: 42px; height: 42px; font-size: 1.25rem;">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h5 class="fw-bold text-dark mb-0">HUIT Schedule Hub</h5>
                <span class="text-muted small">Cổng phân lịch & tra cứu thời khóa biểu</span>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small border-0 mb-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=auth&action=login" class="mb-3">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-medium text-dark mb-1">Tài khoản hoặc Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 py-1 px-2"><i class="fa-regular fa-user small"></i></span>
                        <input type="text" class="form-control border-start-0" id="username" name="username" 
                               placeholder="admin hoặc email@huit.edu.vn" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-medium text-dark mb-1">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0 py-1 px-2"><i class="fa-solid fa-lock small"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" 
                               placeholder="Nhập mật khẩu" required>
                    </div>
                </div>

                <button type="submit" class="btn-modern-primary w-100 justify-content-center py-2 mt-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </button>
            </form>

            <hr class="my-3 opacity-25">

            <!-- Quick Demo Credentials -->
            <div>
                <div class="text-muted small fw-medium mb-2">Đăng nhập nhanh:</div>
                <div class="d-grid gap-1">
                    <a href="index.php?controller=auth&action=quick_login&role=admin" class="btn btn-sm btn-light border text-start d-flex justify-content-between align-items-center py-1 px-2">
                        <span><i class="fa-solid fa-user-shield text-primary me-2"></i><strong>Quản trị viên</strong></span>
                        <span class="badge bg-primary-subtle text-primary small">Admin</span>
                    </a>
                    <a href="index.php?controller=auth&action=quick_login&role=teacher&id=1" class="btn btn-sm btn-light border text-start d-flex justify-content-between align-items-center py-1 px-2">
                        <span><i class="fa-solid fa-chalkboard-user text-success me-2"></i><strong>TS. Nguyễn Văn Hùng</strong></span>
                        <span class="badge bg-success-subtle text-success small">GV</span>
                    </a>
                    <a href="index.php?controller=auth&action=quick_login&role=teacher&id=2" class="btn btn-sm btn-light border text-start d-flex justify-content-between align-items-center py-1 px-2">
                        <span><i class="fa-solid fa-chalkboard-user text-info me-2"></i><strong>ThS. Trần Thị Mai</strong></span>
                        <span class="badge bg-info-subtle text-info small">GV</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
