<?php
$pageTitle = 'Bảng điều khiển';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold text-dark mb-0">
        <i class="fa-solid fa-gauge-high text-primary me-2"></i>Tổng quan Hệ thống
    </h4>
    <div class="d-flex gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn btn-outline-secondary btn-sm text-nowrap">
            <i class="fa-solid fa-table-cells me-1"></i> Ma trận TKB
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn btn-huit-orange btn-sm text-nowrap">
            <i class="fa-solid fa-calendar-plus me-1"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-2 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-nowrap">Tổng lịch phân bổ</div>
                    <div class="fs-4 fw-bold text-dark mb-0"><?= $stats['total_schedules'] ?></div>
                </div>
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2">
                    <i class="fa-solid fa-calendar-days fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-nowrap">Giảng viên</div>
                    <div class="fs-4 fw-bold text-dark mb-0"><?= $stats['total_teachers'] ?></div>
                </div>
                <div class="rounded-3 bg-success bg-opacity-10 text-success p-2">
                    <i class="fa-solid fa-chalkboard-user fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-nowrap">Cơ sở đào tạo</div>
                    <div class="fs-4 fw-bold text-dark mb-0"><?= $stats['total_branches'] ?></div>
                </div>
                <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-2">
                    <i class="fa-solid fa-building-columns fs-5"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-nowrap">Cảnh báo di chuyển</div>
                    <div class="fs-4 fw-bold text-dark mb-0"><?= count($travelAlerts) ?></div>
                </div>
                <div class="rounded-3 bg-info bg-opacity-10 text-info p-2">
                    <i class="fa-solid fa-person-walking-luggage fs-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Campus Travel Alerts Notice -->
<?php if (!empty($travelAlerts)): ?>
<div class="card card-custom mb-3 border-warning bg-warning bg-opacity-10">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
            <span class="fw-bold text-dark small">Lưu ý di chuyển nhiều cơ sở trong ngày:</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach (array_slice($travelAlerts, 0, 4) as $alert): ?>
                <div class="bg-white px-2 py-1 rounded border small text-nowrap">
                    <span class="badge bg-warning text-dark me-1"><?= $alert['day'] ?></span>
                    <strong><?= htmlspecialchars($alert['teacher']) ?>:</strong>
                    <span class="text-primary"><?= htmlspecialchars($alert['branches']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts Row -->
<div class="row g-3 mb-3">
    <!-- Chart 1: Branch Distribution -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="small fw-bold"><i class="fa-solid fa-chart-pie me-1 text-primary"></i>Phân bổ Lịch theo Cơ sở</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-center align-items-center">
                <div style="width: 100%; max-width: 280px; height: 210px;">
                    <canvas id="branchChart"></canvas>
                </div>
                <div class="mt-2 w-100">
                    <ul class="list-group list-group-flush small">
                        <?php foreach ($stats['branch_stats'] as $bs): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0 text-nowrap">
                                <span class="text-truncate" style="max-width: 220px;">
                                    <i class="fa-solid fa-circle me-1 small text-primary" style="font-size: 0.6rem;"></i> <?= htmlspecialchars($bs['branch_name']) ?>
                                </span>
                                <span class="badge bg-light text-dark fw-bold"><?= $bs['count'] ?> lớp</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Day of Week Distribution -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="card-header-custom py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="small fw-bold"><i class="fa-solid fa-chart-column me-1 text-primary"></i>Phân bổ Lịch theo Thứ trong Tuần</span>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;">
                    <canvas id="dayChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter & Recent Schedule Allocation -->
<div class="card card-custom mb-4">
    <div class="card-header-custom py-2 px-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <span class="small fw-bold"><i class="fa-solid fa-list-check me-1 text-primary"></i>Lịch phân bổ mới nhất</span>
        
        <!-- Filter Form -->
        <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 align-items-center text-nowrap">
            <input type="hidden" name="controller" value="dashboard">
            <input type="hidden" name="action" value="index">
            
            <select name="branch_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Tất cả Cơ sở</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($filterBranchId === (int)$b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['branch_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="teacher_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">Tất cả Giảng viên</option>
                <?php foreach ($teachers as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($filterTeacherId === (int)$t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($filterBranchId || $filterTeacherId): ?>
                <a href="index.php?controller=dashboard&action=index" class="btn btn-outline-danger btn-sm py-0 px-2">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0 text-nowrap">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Học phần</th>
                    <th>Giảng viên</th>
                    <th>Cơ sở</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                    <th class="text-center" style="width: 70px;">Sửa</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($displaySchedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted small">
                            Không có lịch phù hợp bộ lọc.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($displaySchedules as $s): ?>
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($s['subject_name']) ?></span>
                            </td>
                            <td>
                                <span class="fw-medium text-primary"><?= htmlspecialchars($s['teacher_name']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-branch-<?= ($s['branch_id'] % 4) + 1 ?>">
                                    <?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark"><?= htmlspecialchars($s['room']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($s['day_of_week']) ?></span>
                            </td>
                            <td>
                                <span class="small"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=edit&id=<?= $s['id'] ?>" class="btn btn-outline-primary btn-sm py-0 px-2" title="Chỉnh sửa">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-top text-center py-2">
        <a href="index.php?controller=schedule&action=index" class="text-decoration-none small fw-semibold">
            Xem tất cả <?= $stats['total_schedules'] ?> lịch giảng dạy <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<!-- Chart.js Setup Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const branchCtx = document.getElementById('branchChart').getContext('2d');
    const branchData = <?= json_encode($stats['branch_stats']) ?>;
    
    new Chart(branchCtx, {
        type: 'doughnut',
        data: {
            labels: branchData.map(b => b.branch_name),
            datasets: [{
                data: branchData.map(b => b.count),
                backgroundColor: ['#0b3c61', '#f37021', '#7c3aed', '#059669', '#64748b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '65%'
        }
    });

    const dayCtx = document.getElementById('dayChart').getContext('2d');
    const dayData = <?= json_encode($stats['day_stats']) ?>;

    new Chart(dayCtx, {
        type: 'bar',
        data: {
            labels: dayData.map(d => d.day_of_week),
            datasets: [{
                label: 'Số ca học',
                data: dayData.map(d => d.count),
                backgroundColor: 'rgba(11, 60, 97, 0.85)',
                hoverBackgroundColor: '#f37021',
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
