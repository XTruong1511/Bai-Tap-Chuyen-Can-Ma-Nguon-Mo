<?php
$pageTitle = 'Cổng thông tin GV';
$isFluid = true;
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
use App\Helpers\Auth;
?>

<!-- Header with Action -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Cổng Thông Tin Giảng Viên
        </h5>
        <p class="text-muted small mb-0 mt-1">Tra cứu thời khóa biểu cá nhân và hướng dẫn di chuyển giữa các cơ sở</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <?php if (Auth::isAdmin()): ?>
            <form method="GET" action="index.php" class="d-flex align-items-center gap-1.5">
                <input type="hidden" name="controller" value="teacher">
                <input type="hidden" name="action" value="portal">
                <span class="small text-muted fw-medium text-nowrap">Chọn GV:</span>
                <select name="teacher_id" class="form-select form-select-sm" style="width: auto; min-width: 190px;" onchange="this.form.submit()">
                    <?php 
                        $groupedByDept = [];
                        foreach ($allTeachers as $t) {
                            $d = $t['department'] ?? 'Bộ môn khác';
                            $groupedByDept[$d][] = $t;
                        }
                        foreach ($groupedByDept as $deptName => $deptTeachers):
                    ?>
                        <optgroup label="<?= htmlspecialchars($deptName) ?>">
                            <?php foreach ($deptTeachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($teacher && $teacher['id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <button onclick="window.print()" class="btn-modern-outline" title="In TKB cá nhân">
            <i class="fa-solid fa-print"></i> In TKB
        </button>
    </div>
</div>

<?php if (!$teacher): ?>
    <div class="alert alert-warning py-3">
        <i class="fa-solid fa-circle-exclamation me-2"></i>Không tìm thấy thông tin giảng viên.
    </div>
<?php else: ?>

<!-- Teacher Profile & Multi-Campus Summary -->
<div class="row g-3 mb-3">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card card-custom h-100 p-3 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="fa-solid fa-user-tie fs-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 text-nowrap"><?= htmlspecialchars($teacher['full_name']) ?></h6>
                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem;">Mã: HUIT-GV<?= str_pad($teacher['id'], 3, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
            <hr class="my-2 text-muted opacity-25">
            <div class="small">
                <div class="mb-1 text-truncate">
                    <i class="fa-solid fa-envelope text-primary me-2"></i> <?= htmlspecialchars($teacher['email']) ?>
                </div>
                <div class="mb-1">
                    <i class="fa-solid fa-phone text-success me-2"></i> <?= htmlspecialchars($teacher['phone']) ?>
                </div>
                <div class="text-truncate">
                    <i class="fa-solid fa-award text-warning me-2"></i> <?= htmlspecialchars($teacher['expertise']) ?>
                </div>
            </div>
            <div class="d-flex justify-content-between pt-2 border-top mt-2 text-center text-nowrap">
                <span class="small text-muted">Lớp phụ trách: <strong class="text-primary"><?= count($schedules) ?> lớp</strong></span>
                <span class="small text-muted">Cơ sở cần đến: <strong class="text-warning"><?= count($branchGrouped) ?> cơ sở</strong></span>
            </div>
        </div>
    </div>

    <!-- Multi-Campus Attendance Guide -->
    <div class="col-lg-8">
        <div class="card card-custom h-100 shadow-sm">
            <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-map-location-dot me-1.5 text-primary"></i>Cơ sở Giảng dạy Cần đến (<?= count($branchGrouped) ?> cơ sở)</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($branchGrouped)): ?>
                    <div class="text-center py-4 text-muted small">
                        Hiện chưa có lịch giảng dạy trong học kỳ này.
                    </div>
                <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($branchGrouped as $bName => $bSchedules): ?>
                            <?php 
                                $bAddress = $bSchedules[0]['branch_address'] ?? '';
                                $bId = $bSchedules[0]['branch_id'] ?? 1;
                            ?>
                            <div class="col-md-6">
                                <div class="p-2.5 rounded border border-start border-3 border-primary bg-white h-100 shadow-none text-nowrap">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge-branch-<?= ($bId % 4) + 1 ?>">
                                            <i class="fa-solid fa-building me-1"></i><?= htmlspecialchars($bName) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border"><?= count($bSchedules) ?> ca học</span>
                                    </div>
                                    <div class="small text-muted text-truncate" title="<?= htmlspecialchars($bAddress) ?>">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($bAddress) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Personal Timetable Grid -->
<div class="card card-custom shadow-sm overflow-hidden mb-3">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fa-solid fa-calendar-check me-1.5 text-primary"></i>
            Thời khóa biểu Tuần: <?= htmlspecialchars($teacher['full_name']) ?>
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-matrix-spacious mb-0 text-nowrap align-middle">
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
                        <td class="bg-light text-start ps-3 align-top py-3" style="background-color: #f8fafc !important;">
                            <div class="text-primary fw-bold" style="font-size: 0.81rem;"><?= $shift ?></div>
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

                        <?php foreach (Schedule::DAYS as $day): ?>
                            <?php $items = $timetableMatrix[$shift][$day] ?? []; ?>
                            <td class="matrix-cell-spacious align-top bg-white p-2">
                                <?php if (!empty($items)): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ($items as $item): ?>
                                            <div class="matrix-card matrix-card-cs<?= ($item['branch_id'] % 4) + 1 ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="fw-bold text-dark text-truncate pe-1" style="font-size: 0.81rem;" title="<?= htmlspecialchars($item['subject_name']) ?>">
                                                        <?= htmlspecialchars($item['subject_name']) ?>
                                                    </div>
                                                    <span class="badge bg-light text-dark border px-1" style="font-size: 0.68rem; font-weight: 600;">
                                                        <i class="fa-solid fa-door-open text-secondary me-0.5" style="font-size: 0.62rem;"></i><?= htmlspecialchars($item['room']) ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                                                    <span class="badge-branch-<?= ($item['branch_id'] % 4) + 1 ?>" style="font-size: 0.64rem; padding: 1px 6px;">
                                                        <?= htmlspecialchars($item['branch_name']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted d-block text-center py-3" style="font-size: 0.76rem;">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Table List of Teacher's Modules -->
<div class="card card-custom shadow-sm overflow-hidden mb-4">
    <div class="card-header-custom py-2 px-3">
        <span><i class="fa-solid fa-list-ul me-1.5 text-primary"></i>Danh sách Chi tiết các Lớp Học Phần Phụ Trách</span>
    </div>
    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 45px;" class="text-center">STT</th>
                    <th>Tên học phần</th>
                    <th>Cơ sở</th>
                    <th>Địa chỉ cơ sở</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            Chưa có dữ liệu phân lịch.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $idx => $s): ?>
                        <tr>
                            <td class="text-muted small fw-medium text-center"><?= $idx + 1 ?></td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($s['subject_name']) ?></span>
                            </td>
                            <td>
                                <span class="badge-branch-<?= ($s['branch_id'] % 4) + 1 ?>">
                                    <?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted"><i class="fa-solid fa-map-pin me-1 text-secondary opacity-75"></i><?= htmlspecialchars($s['branch_address']) ?></span>
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
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
