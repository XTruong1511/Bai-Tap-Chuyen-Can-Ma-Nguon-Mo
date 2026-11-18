<?php
$pageTitle = 'Ma trận Thời khóa biểu';
$isFluid = true; // Use fluid container for spacious calendar matrix
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<!-- Header & Quick Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold text-dark mb-0">Ma trận Thời khóa biểu</h5>
            <span class="badge bg-light text-secondary border fw-medium px-2 py-1" style="font-size: 0.72rem;">
                <i class="fa-regular fa-calendar-days me-1 text-primary"></i>Học kỳ 1 • 2024 - 2025
            </span>
        </div>
        <p class="text-muted small mb-0 mt-1">Lưới lịch giảng dạy tổng quan theo các ca học và cơ sở HUIT</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn-modern-outline" title="In hoặc lưu PDF">
            <i class="fa-solid fa-print"></i> In TKB
        </button>
        <a href="index.php?controller=schedule&action=create" class="btn-modern-primary">
            <i class="fa-solid fa-plus"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Campus Filter & Teacher Filter Controls -->
<div class="card card-custom mb-3 no-print p-2">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-2">
        <!-- Modern Linear Segmented Control for Campus Switching -->
        <div class="campus-segmented-control">
            <a href="index.php?controller=schedule&action=matrix<?= $teacherId ? '&teacher_id='.$teacherId : '' ?>" 
               class="campus-segment-item <?= empty($branchId) ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group me-1"></i> Tất cả cơ sở
            </a>
            <?php foreach ($branches as $b): ?>
                <?php 
                    $dotColors = [1 => '#2563eb', 2 => '#f97316', 3 => '#9333ea', 4 => '#16a34a'];
                    $color = $dotColors[$b['id']] ?? '#64748b';
                ?>
                <a href="index.php?controller=schedule&action=matrix&branch_id=<?= $b['id'] ?><?= $teacherId ? '&teacher_id='.$teacherId : '' ?>" 
                   class="campus-segment-item <?= ($branchId === (int)$b['id']) ? 'active' : '' ?>">
                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background-color:<?= $color ?>; margin-right:5px;"></span>
                    CS<?= $b['id'] ?> - <?= htmlspecialchars($b['branch_name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Teacher Filter & Quick Reset -->
        <div class="d-flex align-items-center gap-2 text-nowrap ms-auto ms-xl-0">
            <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
                <input type="hidden" name="controller" value="schedule">
                <input type="hidden" name="action" value="matrix">
                <?php if ($branchId): ?><input type="hidden" name="branch_id" value="<?= $branchId ?>"><?php endif; ?>
                
                <span class="small text-muted fw-medium"><i class="fa-solid fa-user-filter me-1 text-primary"></i>Lọc theo GV:</span>
                <select name="teacher_id" class="form-select form-select-sm" style="width: auto; min-width: 190px;" onchange="this.form.submit()">
                    <option value="">-- Tất cả giảng viên --</option>
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
                                <option value="<?= $t['id'] ?>" <?= ($teacherId === (int)$t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($teacherId): ?>
                <a href="index.php?controller=schedule&action=matrix<?= $branchId ? '&branch_id='.$branchId : '' ?>" class="btn btn-sm btn-outline-danger py-1 px-2" title="Bỏ lọc giảng viên">
                    <i class="fa-solid fa-xmark me-1"></i> Bỏ lọc GV
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Spacious Matrix Table Grid -->
<div class="card card-custom shadow-sm overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-matrix-spacious text-nowrap align-middle">
            <thead>
                <tr>
                    <th style="width: 140px;" class="py-3 text-start ps-3">
                        <span class="text-secondary fw-semibold small text-uppercase tracking-wider">Ca / Tiết</span>
                    </th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th style="width: 14%;" class="py-3 text-center">
                            <span class="fw-bold text-dark d-block"><?= $d ?></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <!-- Shift Column with Modern Timing Badges -->
                        <td class="bg-light text-start ps-3 align-top py-3" style="background-color: #f8fafc !important; border-right: 1px solid #e2e8f0;">
                            <div class="fw-bold text-primary" style="font-size: 0.82rem;">
                                <?= $shift ?>
                            </div>
                            <div class="d-inline-flex align-items-center gap-1 text-muted fw-normal mt-1 bg-white px-2 py-0.5 rounded border" style="font-size: 0.72rem;">
                                <i class="fa-regular fa-clock text-secondary" style="font-size: 0.68rem;"></i>
                                <?php
                                    if (strpos($shift, 'Tiết 1-3') !== false) echo '07:00 - 09:15';
                                    elseif (strpos($shift, 'Tiết 4-6') !== false) echo '09:30 - 11:45';
                                    elseif (strpos($shift, 'Tiết 7-9') !== false) echo '12:30 - 14:45';
                                    elseif (strpos($shift, 'Tiết 10-12') !== false) echo '15:00 - 17:15';
                                    elseif (strpos($shift, 'Tiết 13-15') !== false) echo '17:30 - 20:30';
                                ?>
                            </div>
                        </td>

                        <!-- Days Columns -->
                        <?php foreach (Schedule::DAYS as $day): ?>
                            <?php $items = $matrix[$shift][$day] ?? []; ?>
                            <td class="matrix-cell-spacious align-top bg-white p-2">
                                <?php if (!empty($items)): ?>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($items as $item): ?>
                                            <div class="matrix-card matrix-card-cs<?= ($item['branch_id'] % 4) + 1 ?>">
                                                <!-- Subject Title and Room -->
                                                <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.81rem;" title="<?= htmlspecialchars($item['subject_name']) ?>">
                                                        <?= htmlspecialchars($item['subject_name']) ?>
                                                    </div>
                                                    <span class="badge bg-light text-dark border px-1 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                        <i class="fa-solid fa-door-open text-secondary me-0.5" style="font-size: 0.62rem;"></i><?= htmlspecialchars($item['room']) ?>
                                                    </span>
                                                </div>

                                                <!-- Teacher and Campus Info -->
                                                <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 0.73rem;">
                                                    <span class="text-truncate text-muted" style="max-width: 115px;" title="<?= htmlspecialchars($item['teacher_name']) ?>">
                                                        <i class="fa-solid fa-chalkboard-user me-1 text-primary" style="font-size: 0.68rem;"></i><?= htmlspecialchars($item['teacher_name']) ?>
                                                    </span>
                                                    
                                                    <?php if (empty($branchId)): ?>
                                                        <span class="badge-branch-<?= ($item['branch_id'] % 4) + 1 ?>" style="font-size: 0.64rem; padding: 1px 6px;">
                                                            CS<?= $item['branch_id'] ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <a href="index.php?controller=schedule&action=edit&id=<?= $item['id'] ?>" class="text-muted hover-text-primary no-print" title="Chỉnh sửa">
                                                            <i class="fa-regular fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Sleek Empty Slot Button -->
                                    <a href="index.php?controller=schedule&action=create&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?><?= $branchId ? '&branch_id=' . $branchId : '' ?><?= $teacherId ? '&teacher_id=' . $teacherId : '' ?>" 
                                       class="matrix-empty-slot no-print" title="Bấm để phân lịch vào ca này">
                                        <i class="fa-solid fa-plus-circle me-1 text-muted opacity-50"></i> Trống
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
