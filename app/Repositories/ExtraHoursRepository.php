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
}
