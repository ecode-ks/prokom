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
            ->join('employees', 'working_hours.employee_id', '=', 'employees.id')
            ->select('working_hours.*', 'employees.employee')
			->where('working_hours.company_id', $companyId)
			->orderByDesc('working_hours.id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getSearchedWorkingHours(int $limit, string $search, int $companyId)
	{
		return DB::table($this->table)
			->join('employees', 'working_hours.employee_id', '=', 'employees.id')
			->select('working_hours.*', 'employees.employee')
			->where('employees.employee', 'like', "%{$search}%")
			->where('working_hours.company_id', $companyId)
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
}
