<?php
$pageTitle = 'Quản lý Lịch giảng dạy';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-list-check text-primary me-2"></i>Quản lý Lịch giảng dạy
        </h3>
        <p class="text-muted mb-0 small">Danh sách các lớp học phần và lịch phân công giảng dạy tại các cơ sở HUIT.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-table-cells me-1"></i> Ma trận TKB & Tiết trống
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn btn-huit-orange btn-sm">
            <i class="fa-solid fa-plus-circle me-1"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-custom mb-4">
    <div class="card-body p-3">
        <form method="GET" action="index.php" class="row g-2 align-items-center">
            <input type="hidden" name="controller" value="schedule">
            <input type="hidden" name="action" value="index">

            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Tìm kiếm môn học, phòng, GV..." 
                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
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
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (($filters['teacher_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
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
                    <option value="">-- Tất cả Ca/Tiết --</option>
                    <?php foreach (Schedule::SHIFTS as $sh): ?>
                        <option value="<?= $sh ?>" <?= (($filters['shift_or_periods'] ?? '') == $sh) ? 'selected' : '' ?>>
                            <?= $sh ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Tìm kiếm / Lọc">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="index.php?controller=schedule&action=index" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Schedule List Table -->
<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-calendar-week me-2 text-primary"></i>Danh sách phân lịch (<?= count($schedules) ?> kết quả)</span>
        <span class="badge bg-secondary-subtle text-secondary small">Học kỳ 1 • 2026-2027</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th>Tên môn học</th>
                    <th>Giảng viên</th>
                    <th>Cơ sở & Địa chỉ</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết học</th>
                    <th>Ghi chú</th>
                    <th class="text-center" style="width: 120px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-calendar-xmark fa-3x mb-3 d-block text-secondary"></i>
                            <h6 class="fw-bold">Không tìm thấy lịch giảng dạy nào</h6>
                            <p class="small mb-3">Thử thay đổi bộ lọc tìm kiếm hoặc thêm lịch giảng dạy mới.</p>
                            <a href="index.php?controller=schedule&action=create" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Thêm lịch mới ngay
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $index => $s): ?>
                        <tr>
                            <td class="text-muted small fw-bold text-center"><?= $index + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($s['subject_name']) ?></div>
                                <span class="badge text-bg-light border text-muted small"><?= htmlspecialchars($s['semester']) ?></span>
                            </td>
                            <td>
                                <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $s['teacher_id'] ?>" class="text-decoration-none fw-semibold text-primary">
                                    <?= htmlspecialchars($s['teacher_name']) ?>
                                </a>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($s['teacher_email']) ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge badge-branch-<?= ($s['branch_id'] % 4) + 1 ?> px-2 py-1">
                                        <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($s['branch_name']) ?>
                                    </span>
                                </div>
                                <small class="text-muted d-block text-truncate mt-1" style="max-width: 200px;" title="<?= htmlspecialchars($s['branch_address']) ?>">
                                    <?= htmlspecialchars($s['branch_address']) ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-dark px-2 py-1 fs-6"><?= htmlspecialchars($s['room']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <?= htmlspecialchars($s['day_of_week']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-nowrap small"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><?= htmlspecialchars($s['notes'] ?? '—') ?></small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=edit&id=<?= $s['id'] ?>" class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=schedule&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Bạn có chắc chắn muốn xóa lịch dạy môn <?= htmlspecialchars($s['subject_name']) ?> của <?= htmlspecialchars($s['teacher_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa lịch">
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
