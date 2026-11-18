<?php
$pageTitle = 'Quản lý Cơ sở Đào tạo';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-building-columns text-primary me-2"></i>Quản lý Cơ sở Đào tạo HUIT
        </h3>
        <p class="text-muted mb-0 small">Danh sách các cơ sở, phòng thực hành và số lượng lớp học phần được phân bổ.</p>
    </div>
    <a href="index.php?controller=branch&action=create" class="btn btn-huit-primary btn-sm">
        <i class="fa-solid fa-plus-circle me-1"></i> Thêm cơ sở mới
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-city me-2 text-primary"></i>Danh sách Cơ sở HUIT (<?= count($branches) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 50px;">Mã</th>
                    <th>Tên cơ sở đào tạo</th>
                    <th>Địa chỉ chi tiết</th>
                    <th class="text-center">Số lớp đang học</th>
                    <th class="text-center" style="width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Chưa có dữ liệu cơ sở đào tạo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($branches as $b): ?>
                        <tr>
                            <td class="text-muted fw-bold small">CS<?= $b['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark">
                                    <span class="badge badge-branch-<?= ($b['id'] % 4) + 1 ?> me-2">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </span>
                                    <?= htmlspecialchars($b['branch_name']) ?>
                                </div>
                            </td>
                            <td>
                                <i class="fa-solid fa-map-pin text-danger me-1"></i>
                                <?= htmlspecialchars($b['address']) ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=index&branch_id=<?= $b['id'] ?>" class="badge bg-primary text-decoration-none px-2 py-1">
                                    <?= $b['schedule_count'] ?> lớp
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=schedule&action=matrix&branch_id=<?= $b['id'] ?>" class="btn btn-outline-info" title="Xem Ma trận TKB cơ sở">
                                        <i class="fa-solid fa-table-cells"></i>
                                    </a>
                                    <a href="index.php?controller=branch&action=edit&id=<?= $b['id'] ?>" class="btn btn-outline-primary" title="Sửa cơ sở">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=branch&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Bạn có chắc muốn xóa cơ sở <?= htmlspecialchars($b['branch_name']) ?>? Tất cả lịch học tại cơ sở này sẽ bị xóa.');">
                                        <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
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
