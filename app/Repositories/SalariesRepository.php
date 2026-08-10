<?php

namespace App\Repositories;

use App\Models\SalariesModel;
use Illuminate\Support\Facades\DB;

class SalariesRepository
{
    protected string $table;

    public function __construct(SalariesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function createMany(array $rows): bool
    {
        if (empty($rows)) {
            return false;
        }

        return DB::table($this->table)->insert($rows);
    }
    //---------------
    public function getSalaries(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select('salaries.*', 'employees.employee')
            ->where('salaries.company_id', $companyId)
            ->orderByDesc('salaries.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedSalaries(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table)
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->select('salaries.*', 'employees.employee')
            ->where('employees.employee', 'like', "%{$search}%")
            ->where('salaries.company_id', $companyId)
            ->orderByDesc('salaries.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function findSalary(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkSalaryExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function calculate(string $fromDate, string $toDate, int $employeeId, ?int $companyId, int $limit = 10)
    {
        $companyId = (int) ($companyId ?? 0);
        $paginator = $this->baseCalculateQuery($fromDate, $toDate, $employeeId, $companyId)
            ->orderBy('e.employee')
            ->orderBy('c.employee_id')
            ->cursorPaginate($limit);

        $paginator->setCollection(
            $this->mapCalculationRows($paginator->getCollection(), $fromDate, $toDate, $companyId)
        );

        return $paginator;
    }
    //---------------
    public function calculateAll(string $fromDate, string $toDate, int $employeeId, ?int $companyId)
    {
        $companyId = (int) ($companyId ?? 0);
        $rows = $this->baseCalculateQuery($fromDate, $toDate, $employeeId, $companyId)
            ->orderBy('e.employee')
            ->orderBy('c.employee_id')
            ->get();

        return $this->mapCalculationRows($rows, $fromDate, $toDate, $companyId);
    }
    //---------------
    public function calculateSummary(string $fromDate, string $toDate, int $employeeId, ?int $companyId): array
    {
        $rows = $this->calculateAll($fromDate, $toDate, $employeeId, $companyId);

        return [
            'employee_count' => $rows->count(),
            'hours_total' => (float) $rows->sum('working_hours'),
            'extra_total' => (float) $rows->sum('extra_total'),
            'payroll_total' => (float) $rows->sum('total_pay'),
        ];
    }
    //---------------
    protected function baseCalculateQuery(string $fromDate, string $toDate, int $employeeId, int $companyId)
    {
        $latestContractIds = DB::table('contracts')
            ->selectRaw('MAX(id) as id')
            ->where('company_id', $companyId)
            ->where('from_date', '<=', $toDate)
            ->where('to_date', '>=', $fromDate)
            ->when($employeeId > 0, fn ($q) => $q->where('employee_id', $employeeId))
            ->groupBy('employee_id');

        return DB::table('contracts as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->joinSub($latestContractIds, 'latest', function ($join) {
                $join->on('c.id', '=', 'latest.id');
            })
            ->leftJoinSub(
                DB::table('working_hours')
                    ->select(
                        'employee_id',
                        DB::raw('COALESCE(SUM(TIME_TO_SEC(hours)), 0) / 3600 as working_hours')
                    )
                    ->where('company_id', $companyId)
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->when($employeeId > 0, fn ($q) => $q->where('employee_id', $employeeId))
                    ->groupBy('employee_id'),
                'wh',
                function ($join) {
                    $join->on('c.employee_id', '=', 'wh.employee_id');
                }
            )
            ->where('c.company_id', $companyId)
            ->where('e.company_id', $companyId)
            ->select(
                'c.employee_id',
                'e.employee',
                'c.position',
                'c.salary',
                'c.extra_hours_price',
                'c.weekend_hours_price',
                'c.from_date as contract_from',
                'c.to_date as contract_to',
                DB::raw('COALESCE(wh.working_hours, 0) as working_hours')
            );
    }
    //---------------
    protected function mapCalculationRows($rows, string $fromDate, string $toDate, int $companyId)
    {
        if ($rows->isEmpty()) {
            return collect();
        }

        $employeeIds = $rows->pluck('employee_id')->all();

        $extraHours = DB::table('extra_hours')
            ->select(
                'employee_id',
                'type',
                DB::raw('COALESCE(SUM(CAST(hours AS DECIMAL(12,2))), 0) as hours')
            )
            ->where('company_id', $companyId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereIn('employee_id', $employeeIds)
            ->groupBy('employee_id', 'type')
            ->get()
            ->groupBy('employee_id');

        return $rows->map(function ($row) use ($extraHours) {
            $extras = $extraHours->get($row->employee_id, collect());

            $extraList = [];
            $overtimeHours = 0.0;
            $weekendHours = 0.0;
            $extraTotal = 0.0;

            foreach ($extras as $extra) {
                $hours = (float) $extra->hours;
                $type = (string) $extra->type;
                $rate = 0.0;

                if ($type === 'Orët jashtë orarit') {
                    $rate = (float) $row->extra_hours_price;
                    $overtimeHours = $hours;
                } elseif ($type === 'Orët në vikend') {
                    $rate = (float) $row->weekend_hours_price;
                    $weekendHours = $hours;
                }

                $amount = $hours * $rate;
                $extraTotal += $amount;

                $extraList[] = (object) [
                    'type' => $type,
                    'hours' => $hours,
                    'amount' => $amount,
                ];
            }

            $salary = (float) $row->salary;

            return (object) [
                'employee_id' => (int) $row->employee_id,
                'employee' => $row->employee,
                'position' => $row->position,
                'salary' => $salary,
                'extra_hours_price' => (float) $row->extra_hours_price,
                'weekend_hours_price' => (float) $row->weekend_hours_price,
                'contract_from' => $row->contract_from,
                'contract_to' => $row->contract_to,
                'working_hours' => (float) $row->working_hours,
                'overtime_hours' => $overtimeHours,
                'weekend_hours' => $weekendHours,
                'extra_hours' => $extraList,
                'extra_total' => $extraTotal,
                'total_pay' => $salary + $extraTotal,
            ];
        });
    }
}
