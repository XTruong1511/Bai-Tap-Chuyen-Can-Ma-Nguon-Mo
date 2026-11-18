<?php
$pageTitle = 'Tổng quan';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Header Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
        <h5 class="fw-bold text-dark mb-0">
            <i class="fa-solid fa-chart-pie text-primary me-2"></i>Tổng quan Hệ thống
        </h5>
        <p class="text-muted small mb-0 mt-1">Báo cáo phân bổ giảng dạy, lưu lượng ca học và cảnh báo liên cơ sở</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn-modern-outline">
            <i class="fa-solid fa-table-cells"></i> Ma trận TKB
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn-modern-primary">
            <i class="fa-solid fa-plus"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Modern KPI Stats Cards -->
<div class="row g-3 mb-3">
    <!-- Stat 1: Total Schedules -->
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium text-uppercase tracking-wider" style="font-size: 0.72rem;">Tổng lịch phân bổ</span>
                    <div class="fs-3 fw-bold text-dark mt-1" style="letter-spacing: -0.02em;"><?= $stats['total_schedules'] ?></div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle mt-1" style="font-size: 0.68rem; font-weight: 500;">
                        <i class="fa-solid fa-calendar-check me-1"></i>Học kỳ 1
                    </span>
                </div>
                <div class="kpi-icon-box bg-primary-subtle text-primary">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Teachers -->
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium text-uppercase tracking-wider" style="font-size: 0.72rem;">Giảng viên Khoa</span>
                    <div class="fs-3 fw-bold text-dark mt-1" style="letter-spacing: -0.02em;"><?= $stats['total_teachers'] ?></div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 0.68rem; font-weight: 500;">
                        <i class="fa-solid fa-chalkboard-user me-1"></i>Khoa CNTT
                    </span>
                </div>
                <div class="kpi-icon-box bg-success-subtle text-success">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Campuses -->
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium text-uppercase tracking-wider" style="font-size: 0.72rem;">Cơ sở Đào tạo</span>
                    <div class="fs-3 fw-bold text-dark mt-1" style="letter-spacing: -0.02em;"><?= $stats['total_branches'] ?></div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1" style="font-size: 0.68rem; font-weight: 500;">
                        <i class="fa-solid fa-location-dot me-1"></i>Đa cơ sở
                    </span>
                </div>
                <div class="kpi-icon-box bg-warning-subtle text-warning">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Travel Alerts -->
    <div class="col-6 col-lg-3">
        <div class="kpi-card h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium text-uppercase tracking-wider" style="font-size: 0.72rem;">Lưu ý di chuyển</span>
                    <div class="fs-3 fw-bold text-dark mt-1" style="letter-spacing: -0.02em;"><?= count($travelAlerts) ?></div>
                    <span class="badge bg-info-subtle text-info border border-info-subtle mt-1" style="font-size: 0.68rem; font-weight: 500;">
                        <i class="fa-solid fa-route me-1"></i>Cùng ngày
                    </span>
                </div>
                <div class="kpi-icon-box bg-info-subtle text-info">
                    <i class="fa-solid fa-person-walking-luggage"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Campus Travel Alerts Notice Banner -->
<?php if (!empty($travelAlerts)): ?>
<div class="card card-custom mb-3 border-start border-4 border-warning shadow-sm" style="background-color: #fffdf5;">
    <div class="card-body py-2 px-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-warning fs-6"></i>
                <span class="fw-semibold text-dark small">Giảng viên có lịch dạy tại nhiều cơ sở trong cùng một ngày:</span>
            </div>
            <span class="badge bg-warning text-dark fw-medium" style="font-size: 0.7rem;">Cần chú ý thời gian di chuyển</span>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($travelAlerts as $alert): ?>
                <div class="bg-white px-2 py-1 rounded border shadow-none small d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-dark border py-0 px-1.5" style="font-size: 0.7rem;"><?= $alert['day'] ?></span>
                    <strong class="text-dark"><?= htmlspecialchars($alert['teacher']) ?>:</strong>
                    <span class="text-primary small"><i class="fa-solid fa-route me-1"></i><?= htmlspecialchars($alert['branches']) ?></span>
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
                <span><i class="fa-solid fa-chart-pie me-1.5 text-primary"></i>Phân bổ theo Cơ sở</span>
                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.7rem;">4 Cơ sở</span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="width: 100%; max-width: 260px; height: 185px; margin: 0 auto;">
                    <canvas id="branchChart"></canvas>
                </div>
                <div class="mt-3 w-100">
                    <ul class="list-group list-group-flush small">
                        <?php 
                            $dotColors = [1 => '#2563eb', 2 => '#f97316', 3 => '#9333ea', 4 => '#16a34a'];
                        ?>
                        <?php foreach ($stats['branch_stats'] as $bs): ?>
                            <?php $dotColor = $dotColors[$bs['branch_id']] ?? '#64748b'; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0 text-nowrap">
                                <span class="text-truncate d-flex align-items-center" style="max-width: 220px;">
                                    <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background-color:<?= $dotColor ?>; margin-right:6px;"></span>
                                    <?= htmlspecialchars($bs['branch_name']) ?>
                                </span>
                                <span class="badge bg-light text-dark fw-semibold border"><?= $bs['count'] ?> lớp</span>
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
                <span><i class="fa-solid fa-chart-column me-1.5 text-primary"></i>Phân bổ theo Thứ trong Tuần</span>
                <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.7rem;">Thứ 2 - Chủ Nhật</span>
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
<div class="card card-custom mb-4 shadow-sm">
    <div class="card-header-custom py-2 px-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span><i class="fa-solid fa-list-check me-1 text-primary"></i>Lịch phân bổ gần đây</span>
            <span class="badge bg-light text-secondary border fw-medium" style="font-size: 0.7rem;"><?= count($displaySchedules) ?> bản ghi</span>
        </div>
        
        <!-- Filter Form -->
        <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 align-items-center text-nowrap">
            <input type="hidden" name="controller" value="dashboard">
            <input type="hidden" name="action" value="index">
            
            <select name="branch_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">-- Tất cả Cơ sở --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($filterBranchId === (int)$b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['branch_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="teacher_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="">-- Tất cả Giảng viên --</option>
                <?php 
                    $groupedByDept = [];
                    foreach ($teachers as $t) {
                        $d = $t['department'] ?? 'Bộ môn khác';
                        $groupedByDept[$d][] = $t;
                    }
                    foreach ($groupedByDept as $deptName => $deptTeachers):
                ?>
                    <optgroup label="<?= htmlspecialchars($deptName) ?>">
                        <?php foreach ($deptTeachers as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= ($filterTeacherId === (int)$t['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>

            <?php if ($filterBranchId || $filterTeacherId): ?>
                <a href="index.php?controller=dashboard&action=index" class="btn btn-sm btn-outline-danger py-0 px-2" title="Xóa bộ lọc">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-modern table-hover align-middle mb-0 text-nowrap">
            <thead>
                <tr>
                    <th>Học phần</th>
                    <th>Giảng viên phụ trách</th>
                    <th>Cơ sở</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết</th>
                    <th class="text-center" style="width: 70px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($displaySchedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted small">
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
                                <div class="d-flex align-items-center gap-2">
                                    <span class="teacher-avatar-sm">
                                        <?= mb_substr($s['teacher_name'] ?? 'G', 0, 1, 'UTF-8') ?>
                                    </span>
                                    <span class="text-dark fw-medium"><?= htmlspecialchars($s['teacher_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-branch-<?= ($s['branch_id'] % 4) + 1 ?>">
                                    <?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="fa-solid fa-door-open me-1 text-secondary" style="font-size: 0.65rem;"></i><?= htmlspecialchars($s['room']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border fw-medium"><?= htmlspecialchars($s['day_of_week']) ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?controller=schedule&action=edit&id=<?= $s['id'] ?>" class="btn-modern-outline py-0 px-2" title="Chỉnh sửa">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-top text-center py-2.5">
        <a href="index.php?controller=schedule&action=index" class="text-decoration-none small fw-semibold text-primary">
            Xem toàn bộ <?= $stats['total_schedules'] ?> lịch giảng dạy <i class="fa-solid fa-arrow-right ms-1"></i>
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
                backgroundColor: ['#2563eb', '#f97316', '#9333ea', '#16a34a', '#64748b'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    cornerRadius: 8,
                    padding: 8
                }
            },
            cutout: '70%'
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
                backgroundColor: '#3b82f6',
                hoverBackgroundColor: '#2563eb',
                borderRadius: 6,
                maxBarThickness: 38
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } }, 
                    grid: { color: '#f1f5f9' },
                    border: { dash: [4, 4] }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11 } }
                }
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    cornerRadius: 8,
                    padding: 8
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
