<?php
$pageTitle = 'Cơ sở đào tạo';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-building-columns text-primary me-2"></i>Quản lý Cơ sở Đào tạo HUIT
    </h4>
    <a href="index.php?controller=branch&action=create" class="btn btn-huit-primary btn-sm text-nowrap">
        <i class="fa-solid fa-plus-circle me-1"></i> Thêm cơ sở
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span class="small fw-bold"><i class="fa-solid fa-city me-1 text-primary"></i>Danh sách Cơ sở (<?= count($branches) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 45px;" class="text-center">Mã</th>
                    <th>Tên cơ sở</th>
                    <th>Địa chỉ chi tiết</th>
                    <th class="text-center" style="width: 90px;">Số lớp</th>
                    <th class="text-center" style="width: 100px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted small">
                            Chưa có dữ liệu cơ sở đào tạo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($branches as $b): ?>
                        <tr>
                            <td class="text-muted fw-bold small text-center">CS<?= $b['id'] ?></td>
                            <td>
                                <span class="badge badge-branch-<?= ($b['id'] % 4) + 1 ?> me-1">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($b['branch_name']) ?></span>
                            </td>
                            <td>
                                <span class="text-muted small"><?= htmlspecialchars($b['address']) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=index&branch_id=<?= $b['id'] ?>" class="badge bg-primary text-decoration-none px-2 py-1">
                                    <?= $b['schedule_count'] ?> lớp
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=matrix&branch_id=<?= $b['id'] ?>" class="btn btn-outline-info py-0 px-2" title="Xem TKB">
                                        <i class="fa-solid fa-table-cells"></i>
                                    </a>
                                    <a href="index.php?controller=branch&action=edit&id=<?= $b['id'] ?>" class="btn btn-outline-primary py-0 px-2" title="Sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=branch&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa cơ sở <?= htmlspecialchars($b['branch_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
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
