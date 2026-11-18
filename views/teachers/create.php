<?php
$pageTitle = 'Thêm Giảng viên mới';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-user-plus text-primary me-2"></i>Thêm Giảng viên Mới
        </h3>
        <p class="text-muted mb-0 small">Khai báo thông tin giảng viên và lĩnh vực chuyên môn để tối ưu thuật toán xếp lịch.</p>
    </div>
    <a href="index.php?controller=teacher&action=index" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fa-solid fa-id-card me-2 text-primary"></i>Hồ sơ Giảng viên
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=teacher&action=create">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               placeholder="Ví dụ: TS. Nguyễn Văn A hoặc ThS. Trần Thị B"
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email HUIT <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="tên@huit.edu.vn"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="090..."
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="expertise" class="form-label fw-bold">Chuyên môn chính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="expertise" name="expertise" 
                               placeholder="Ví dụ: Công nghệ phần mềm & Lập trình Web PHP, Trí tuệ nhân tạo..."
                               value="<?= htmlspecialchars($_POST['expertise'] ?? '') ?>" required>
                        <div class="form-text small">
                            Các từ khóa trong mục này sẽ được hệ thống phân tích để gợi ý môn học phù hợp.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Mật khẩu đăng nhập Cổng thông tin</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Mặc định: 123456 nếu để trống">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=teacher&action=index" class="btn btn-light px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-check me-1"></i> Lưu thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
