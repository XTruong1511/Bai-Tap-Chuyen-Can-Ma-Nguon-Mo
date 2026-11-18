<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Schedule;

class DashboardController
{
    private Schedule $scheduleModel;
    private Teacher $teacherModel;
    private Branch $branchModel;

    public function __construct()
    {
        Auth::requireLogin();
        $this->scheduleModel = new Schedule();
        $this->teacherModel = new Teacher();
        $this->branchModel = new Branch();
    }

    public function index(): void
    {
        $stats = $this->scheduleModel->getStatistics();
        $branches = $this->branchModel->all();
        $teachers = $this->teacherModel->all();

        // Filter options for dashboard widget
        $filterBranchId = isset($_GET['branch_id']) && $_GET['branch_id'] !== '' ? (int)$_GET['branch_id'] : null;
        $filterTeacherId = isset($_GET['teacher_id']) && $_GET['teacher_id'] !== '' ? (int)$_GET['teacher_id'] : null;

        $recentSchedules = $this->scheduleModel->all([
            'branch_id' => $filterBranchId,
            'teacher_id' => $filterTeacherId,
        ]);

        // Limit to 8 recent/highlighted items for overview table
        $displaySchedules = array_slice($recentSchedules, 0, 8);

        // Analyze multi-branch travel alerts in the system
        $travelAlerts = [];
        $days = Schedule::DAYS;
        foreach ($teachers as $t) {
            foreach ($days as $day) {
                $teacherDaySchedules = array_filter($recentSchedules, function($s) use ($t, $day) {
                    return $s['teacher_id'] == $t['id'] && $s['day_of_week'] == $day;
                });
                $branchIdsInDay = array_unique(array_column($teacherDaySchedules, 'branch_id'));
                if (count($branchIdsInDay) > 1) {
                    $branchNames = array_unique(array_column($teacherDaySchedules, 'branch_name'));
                    $travelAlerts[] = [
                        'teacher' => $t['full_name'],
                        'day' => $day,
                        'branches' => implode(' ➔ ', $branchNames),
                        'count' => count($teacherDaySchedules)
                    ];
                }
            }
        }

        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
