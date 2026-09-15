<?php

namespace App\Http\Controllers;

use App\Services\CheckInService;
use App\Services\ClientsService;
use App\Services\CompaniesService;
use App\Services\CompanyNewsService;
use App\Services\ContractsService;
use App\Services\EmployeesService;
use App\Services\FinancialTransactionsService;
use App\Services\OffersService;
use App\Services\PackagesService;
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
    protected CompaniesService $companiesService;
    protected PackagesService $packagesService;

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
        CompanyNewsService $companyNewsService,
        CompaniesService $companiesService,
        PackagesService $packagesService
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
        $this->companiesService = $companiesService;
        $this->packagesService = $packagesService;
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
            'displayName' => session('user') ?: session('company') ?: __('pages.dash_user'),
            'todayLabel' => now()->format('d/m/Y'),
            'isCompany' => $role === 'company',
            'isManager' => $role === 'manager',
            'isEmployee' => $role === 'employee',
            'isFinance' => $role === 'finance',
            'isHr' => $role === 'hr',
            'isAdmin' => $role === 'admin',
            'showCheckIn' => in_array($role, ['employee', 'manager', 'finance', 'hr'], true),
            'showTeamMap' => in_array($role, ['company', 'manager'], true),
            'showQuickActions' => in_array($role, ['admin', 'company', 'manager', 'employee', 'finance', 'hr'], true),
            'dashboardNews' => $companyId > 0
                ? $this->companyNewsService->getLatestForDashboard($companyId, 3)
                : collect(),
            'companyKpis' => null,
        ];

        if (in_array($role, ['employee', 'manager', 'finance', 'hr'], true)) {
            $data = array_merge($data, $this->checkInViewData());
        } else {
            $data['canCheckIn'] = false;
            $data['checkIn'] = null;
            $data['isActive'] = false;
        }

        if ($role === 'company') {
            $clientStats = $this->clientsService->getClientStatistics($companyId);
            $offerStats = $this->offersService->getOfferStatistics();

            $data['companyKpis'] = (object) [
                'employees' => $this->employeesService->countAllEmployees(),
                'projects' => $this->projectsService->countCompanyProjects($companyId),
                'clients' => (int) ($clientStats->total_count ?? 0),
                'offers' => (int) ($offerStats->offer_count ?? 0),
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
