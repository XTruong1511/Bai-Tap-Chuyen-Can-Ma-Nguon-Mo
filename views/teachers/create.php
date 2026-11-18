<?php
$pageTitle = 'Thêm Giảng viên mới';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Teacher;

$allFaculties = Teacher::HUIT_FACULTIES;
$selectedFac = $_POST['faculty'] ?? array_key_first($allFaculties);
$selectedDept = $_POST['department'] ?? ($allFaculties[$selectedFac][0] ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-user-plus text-primary me-2"></i>Thêm Giảng viên Mới
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
    <div class="col-lg-8">
        <div class="card card-custom shadow-sm">
            <div class="card-header-custom">
                <i class="fa-solid fa-id-card me-2 text-primary"></i>Hồ sơ Giảng viên HUIT
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=teacher&action=create">
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               placeholder="Ví dụ: TS. Nguyễn Văn A hoặc ThS. Trần Thị B"
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                    </div>

                    <!-- Khoa & Bộ môn -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="faculty" class="form-label fw-bold">Khoa quản lý <span class="text-danger">*</span></label>
                            <select class="form-select" id="faculty" name="faculty" required>
                                <?php foreach ($allFaculties as $facName => $depts): ?>
                                    <option value="<?= htmlspecialchars($facName) ?>" <?= ($selectedFac === $facName) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($facName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label fw-bold">Bộ môn trực thuộc <span class="text-danger">*</span></label>
                            <select class="form-select" id="department" name="department" required>
                                <?php foreach ($allFaculties[$selectedFac] ?? [] as $d): ?>
                                    <option value="<?= htmlspecialchars($d) ?>" <?= ($selectedDept === $d) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
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
                               placeholder="Ví dụ: Công nghệ chế biến thực phẩm, Quản trị Marketing, An toàn thông tin..."
                               value="<?= htmlspecialchars($_POST['expertise'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Mật khẩu đăng nhập Cổng thông tin</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Mặc định: 123456 nếu để trống">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=teacher&action=index" class="btn-modern-outline">Hủy</a>
                        <button type="submit" class="btn-modern-primary">
                            <i class="fa-solid fa-check"></i> Lưu Giảng viên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const facultySelect = document.getElementById('faculty');
    const deptSelect = document.getElementById('department');
    const facultyMap = <?= json_encode($allFaculties) ?>;

    facultySelect.addEventListener('change', function () {
        const fac = this.value;
        deptSelect.innerHTML = '';
        if (facultyMap[fac]) {
            facultyMap[fac].forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                deptSelect.appendChild(opt);
            });
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
