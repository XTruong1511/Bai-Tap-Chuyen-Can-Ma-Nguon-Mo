<?php
$pageTitle = 'Lịch giảng dạy';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-list-check text-primary me-2"></i>Quản lý Lịch Giảng Dạy
        </h5>
        <p class="text-muted small mb-0 mt-1">Danh sách phân bổ lớp học theo các cơ sở và thời gian trong tuần</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn-modern-outline">
            <i class="fa-solid fa-table-cells"></i> Ma trận TKB
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn-modern-primary">
            <i class="fa-solid fa-plus"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-custom mb-3 shadow-sm">
    <div class="card-body p-2.5">
        <form method="GET" action="index.php" class="row g-2 align-items-center text-nowrap">
            <input type="hidden" name="controller" value="schedule">
            <input type="hidden" name="action" value="index">

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Tìm môn học, phòng, GV..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>

            <div class="col-md-2">
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả Cơ sở --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (($filters['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="teacher_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả Giảng viên --</option>
                    <?php 
                        $groupedByDept = [];
                        foreach ($teachers as $t) {
                            $d = $t['department'] ?? 'Bộ môn khác';
                            $groupedByDept[$d][] = $t;
                        }
                        foreach ($groupedByDept as $deptName => $deptTeachers):
                    ?>
                        <optgroup label="<?= htmlspecialchars($deptName) ?>">
                            <?php foreach ($deptTeachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (($filters['teacher_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="day_of_week" class="form-select form-select-sm">
                    <option value="">-- Tất cả Thứ --</option>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <option value="<?= $d ?>" <?= (($filters['day_of_week'] ?? '') == $d) ? 'selected' : '' ?>>
                            <?= $d ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="shift_or_periods" class="form-select form-select-sm">
                    <option value="">-- Tất cả Ca học --</option>
                    <?php foreach (Schedule::SHIFTS as $sh): ?>
                        <option value="<?= $sh ?>" <?= (($filters['shift_or_periods'] ?? '') == $sh) ? 'selected' : '' ?>>
                            <?= $sh ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-modern-primary w-100 justify-content-center py-1" title="Lọc dữ liệu">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="index.php?controller=schedule&action=index" class="btn btn-modern-outline justify-content-center py-1 px-2" title="Đặt lại bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Schedule List Table -->
<div class="card card-custom shadow-sm overflow-hidden mb-4">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-calendar-days me-1.5 text-primary"></i>Danh sách Lịch giảng dạy</span>
        <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.72rem;"><?= count($schedules) ?> kết quả</span>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 45px;" class="text-center">STT</th>
                    <th>Học phần</th>
                    <th>Giảng viên</th>
                    <th>Cơ sở</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                    <th class="text-center" style="width: 80px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted small">
                            <i class="fa-regular fa-calendar-xmark fa-2x mb-2 d-block text-secondary opacity-50"></i>
                            Không tìm thấy lịch giảng dạy phù hợp bộ lọc.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $index => $s): ?>
                        <tr>
                            <td class="text-muted small fw-medium text-center"><?= $index + 1 ?></td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($s['subject_name']) ?></span>
                            </td>
                            <td>
                                <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $s['teacher_id'] ?>" class="text-decoration-none d-inline-flex align-items-center gap-1.5 text-dark hover-text-primary">
                                    <span class="teacher-avatar-sm">
                                        <?= mb_substr($s['teacher_name'] ?? 'G', 0, 1, 'UTF-8') ?>
                                    </span>
                                    <span class="fw-medium text-primary"><?= htmlspecialchars($s['teacher_name']) ?></span>
                                </a>
                            </td>
                            <td>
                                <span class="badge-branch-<?= ((($s['branch_id'] ?? 1) - 1) % 7) + 1 ?>">
                                    <?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-0.5">
                                    <i class="fa-solid fa-door-open me-1 text-secondary" style="font-size: 0.65rem;"></i><?= htmlspecialchars($s['room']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border fw-medium"><?= htmlspecialchars($s['day_of_week']) ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=edit&id=<?= $s['id'] ?>" class="btn-modern-outline py-0.5 px-2" title="Chỉnh sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=schedule&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa lịch môn <?= htmlspecialchars($s['subject_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0.5 px-2 border" title="Xóa">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
