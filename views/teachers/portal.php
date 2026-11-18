<?php
$pageTitle = 'Cổng thông tin Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
use App\Helpers\Auth;
?>

<!-- Header with Action -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Cổng Thông Tin Giảng Viên
        </h3>
        <p class="text-muted mb-0 small">Tra cứu thời khóa biểu cá nhân, phòng học và nắm bắt cơ sở giảng dạy tại HUIT.</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <?php if (Auth::isAdmin()): ?>
            <!-- Admin Preview Selector -->
            <form method="GET" action="index.php" class="d-flex align-items-center gap-2">
                <input type="hidden" name="controller" value="teacher">
                <input type="hidden" name="action" value="portal">
                <select name="teacher_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach ($allTeachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($teacher && $teacher['id'] == $t['id']) ? 'selected' : '' ?>>
                            Xem GV: <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print me-1"></i> In Thời khóa biểu
        </button>
    </div>
</div>

<?php if (!$teacher): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-circle-exclamation me-2"></i>Không tìm thấy thông tin giảng viên.
    </div>
<?php else: ?>

<!-- Teacher Profile & Multi-Campus Summary -->
<div class="row g-4 mb-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card card-custom h-100">
            <div class="card-body p-4 text-center">
                <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="fa-solid fa-user-tie fa-3x" style="color: var(--huit-blue);"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($teacher['full_name']) ?></h4>
                <div class="text-muted small mb-3">Mã GV: <strong>HUIT-GV<?= str_pad($teacher['id'], 3, '0', STR_PAD_LEFT) ?></strong></div>

                <div class="text-start bg-light p-3 rounded-3 small mb-3">
                    <div class="mb-2">
                        <i class="fa-solid fa-envelope text-primary me-2"></i>
                        <strong>Email:</strong> <?= htmlspecialchars($teacher['email']) ?>
                    </div>
                    <div class="mb-2">
                        <i class="fa-solid fa-phone text-success me-2"></i>
                        <strong>SĐT:</strong> <?= htmlspecialchars($teacher['phone']) ?>
                    </div>
                    <div>
                        <i class="fa-solid fa-award text-warning me-2"></i>
                        <strong>Chuyên môn:</strong> <?= htmlspecialchars($teacher['expertise']) ?>
                    </div>
                </div>

                <div class="d-flex justify-content-around text-center pt-2 border-top">
                    <div>
                        <div class="fs-4 fw-bold text-primary"><?= count($schedules) ?></div>
                        <div class="text-muted small">Lớp phụ trách</div>
                    </div>
                    <div class="border-end"></div>
                    <div>
                        <div class="fs-4 fw-bold text-warning"><?= count($branchGrouped) ?></div>
                        <div class="text-muted small">Cơ sở cần đến</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-Campus Attendance Guide -->
    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span>
                    <i class="fa-solid fa-map-location-dot me-2 text-primary"></i>
                    Chỉ dẫn Cơ sở Cần đến Giảng dạy
                </span>
                <span class="badge bg-primary text-white"><?= count($branchGrouped) ?> Cơ sở</span>
            </div>
            <div class="card-body p-4">
                <?php if (empty($branchGrouped)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fa-regular fa-calendar fa-2x mb-2 d-block"></i>
                        Thầy/Cô hiện chưa có lịch giảng dạy nào trong học kỳ này.
                    </div>
                <?php else: ?>
                    <p class="small text-muted mb-3">
                        Dưới đây là các cơ sở Thầy/Cô có lịch giảng dạy trong tuần. Vui lòng kiểm tra kỹ địa chỉ cơ sở và phòng học trước mỗi buổi lên lớp:
                    </p>
                    <div class="row g-3">
                        <?php foreach ($branchGrouped as $bName => $bSchedules): ?>
                            <?php 
                                $bAddress = $bSchedules[0]['branch_address'] ?? '';
                                $bId = $bSchedules[0]['branch_id'] ?? 1;
                            ?>
                            <div class="col-md-6">
                                <div class="p-3 rounded border border-start border-4 border-primary bg-white h-100 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge badge-branch-<?= ($bId % 4) + 1 ?> px-2 py-1">
                                            <i class="fa-solid fa-building me-1"></i><?= htmlspecialchars($bName) ?>
                                        </span>
                                        <span class="badge bg-light text-dark fw-bold"><?= count($bSchedules) ?> ca dạy</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                        <strong>Địa chỉ:</strong> <?= htmlspecialchars($bAddress) ?>
                                    </div>
                                    <div class="small text-dark fw-semibold">
                                        Các ca dạy:
                                        <ul class="mb-0 ps-3 mt-1 text-muted" style="font-size: 0.8rem;">
                                            <?php foreach ($bSchedules as $bs): ?>
                                                <li><?= $bs['day_of_week'] ?> - <?= $bs['shift_or_periods'] ?> (Phòng <?= htmlspecialchars($bs['room']) ?>)</li>
                                            <?php endforeach; ?>
                                        </ul>
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
<div class="card card-custom mb-4">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span>
            <i class="fa-solid fa-calendar-check me-2 text-primary"></i>
            Thời khóa biểu Cá nhân theo Tuần - <?= htmlspecialchars($teacher['full_name']) ?>
        </span>
        <span class="badge text-bg-light small">Học kỳ 1 • 2026-2027</span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-matrix mb-0">
            <thead class="table-dark" style="background-color: var(--huit-dark);">
                <tr>
                    <th style="width: 140px;" class="py-3">Ca / Tiết</th>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <th class="py-3"><?= $d ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Schedule::SHIFTS as $shift): ?>
                    <tr>
                        <td class="bg-light fw-bold text-dark small text-start ps-3 align-middle">
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
                            <?php $items = $timetableMatrix[$shift][$day] ?? []; ?>
                            <td class="matrix-cell align-top bg-white">
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <div class="schedule-pill text-white badge-branch-<?= ($item['branch_id'] % 4) + 1 ?>">
                                            <div class="fw-bold text-truncate" title="<?= htmlspecialchars($item['subject_name']) ?>">
                                                <?= htmlspecialchars($item['subject_name']) ?>
                                            </div>
                                            <div class="mt-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-door-open me-1"></i>Phòng: <strong><?= htmlspecialchars($item['room']) ?></strong>
                                            </div>
                                            <div class="text-white-50 text-truncate mt-1" style="font-size: 0.7rem;" title="<?= htmlspecialchars($item['branch_name']) ?>">
                                                <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($item['branch_name']) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-muted small py-3" style="font-size: 0.75rem; color: #cbd5e1 !important;">
                                        Trống
                                    </div>
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
    <div class="card-header-custom">
        <i class="fa-solid fa-list-ul me-2 text-primary"></i>Danh sách Chi tiết các Lớp Học Phần Phụ trách
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>STT</th>
                    <th>Tên học phần</th>
                    <th>Cơ sở giảng dạy</th>
                    <th>Địa chỉ cụ thể</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Chưa có dữ liệu phân lịch.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $idx => $s): ?>
                        <tr>
                            <td class="text-muted fw-bold"><?= $idx + 1 ?></td>
                            <td>
                                <strong class="text-dark"><?= htmlspecialchars($s['subject_name']) ?></strong>
                                <span class="badge text-bg-light border text-muted small d-block" style="width: fit-content;"><?= htmlspecialchars($s['semester']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-branch-<?= ($s['branch_id'] % 4) + 1 ?> px-2 py-1">
                                    <i class="fa-solid fa-building me-1"></i><?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($s['branch_address']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-dark px-2 py-1"><?= htmlspecialchars($s['room']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($s['day_of_week']) ?></span>
                            </td>
                            <td>
                                <span class="fw-semibold small"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($s['notes'] ?? '—') ?></small>
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
