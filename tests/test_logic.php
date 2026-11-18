<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Branch;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

echo "--- Testing Models ---\n";

$branchModel = new Branch();
$branches = $branchModel->all();
echo "Branches: " . count($branches) . "\n";

$teacherModel = new Teacher();
$teachers = $teacherModel->all();
echo "Teachers: " . count($teachers) . "\n";

$scheduleModel = new Schedule();
$stats = $scheduleModel->getStatistics();
echo "Total Schedules: " . $stats['total_schedules'] . "\n";

echo "\n--- Testing Smart Recommendation Algorithm ---\n";
$recom = $scheduleModel->getSmartRecommendations('Lập trình mã nguồn mở PHP', 'Thứ 2', 'Sáng (Tiết 1-3)', 1);
echo "Top Recommended for 'Lập trình mã nguồn mở PHP': " . $recom[0]['full_name'] . " (" . $recom[0]['faculty'] . ")\n";

$recomFood = $scheduleModel->getSmartRecommendations('Hóa sinh thực phẩm', 'Thứ 3', 'Sáng (Tiết 1-3)', 1);
echo "Top Recommended for 'Hóa sinh thực phẩm': " . $recomFood[0]['full_name'] . " (" . $recomFood[0]['faculty'] . " - " . $recomFood[0]['department'] . ")\n";

$recomBiz = $scheduleModel->getSmartRecommendations('Marketing căn bản', 'Thứ 4', 'Chiều (Tiết 7-9)', 1);
echo "Top Recommended for 'Marketing căn bản': " . $recomBiz[0]['full_name'] . " (" . $recomBiz[0]['faculty'] . " - " . $recomBiz[0]['department'] . ")\n";

echo "\n--- Testing Conflict Checking ---\n";
// Let's get the first existing schedule and test conflict detection
$firstSchedule = $scheduleModel->all()[0];
$conflicts = $scheduleModel->checkConflict(
    $firstSchedule['teacher_id'],
    $firstSchedule['branch_id'],
    $firstSchedule['room'],
    $firstSchedule['day_of_week'],
    $firstSchedule['shift_or_periods']
);
echo "Detected conflicts for identical slot (expected 2: teacher and room): " . count($conflicts) . "\n";
foreach ($conflicts as $c) {
    echo " - [{$c['type']}] {$c['title']}: {$c['message']}\n";
}

echo "\n--- Testing Travel Warning ---\n";
// Find if any teacher has schedules in multiple branches on the same day
$travelTeacher = null;
foreach ($teachers as $t) {
    foreach (Schedule::DAYS as $d) {
        $warning = $scheduleModel->checkTravelWarning($t['id'], $d, 'Chiều (Tiết 7-9)', 1);
        if ($warning) {
            echo "Found travel warning for {$t['full_name']} on {$d}:\n";
            echo " Target: {$warning['target_branch']}\n";
            echo " Notice: {$warning['message']}\n";
            break 2;
        }
    }
}

echo "\nAll Tests Completed Successfully!\n";
