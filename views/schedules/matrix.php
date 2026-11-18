<?php
$pageTitle = 'Ma trận Thời khóa biểu';
$isFluid = true; // Use fluid container to give maximum horizontal space
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-table-cells text-primary me-2"></i>Ma trận Thời khóa biểu & Tiết trống
        </h5>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn-modern-outline">
            <i class="fa-solid fa-print"></i> In TKB
        </button>
        <a href="index.php?controller=schedule&action=create" class="btn-modern-primary">
            <i class="fa-solid fa-plus"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Campus Tabs & Filters Bar -->
<div class="card card-custom mb-3 no-print p-2">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
        <!-- Campus Filter Tabs (1-Click switch to spread out classes) -->
        <div class="d-flex flex-wrap nav-tabs-branch">
            <a href="index.php?controller=schedule&action=matrix<?= $teacherId ? '&teacher_id='.$teacherId : '' ?>" 
               class="nav-link-tab <?= empty($branchId) ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group me-1"></i> Tất cả cơ sở
            </a>
            <?php foreach ($branches as $b): ?>
                <a href="index.php?controller=schedule&action=matrix&branch_id=<?= $b['id'] ?><?= $teacherId ? '&teacher_id='.$teacherId : '' ?>" 
                   class="nav-link-tab <?= ($branchId === (int)$b['id']) ? 'active' : '' ?>">
                    <span class="badge-branch-<?= ($b['id'] % 4) + 1 ?> me-1" style="font-size: 0.65rem;">CS<?= $b['id'] ?></span>
                    <?= htmlspecialchars($b['branch_name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Teacher Filter Dropdown -->
        <div class="d-flex align-items-center gap-2 text-nowrap">
            <form method="GET" action="index.php" class="d-flex align-items-center gap-1">
                <input type="hidden" name="controller" value="schedule">
                <input type="hidden" name="action" value="matrix">
                <?php if ($branchId): ?><input type="hidden" name="branch_id" value="<?= $branchId ?>"><?php endif; ?>
                
                <span class="small text-muted fw-medium">Lọc GV:</span>
                <select name="teacher_id" class="form-select form-select-sm" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                    <option value="">-- Tất cả giảng viên --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacherId === (int)$t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($teacherId): ?>
                <a href="index.php?controller=schedule&action=matrix<?= $branchId ? '&branch_id='.$branchId : '' ?>" class="btn btn-sm btn-outline-danger py-1 px-2" title="Bỏ lọc GV">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Spacious Matrix Table Grid -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-matrix-spacious text-nowrap">
            <thead>
                <tr>
                    <th style="width: 130px;" class="py-3">Ca / Tiết học</th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th style="width: 14%;" class="py-3"><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <!-- Shift column -->
                        <td class="bg-light fw-semibold text-dark small text-start ps-3 align-middle" style="background-color: #f8fafc !important;">
                            <div class="text-primary fw-bold" style="font-size: 0.8rem;"><?= $shift ?></div>
                            <div class="text-muted fw-normal mt-1" style="font-size: 0.72rem;">
                                <?php
                                    if (strpos($shift, 'Tiết 1-3') !== false) echo '07:00 - 09:15';
                                    elseif (strpos($shift, 'Tiết 4-6') !== false) echo '09:30 - 11:45';
                                    elseif (strpos($shift, 'Tiết 7-9') !== false) echo '12:30 - 14:45';
                                    elseif (strpos($shift, 'Tiết 10-12') !== false) echo '15:00 - 17:15';
                                    elseif (strpos($shift, 'Tiết 13-15') !== false) echo '17:30 - 20:30';
                                ?>
                            </div>
                        </td>

                        <!-- Days columns -->
                        <?php foreach (Schedule::DAYS as $day): ?>
                            <?php $items = $matrix[$shift][$day] ?? []; ?>
                            <td class="matrix-cell-spacious align-top bg-white">
                                <?php if (!empty($items)): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ($items as $item): ?>
                                            <div class="schedule-card-spacious card-branch-<?= ($item['branch_id'] % 4) + 1 ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="fw-bold text-dark text-truncate pe-1" style="font-size: 0.8rem;" title="<?= htmlspecialchars($item['subject_name']) ?>">
                                                        <?= htmlspecialchars($item['subject_name']) ?>
                                                    </div>
                                                    <span class="badge bg-light text-dark border px-1" style="font-size: 0.7rem; font-weight: 600;">
                                                        <?= htmlspecialchars($item['room']) ?>
                                                    </span>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.73rem;">
                                                    <span class="text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($item['teacher_name']) ?>">
                                                        <i class="fa-regular fa-user me-1 text-primary"></i><?= htmlspecialchars($item['teacher_name']) ?>
                                                    </span>
                                                    
                                                    <?php if (empty($branchId)): ?>
                                                        <span class="badge-branch-<?= ($item['branch_id'] % 4) + 1 ?>" style="font-size: 0.65rem; padding: 2px 5px;">
                                                            <?= htmlspecialchars($item['branch_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Spacious Empty Slot Button -->
                                    <a href="index.php?controller=schedule&action=create&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?><?= $branchId ? '&branch_id=' . $branchId : '' ?><?= $teacherId ? '&teacher_id=' . $teacherId : '' ?>" 
                                       class="empty-slot-spacious no-print" title="Bấm để phân lịch vào ca này">
                                        <i class="fa-solid fa-plus me-1 text-muted opacity-75"></i> Tiết trống
                                    </a>
                                    <span class="text-muted small d-none d-print-block text-center py-2">—</span>
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
