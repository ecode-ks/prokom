<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use App\Services\ProjectReportsService;
use App\Services\ProjectsService;
use App\Services\TasksService;

class DashboardController extends Controller
{
    protected CheckInService $checkinService;
    protected TasksService $tasksService;
    protected ProjectsService $projectsService;
    protected ProjectReportsService $projectReportsService;

    public function __construct(
        CheckInService $checkinService,
        TasksService $tasksService,
        ProjectsService $projectsService,
        ProjectReportsService $projectReportsService
    ) {
        $this->checkinService = $checkinService;
        $this->tasksService = $tasksService;
        $this->projectsService = $projectsService;
        $this->projectReportsService = $projectReportsService;
    }
    //---------------
    public function dashboard()
    {
        $role = (string) (session('role') ?? '');
        $employeeId = (int) (session('employee_id') ?? 0);
        $companyId = (int) (session('company_id') ?? 0);

        $canCheckIn = $this->checkinService->canCheckIn();
        $checkIn = $canCheckIn ? $this->checkinService->getTodayCheckIn() : null;
        $isActive = $checkIn && empty($checkIn->check_out_time);

        $isManager = $role === 'manager' && $employeeId > 0;
        $managerKpis = null;
        $managerProjects = collect();
        $managerTeamTasks = collect();
        $managerPendingReports = collect();
        $managerTeamPresence = null;

        if ($isManager) {
            $taskCounts = $this->tasksService->getManagerTaskCounts($employeeId, $companyId);
            $managerKpis = (object) [
                'active_projects' => $this->projectsService->countManagerActiveProjects($employeeId, $companyId),
                'open_tasks' => $taskCounts->open_tasks,
                'overdue_tasks' => $taskCounts->overdue_tasks,
                'due_soon_tasks' => $taskCounts->due_soon_tasks,
            ];
            $managerProjects = $this->projectsService->getManagerUpcomingProjects($employeeId, $companyId, 5);
            $managerTeamTasks = $this->tasksService->getManagerTeamTasks($employeeId, $companyId, 5);
            $managerPendingReports = $this->projectReportsService->getManagerPendingReports($employeeId, $companyId, 4);
            $managerTeamPresence = $this->tasksService->getManagerTeamPresence($employeeId, $companyId);
        }

        return view('management.dashboard')
            ->with('page', 'Dashboard')
            ->with('canCheckIn', $canCheckIn)
            ->with('checkIn', $checkIn)
            ->with('isActive', $isActive)
            ->with('isManager', $isManager)
            ->with('managerKpis', $managerKpis)
            ->with('managerProjects', $managerProjects)
            ->with('managerTeamTasks', $managerTeamTasks)
            ->with('managerPendingReports', $managerPendingReports)
            ->with('managerTeamPresence', $managerTeamPresence);
    }
}
