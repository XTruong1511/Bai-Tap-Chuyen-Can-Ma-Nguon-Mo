<?php
$pageTitle = 'Chỉnh sửa Cơ sở';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Chỉnh sửa Cơ sở #<?= $branch['id'] ?>
        </h3>
        <p class="text-muted mb-0 small">Cập nhật tên hoặc địa chỉ cơ sở đào tạo HUIT.</p>
    </div>
    <a href="index.php?controller=branch&action=index" class="btn btn-outline-secondary btn-sm">
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
    <div class="col-lg-6">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fa-solid fa-building-columns me-2 text-primary"></i>Thông tin Cơ sở
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=branch&action=edit&id=<?= $branch['id'] ?>">
                    <div class="mb-3">
                        <label for="branch_name" class="form-label fw-bold">Tên cơ sở đào tạo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" 
                               value="<?= htmlspecialchars($_POST['branch_name'] ?? $branch['branch_name']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" 
                               value="<?= htmlspecialchars($_POST['address'] ?? $branch['address']) ?>" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=branch&action=index" class="btn btn-light px-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Cập nhật cơ sở
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
