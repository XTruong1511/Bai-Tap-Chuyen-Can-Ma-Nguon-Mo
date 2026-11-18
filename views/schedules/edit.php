<?php
$pageTitle = 'Chỉnh sửa lịch giảng dạy';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;

$currentTeacherObj = null;
$targetTeacherId = (int)($_POST['teacher_id'] ?? $schedule['teacher_id'] ?? 0);
foreach ($teachers as $t) {
    if ($t['id'] == $targetTeacherId) {
        $currentTeacherObj = $t;
        break;
    }
}
$initialFaculty = $currentTeacherObj['faculty'] ?? ($_POST['faculty'] ?? '');
$initialDept = $currentTeacherObj['department'] ?? ($_POST['department'] ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Chỉnh sửa Lịch Giảng Dạy #<?= $schedule['id'] ?>
    </h5>
    <a href="index.php?controller=schedule&action=index" class="btn-modern-outline">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <div class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Không thể cập nhật lịch:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header-custom">
                <i class="fa-solid fa-calendar-check me-2 text-primary"></i>Chi tiết Lịch Giảng Dạy
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=schedule&action=edit&id=<?= $schedule['id'] ?>">
                    <div class="mb-3">
                        <label for="subject_name" class="form-label fw-bold">Tên học phần <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                               value="<?= htmlspecialchars($_POST['subject_name'] ?? $schedule['subject_name']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="branch_id" class="form-label fw-bold">Cơ sở đào tạo <span class="text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (($schedule['branch_id'] == $b['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="room" class="form-label fw-bold">Phòng học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="room" name="room" 
                                   value="<?= htmlspecialchars($_POST['room'] ?? $schedule['room']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="day_of_week" class="form-label fw-bold">Thứ trong tuần <span class="text-danger">*</span></label>
                            <select class="form-select" id="day_of_week" name="day_of_week" required>
                                <?php foreach (Schedule::DAYS as $d): ?>
                                    <option value="<?= $d ?>" <?= (($schedule['day_of_week'] == $d)) ? 'selected' : '' ?>>
                                        <?= $d ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="shift_or_periods" class="form-label fw-bold">Ca / Tiết học <span class="text-danger">*</span></label>
                            <select class="form-select" id="shift_or_periods" name="shift_or_periods" required>
                                <?php foreach (Schedule::SHIFTS as $sh): ?>
                                    <option value="<?= $sh ?>" <?= (($schedule['shift_or_periods'] == $sh)) ? 'selected' : '' ?>>
                                        <?= $sh ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Teacher Selection (Khoa -> Bộ môn -> Giảng viên) -->
                    <div class="p-3 rounded border bg-light-subtle mb-3">
                        <label class="form-label fw-bold mb-2 text-dark">
                            <i class="fa-solid fa-chalkboard-user text-primary me-1"></i> Phân công Giảng viên <span class="text-danger">*</span>
                        </label>

                        <!-- 1. Chọn Khoa & 2. Chọn Bộ môn -->
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="faculty_select" class="form-label small fw-semibold text-muted mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary me-1">1</span> Chọn Khoa
                                </label>
                                <select class="form-select form-select-sm" id="faculty_select">
                                    <option value="">-- Chọn Khoa --</option>
                                    <?php foreach (array_keys($facultiesAndDepartments) as $fac): ?>
                                        <option value="<?= htmlspecialchars($fac) ?>" <?= ($initialFaculty === $fac) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fac) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="department_select" class="form-label small fw-semibold text-muted mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary me-1">2</span> Chọn Bộ môn
                                </label>
                                <select class="form-select form-select-sm" id="department_select" <?= empty($initialFaculty) ? 'disabled' : '' ?>>
                                    <?php if (empty($initialFaculty)): ?>
                                        <option value="">-- Vui lòng chọn Khoa trước --</option>
                                    <?php else: ?>
                                        <option value="">-- Tất cả Bộ môn trong Khoa --</option>
                                        <?php foreach ($facultiesAndDepartments[$initialFaculty] ?? [] as $dept): ?>
                                            <option value="<?= htmlspecialchars($dept) ?>" <?= ($initialDept === $dept) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($dept) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- 3. Giảng viên phụ trách (tên sạch, không có đuôi ngoài sau) -->
                        <div>
                            <label for="teacher_id" class="form-label small fw-semibold text-muted mb-1">
                                <span class="badge bg-primary text-white me-1">3</span> Giảng viên <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">-- Chọn Giảng viên --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>" 
                                            data-faculty="<?= htmlspecialchars($t['faculty'] ?? '') ?>" 
                                            data-department="<?= htmlspecialchars($t['department'] ?? '') ?>"
                                            <?= (($schedule['teacher_id'] == $t['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"><?= htmlspecialchars($_POST['notes'] ?? $schedule['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=schedule&action=index" class="btn-modern-outline">Hủy</a>
                        <button type="submit" class="btn-modern-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Cập nhật Lịch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const teacherSelect = document.getElementById('teacher_id');

    const facultyDeptMap = <?= json_encode($facultiesAndDepartments) ?>;
    const allTeacherOptions = Array.from(teacherSelect.querySelectorAll('option')).filter(opt => opt.value !== "");

    // 1. Update Department dropdown strictly based on selected faculty
    function updateDepartmentOptions(selectedFaculty, preselectedDept = '') {
        departmentSelect.innerHTML = '';
        if (selectedFaculty && facultyDeptMap[selectedFaculty]) {
            departmentSelect.disabled = false;
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Tất cả Bộ môn trong Khoa --';
            departmentSelect.appendChild(defaultOpt);

            facultyDeptMap[selectedFaculty].forEach(dept => {
                const opt = document.createElement('option');
                opt.value = dept;
                opt.textContent = dept;
                if (preselectedDept && dept === preselectedDept) {
                    opt.selected = true;
                }
                departmentSelect.appendChild(opt);
            });
            if (preselectedDept) {
                departmentSelect.value = preselectedDept;
            }
        } else {
            departmentSelect.disabled = true;
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Vui lòng chọn Khoa trước --';
            departmentSelect.appendChild(defaultOpt);
            departmentSelect.value = '';
        }
    }

    // 2. Filter teacher select based on selected Faculty and Department
    function filterTeachers() {
        const selectedFaculty = facultySelect.value;
        const selectedDept = departmentSelect.value;
        const currentTeacherVal = teacherSelect.value;

        teacherSelect.innerHTML = '';

        if (!selectedFaculty) {
            teacherSelect.innerHTML = '<option value="">-- Vui lòng chọn Khoa ở trên --</option>';
            allTeacherOptions.forEach(opt => {
                const clone = opt.cloneNode(true);
                if (clone.value === currentTeacherVal) clone.selected = true;
                teacherSelect.appendChild(clone);
            });
            return;
        }

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = selectedDept ? '-- Chọn Giảng viên thuộc bộ môn này --' : '-- Chọn Giảng viên thuộc khoa này --';
        teacherSelect.appendChild(defaultOpt);

        let matchCount = 0;
        allTeacherOptions.forEach(opt => {
            const optFac = opt.getAttribute('data-faculty');
            const optDept = opt.getAttribute('data-department');

            const matchFac = (optFac === selectedFaculty);
            const matchDept = !selectedDept || (optDept === selectedDept);

            if (matchFac && matchDept) {
                matchCount++;
                const clone = opt.cloneNode(true);
                if (clone.value === currentTeacherVal) {
                    clone.selected = true;
                }
                teacherSelect.appendChild(clone);
            }
        });

        if (matchCount === 0) {
            defaultOpt.textContent = '-- Chưa có giảng viên thuộc bộ môn này --';
        }
    }

    // 3. When Faculty changes
    facultySelect.addEventListener('change', function () {
        const selectedFaculty = this.value;
        updateDepartmentOptions(selectedFaculty);
        filterTeachers();
    });

    // 4. When Department changes
    departmentSelect.addEventListener('change', function () {
        filterTeachers();
    });

    // 5. When Teacher changes directly
    teacherSelect.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const fac = selectedOpt.getAttribute('data-faculty');
            const dept = selectedOpt.getAttribute('data-department');
            if (fac && facultySelect.value !== fac) {
                facultySelect.value = fac;
                updateDepartmentOptions(fac, dept);
            } else if (dept && departmentSelect.value !== dept) {
                departmentSelect.value = dept;
            }
        }
    });

    // Initial sync with schedule's current teacher
    if (facultySelect.value) {
        updateDepartmentOptions(facultySelect.value, "<?= htmlspecialchars($initialDept) ?>");
        filterTeachers();
        teacherSelect.value = "<?= $targetTeacherId ?>";
    } else {
        updateDepartmentOptions('');
        filterTeachers();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
