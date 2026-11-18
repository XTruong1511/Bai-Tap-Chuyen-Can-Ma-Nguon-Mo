<?php
$pageTitle = 'Bảng điều khiển Tổng quan';
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Header Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-gauge-high text-primary me-2"></i>Tổng quan Hệ thống Phân lịch
        </h3>
        <p class="text-muted mb-0 small">Theo dõi phân bổ thời khóa biểu, ma trận lịch trống và tối ưu di chuyển đa cơ sở HUIT.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?controller=schedule&action=matrix" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-table-cells me-1"></i> Ma trận TKB
        </a>
        <a href="index.php?controller=schedule&action=create" class="btn btn-huit-orange btn-sm">
            <i class="fa-solid fa-calendar-plus me-1"></i> Thêm lịch mới
        </a>
    </div>
</div>

<!-- Stats Counter Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Tổng lịch phân bổ</div>
                    <div class="fs-3 fw-bold text-dark"><?= $stats['total_schedules'] ?></div>
                    <div class="text-success small"><i class="fa-solid fa-check me-1"></i>Đang áp dụng</div>
                </div>
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                    <i class="fa-solid fa-calendar-days fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Giảng viên tham gia</div>
                    <div class="fs-3 fw-bold text-dark"><?= $stats['total_teachers'] ?></div>
                    <div class="text-muted small">Cơ hữu & thỉnh giảng</div>
                </div>
                <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                    <i class="fa-solid fa-chalkboard-user fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Cơ sở đào tạo</div>
                    <div class="fs-3 fw-bold text-dark"><?= $stats['total_branches'] ?></div>
                    <div class="text-muted small">Khu vực Tân Phú & lân cận</div>
                </div>
                <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                    <i class="fa-solid fa-building-columns fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold">Cảnh báo di chuyển</div>
                    <div class="fs-3 fw-bold text-dark"><?= count($travelAlerts) ?></div>
                    <div class="text-warning small"><i class="fa-solid fa-route me-1"></i>Cần lưu ý</div>
                </div>
                <div class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                    <i class="fa-solid fa-person-walking-luggage fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Campus Travel Alerts Notice -->
<?php if (!empty($travelAlerts)): ?>
<div class="card card-custom mb-4 border-warning bg-warning bg-opacity-10">
    <div class="card-body py-3">
        <div class="d-flex align-items-start gap-3">
            <div class="text-warning fs-3 mt-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-dark mb-1">
                    Phân tích Tối ưu Di chuyển Đa Cơ sở (Thông minh)
                </h6>
                <p class="small text-muted mb-2">
                    Hệ thống tự động phát hiện các giảng viên có lịch giảng dạy ở từ <strong>2 cơ sở khác nhau trở lên trong cùng 1 ngày</strong>. Hãy lưu ý thời gian nghỉ chuyển ca để giảng viên kịp di chuyển:
                </p>
                <div class="row g-2">
                    <?php foreach (array_slice($travelAlerts, 0, 4) as $alert): ?>
                        <div class="col-md-6">
                            <div class="bg-white p-2 rounded border small">
                                <span class="badge bg-warning text-dark me-1"><?= $alert['day'] ?></span>
                                <strong><?= htmlspecialchars($alert['teacher']) ?>:</strong>
                                <span class="text-primary"><?= htmlspecialchars($alert['branches']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Branch Distribution -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Phân bổ Lịch theo Cơ sở</span>
                <span class="badge text-bg-light small">Học kỳ 1</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div style="width: 100%; max-width: 320px; height: 260px;">
                    <canvas id="branchChart"></canvas>
                </div>
                <div class="mt-3 w-100">
                    <ul class="list-group list-group-flush small">
                        <?php foreach ($stats['branch_stats'] as $bs): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                <span class="text-truncate" style="max-width: 220px;">
                                    <i class="fa-solid fa-circle me-1 small text-primary"></i> <?= htmlspecialchars($bs['branch_name']) ?>
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
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-chart-column me-2 text-primary"></i>Phân bổ Lịch theo Thứ trong Tuần</span>
                <span class="badge text-bg-light small">Tuần học chuẩn</span>
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="dayChart"></canvas>
                </div>
                <div class="alert alert-light border small mt-3 mb-0 d-flex align-items-center">
                    <i class="fa-solid fa-lightbulb text-warning me-2 fs-5"></i>
                    <div>
                        <strong>Gợi ý xếp lịch:</strong> Các ngày có mật độ tiết cao nên hạn chế xếp thêm môn mới để tránh quá tải phòng học và giảm thời gian di chuyển giữa các cơ sở.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter & Recent Schedule Allocation -->
<div class="card card-custom mb-4">
    <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <span><i class="fa-solid fa-list-check me-2 text-primary"></i>Lịch Giảng Dạy Đã Phân Bổ (Mới nhất)</span>
        
        <!-- Filter Form -->
        <form method="GET" action="index.php" class="d-flex flex-wrap gap-2 align-items-center">
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
                <?php foreach ($teachers as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($filterTeacherId === (int)$t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($filterBranchId || $filterTeacherId): ?>
                <a href="index.php?controller=dashboard&action=index" class="btn btn-outline-danger btn-sm">
                    <i class="fa-solid fa-xmark"></i> Xóa lọc
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th>Học phần</th>
                    <th>Giảng viên</th>
                    <th>Cơ sở đào tạo</th>
                    <th>Phòng</th>
                    <th>Thứ</th>
                    <th>Ca / Tiết học</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($displaySchedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-regular fa-folder-open fa-2x mb-2 d-block"></i>
                            Không có lịch giảng dạy nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($displaySchedules as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($s['subject_name']) ?></div>
                                <span class="badge text-bg-light border text-muted small"><?= htmlspecialchars($s['semester']) ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold text-primary"><?= htmlspecialchars($s['teacher_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($s['teacher_expertise']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-branch-<?= ($s['branch_id'] % 4) + 1 ?> p-2">
                                    <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($s['branch_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark px-2 py-1"><?= htmlspecialchars($s['room']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($s['day_of_week']) ?></span>
                            </td>
                            <td>
                                <span class="text-nowrap small fw-medium"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
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

    <div class="card-footer bg-white border-top text-center py-3">
        <a href="index.php?controller=schedule&action=index" class="btn btn-outline-primary btn-sm fw-semibold">
            Xem toàn bộ <?= $stats['total_schedules'] ?> lịch giảng dạy <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<!-- Chart.js Setup Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Branch Doughnut Chart
    const branchCtx = document.getElementById('branchChart').getContext('2d');
    const branchData = <?= json_encode($stats['branch_stats']) ?>;
    
    new Chart(branchCtx, {
        type: 'doughnut',
        data: {
            labels: branchData.map(b => b.branch_name),
            datasets: [{
                data: branchData.map(b => b.count),
                backgroundColor: [
                    '#0b3c61',
                    '#f37021',
                    '#7c3aed',
                    '#059669',
                    '#64748b'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '65%'
        }
    });

    // Day Bar Chart
    const dayCtx = document.getElementById('dayChart').getContext('2d');
    const dayData = <?= json_encode($stats['day_stats']) ?>;

    new Chart(dayCtx, {
        type: 'bar',
        data: {
            labels: dayData.map(d => d.day_of_week),
            datasets: [{
                label: 'Số ca học phân bổ',
                data: dayData.map(d => d.count),
                backgroundColor: 'rgba(11, 60, 97, 0.85)',
                hoverBackgroundColor: '#f37021',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: '#f1f5f9'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
