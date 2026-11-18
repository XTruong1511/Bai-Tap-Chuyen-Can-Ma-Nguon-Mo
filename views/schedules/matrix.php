<?php
$pageTitle = 'Ma trận Thời khóa biểu & Tiết trống';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-table-cells text-primary me-2"></i>Ma trận Thời khóa biểu & Tiết trống
        </h3>
        <p class="text-muted mb-0 small">
            Trực quan hóa thời khóa biểu tuần, phát hiện khoảng trống chết giữa các tiết và tối ưu lịch di chuyển đa cơ sở.
        </p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print">
            <i class="fa-solid fa-print me-1"></i> In Ma trận TKB
        </button>
        <a href="index.php?controller=schedule&action=create" class="btn btn-huit-orange btn-sm no-print">
            <i class="fa-solid fa-plus-circle me-1"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom mb-4 no-print">
    <div class="card-body p-3">
        <form method="GET" action="index.php" class="d-flex flex-wrap gap-3 align-items-center">
            <input type="hidden" name="controller" value="schedule">
            <input type="hidden" name="action" value="matrix">

            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold mb-0 text-nowrap"><i class="fa-solid fa-building-columns text-primary me-1"></i> Cơ sở:</label>
                <select name="branch_id" class="form-select form-select-sm" style="min-width: 220px;" onchange="this.form.submit()">
                    <option value="">-- Tất cả các cơ sở --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($branchId === (int)$b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <label class="form-label fw-bold mb-0 text-nowrap"><i class="fa-solid fa-chalkboard-user text-primary me-1"></i> Giảng viên:</label>
                <select name="teacher_id" class="form-select form-select-sm" style="min-width: 220px;" onchange="this.form.submit()">
                    <option value="">-- Tất cả giảng viên --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacherId === (int)$t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($branchId || $teacherId): ?>
                <a href="index.php?controller=schedule&action=matrix" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-xmark me-1"></i> Bỏ lọc
                </a>
            <?php endif; ?>

            <div class="ms-auto text-muted small d-none d-lg-block">
                <span class="badge badge-branch-1 me-1">CS Chính</span>
                <span class="badge badge-branch-2 me-1">Tân Kỳ Tân Quý</span>
                <span class="badge badge-branch-3 me-1">Nguyễn Đỗ Cung</span>
                <span class="badge badge-branch-4">Chế Lan Viên</span>
            </div>
        </form>
    </div>
</div>

<!-- Matrix Table Grid -->
<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span>
            <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
            Biểu đồ Phân bổ Thời khóa biểu Tuần (Học kỳ 1)
        </span>
        <span class="badge bg-light text-dark border">
            <?= $branchId ? 'Đang lọc cơ sở' : ($teacherId ? 'Đang lọc giảng viên' : 'Toàn trường') ?>
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-matrix mb-0">
            <thead class="table-dark" style="background-color: var(--huit-dark);">
                <tr>
                    <th style="width: 140px;" class="py-3">Ca / Tiết học</th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th class="py-3"><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <td class="bg-light fw-bold text-dark small text-start ps-3 align-top">
                            <div class="text-primary"><?= $shift ?></div>
                            <div class="text-muted fw-normal" style="font-size: 0.7rem;">
                                <?php
                                    if (strpos($shift, 'Tiết 1-3') !== false) echo '07:00 - 09:15';
                                    elseif (strpos($shift, 'Tiết 4-6') !== false) echo '09:30 - 11:45';
                                    elseif (strpos($shift, 'Tiết 7-9') !== false) echo '12:30 - 14:45';
                                    elseif (strpos($shift, 'Tiết 10-12') !== false) echo '15:00 - 17:15';
                                    elseif (strpos($shift, 'Tiết 13-15') !== false) echo '17:30 - 20:30';
                                ?>
                            </div>
                        </td>

                        <?php foreach (Schedule::DAYS as $day): ?>
                            <?php $items = $matrix[$shift][$day] ?? []; ?>
                            <td class="matrix-cell align-top bg-white">
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <div class="schedule-pill text-white badge-branch-<?= ($item['branch_id'] % 4) + 1 ?> position-relative">
                                            <div class="fw-bold text-truncate" title="<?= htmlspecialchars($item['subject_name']) ?>">
                                                <?= htmlspecialchars($item['subject_name']) ?>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 0.72rem;">
                                                <span class="text-truncate" style="max-width: 95px;" title="<?= htmlspecialchars($item['teacher_name']) ?>">
                                                    <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($item['teacher_name']) ?>
                                                </span>
                                                <span class="badge bg-dark bg-opacity-50 px-1">
                                                    <?= htmlspecialchars($item['room']) ?>
                                                </span>
                                            </div>
                                            <div class="text-white-50 text-truncate" style="font-size: 0.68rem;" title="<?= htmlspecialchars($item['branch_name']) ?>">
                                                <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($item['branch_name']) ?>
                                            </div>
                                            <div class="text-end mt-1 no-print">
                                                <a href="index.php?controller=schedule&action=edit&id=<?= $item['id'] ?>" class="text-white text-decoration-none" title="Sửa">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Tiết trống (Empty Slot) -->
                                    <div class="h-100 d-flex flex-column justify-content-center">
                                        <a href="index.php?controller=schedule&action=create&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?><?= $branchId ? '&branch_id=' . $branchId : '' ?><?= $teacherId ? '&teacher_id=' . $teacherId : '' ?>" 
                                           class="empty-slot-btn no-print" title="Bấm để phân lịch vào tiết trống này">
                                            <i class="fa-solid fa-plus me-1"></i> Tiết trống
                                        </a>
                                        <span class="text-muted small d-none d-print-block">—</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light p-3 small text-muted">
        <i class="fa-solid fa-circle-info me-1 text-primary"></i>
        <strong>Mẹo tối ưu:</strong> Sử dụng các ô <em>Tiết trống</em> để phân công lịch dạy cho giảng viên mà không gây trùng lặp thời gian hoặc khoảng cách di chuyển giữa các cơ sở.
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
