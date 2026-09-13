<?php

namespace App\Services\Api;

use App\Repositories\ContractsRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\ExtraHoursRepository;
use App\Repositories\TasksRepository;
use App\Repositories\VacationsRepository;
use App\Repositories\WorkingHoursRepository;

class EmployeeReportApiService
{
    public function __construct(
        protected EmployeesRepository $employees,
        protected VacationsRepository $vacations,
        protected ContractsRepository $contracts,
        protected WorkingHoursRepository $workingHours,
        protected ExtraHoursRepository $extraHours,
        protected TasksRepository $tasks
    ) {
    }

    public function getReport(string $fromDate, string $toDate, int $companyId, int $employeeId): ?array
    {
        $employee = $this->employees->findEmployee($employeeId, $companyId);
        if (!$employee) {
            return null;
        }

        $contractEntries = $this->contracts->getEmployeeContractEntries(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate,
            10,
            'contracts_cursor'
        )->through(function ($entry) {
            unset($entry->salary);
            return $entry;
        });

        $latestContract = collect($contractEntries->items())->first();
        $extraHoursPrice = (float) ($latestContract->extra_hours_price ?? 0);
        $weekendHoursPrice = (float) ($latestContract->weekend_hours_price ?? 0);

        $extraHours = $this->extraHours->getEmployeeExtraHoursStatistics(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate,
            'Orët jashtë orarit'
        );
        $extraWeekendHours = $this->extraHours->getEmployeeExtraHoursStatistics(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate,
            'Orët në vikend'
        );

        $extraHourEntries = $this->extraHours->getEmployeeExtraHoursEntries(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate,
            'Orët jashtë orarit',
            10,
            'extra_hours_cursor'
        )->through(function ($entry) use ($extraHoursPrice) {
            $hours = (float) ($entry->hours_decimal ?? 0);
            $entry->price_per_hour = $extraHoursPrice;
            $entry->value_eur = round($hours * $extraHoursPrice, 2);
            return $entry;
        });

        $extraWeekendHourEntries = $this->extraHours->getEmployeeExtraHoursEntries(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate,
            'Orët në vikend',
            10,
            'extra_weekend_hours_cursor'
        )->through(function ($entry) use ($weekendHoursPrice) {
            $hours = (float) ($entry->hours_decimal ?? 0);
            $entry->price_per_hour = $weekendHoursPrice;
            $entry->value_eur = round($hours * $weekendHoursPrice, 2);
            return $entry;
        });

        $vacations = $this->vacations->getEmployeeVacationStatistics(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate
        );
        $workingHours = $this->workingHours->getEmployeeWorkingHoursStatistics(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate
        );
        $tasks = $this->tasks->getEmployeeTaskCompletionStatistics(
            $companyId,
            $employeeId,
            $fromDate,
            $toDate
        );

        $vacationTotal = (int) ($vacations->total_count ?? 0);
        $completedTasks = (int) ($tasks->completed_count ?? 0);
        $totalTasks = (int) ($tasks->total_count ?? 0);

        return [
            'employee' => [
                'id' => (int) $employee->id,
                'name' => $employee->employee,
                'username' => $employee->username,
                'role' => $employee->role,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'status' => $employee->status,
            ],
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            'working_hours' => [
                'statistics' => $workingHours,
                'entries' => $this->workingHours->getEmployeeWorkingHoursEntries(
                    $companyId,
                    $employeeId,
                    $fromDate,
                    $toDate,
                    10,
                    'working_hours_cursor'
                ),
            ],
            'vacations' => [
                'statistics' => $vacations,
                'approval_rate' => $vacationTotal > 0
                    ? round(((int) ($vacations->approved_count ?? 0) / $vacationTotal) * 100, 1)
                    : 0,
                'entries' => $this->vacations->getEmployeeVacationEntries(
                    $companyId,
                    $employeeId,
                    $fromDate,
                    $toDate,
                    10,
                    'vacations_cursor'
                ),
            ],
            'contracts' => [
                'statistics' => $this->contracts->getEmployeeContractStatistics(
                    $companyId,
                    $employeeId,
                    $fromDate,
                    $toDate
                ),
                'entries' => $contractEntries,
            ],
            'extra_hours' => [
                'statistics' => $extraHours,
                'value_eur' => round((float) collect($extraHourEntries->items())->sum('value_eur'), 2),
                'entries' => $extraHourEntries,
            ],
            'extra_weekend_hours' => [
                'statistics' => $extraWeekendHours,
                'value_eur' => round((float) collect($extraWeekendHourEntries->items())->sum('value_eur'), 2),
                'entries' => $extraWeekendHourEntries,
            ],
            'tasks' => [
                'statistics' => $tasks,
                'completion_rate' => $totalTasks > 0
                    ? round(($completedTasks / $totalTasks) * 100, 1)
                    : 0,
                'entries' => $this->tasks->getEmployeeTaskEntries(
                    $companyId,
                    $employeeId,
                    $fromDate,
                    $toDate,
                    10,
                    'tasks_cursor'
                ),
            ],
        ];
    }

    public function getEmployee(int $companyId, int $employeeId)
    {
        return $this->employees->findEmployee($employeeId, $companyId);
    }
}
