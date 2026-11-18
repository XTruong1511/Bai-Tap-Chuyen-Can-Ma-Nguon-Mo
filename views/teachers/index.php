<?php
$pageTitle = 'Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>Quản lý Giảng viên
    </h4>
    <a href="index.php?controller=teacher&action=create" class="btn btn-huit-primary btn-sm text-nowrap">
        <i class="fa-solid fa-user-plus me-1"></i> Thêm giảng viên
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span class="small fw-bold"><i class="fa-solid fa-users me-1 text-primary"></i>Danh sách Giảng viên (<?= count($teachers) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 45px;" class="text-center">Mã</th>
                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Chuyên môn chính</th>
                    <th class="text-center" style="width: 90px;">Số lớp</th>
                    <th class="text-center" style="width: 100px;">Thao tác</th>
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
                            <td class="text-muted fw-bold small text-center">GV<?= str_pad($t['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($t['full_name']) ?></span>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($t['email']) ?></code>
                            </td>
                            <td><?= htmlspecialchars($t['phone']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($t['expertise']) ?>">
                                    <i class="fa-solid fa-award text-warning me-1"></i>
                                    <?= htmlspecialchars($t['expertise']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=index&teacher_id=<?= $t['id'] ?>" class="badge bg-primary text-decoration-none px-2 py-1">
                                    <?= $t['schedule_count'] ?> lớp
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $t['id'] ?>" class="btn btn-outline-info py-0 px-2" title="Xem Cổng GV">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="index.php?controller=teacher&action=edit&id=<?= $t['id'] ?>" class="btn btn-outline-primary py-0 px-2" title="Sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=teacher&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa giảng viên <?= htmlspecialchars($t['full_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger py-0 px-2" title="Xóa">
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
