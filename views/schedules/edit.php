<?php
$pageTitle = 'Chỉnh sửa lịch giảng dạy';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
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
                                    <option value="">-- Tất cả Khoa --</option>
                                    <?php foreach (array_keys($facultiesAndDepartments) as $fac): ?>
                                        <option value="<?= htmlspecialchars($fac) ?>"><?= htmlspecialchars($fac) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="department_select" class="form-label small fw-semibold text-muted mb-1">
                                    <span class="badge bg-secondary-subtle text-secondary me-1">2</span> Chọn Bộ môn
                                </label>
                                <select class="form-select form-select-sm" id="department_select">
                                    <option value="">-- Tất cả Bộ môn --</option>
                                    <?php 
                                        $allDepts = [];
                                        foreach ($facultiesAndDepartments as $fac => $depts) {
                                            foreach ($depts as $d) {
                                                if (!in_array($d, $allDepts)) $allDepts[] = $d;
                                            }
                                        }
                                        sort($allDepts);
                                        foreach ($allDepts as $dept):
                                    ?>
                                        <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                                    <?php endforeach; ?>
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

    // 1. When Faculty changes
    facultySelect.addEventListener('change', function () {
        const selectedFaculty = this.value;

        departmentSelect.innerHTML = '<option value="">-- Tất cả Bộ môn --</option>';
        if (selectedFaculty && facultyDeptMap[selectedFaculty]) {
            facultyDeptMap[selectedFaculty].forEach(dept => {
                const opt = document.createElement('option');
                opt.value = dept;
                opt.textContent = dept;
                departmentSelect.appendChild(opt);
            });
        } else {
            const allUniqueDepts = new Set();
            Object.values(facultyDeptMap).forEach(depts => depts.forEach(d => allUniqueDepts.add(d)));
            Array.from(allUniqueDepts).sort().forEach(dept => {
                const opt = document.createElement('option');
                opt.value = dept;
                opt.textContent = dept;
                departmentSelect.appendChild(opt);
            });
        }

        filterTeachers();
    });

    // 2. When Department changes
    departmentSelect.addEventListener('change', function () {
        filterTeachers();
    });

    function filterTeachers() {
        const selectedFaculty = facultySelect.value;
        const selectedDept = departmentSelect.value;
        const currentTeacherVal = teacherSelect.value;

        teacherSelect.innerHTML = '<option value="">-- Chọn Giảng viên --</option>';

        allTeacherOptions.forEach(opt => {
            const optFac = opt.getAttribute('data-faculty');
            const optDept = opt.getAttribute('data-department');

            const matchFac = !selectedFaculty || optFac === selectedFaculty;
            const matchDept = !selectedDept || optDept === selectedDept;

            if (matchFac && matchDept) {
                const clone = opt.cloneNode(true);
                if (clone.value === currentTeacherVal) {
                    clone.selected = true;
                }
                teacherSelect.appendChild(clone);
            }
        });
    }

    // 3. When Teacher changes directly
    teacherSelect.addEventListener('change', function () {
        const selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.value) {
            const fac = selectedOpt.getAttribute('data-faculty');
            const dept = selectedOpt.getAttribute('data-department');
            if (fac && facultySelect.value !== fac) {
                facultySelect.value = fac;
                departmentSelect.innerHTML = '<option value="">-- Tất cả Bộ môn --</option>';
                if (facultyDeptMap[fac]) {
                    facultyDeptMap[fac].forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        if (d === dept) opt.selected = true;
                        departmentSelect.appendChild(opt);
                    });
                }
            } else if (dept && departmentSelect.value !== dept) {
                departmentSelect.value = dept;
            }
        }
    });

    // Initial sync with schedule's current teacher
    if (teacherSelect.value) {
        const selectedOpt = teacherSelect.querySelector(`option[value="${teacherSelect.value}"]`);
        if (selectedOpt) {
            const fac = selectedOpt.getAttribute('data-faculty');
            const dept = selectedOpt.getAttribute('data-department');
            if (fac) facultySelect.value = fac;
            if (dept) {
                departmentSelect.innerHTML = '<option value="">-- Tất cả Bộ môn --</option>';
                if (facultyDeptMap[fac]) {
                    facultyDeptMap[fac].forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        if (d === dept) opt.selected = true;
                        departmentSelect.appendChild(opt);
                    });
                }
                departmentSelect.value = dept;
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
