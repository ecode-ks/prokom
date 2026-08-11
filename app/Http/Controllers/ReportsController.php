<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ReportsService;
use App\Services\EmployeesService;
use App\Services\VacationsService;
use App\Services\ContractsService;
use App\Services\WorkingHoursService;
use App\Services\ExtraHoursService;
use App\Services\SalariesService;
use App\Services\TasksService;

class ReportsController extends Controller
{
    protected ReportsService $service;
    protected EmployeesService $employeeService;
    protected VacationsService $vacationsService;
    protected ContractsService $contractsService;
    protected WorkingHoursService $workingHoursService;
    protected ExtraHoursService $extraHoursService;
    protected SalariesService $salariesService;
    protected TasksService $tasksService;

    public function __construct(ReportsService $service, EmployeesService $employeeService, VacationsService $vacationsService, ContractsService $contractsService, WorkingHoursService $workingHoursService, ExtraHoursService $extraHoursService, SalariesService $salariesService, TasksService $tasksService)
    {
        $this->service = $service;
        $this->employeeService = $employeeService;
        $this->vacationsService = $vacationsService;
        $this->contractsService = $contractsService;
        $this->workingHoursService = $workingHoursService;
        $this->extraHoursService = $extraHoursService;
        $this->salariesService = $salariesService;
        $this->tasksService = $tasksService;
    }
    //---------------
    public function read()
    {
        $page = 'Employee Report';
        $employees = $this->employeeService->getAllEmployees();
        return view('management.employee-report')
            ->with('page', $page)
            ->with('employees', $employees);
    }
    //---------------
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'employee_id' => 'required|integer|exists:employees,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('employee.report')->with('error','Të gjitha fushat duhet të plotësohen sipas rregullave.')->withInput();
        } else {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $employeeId = $request->input('employee_id');
            $reportLimit = 10;

            try {
                $contractEntries = $this->contractsService->getEmployeeReportEntries($fromDate, $toDate, $employeeId, $reportLimit, 'contracts_cursor');
                $latestContract = collect($contractEntries->items())->first();
                $extraHourPrice = (float) ($latestContract->extra_hours_price ?? 0);
                $weekendHourPrice = (float) ($latestContract->weekend_hours_price ?? 0);
                $extraHourEntries = $this->extraHoursService->getEmployeeReportEntries($fromDate, $toDate, $employeeId, 'Orët jashtë orarit', $extraHourPrice, $reportLimit, 'extra_hours_cursor');
                $weekendHourEntries = $this->extraHoursService->getEmployeeReportEntries($fromDate, $toDate, $employeeId, 'Orët në vikend', $weekendHourPrice, $reportLimit, 'extra_weekend_hours_cursor');

                $reportData = [
                    'vacations' => $this->vacationsService->generateEmployeeReport($fromDate, $toDate, $employeeId),
                    'vacation_entries' => $this->vacationsService->getEmployeeReportEntries($fromDate, $toDate, $employeeId, $reportLimit, 'vacations_cursor'),
                    'contracts' => $this->contractsService->generateEmployeeReport($fromDate, $toDate, $employeeId),
                    'contract_entries' => $contractEntries,
                    'working_hours' => $this->workingHoursService->generateEmployeeReport($fromDate, $toDate, $employeeId),
                    'working_hour_entries' => $this->workingHoursService->getEmployeeReportEntries($fromDate, $toDate, $employeeId, $reportLimit, 'working_hours_cursor'),
                    'extra_hours' => $this->extraHoursService->generateEmployeeReport($fromDate, $toDate, $employeeId),
                    'extra_hour_entries' => $extraHourEntries,
                    'extra_hours_value_eur' => round((float) collect($extraHourEntries->items())->sum('value_eur'), 2),
                    'extra_weekend_hours' => $this->extraHoursService->generateExtraWeekendHoursReport($fromDate, $toDate, $employeeId),
                    'extra_weekend_hour_entries' => $weekendHourEntries,
                    'extra_weekend_hours_value_eur' => round((float) collect($weekendHourEntries->items())->sum('value_eur'), 2),
                    'salary' => $this->salariesService->generateEmployeeSalaryReport($fromDate, $toDate, $employeeId),
                    'finished_tasks' => $this->tasksService->generateEmployeeFinishedTasksReport($fromDate, $toDate, $employeeId),
                    'tasks' => $this->tasksService->getEmployeeTaskCompletionReport($fromDate, $toDate, $employeeId),
                    'task_entries' => $this->tasksService->getEmployeeTaskEntriesReport($fromDate, $toDate, $employeeId, $reportLimit, 'tasks_cursor'),
                ];

                return view('management.employee-report')
                    ->with('page', 'Employee Report')
                    ->with('reportData', $reportData)
                    ->with('employees', $this->employeeService->getAllEmployees())
                    ->with('fromDate', $fromDate)
                    ->with('toDate', $toDate)
                    ->with('employeeId', $employeeId);
            } catch (\Exception $e) {
                return redirect()->route('employee.report')->with('error', 'Ndodhi një problem gjatë gjenerimit të raportit. Ju lutem provoni përsëri. '.$e->getMessage())->withInput();
            }
        }
    }
}
