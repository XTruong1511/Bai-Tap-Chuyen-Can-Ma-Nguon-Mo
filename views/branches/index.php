<?php
$pageTitle = 'Cơ sở đào tạo';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-building-columns text-primary me-2"></i>Quản lý Cơ sở Đào tạo HUIT
        </h5>
        <p class="text-muted small mb-0 mt-1">Các điểm trường phân bổ cơ sở vật chất và giảng dạy của Trường</p>
    </div>
    <a href="index.php?controller=branch&action=create" class="btn-modern-primary">
        <i class="fa-solid fa-plus"></i> Thêm cơ sở
    </a>
</div>

<div class="card card-custom shadow-sm overflow-hidden mb-4">
    <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-city me-1.5 text-primary"></i>Danh sách Cơ sở Đào tạo (<?= count($branches) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">Mã</th>
                    <th>Tên cơ sở</th>
                    <th>Địa chỉ chi tiết</th>
                    <th class="text-center" style="width: 90px;">Số lớp</th>
                    <th class="text-center" style="width: 90px;">Thao tác</th>
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
                            <td class="text-muted small fw-medium text-center">CS<?= $b['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge-branch-<?= ($b['id'] % 4) + 1 ?>">
                                        <i class="fa-solid fa-location-dot"></i> CS<?= $b['id'] ?>
                                    </span>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($b['branch_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="fa-solid fa-map-pin me-1 text-secondary opacity-75"></i><?= htmlspecialchars($b['address']) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=index&branch_id=<?= $b['id'] ?>" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none px-2 py-1 fw-semibold">
                                    <?= $b['schedule_count'] ?> lớp
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=matrix&branch_id=<?= $b['id'] ?>" class="btn-modern-outline py-0.5 px-2" title="Xem Ma trận TKB cơ sở này">
                                        <i class="fa-solid fa-table-cells text-primary"></i>
                                    </a>
                                    <a href="index.php?controller=branch&action=edit&id=<?= $b['id'] ?>" class="btn-modern-outline py-0.5 px-2" title="Chỉnh sửa">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=branch&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Xóa cơ sở <?= htmlspecialchars($b['branch_name']) ?>?');">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0.5 px-2 border" title="Xóa">
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
