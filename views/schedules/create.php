<?php
$pageTitle = 'Thêm lịch giảng dạy mới';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;

$defaultDay = $_GET['day'] ?? 'Thứ 2';
$defaultShift = $_GET['shift'] ?? 'Sáng (Tiết 1-3)';
$defaultBranch = (int)($_GET['branch_id'] ?? 1);
$defaultTeacher = (int)($_GET['teacher_id'] ?? 0);

$selectedTeacherObj = null;
$targetTeacherId = (int)($_POST['teacher_id'] ?? $defaultTeacher ?? 0);
if ($targetTeacherId > 0) {
    foreach ($teachers as $t) {
        if ($t['id'] == $targetTeacherId) {
            $selectedTeacherObj = $t;
            break;
        }
    }
}
$initialFaculty = $selectedTeacherObj['faculty'] ?? ($_POST['faculty'] ?? '');
$initialDept = $selectedTeacherObj['department'] ?? ($_POST['department'] ?? '');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-calendar-plus text-primary me-2"></i>Thêm Lịch Giảng Dạy Mới
    </h5>
    <a href="index.php?controller=schedule&action=index" class="btn-modern-outline">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm border-0 mb-4">
        <div class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i>Không thể lưu lịch do có xung đột hoặc lỗi nhập liệu:</div>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left Column: Schedule Form -->
    <div class="col-lg-7">
        <div class="card card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Thông tin Phân lịch Lớp học</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-medium">Bước 1: Nhập thông tin</span>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="index.php?controller=schedule&action=create" id="scheduleForm">
                    <!-- Subject Name -->
                    <div class="mb-3">
                        <label for="subject_name" class="form-label fw-bold">
                            Tên học phần <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-book-open text-muted"></i></span>
                            <input type="text" class="form-control" id="subject_name" name="subject_name" 
                                   placeholder="Nhập tên môn học (ví dụ: Lập trình mã nguồn mở PHP)..." 
                                   value="<?= htmlspecialchars($_POST['subject_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Campus & Room -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="branch_id" class="form-label fw-bold">
                                Cơ sở đào tạo <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (($defaultBranch == $b['id']) || (isset($_POST['branch_id']) && $_POST['branch_id'] == $b['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="room" class="form-label fw-bold">
                                Phòng học <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="room" name="room" 
                                   placeholder="Ví dụ: A1.01, LAB-02" 
                                   value="<?= htmlspecialchars($_POST['room'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Day of Week & Shift -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="day_of_week" class="form-label fw-bold">
                                Thứ trong tuần <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="day_of_week" name="day_of_week" required>
                                <?php foreach (Schedule::DAYS as $d): ?>
                                    <option value="<?= $d ?>" <?= (($defaultDay == $d) || (isset($_POST['day_of_week']) && $_POST['day_of_week'] == $d)) ? 'selected' : '' ?>>
                                        <?= $d ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="shift_or_periods" class="form-label fw-bold">
                                Ca / Tiết học <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="shift_or_periods" name="shift_or_periods" required>
                                <?php foreach (Schedule::SHIFTS as $sh): ?>
                                    <option value="<?= $sh ?>" <?= (($defaultShift == $sh) || (isset($_POST['shift_or_periods']) && $_POST['shift_or_periods'] == $sh)) ? 'selected' : '' ?>>
                                        <?= $sh ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Teacher Selection (Khoa -> Bộ môn -> Giảng viên) -->
                    <div class="p-3 rounded border bg-light-subtle mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0 text-dark">
                                <i class="fa-solid fa-chalkboard-user text-primary me-1"></i> Phân công Giảng viên <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn-modern-outline py-0 px-2" id="btnTriggerRecommend">
                                <i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i> Gợi ý thông minh
                            </button>
                        </div>

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

                        <!-- 3. Giảng viên phụ trách (tên sạch, không có đuôi) -->
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
                                            <?= (($defaultTeacher == $t['id']) || (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $t['id'])) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Realtime Conflict Alert Box -->
                    <div id="conflictAlertBox" class="mb-3" style="display: none;"></div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Ghi chú phân lịch</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Ghi chú về nhóm lớp, sĩ số hoặc lưu ý di chuyển..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php?controller=schedule&action=index" class="btn-modern-outline">Hủy bỏ</a>
                        <button type="submit" class="btn-modern-primary" id="btnSubmit">
                            <i class="fa-solid fa-check"></i> Lưu và Phân lịch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Smart Teacher Recommendation Panel -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header-custom py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                <span class="small fw-bold">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i>Gợi ý Giảng viên
                </span>
            </div>
            <div class="card-body p-3">

                <div id="recommendationLoading" class="text-center py-4 text-muted" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <div class="small">Đang phân tích chuyên môn và kiểm tra lịch trống...</div>
                </div>

                <div id="recommendationPlaceholder" class="text-center py-5 text-muted">
                    <i class="fa-solid fa-lightbulb fa-3x mb-3 text-warning"></i>
                    <p class="small mb-2">Nhập <strong>Tên học phần</strong> ở bên trái để kích hoạt gợi ý thông minh.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="quickFillSubject('Lập trình mã nguồn mở PHP')">
                        Thử môn: Lập trình PHP
                    </button>
                </div>

                <div id="recommendationResults" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold" id="recommendCount">0 Giảng viên phù hợp</span>
                        <span class="small text-muted">Sắp xếp theo độ phù hợp</span>
                    </div>
                    <div id="recommendList" class="d-flex flex-column gap-2" style="max-height: 520px; overflow-y: auto;">
                        <!-- Injected via JavaScript -->
                    </div>
                </div>
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

// Global live conflict checking
window.checkLiveConflict = function() {
    const teacherSelect = document.getElementById('teacher_id');
    const branchSelect = document.getElementById('branch_id');
    const roomInput = document.getElementById('room');
    const daySelect = document.getElementById('day_of_week');
    const shiftSelect = document.getElementById('shift_or_periods');
    const conflictBox = document.getElementById('conflictAlertBox');

    if (!teacherSelect || !branchSelect || !roomInput || !daySelect || !shiftSelect || !conflictBox) return;

    const teacherId = teacherSelect.value;
    const branchId = branchSelect.value;
    const room = roomInput.value.trim();
    const day = daySelect.value;
    const shift = shiftSelect.value;

    if (!teacherId || !branchId || !room || !day || !shift) {
        conflictBox.style.display = 'none';
        return;
    }

    fetch(`index.php?controller=api&action=check_conflict&teacher_id=${teacherId}&branch_id=${branchId}&room=${encodeURIComponent(room)}&day=${encodeURIComponent(day)}&shift=${encodeURIComponent(shift)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.has_conflict) {
                    let html = '<div class="alert alert-danger mb-0 small"><div class="fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Phát hiện Xung đột lịch dạy:</div><ul class="mb-0 ps-3 mt-1">';
                    data.conflicts.forEach(c => {
                        html += `<li><strong>${c.title}:</strong> ${c.message}</li>`;
                    });
                    html += '</ul></div>';
                    conflictBox.innerHTML = html;
                    conflictBox.style.display = 'block';
                } else if (data.has_travel_warning) {
                    let html = `<div class="alert alert-warning mb-0 small"><i class="fa-solid fa-person-walking-luggage me-1 text-dark"></i><strong>${data.travel_warning.title}:</strong> ${data.travel_warning.message}</div>`;
                    conflictBox.innerHTML = html;
                    conflictBox.style.display = 'block';
                } else {
                    conflictBox.innerHTML = '<div class="alert alert-success mb-0 small py-2"><i class="fa-solid fa-circle-check me-1"></i><strong>Lịch hợp lệ:</strong> Giảng viên và phòng học đều sẵn sàng trong khung giờ này!</div>';
                    conflictBox.style.display = 'block';
                }
            }
        })
        .catch(console.error);
};

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
        window.checkLiveConflict();
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

    window.checkLiveConflict();
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

    window.checkLiveConflict();
};

// Handler: Select teacher from recommendation card
window.selectTeacher = function(id, name, faculty, department) {
    const facultySelect = document.getElementById('faculty_select');
    const teacherSelect = document.getElementById('teacher_id');

    if (faculty) {
        handleFacultyChange(faculty, department, id);
    } else if (department) {
        handleDepartmentChange(department, id);
    }
    if (teacherSelect) {
        teacherSelect.value = id;
    }
    handleTeacherChange(id);

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: `Đã chọn: ${name}`,
        text: `${faculty || ''} - ${department || ''}`,
        showConfirmButton: false,
        timer: 2000
    });
};

function initScheduleForm() {
    const subjectInput = document.getElementById('subject_name');
    const branchSelect = document.getElementById('branch_id');
    const daySelect = document.getElementById('day_of_week');
    const shiftSelect = document.getElementById('shift_or_periods');
    const roomInput = document.getElementById('room');
    const btnTriggerRecommend = document.getElementById('btnTriggerRecommend');

    const facultySelect = document.getElementById('faculty_select');
    const departmentSelect = document.getElementById('department_select');
    const teacherSelect = document.getElementById('teacher_id');

    const loadingElem = document.getElementById('recommendationLoading');
    const placeholderElem = document.getElementById('recommendationPlaceholder');
    const resultsElem = document.getElementById('recommendationResults');
    const listElem = document.getElementById('recommendList');
    const countElem = document.getElementById('recommendCount');

    // Cache all original teacher options
    if (teacherSelect && (!window.allTeacherOptions || window.allTeacherOptions.length === 0)) {
        window.allTeacherOptions = Array.from(teacherSelect.querySelectorAll('option')).filter(opt => opt.value !== "");
    }

    // Initial sync
    const initialFac = "<?= htmlspecialchars($initialFaculty) ?>";
    const initialDep = "<?= htmlspecialchars($initialDept) ?>";
    const targetTeacher = "<?= $targetTeacherId ?>";

    if (initialFac) {
        handleFacultyChange(initialFac, initialDep, targetTeacher);
    } else {
        handleFacultyChange('');
    }

    let debounceTimer = null;

    // Trigger recommendation on subject input change
    if (subjectInput) {
        subjectInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchRecommendations, 400);
        });
    }

    if (branchSelect) branchSelect.addEventListener('change', fetchRecommendations);
    if (daySelect) daySelect.addEventListener('change', fetchRecommendations);
    if (shiftSelect) shiftSelect.addEventListener('change', fetchRecommendations);
    if (btnTriggerRecommend) btnTriggerRecommend.addEventListener('click', fetchRecommendations);

    // Live conflict check when inputs change
    [branchSelect, daySelect, shiftSelect].forEach(elem => {
        if (elem) elem.addEventListener('change', window.checkLiveConflict);
    });
    if (roomInput) {
        roomInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(window.checkLiveConflict, 400);
        });
    }

    function fetchRecommendations() {
        if (!subjectInput) return;
        const subject = subjectInput.value.trim();
        if (!subject) {
            if (placeholderElem) placeholderElem.style.display = 'block';
            if (resultsElem) resultsElem.style.display = 'none';
            if (loadingElem) loadingElem.style.display = 'none';
            return;
        }

        if (placeholderElem) placeholderElem.style.display = 'none';
        if (loadingElem) loadingElem.style.display = 'block';
        if (resultsElem) resultsElem.style.display = 'none';

        const day = daySelect ? daySelect.value : '';
        const shift = shiftSelect ? shiftSelect.value : '';
        const branchId = branchSelect ? branchSelect.value : '';

        fetch(`index.php?controller=api&action=recommend_teachers&subject=${encodeURIComponent(subject)}&day=${encodeURIComponent(day)}&shift=${encodeURIComponent(shift)}&branch_id=${branchId}`)
            .then(res => res.json())
            .then(data => {
                if (loadingElem) loadingElem.style.display = 'none';
                if (data.success && data.data) {
                    renderRecommendations(data.data);
                }
            })
            .catch(err => {
                if (loadingElem) loadingElem.style.display = 'none';
                console.error(err);
            });
    }

    function renderRecommendations(teachers) {
        if (!listElem) return;
        listElem.innerHTML = '';
        if (resultsElem) resultsElem.style.display = 'block';
        if (countElem) countElem.innerText = `${teachers.length} Giảng viên được phân tích`;

        teachers.forEach(t => {
            const card = document.createElement('div');
            card.className = 'p-3 rounded border bg-white shadow-sm position-relative';
            
            if (t.is_busy) {
                card.classList.add('opacity-75', 'border-danger-subtle', 'bg-light');
            } else if (t.travel_status === 'same_branch_bonus') {
                card.classList.add('border-success-subtle');
            }

            let statusBadge = '';
            if (t.is_busy) {
                statusBadge = `<span class="badge bg-danger"><i class="fa-solid fa-ban me-1"></i>Trùng lịch dạy</span>`;
            } else {
                statusBadge = `<span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Lịch trống</span>`;
            }

            let travelBadge = '';
            if (t.travel_status === 'same_branch_bonus') {
                travelBadge = `<span class="badge bg-info text-dark ms-1"><i class="fa-solid fa-building me-1"></i>Cùng cơ sở trong ngày</span>`;
            } else if (t.travel_status === 'other_branch_warning') {
                travelBadge = `<span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-route me-1"></i>Ở cơ sở khác trong ngày</span>`;
            }

            let scorePercent = Math.min(100, Math.round(t.match_score));
            let scoreColor = scorePercent >= 70 ? 'success' : (scorePercent >= 40 ? 'warning' : 'secondary');

            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <strong class="text-dark fs-6">${t.full_name}</strong>
                        <div class="mt-1">${statusBadge} ${travelBadge}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-${scoreColor}-subtle text-${scoreColor} border border-${scoreColor}-subtle fw-bold">
                            Độ khớp: ${scorePercent}%
                        </span>
                        <div class="small text-muted mt-1">${t.total_weekly_classes} tiết/tuần</div>
                    </div>
                </div>

                <div class="small text-muted mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">${t.department || 'Bộ môn'}</span>
                    <span class="text-secondary">${t.faculty || ''}</span>
                </div>

                ${t.is_busy ? `
                    <div class="small text-danger bg-danger bg-opacity-10 p-2 rounded mb-2">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>${t.busy_details}
                    </div>
                ` : `
                    <div class="small text-secondary mb-2">
                        <i class="fa-solid fa-location-arrow me-1"></i>${t.travel_note}
                    </div>
                `}
            `;

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'd-flex justify-content-between align-items-center mt-2';

            const emailDiv = document.createElement('div');
            emailDiv.className = 'small text-muted';
            emailDiv.innerHTML = `<i class="fa-solid fa-envelope me-1"></i>${t.email || ''}`;
            actionsDiv.appendChild(emailDiv);

            const selectBtn = document.createElement('button');
            selectBtn.type = 'button';
            selectBtn.className = `btn btn-sm ${t.is_busy ? 'btn-modern-outline disabled' : 'btn-modern-primary'} py-1 px-3`;
            selectBtn.disabled = Boolean(t.is_busy);
            selectBtn.innerHTML = t.is_busy ? 'Không khả dụng' : '<i class="fa-solid fa-check me-1"></i>Chọn GV này';
            if (!t.is_busy) {
                selectBtn.onclick = function() {
                    window.selectTeacher(t.teacher_id, t.full_name, t.faculty || '', t.department || '');
                };
            }
            actionsDiv.appendChild(selectBtn);
            card.appendChild(actionsDiv);

            listElem.appendChild(card);
        });
    }

    window.quickFillSubject = function(name) {
        if (subjectInput) {
            subjectInput.value = name;
            fetchRecommendations();
        }
    };

    // Auto load if prefilled
    if (subjectInput && subjectInput.value.trim()) {
        fetchRecommendations();
    }
}

// Run immediately since DOM elements are already parsed above
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScheduleForm);
} else {
    initScheduleForm();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
