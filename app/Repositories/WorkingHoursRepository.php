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
            ->select(
                'working_hours.*',
                DB::raw("COALESCE(employees.employee, companies.company, 'Kompania') as employee")
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
			->select(
                'working_hours.*',
                DB::raw("COALESCE(employees.employee, companies.company, 'Kompania') as employee")
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
    public function getWorkingHoursStatistics(int $companyId): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->selectRaw('SUM(CASE WHEN `date` = CURDATE() THEN 1 ELSE 0 END) as today_count')
            ->selectRaw('COALESCE(SUM(CASE WHEN MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) THEN TIMESTAMPDIFF(MINUTE, check_in, check_out) ELSE 0 END), 0) as month_minutes')
            ->selectRaw('COUNT(DISTINCT CASE WHEN MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) THEN employee_id END) as active_employees_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) THEN `date` END) as month_days_count')
            ->first();
    }
}
