<?php
$pageTitle = 'Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">Quản lý Giảng viên</h5>
    </div>
    <a href="index.php?controller=teacher&action=create" class="btn-modern-primary">
        <i class="fa-solid fa-user-plus"></i> Thêm giảng viên
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-users me-1 text-primary"></i>Danh sách Giảng viên (<?= count($teachers) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 45px;" class="text-center">Mã</th>
                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Chuyên môn chính</th>
                    <th class="text-center" style="width: 80px;">Số lớp</th>
                    <th class="text-center" style="width: 80px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
                            Chưa có dữ liệu giảng viên.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td class="text-muted small fw-medium text-center">GV<?= str_pad($t['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($t['full_name']) ?></span>
                            </td>
                            <td>
                                <code class="text-primary small"><?= htmlspecialchars($t['email']) ?></code>
                            </td>
                            <td>
                                <span class="text-muted"><?= htmlspecialchars($t['phone']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($t['expertise']) ?>">
                                    <?= htmlspecialchars($t['expertise']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=index&teacher_id=<?= $t['id'] ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-2 py-1">
                                    <?= $t['schedule_count'] ?> lớp
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $t['id'] ?>" class="btn-modern-outline py-0 px-2" title="Xem Cổng GV">
                                        <i class="fa-solid fa-eye text-primary"></i>
                                    </a>
                                    <a href="index.php?controller=teacher&action=edit&id=<?= $t['id'] ?>" class="btn-modern-outline py-0 px-2" title="Sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=teacher&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa giảng viên <?= htmlspecialchars($t['full_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
