<?php
$pageTitle = 'Thêm lịch giảng dạy mới';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;

$defaultDay = $_GET['day'] ?? 'Thứ 2';
$defaultShift = $_GET['shift'] ?? 'Sáng (Tiết 1-3)';
$defaultBranch = (int)($_GET['branch_id'] ?? 1);
$defaultTeacher = (int)($_GET['teacher_id'] ?? 0);
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

                        <!-- 3. Giảng viên phụ trách (tên sạch, không có đuôi) -->
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
document.addEventListener('DOMContentLoaded', function () {
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
    const conflictBox = document.getElementById('conflictAlertBox');

    // Faculty & Department hierarchy map
    const facultyDeptMap = <?= json_encode($facultiesAndDepartments) ?>;

    // Cache all original teacher options
    const allTeacherOptions = Array.from(teacherSelect.querySelectorAll('option')).filter(opt => opt.value !== "");

    // 1. When Faculty changes
    facultySelect.addEventListener('change', function () {
        const selectedFaculty = this.value;

        // Repopulate department dropdown
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

    // Filter teacher select based on selected Faculty and Department
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

        checkLiveConflict();
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
        checkLiveConflict();
    });

    // Initial check: if teacher preselected, sync Khoa and Bộ môn
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

    let debounceTimer = null;

    // Trigger recommendation on subject input change
    subjectInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchRecommendations, 400);
    });

    branchSelect.addEventListener('change', fetchRecommendations);
    daySelect.addEventListener('change', fetchRecommendations);
    shiftSelect.addEventListener('change', fetchRecommendations);
    btnTriggerRecommend.addEventListener('click', fetchRecommendations);

    // Live conflict check when inputs change
    [branchSelect, daySelect, shiftSelect, roomInput].forEach(elem => {
        elem.addEventListener('change', checkLiveConflict);
    });
    roomInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(checkLiveConflict, 400);
    });

    function fetchRecommendations() {
        const subject = subjectInput.value.trim();
        if (!subject) {
            placeholderElem.style.display = 'block';
            resultsElem.style.display = 'none';
            loadingElem.style.display = 'none';
            return;
        }

        placeholderElem.style.display = 'none';
        loadingElem.style.display = 'block';
        resultsElem.style.display = 'none';

        const day = daySelect.value;
        const shift = shiftSelect.value;
        const branchId = branchSelect.value;

        fetch(`index.php?controller=api&action=recommend_teachers&subject=${encodeURIComponent(subject)}&day=${encodeURIComponent(day)}&shift=${encodeURIComponent(shift)}&branch_id=${branchId}`)
            .then(res => res.json())
            .then(data => {
                loadingElem.style.display = 'none';
                if (data.success && data.data) {
                    renderRecommendations(data.data);
                }
            })
            .catch(err => {
                loadingElem.style.display = 'none';
                console.error(err);
            });
    }

    function renderRecommendations(teachers) {
        listElem.innerHTML = '';
        resultsElem.style.display = 'block';
        countElem.innerText = `${teachers.length} Giảng viên được phân tích`;

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

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="small text-muted">
                        <i class="fa-solid fa-envelope me-1"></i>${t.email}
                    </div>
                    <button type="button" class="btn btn-sm ${t.is_busy ? 'btn-modern-outline disabled' : 'btn-modern-primary'} py-1 px-3"
                            onclick="selectTeacher(${t.teacher_id}, '${t.full_name.replace(/'/g, "\\'")}', '${(t.faculty||'').replace(/'/g, "\\'")}', '${(t.department||'').replace(/'/g, "\\')}')"
                            ${t.is_busy ? 'disabled' : ''}>
                        ${t.is_busy ? 'Không khả dụng' : '<i class="fa-solid fa-check me-1"></i>Chọn GV này'}
                    </button>
                </div>
            `;

            listElem.appendChild(card);
        });
    }

    function checkLiveConflict() {
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
    }

    window.selectTeacher = function(id, name, faculty, department) {
        if (faculty) facultySelect.value = faculty;
        if (department) {
            departmentSelect.innerHTML = '<option value="">-- Tất cả Bộ môn --</option>';
            if (facultyDeptMap[faculty]) {
                facultyDeptMap[faculty].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    if (d === department) opt.selected = true;
                    departmentSelect.appendChild(opt);
                });
            }
            departmentSelect.value = department;
        }
        filterTeachers();
        teacherSelect.value = id;
        checkLiveConflict();
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Đã chọn: ${name}`,
            showConfirmButton: false,
            timer: 2000
        });
    };

    window.quickFillSubject = function(name) {
        subjectInput.value = name;
        fetchRecommendations();
    };

    // Auto load if prefilled
    if (subjectInput.value.trim()) {
        fetchRecommendations();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
