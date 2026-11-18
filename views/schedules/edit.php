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
                                    <span class="badge bg-primary text-white me-1" id="badge_step_1">1</span> Chọn Khoa
                                </label>
                                <select class="form-select form-select-sm" id="faculty_select" onchange="handleFacultyChange(this.value)">
                                    <option value="">-- 1. Chọn Khoa quản lý --</option>
                                    <?php foreach (array_keys($facultiesAndDepartments) as $fac): ?>
                                        <option value="<?= htmlspecialchars($fac) ?>" <?= ($initialFaculty === $fac) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fac) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="department_select" class="form-label small fw-semibold text-muted mb-1">
                                    <span class="badge <?= empty($initialFaculty) ? 'bg-secondary-subtle text-secondary' : 'bg-primary text-white' ?> me-1" id="badge_step_2">2</span> Chọn Bộ môn
                                </label>
                                <select class="form-select form-select-sm" id="department_select" onchange="handleDepartmentChange(this.value)" <?= empty($initialFaculty) ? 'disabled' : '' ?>>
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
                                <span class="badge <?= empty($initialFaculty) ? 'bg-secondary-subtle text-secondary' : 'bg-primary text-white' ?> me-1" id="badge_step_3">3</span> Giảng viên <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="teacher_id" name="teacher_id" onchange="handleTeacherChange(this.value)" required>
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
// 1. Hierarchy Data
window.facultyDeptMap = <?= json_encode($facultiesAndDepartments, JSON_UNESCAPED_UNICODE) ?>;
window.allTeacherOptions = [];

// Helper: Case-insensitive faculty lookup
function getDeptsForFaculty(facName) {
    if (!facName) return null;
    const trimmed = facName.trim();
    if (window.facultyDeptMap[trimmed]) {
        return window.facultyDeptMap[trimmed];
    }
    const lower = trimmed.toLowerCase();
    for (const key in window.facultyDeptMap) {
        if (key.trim().toLowerCase() === lower) {
            return window.facultyDeptMap[key];
        }
    }
    return null;
}

// Handler: 1. When Faculty changes (Khoa -> Bộ môn)
window.handleFacultyChange = function(facultyVal, preselectedDept = '', preselectedTeacher = '') {
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const badgeStep1 = document.getElementById('badge_step_1');
    const badgeStep2 = document.getElementById('badge_step_2');

    const cleanFac = (facultyVal || (facultySelect ? facultySelect.value : '') || '').trim();
    const depts = getDeptsForFaculty(cleanFac);

    if (facultySelect && facultySelect.value !== cleanFac) {
        facultySelect.value = cleanFac;
    }

    if (departmentSelect) {
        departmentSelect.innerHTML = '';
        if (cleanFac && depts && depts.length > 0) {
            departmentSelect.disabled = false;
            departmentSelect.removeAttribute('disabled');

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Tất cả Bộ môn trong Khoa --';
            departmentSelect.appendChild(defaultOpt);

            depts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.textContent = d;
                if (preselectedDept && d === preselectedDept) {
                    opt.selected = true;
                }
                departmentSelect.appendChild(opt);
            });

            if (preselectedDept) {
                departmentSelect.value = preselectedDept;
            }

            if (badgeStep1) {
                badgeStep1.className = 'badge bg-success text-white me-1';
                badgeStep1.innerHTML = '<i class="fa-solid fa-check"></i> 1';
            }
            if (badgeStep2) {
                badgeStep2.className = preselectedDept ? 'badge bg-success text-white me-1' : 'badge bg-primary text-white me-1';
                badgeStep2.innerHTML = preselectedDept ? '<i class="fa-solid fa-check"></i> 2' : '2';
            }
        } else {
            departmentSelect.disabled = true;
            departmentSelect.setAttribute('disabled', 'disabled');

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Vui lòng chọn Khoa trước --';
            departmentSelect.appendChild(defaultOpt);
            departmentSelect.value = '';

            if (badgeStep1) {
                badgeStep1.className = 'badge bg-primary text-white me-1';
                badgeStep1.textContent = '1';
            }
            if (badgeStep2) {
                badgeStep2.className = 'badge bg-secondary-subtle text-secondary me-1';
                badgeStep2.textContent = '2';
            }
        }
    }

    // Filter step 3: Teachers
    handleDepartmentChange(departmentSelect ? departmentSelect.value : '', preselectedTeacher);
};

// Handler: 2. When Department changes (Bộ môn -> Giảng viên)
window.handleDepartmentChange = function(deptVal, preselectedTeacher = '') {
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const teacherSelect = document.getElementById('teacher_id');
    const badgeStep2 = document.getElementById('badge_step_2');
    const badgeStep3 = document.getElementById('badge_step_3');

    if (!teacherSelect) return;

    if (!window.allTeacherOptions || window.allTeacherOptions.length === 0) {
        window.allTeacherOptions = Array.from(teacherSelect.querySelectorAll('option')).filter(opt => opt.value !== "");
    }

    const cleanFac = (facultySelect ? facultySelect.value : '').trim();
    const cleanDept = (deptVal || (departmentSelect ? departmentSelect.value : '') || '').trim();
    const currentTeacherVal = preselectedTeacher || teacherSelect.value;

    teacherSelect.innerHTML = '';

    if (!cleanFac) {
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '-- Vui lòng chọn Khoa ở Bước 1 trước --';
        teacherSelect.appendChild(defaultOpt);

        window.allTeacherOptions.forEach(opt => {
            const clone = opt.cloneNode(true);
            if (clone.value === currentTeacherVal) clone.selected = true;
            teacherSelect.appendChild(clone);
        });

        if (badgeStep2) {
            badgeStep2.className = 'badge bg-secondary-subtle text-secondary me-1';
            badgeStep2.textContent = '2';
        }
        if (badgeStep3) {
            badgeStep3.className = 'badge bg-secondary-subtle text-secondary me-1';
            badgeStep3.textContent = '3';
        }
        return;
    }

    if (badgeStep2) {
        badgeStep2.className = cleanDept ? 'badge bg-success text-white me-1' : 'badge bg-primary text-white me-1';
        badgeStep2.innerHTML = cleanDept ? '<i class="fa-solid fa-check"></i> 2' : '2';
    }

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = cleanDept 
        ? `-- Chọn Giảng viên thuộc: ${cleanDept} --`
        : `-- Chọn Giảng viên thuộc: ${cleanFac} --`;
    teacherSelect.appendChild(defaultOpt);

    let matchCount = 0;
    window.allTeacherOptions.forEach(opt => {
        const optFac = (opt.getAttribute('data-faculty') || '').trim();
        const optDept = (opt.getAttribute('data-department') || '').trim();

        const matchFac = (optFac.toLowerCase() === cleanFac.toLowerCase());
        const matchDept = !cleanDept || (optDept.toLowerCase() === cleanDept.toLowerCase());

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

    if (badgeStep3) {
        badgeStep3.className = teacherSelect.value ? 'badge bg-success text-white me-1' : 'badge bg-primary text-white me-1';
        badgeStep3.innerHTML = teacherSelect.value ? '<i class="fa-solid fa-check"></i> 3' : '3';
    }
};

// Handler: 3. When Teacher changes directly
window.handleTeacherChange = function(teacherVal) {
    const teacherSelect = document.getElementById('teacher_id');
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const badgeStep3 = document.getElementById('badge_step_3');

    if (!teacherSelect) return;
    const selectedOpt = teacherSelect.options[teacherSelect.selectedIndex];

    if (selectedOpt && selectedOpt.value) {
        const fac = (selectedOpt.getAttribute('data-faculty') || '').trim();
        const dept = (selectedOpt.getAttribute('data-department') || '').trim();

        if (fac && facultySelect && facultySelect.value !== fac) {
            handleFacultyChange(fac, dept, selectedOpt.value);
        } else if (dept && departmentSelect && departmentSelect.value !== dept) {
            departmentSelect.value = dept;
            handleDepartmentChange(dept, selectedOpt.value);
        }

        if (badgeStep3) {
            badgeStep3.className = 'badge bg-success text-white me-1';
            badgeStep3.innerHTML = '<i class="fa-solid fa-check"></i> 3';
        }
    } else {
        if (badgeStep3) {
            badgeStep3.className = 'badge bg-primary text-white me-1';
            badgeStep3.textContent = '3';
        }
    }
};

function initEditScheduleForm() {
    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const teacherSelect = document.getElementById('teacher_id');

    if (teacherSelect && (!window.allTeacherOptions || window.allTeacherOptions.length === 0)) {
        window.allTeacherOptions = Array.from(teacherSelect.querySelectorAll('option')).filter(opt => opt.value !== "");
    }

    const initialFac = "<?= htmlspecialchars($initialFaculty) ?>";
    const initialDep = "<?= htmlspecialchars($initialDept) ?>";
    const targetTeacher = "<?= $targetTeacherId ?>";

    if (initialFac) {
        handleFacultyChange(initialFac, initialDep, targetTeacher);
    } else {
        handleFacultyChange('');
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditScheduleForm);
} else {
    initEditScheduleForm();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
