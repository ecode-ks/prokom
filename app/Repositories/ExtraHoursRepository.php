<?php

namespace App\Repositories;

use App\Models\ExtraHoursModel;
use Illuminate\Support\Facades\DB;

class ExtraHoursRepository
{
    protected string $table;

    public function __construct(ExtraHoursModel $model)
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
    public function getExtraHours(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->join('employees', 'extra_hours.employee_id', '=', 'employees.id')
            ->select('extra_hours.*', 'employees.employee')
            ->where('extra_hours.company_id', $companyId)
            ->orderByDesc('extra_hours.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedExtraHours(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table)
            ->join('employees', 'extra_hours.employee_id', '=', 'employees.id')
            ->select('extra_hours.*', 'employees.employee')
            ->where('employees.employee', 'like', "%{$search}%")
            ->where('extra_hours.company_id', $companyId)
            ->orderByDesc('extra_hours.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function findExtraHour(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkExtraHourExist(int $id, int $companyId): bool
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
    public function getEmployeeExtraHoursStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate, string $type): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->where('type', $type)
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('COALESCE(COUNT(*), 0) as entry_count')
            ->selectRaw('COALESCE(SUM(CAST(hours AS DECIMAL(12,2))), 0) as total_hours')
            ->first();
    }
    //---------------
    public function getEmployeeExtraHoursEntries(int $companyId, int $employeeId, string $fromDate, string $toDate, string $type, int $limit = 10, string $cursorName = 'extra_hours_cursor')
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->where('type', $type)
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->cursorPaginate($limit, [
                'id',
                'date',
                'type',
                'hours',
                DB::raw('COALESCE(CAST(hours AS DECIMAL(12,2)), 0) as hours_decimal'),
            ], $cursorName)
            ->withQueryString();
    }
}
