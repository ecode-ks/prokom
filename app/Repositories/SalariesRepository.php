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
    public function calculate(string $fromDate, string $toDate, int $employeeId, ?int $companyId)
    {
    }
}
