<?php
$pageTitle = 'Ma trận TKB';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-table-cells text-primary me-2"></i>Ma trận Thời khóa biểu & Tiết trống
    </h4>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print text-nowrap">
            <i class="fa-solid fa-print me-1"></i> In TKB
        </button>
        <a href="index.php?controller=schedule&action=create" class="btn btn-huit-orange btn-sm no-print text-nowrap">
            <i class="fa-solid fa-plus me-1"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom mb-3 no-print">
    <div class="card-body p-2">
        <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 align-items-center text-nowrap">
            <input type="hidden" name="controller" value="schedule">
            <input type="hidden" name="action" value="matrix">

            <div class="d-flex align-items-center gap-1">
                <span class="small fw-bold">Cơ sở:</span>
                <select name="branch_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Tất cả cơ sở</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($branchId === (int)$b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex align-items-center gap-1">
                <span class="small fw-bold">Giảng viên:</span>
                <select name="teacher_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Tất cả giảng viên</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacherId === (int)$t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($branchId || $teacherId): ?>
                <a href="index.php?controller=schedule&action=matrix" class="btn btn-outline-danger btn-sm py-0 px-2">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>

            <div class="ms-auto small d-none d-lg-flex gap-1">
                <span class="badge badge-branch-1">CS Chính</span>
                <span class="badge badge-branch-2">Tân Kỳ Tân Quý</span>
                <span class="badge badge-branch-3">Nguyễn Đỗ Cung</span>
                <span class="badge badge-branch-4">Chế Lan Viên</span>
            </div>
        </form>
    </div>
</div>

<!-- Matrix Table Grid -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-matrix mb-0 text-nowrap">
            <thead class="table-dark" style="background-color: var(--huit-dark);">
                <tr>
                    <th style="width: 130px;" class="py-2 text-center">Ca / Tiết</th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th class="py-2 text-center" style="width: 14%;"><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <td class="bg-light fw-bold text-dark small text-start ps-2 align-middle">
                            <div class="text-primary text-truncate" style="font-size: 0.78rem;"><?= $shift ?></div>
                            <div class="text-muted fw-normal" style="font-size: 0.68rem;">
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
                            <td class="matrix-cell align-middle bg-white text-center">
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <div class="schedule-pill text-white badge-branch-<?= ($item['branch_id'] % 4) + 1 ?>" 
                                             title="<?= htmlspecialchars($item['subject_name']) ?> - <?= htmlspecialchars($item['teacher_name']) ?> (<?= htmlspecialchars($item['room']) ?>)">
                                            <div class="fw-bold text-truncate" style="font-size: 0.73rem;"><?= htmlspecialchars($item['subject_name']) ?></div>
                                            <div class="d-flex justify-content-between text-truncate" style="font-size: 0.68rem;">
                                                <span class="text-truncate" style="max-width: 85px;"><?= htmlspecialchars($item['teacher_name']) ?></span>
                                                <span class="badge bg-dark bg-opacity-50 py-0 px-1"><?= htmlspecialchars($item['room']) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Tiết trống (Empty Slot) -->
                                    <a href="index.php?controller=schedule&action=create&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?><?= $branchId ? '&branch_id=' . $branchId : '' ?><?= $teacherId ? '&teacher_id=' . $teacherId : '' ?>" 
                                       class="empty-slot-btn no-print" title="Bấm để phân lịch">
                                        <i class="fa-solid fa-plus me-1"></i>Trống
                                    </a>
                                    <span class="text-muted small d-none d-print-block">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
