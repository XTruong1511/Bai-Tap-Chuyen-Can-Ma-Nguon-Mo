<?php
$pageTitle = 'Thêm Cơ sở mới';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-plus-circle text-primary me-2"></i>Thêm Cơ sở Mới
    </h4>
    <a href="index.php?controller=branch&action=index" class="btn btn-outline-secondary btn-sm text-nowrap">
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
                <form method="POST" action="index.php?controller=branch&action=create">
                    <div class="mb-3">
                        <label for="branch_name" class="form-label fw-bold">Tên cơ sở đào tạo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" 
                               placeholder="Ví dụ: Cơ sở chính (Lê Trọng Tấn)"
                               value="<?= htmlspecialchars($_POST['branch_name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" 
                               placeholder="Ví dụ: 140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP.HCM"
                               value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=branch&action=index" class="btn-modern-outline">Hủy bỏ</a>
                        <button type="submit" class="btn-modern-primary">
                            <i class="fa-solid fa-check"></i> Lưu cơ sở
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
