<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use App\Services\ClientsService;
use App\Services\CompanyNewsService;
use App\Services\ContractsService;
use App\Services\EmployeesService;
use App\Services\FinancialTransactionsService;
use App\Services\OffersService;
use App\Services\ProjectReportsService;
use App\Services\ProjectsService;
use App\Services\TaskReportsService;
use App\Services\TasksService;
use App\Services\VacationsService;

class DashboardController extends Controller
{
    protected CheckInService $checkinService;
    protected TasksService $tasksService;
    protected ProjectsService $projectsService;
    protected ProjectReportsService $projectReportsService;
    protected TaskReportsService $taskReportsService;
    protected FinancialTransactionsService $financialTransactionsService;
    protected ClientsService $clientsService;
    protected OffersService $offersService;
    protected EmployeesService $employeesService;
    protected VacationsService $vacationsService;
    protected ContractsService $contractsService;
    protected CompanyNewsService $companyNewsService;

    public function __construct(
        CheckInService $checkinService,
        TasksService $tasksService,
        ProjectsService $projectsService,
        ProjectReportsService $projectReportsService,
        TaskReportsService $taskReportsService,
        FinancialTransactionsService $financialTransactionsService,
        ClientsService $clientsService,
        OffersService $offersService,
        EmployeesService $employeesService,
        VacationsService $vacationsService,
        ContractsService $contractsService,
        CompanyNewsService $companyNewsService
    ) {
        $this->checkinService = $checkinService;
        $this->tasksService = $tasksService;
        $this->projectsService = $projectsService;
        $this->projectReportsService = $projectReportsService;
        $this->taskReportsService = $taskReportsService;
        $this->financialTransactionsService = $financialTransactionsService;
        $this->clientsService = $clientsService;
        $this->offersService = $offersService;
        $this->employeesService = $employeesService;
        $this->vacationsService = $vacationsService;
        $this->contractsService = $contractsService;
        $this->companyNewsService = $companyNewsService;
    }
    //---------------
    public function dashboard()
    {
        $role = (string) (session('role') ?? '');
        $companyId = (int) (session('company_id') ?? 0);
        $employeeId = (int) (session('employee_id') ?? 0);

        $data = [
            'page' => 'Dashboard',
            'role' => $role,
            'displayName' => session('user') ?: session('company') ?: 'Përdorues',
            'todayLabel' => now()->format('d/m/Y'),
            'isCompany' => $role === 'company',
            'isManager' => $role === 'manager',
            'isEmployee' => $role === 'employee',
            'isFinance' => $role === 'finance',
            'isHr' => $role === 'hr',
            'showCheckIn' => in_array($role, ['employee', 'manager', 'finance', 'hr'], true),
            'showTeamMap' => $role === 'company',
            'showQuickActions' => in_array($role, ['company', 'manager', 'finance', 'hr'], true),
            'showLists' => in_array($role, ['company', 'manager', 'employee'], true),
            'dashboardNews' => $companyId > 0
                ? $this->companyNewsService->getLatestForDashboard($companyId, 5)
                : collect(),
            'companyKpis' => null,
            'managerKpis' => null,
            'employeeKpis' => null,
            'financeKpis' => null,
            'hrKpis' => null,
            'companyProjects' => collect(),
            'companyLatestTransactions' => collect(),
            'companyPendingVacations' => collect(),
            'companyLatestOffers' => collect(),
            'companyExpiringContracts' => collect(),
            'managerProjects' => collect(),
            'managerTeamTasks' => collect(),
            'managerPendingReports' => collect(),
            'managerTeamPresence' => null,
            'employeeTasks' => collect(),
        ];

        if (in_array($role, ['employee', 'manager', 'finance', 'hr'], true)) {
            $data = array_merge($data, $this->checkInViewData());
        } else {
            $data['canCheckIn'] = false;
            $data['checkIn'] = null;
            $data['isActive'] = false;
        }

        if ($role === 'company') {
            $projectStats = $this->projectsService->getProjectStatistics([], $companyId);
            $fromDate = now()->startOfMonth()->toDateString();
            $toDate = now()->toDateString();
            $report = $this->financialTransactionsService->getFinancialReport($fromDate, $toDate, 1, $companyId);
            $summary = $report['summary'] ?? [];
            $offerStats = $this->offersService->getOfferStatistics();
            $vacationStats = $this->vacationsService->getVacationStatistics('', $companyId);
            $contractStats = $this->contractsService->getContractStatistics('', $companyId);
            $presence = $this->checkinService->getCompanyPresenceToday($companyId);
            $pendingVacations = (int) ($vacationStats->pending_manager_count ?? 0)
                + (int) ($vacationStats->pending_hr_count ?? 0);

            $data['companyKpis'] = (object) [
                'projects' => $this->projectsService->countCompanyProjects($companyId),
                'active_projects' => (int) ($projectStats->active_count ?? 0),
                'employees' => $this->employeesService->countAllEmployees(),
                'active_employees' => $this->employeesService->countActiveEmployees(),
                'balance' => (float) ($summary['balance'] ?? 0),
                'revenues' => (float) ($summary['revenues'] ?? 0),
                'expenses' => (float) ($summary['expenses'] ?? 0),
                'pending_offers' => (int) ($offerStats->pending_count ?? 0),
                'checked_in_today' => (int) ($presence->checked_in_today ?? 0),
                'currently_working' => (int) ($presence->currently_working ?? 0),
                'pending_vacations' => $pendingVacations,
                'expiring_contracts' => (int) ($contractStats->expiring_count ?? 0),
            ];
            $data['companyProjects'] = $this->projectsService->getCompanyUpcomingProjects($companyId, 5);
            $data['companyLatestTransactions'] = $this->financialTransactionsService->getLatestForDashboard($companyId, 5);
            $data['companyLatestOffers'] = $this->offersService->getLatestForDashboard(5);
            $data['companyPendingVacations'] = $this->vacationsService->getPendingForDashboard($companyId, 5);
            $data['companyExpiringContracts'] = $this->contractsService->getExpiringForDashboard($companyId, 5);
        } elseif ($role === 'manager') {
            $taskCounts = $this->tasksService->getManagerTaskCounts($employeeId, $companyId);
            $data['managerKpis'] = (object) [
                'active_projects' => $this->projectsService->countManagerActiveProjects($employeeId, $companyId),
                'open_tasks' => $taskCounts->open_tasks,
                'overdue_tasks' => $taskCounts->overdue_tasks,
                'due_soon_tasks' => $taskCounts->due_soon_tasks,
            ];
            $data['managerProjects'] = $this->projectsService->getManagerUpcomingProjects($employeeId, $companyId, 5);
            $data['managerTeamTasks'] = $this->tasksService->getManagerTeamTasks($employeeId, $companyId, 5);
            $data['managerPendingReports'] = $this->projectReportsService->getManagerPendingReports($employeeId, $companyId, 4);
            $data['managerTeamPresence'] = $this->tasksService->getManagerTeamPresence($employeeId, $companyId);
        } elseif ($role === 'employee') {
            $taskCounts = $this->tasksService->getEmployeeTaskCounts($employeeId, $companyId);
            $reportStats = $this->taskReportsService->getReportStatistics($companyId, 'employee', $employeeId);
            $data['employeeKpis'] = (object) [
                'my_tasks' => $taskCounts->my_tasks,
                'working_tasks' => $taskCounts->working_tasks,
                'review_tasks' => $taskCounts->review_tasks,
                'my_reports' => (int) (($reportStats->active_count ?? 0)
                    + ($reportStats->under_review_count ?? 0)
                    + ($reportStats->resolved_count ?? 0)
                    + ($reportStats->closed_count ?? 0)),
            ];
            $data['employeeTasks'] = $this->tasksService->getMyOpenTasks($employeeId, $companyId, 5);
        } elseif ($role === 'finance') {
            $fromDate = now()->startOfMonth()->toDateString();
            $toDate = now()->toDateString();
            $report = $this->financialTransactionsService->getFinancialReport($fromDate, $toDate, 1, $companyId);
            $summary = $report['summary'] ?? [];
            $clientStats = $this->clientsService->getClientStatistics($companyId);
            $offerStats = $this->offersService->getOfferStatistics();

            $data['financeKpis'] = (object) [
                'balance' => (float) ($summary['balance'] ?? 0),
                'revenues' => (float) ($summary['revenues'] ?? 0),
                'expenses' => (float) ($summary['expenses'] ?? 0),
                'clients' => (int) ($clientStats->total_count ?? 0),
                'pending_offers' => (int) ($offerStats->pending_count ?? 0),
            ];
        } elseif ($role === 'hr') {
            $vacationStats = $this->vacationsService->getVacationStatistics('', $companyId);
            $contractStats = $this->contractsService->getContractStatistics('', $companyId);

            $data['hrKpis'] = (object) [
                'employees' => $this->employeesService->countAllEmployees(),
                'active_employees' => $this->employeesService->countActiveEmployees(),
                'pending_vacations' => (int) ($vacationStats->pending_hr_count ?? 0),
                'active_contracts' => (int) ($contractStats->active_count ?? 0),
            ];
        }

        return view('management.dashboard', $data);
    }
    //---------------
    private function checkInViewData(): array
    {
        $canCheckIn = $this->checkinService->canCheckIn();
        $checkIn = $canCheckIn ? $this->checkinService->getTodayCheckIn() : null;

        return [
            'canCheckIn' => $canCheckIn,
            'checkIn' => $checkIn,
            'isActive' => $checkIn && empty($checkIn->check_out_time),
        ];
    }
}
