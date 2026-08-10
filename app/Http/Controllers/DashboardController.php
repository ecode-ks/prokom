<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use App\Services\ClientsService;
use App\Services\ContractsService;
use App\Services\EmployeesService;
use App\Services\FinancialTransactionsService;
use App\Services\OffersService;
use App\Services\ProjectReportsService;
use App\Services\ProjectsService;
use App\Services\TasksService;
use App\Services\VacationsService;

class DashboardController extends Controller
{
    protected CheckInService $checkinService;
    protected TasksService $tasksService;
    protected ProjectsService $projectsService;
    protected ProjectReportsService $projectReportsService;
    protected FinancialTransactionsService $financialTransactionsService;
    protected ClientsService $clientsService;
    protected OffersService $offersService;
    protected EmployeesService $employeesService;
    protected VacationsService $vacationsService;
    protected ContractsService $contractsService;

    public function __construct(
        CheckInService $checkinService,
        TasksService $tasksService,
        ProjectsService $projectsService,
        ProjectReportsService $projectReportsService,
        FinancialTransactionsService $financialTransactionsService,
        ClientsService $clientsService,
        OffersService $offersService,
        EmployeesService $employeesService,
        VacationsService $vacationsService,
        ContractsService $contractsService
    ) {
        $this->checkinService = $checkinService;
        $this->tasksService = $tasksService;
        $this->projectsService = $projectsService;
        $this->projectReportsService = $projectReportsService;
        $this->financialTransactionsService = $financialTransactionsService;
        $this->clientsService = $clientsService;
        $this->offersService = $offersService;
        $this->employeesService = $employeesService;
        $this->vacationsService = $vacationsService;
        $this->contractsService = $contractsService;
    }
    //---------------
    public function dashboard()
    {
        $role = (string) (session('role') ?? '');

        return match ($role) {
            'company' => $this->companyDashboard(),
            'manager' => $this->managerDashboard(),
            'employee' => $this->employeeDashboard(),
            'finance' => $this->financeDashboard(),
            'hr' => $this->hrDashboard(),
            default => $this->employeeDashboard(),
        };
    }
    //---------------
    private function companyDashboard()
    {
        return view('management.dashboard-company')
            ->with('page', 'Dashboard')
            ->with($this->checkInViewData());
    }
    //---------------
    private function managerDashboard()
    {
        $employeeId = (int) (session('employee_id') ?? 0);
        $companyId = (int) (session('company_id') ?? 0);

        $taskCounts = $this->tasksService->getManagerTaskCounts($employeeId, $companyId);
        $managerKpis = (object) [
            'active_projects' => $this->projectsService->countManagerActiveProjects($employeeId, $companyId),
            'open_tasks' => $taskCounts->open_tasks,
            'overdue_tasks' => $taskCounts->overdue_tasks,
            'due_soon_tasks' => $taskCounts->due_soon_tasks,
        ];

        return view('management.dashboard-manager')
            ->with('page', 'Dashboard')
            ->with($this->checkInViewData())
            ->with('managerKpis', $managerKpis)
            ->with('managerProjects', $this->projectsService->getManagerUpcomingProjects($employeeId, $companyId, 5))
            ->with('managerTeamTasks', $this->tasksService->getManagerTeamTasks($employeeId, $companyId, 5))
            ->with('managerPendingReports', $this->projectReportsService->getManagerPendingReports($employeeId, $companyId, 4))
            ->with('managerTeamPresence', $this->tasksService->getManagerTeamPresence($employeeId, $companyId));
    }
    //---------------
    private function employeeDashboard()
    {
        return view('management.dashboard-employee')
            ->with('page', 'Dashboard')
            ->with($this->checkInViewData());
    }
    //---------------
    private function financeDashboard()
    {
        $companyId = (int) (session('company_id') ?? 0);
        $fromDate = now()->startOfMonth()->toDateString();
        $toDate = now()->toDateString();

        $report = $this->financialTransactionsService->getFinancialReport($fromDate, $toDate, 1, $companyId);
        $summary = $report['summary'] ?? [];
        $clientStats = $this->clientsService->getClientStatistics($companyId);
        $offerStats = $this->offersService->getOfferStatistics();

        $financeKpis = (object) [
            'balance' => (float) ($summary['balance'] ?? 0),
            'revenues' => (float) ($summary['revenues'] ?? 0),
            'expenses' => (float) ($summary['expenses'] ?? 0),
            'clients' => (int) ($clientStats->total_count ?? 0),
            'pending_offers' => (int) ($offerStats->pending_count ?? 0),
        ];

        return view('management.dashboard-finance')
            ->with('page', 'Dashboard')
            ->with($this->checkInViewData())
            ->with('financeKpis', $financeKpis);
    }
    //---------------
    private function hrDashboard()
    {
        $companyId = (int) (session('company_id') ?? 0);
        $vacationStats = $this->vacationsService->getVacationStatistics('', $companyId);
        $contractStats = $this->contractsService->getContractStatistics('', $companyId);

        $hrKpis = (object) [
            'employees' => $this->employeesService->countAllEmployees(),
            'active_employees' => $this->employeesService->countActiveEmployees(),
            'pending_vacations' => (int) ($vacationStats->pending_hr_count ?? 0),
            'active_contracts' => (int) ($contractStats->active_count ?? 0),
        ];

        return view('management.dashboard-hr')
            ->with('page', 'Dashboard')
            ->with($this->checkInViewData())
            ->with('hrKpis', $hrKpis);
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
