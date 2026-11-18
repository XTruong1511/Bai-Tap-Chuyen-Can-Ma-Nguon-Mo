<?php
$pageTitle = 'Quản lý Giảng viên';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>Quản lý Giảng viên
        </h3>
        <p class="text-muted mb-0 small">Danh sách đội ngũ giảng viên, lĩnh vực chuyên môn và số lượng lịch phân bổ.</p>
    </div>
    <a href="index.php?controller=teacher&action=create" class="btn btn-huit-primary btn-sm">
        <i class="fa-solid fa-user-plus me-1"></i> Thêm giảng viên mới
    </a>
</div>

<div class="card card-custom">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <span><i class="fa-solid fa-users me-2 text-primary"></i>Danh sách Giảng viên (<?= count($teachers) ?>)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 50px;">Mã</th>
                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Lĩnh vực Chuyên môn chính</th>
                    <th class="text-center">Số lớp phụ trách</th>
                    <th class="text-center" style="width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có dữ liệu giảng viên.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td class="text-muted fw-bold small">GV<?= str_pad($t['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($t['full_name']) ?></div>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($t['email']) ?></code>
                            </td>
                            <td><?= htmlspecialchars($t['phone']) ?></td>
                            <td>
                                <span class="badge bg-light text-dark border p-2">
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
                                    <a href="index.php?controller=teacher&action=portal&teacher_id=<?= $t['id'] ?>" class="btn btn-outline-info" title="Xem Cổng thông tin GV">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="index.php?controller=teacher&action=edit&id=<?= $t['id'] ?>" class="btn btn-outline-primary" title="Sửa thông tin">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="index.php?controller=teacher&action=delete" style="display:inline;" onsubmit="return confirmDelete(this, 'Bạn có chắc muốn xóa giảng viên <?= htmlspecialchars($t['full_name']) ?>? Tất cả lịch dạy liên quan sẽ bị xóa.');">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
