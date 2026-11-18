<?php
$pageTitle = 'Cổng thông tin GV';
$isFluid = true;
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
use App\Helpers\Auth;
?>

<!-- Header with Action -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Cổng Thông Tin Giảng Viên</h5>
    </div>
    <div class="d-flex gap-2 no-print">
        <?php if (Auth::isAdmin()): ?>
            <form method="GET" action="index.php" class="d-flex align-items-center gap-1">
                <input type="hidden" name="controller" value="teacher">
                <input type="hidden" name="action" value="portal">
                <select name="teacher_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <?php foreach ($allTeachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacher && $teacher['id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <button onclick="window.print()" class="btn-modern-outline">
            <i class="fa-solid fa-print"></i> In TKB
        </button>
    </div>
</div>

<?php if (!$teacher): ?>
    <div class="alert alert-warning py-2">
        <i class="fa-solid fa-circle-exclamation me-1"></i>Không tìm thấy thông tin giảng viên.
    </div>
<?php else: ?>

<!-- Teacher Profile & Multi-Campus Summary -->
<div class="row g-3 mb-3">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card card-custom h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                    <i class="fa-solid fa-user-tie fs-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 text-nowrap"><?= htmlspecialchars($teacher['full_name']) ?></h6>
                    <span class="badge bg-light text-secondary border mt-1">Mã: HUIT-GV<?= str_pad($teacher['id'], 3, '0', STR_PAD_LEFT) ?></span>
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
                <span class="small text-muted">Lớp phụ trách: <strong class="text-primary"><?= count($schedules) ?></strong></span>
                <span class="small text-muted">Cơ sở cần đến: <strong class="text-warning"><?= count($branchGrouped) ?></strong></span>
            </div>
        </div>
    </div>

    <!-- Multi-Campus Attendance Guide -->
    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-map-location-dot me-1 text-primary"></i>Cơ sở Cần đến Giảng dạy (<?= count($branchGrouped) ?> cơ sở)</span>
            </div>
            <div class="card-body p-3">
                <?php if (empty($branchGrouped)): ?>
                    <div class="text-center py-3 text-muted small">
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
                                <div class="p-2 rounded border border-start border-3 border-primary bg-white h-100 shadow-sm text-nowrap">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge-branch-<?= ($bId % 4) + 1 ?>">
                                            <i class="fa-solid fa-building me-1"></i><?= htmlspecialchars($bName) ?>
                                        </span>
                                        <span class="badge bg-light text-dark border"><?= count($bSchedules) ?> ca</span>
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
<div class="card card-custom mb-3">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="fa-solid fa-calendar-check me-1 text-primary"></i>
            Thời khóa biểu Tuần: <?= htmlspecialchars($teacher['full_name']) ?>
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-matrix-spacious mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 130px;" class="py-3">Ca / Tiết</th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th style="width: 14%;" class="py-3"><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <td class="bg-light fw-semibold text-dark small text-start ps-3 align-middle" style="background-color: #f8fafc !important;">
                            <div class="text-primary fw-bold" style="font-size: 0.8rem;"><?= $shift ?></div>
                        </td>

                        <?php foreach (Schedule::DAYS as $day): ?>
                            <?php $items = $timetableMatrix[$shift][$day] ?? []; ?>
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
                                                <div class="small text-muted text-truncate" style="font-size: 0.73rem;">
                                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($item['branch_name']) ?>
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
<div class="card card-custom">
    <div class="card-header-custom py-2 px-3">
        <span><i class="fa-solid fa-list-ul me-1 text-primary"></i>Danh sách Chi tiết các Lớp Học Phần</span>
    </div>
    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 45px;" class="text-center">STT</th>
                    <th>Tên học phần</th>
                    <th>Cơ sở</th>
                    <th>Địa chỉ</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted small">
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
                                <span class="small text-muted"><?= htmlspecialchars($s['branch_address']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($s['room']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border"><?= htmlspecialchars($s['day_of_week']) ?></span>
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
