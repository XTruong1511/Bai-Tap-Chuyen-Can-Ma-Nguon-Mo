<?php
$pageTitle = 'Chỉnh sửa Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-user-pen text-primary me-2"></i>Chỉnh sửa Giảng viên #<?= $teacher['id'] ?>
    </h5>
    <a href="index.php?controller=teacher&action=index" class="btn-modern-outline">
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
        <div class="card card-custom shadow-sm">
            <div class="card-header-custom">
                <i class="fa-solid fa-id-card me-2 text-primary"></i>Hồ sơ Giảng viên
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=teacher&action=edit&id=<?= $teacher['id'] ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_POST['full_name'] ?? $teacher['full_name']) ?>" required>
                    </div>

                    <!-- Khoa & Bộ môn -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="faculty" class="form-label fw-bold">Khoa quản lý <span class="text-danger">*</span></label>
                            <?php $currentFac = $_POST['faculty'] ?? $teacher['faculty'] ?? 'Khoa Công nghệ Thông tin'; ?>
                            <select class="form-select" id="faculty" name="faculty" required>
                                <option value="Khoa Công nghệ Thông tin" <?= ($currentFac === 'Khoa Công nghệ Thông tin') ? 'selected' : '' ?>>Khoa Công nghệ Thông tin</option>
                                <option value="Khoa Công nghệ Thực phẩm" <?= ($currentFac === 'Khoa Công nghệ Thực phẩm') ? 'selected' : '' ?>>Khoa Công nghệ Thực phẩm</option>
                                <option value="Khoa Quản trị Kinh doanh" <?= ($currentFac === 'Khoa Quản trị Kinh doanh') ? 'selected' : '' ?>>Khoa Quản trị Kinh doanh</option>
                                <option value="Khoa Ngoại ngữ" <?= ($currentFac === 'Khoa Ngoại ngữ') ? 'selected' : '' ?>>Khoa Ngoại ngữ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label fw-bold">Bộ môn trực thuộc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="department" name="department" 
                                   list="department_list"
                                   placeholder="Ví dụ: Bộ môn Công nghệ phần mềm"
                                   value="<?= htmlspecialchars($_POST['department'] ?? $teacher['department'] ?? 'Bộ môn Công nghệ phần mềm') ?>" required>
                            <datalist id="department_list">
                                <option value="Bộ môn Công nghệ phần mềm">
                                <option value="Bộ môn Mạng máy tính & An ninh mạng">
                                <option value="Bộ môn Hệ thống thông tin">
                                <option value="Bộ môn Khoa học dữ liệu & Trí tuệ nhân tạo">
                                <option value="Bộ môn Khoa học máy tính">
                                <option value="Bộ môn Thiết kế đồ họa & Đa phương tiện">
                                <option value="Bộ môn Công nghệ thực phẩm & Hóa sinh">
                            </datalist>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email HUIT <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? $teacher['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? $teacher['phone']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="expertise" class="form-label fw-bold">Chuyên môn chính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="expertise" name="expertise" 
                               value="<?= htmlspecialchars($_POST['expertise'] ?? $teacher['expertise']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=teacher&action=index" class="btn-modern-outline">Hủy bỏ</a>
                        <button type="submit" class="btn-modern-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
