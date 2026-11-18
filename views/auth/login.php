<?php
$pageTitle = 'Đăng nhập hệ thống';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center py-4">
    <div class="col-md-8 col-lg-5">
        <div class="card card-custom shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-2">
                        <i class="fa-solid fa-graduation-cap fa-2x" style="color: var(--huit-blue);"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">HUIT Schedule Hub</h4>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?controller=auth&action=login" class="mb-4">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Tên đăng nhập hoặc Email HUIT</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-user text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="username" name="username" 
                                   placeholder="admin hoặc email@huit.edu.vn" required autofocus>
                        </div>
                        <div class="form-text small">Admin: <code>admin</code> | Giảng viên: <code>hungnv@huit.edu.vn</code></div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" class="form-control border-start-0" id="password" name="password" 
                                   placeholder="Nhập mật khẩu" required>
                        </div>
                        <div class="form-text small">Mật khẩu admin: <code>admin123</code> | Giảng viên: <code>123456</code></div>
                    </div>

                    <button type="submit" class="btn btn-huit-primary w-100 py-2 fw-bold">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Đăng nhập hệ thống
                    </button>
                </form>

                <hr class="my-4">

                <!-- One-click Demo Credentials -->
                <div class="p-3 bg-light rounded-3">
                    <div class="fw-bold text-dark small mb-2 d-flex align-items-center">
                        <i class="fa-solid fa-bolt text-warning me-1"></i> Đăng nhập nhanh để trải nghiệm:
                    </div>
                    <div class="d-grid gap-2">
                        <a href="index.php?controller=auth&action=quick_login&role=admin" class="btn btn-outline-primary btn-sm text-start">
                            <i class="fa-solid fa-user-shield me-2"></i> <strong>Admin</strong> (Toàn quyền quản trị & phân lịch)
                        </a>
                        <a href="index.php?controller=auth&action=quick_login&role=teacher&id=1" class="btn btn-outline-success btn-sm text-start">
                            <i class="fa-solid fa-chalkboard-user me-2"></i> <strong>TS. Nguyễn Văn Hùng</strong> (Cổng thông tin GV)
                        </a>
                        <a href="index.php?controller=auth&action=quick_login&role=teacher&id=2" class="btn btn-outline-secondary btn-sm text-start">
                            <i class="fa-solid fa-chalkboard-user me-2"></i> <strong>ThS. Trần Thị Mai</strong> (Cổng thông tin GV)
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
