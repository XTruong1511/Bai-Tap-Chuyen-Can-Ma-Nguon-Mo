<?php
$pageTitle = 'Lịch giảng dạy';
require_once __DIR__ . '/../layouts/header.php';
use App\Models\Schedule;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Quản lý Lịch giảng dạy</h5>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn-modern-outline">
            <i class="fa-solid fa-table-cells"></i> Ma trận TKB
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn-modern-primary">
            <i class="fa-solid fa-plus"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card card-custom mb-3">
    <div class="card-body p-2">
        <form method="GET" action="index.php" class="row g-2 align-items-center text-nowrap">
            <input type="hidden" name="controller" value="schedule">
            <input type="hidden" name="action" value="index">

            <div class="col-md-3">
                <input type="text" name="search" class="form-control" 
                       placeholder="Tìm môn học, phòng, GV..." 
                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            </div>

            <div class="col-md-2">
                <select name="branch_id" class="form-select">
                    <option value="">Tất cả Cơ sở</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (($filters['branch_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="teacher_id" class="form-select">
                    <option value="">Tất cả Giảng viên</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (($filters['teacher_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="day_of_week" class="form-select">
                    <option value="">Tất cả Thứ</option>
                    <?php foreach (Schedule::DAYS as $d): ?>
                        <option value="<?= $d ?>" <?= (($filters['day_of_week'] ?? '') == $d) ? 'selected' : '' ?>>
                            <?= $d ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <select name="shift_or_periods" class="form-select">
                    <option value="">Tất cả Ca học</option>
                    <?php foreach (Schedule::SHIFTS as $sh): ?>
                        <option value="<?= $sh ?>" <?= (($filters['shift_or_periods'] ?? '') == $sh) ? 'selected' : '' ?>>
                            <?= $sh ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-modern-primary w-100 justify-content-center" title="Lọc">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="index.php?controller=schedule&action=index" class="btn btn-modern-outline justify-content-center" title="Đặt lại">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Schedule List Table -->
<div class="card card-custom">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-calendar-days me-1 text-primary"></i>Danh sách Lịch (<?= count($schedules) ?> kết quả)</span>
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
                            <i class="fa-regular fa-calendar-xmark fa-2x mb-2 d-block text-secondary"></i>
                            Không tìm thấy lịch giảng dạy phù hợp.
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
                                <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $s['teacher_id'] ?>" class="text-decoration-none text-primary fw-medium">
                                    <?= htmlspecialchars($s['teacher_name']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge-branch-<?= ($s['branch_id'] % 4) + 1 ?>">
                                    <?= htmlspecialchars($s['branch_name']) ?>
                                </span>
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
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=edit&id=<?= $s['id'] ?>" class="btn-modern-outline py-0 px-2" title="Sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=schedule&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa lịch môn <?= htmlspecialchars($s['subject_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2 border" title="Xóa">
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
