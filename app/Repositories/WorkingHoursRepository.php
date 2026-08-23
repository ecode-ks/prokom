<?php

namespace App\Repositories;

use App\Models\WorkingHoursModel;
use Illuminate\Support\Facades\DB;

class WorkingHoursRepository
{
    protected string $table;
    public function __construct(WorkingHoursModel $model)
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
	public function getWorkingHours(int $limit, int $companyId)
	{
		return DB::table($this->table)
            ->leftJoin('employees', 'working_hours.employee_id', '=', 'employees.id')
            ->leftJoin('companies', 'working_hours.company_id', '=', 'companies.id')
            ->leftJoin('check_ins as ci', function ($join) {
                $join->on('working_hours.company_id', '=', 'ci.company_id')
                    ->on('working_hours.check_in', '=', 'ci.check_in_time')
                    ->whereRaw('(working_hours.employee_id = ci.employee_id OR (working_hours.employee_id IS NULL AND ci.employee_id IS NULL))');
            })
            ->select(
                'working_hours.*',
                DB::raw("COALESCE(employees.employee, companies.company, 'Kompania') as employee"),
                'ci.check_in_location',
                'ci.check_out_location'
            )
			->where('working_hours.company_id', $companyId)
			->orderByDesc('working_hours.id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getSearchedWorkingHours(int $limit, string $search, int $companyId)
	{
		return DB::table($this->table)
			->leftJoin('employees', 'working_hours.employee_id', '=', 'employees.id')
            ->leftJoin('companies', 'working_hours.company_id', '=', 'companies.id')
            ->leftJoin('check_ins as ci', function ($join) {
                $join->on('working_hours.company_id', '=', 'ci.company_id')
                    ->on('working_hours.check_in', '=', 'ci.check_in_time')
                    ->whereRaw('(working_hours.employee_id = ci.employee_id OR (working_hours.employee_id IS NULL AND ci.employee_id IS NULL))');
            })
			->select(
                'working_hours.*',
                DB::raw("COALESCE(employees.employee, companies.company, 'Kompania') as employee"),
                'ci.check_in_location',
                'ci.check_out_location'
            )
			->where('working_hours.company_id', $companyId)
            ->where(function ($q) use ($search) {
                $q->where('employees.employee', 'like', "%{$search}%")
                    ->orWhere('companies.company', 'like', "%{$search}%");
            })
			->orderByDesc('working_hours.id')
			->cursorPaginate($limit);
	}
    //---------------
    public function checkWorkingHourExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function findWorkingHour(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
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
    public function getEmployeeWorkingHoursStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->selectRaw('COALESCE(COUNT(*), 0) as entry_count')
            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(hours)), 0) / 3600 as total_hours')
            ->selectRaw('COALESCE(AVG(TIME_TO_SEC(hours)), 0) / 3600 as average_hours')
            ->first();
    }
    //---------------
    public function getEmployeeWorkingHoursEntries(int $companyId, int $employeeId, string $fromDate, string $toDate, int $limit = 10, string $cursorName = 'working_hours_cursor')
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->cursorPaginate($limit, ['id', 'date', 'check_in', 'check_out', 'hours'], $cursorName)
            ->withQueryString();
    }
    //---------------
    public function getEmployeeWorkingHoursList(int $companyId, int $employeeId, ?string $fromDate, ?string $toDate, int $limit = 10)
    {
        $query = DB::table($this->table)
            ->leftJoin('employees', 'working_hours.employee_id', '=', 'employees.id')
            ->leftJoin('check_ins as ci', function ($join) {
                $join->on('working_hours.company_id', '=', 'ci.company_id')
                    ->on('working_hours.check_in', '=', 'ci.check_in_time')
                    ->whereRaw('(working_hours.employee_id = ci.employee_id OR (working_hours.employee_id IS NULL AND ci.employee_id IS NULL))');
            })
            ->select(
                'working_hours.*',
                DB::raw("COALESCE(employees.employee, 'Kompania') as employee"),
                'ci.check_in_location',
                'ci.check_out_location'
            )
            ->where('working_hours.company_id', $companyId)
            ->where('working_hours.employee_id', $employeeId);

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('working_hours.date', [$fromDate, $toDate]);
        }

        return $query
            ->orderByDesc('working_hours.date')
            ->orderByDesc('working_hours.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getEmployeeWorkingHoursTotalHours(int $companyId, int $employeeId, ?string $fromDate, ?string $toDate): float
    {
        $query = DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId);

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        return (float) $query->selectRaw('COALESCE(SUM(TIME_TO_SEC(hours)), 0) / 3600 as total_hours')->value('total_hours');
    }
}
