<?php

namespace App\Repositories;

use App\Models\CheckInModel;
use Illuminate\Support\Facades\DB;

class CheckInRepository
{
    protected string $table;
    public function __construct(CheckInModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    private function applyOwner($query, ?int $employeeId, int $companyId)
    {
        $query->where('company_id', $companyId);

        if ($employeeId === null || $employeeId <= 0) {
            $query->whereNull('employee_id');
        } else {
            $query->where('employee_id', $employeeId);
        }

        return $query;
    }
    //---------------
    public function getActiveCheckIn(?int $employeeId, int $companyId)
    {
        return $this->applyOwner(DB::table($this->table), $employeeId, $companyId)
            ->whereNull('check_out_time')
            ->first();
    }
    //---------------
    public function getTodayCheckIn(?int $employeeId, int $companyId)
    {
        $active = $this->getActiveCheckIn($employeeId, $companyId);
        if ($active) {
            return $active;
        }

        return $this->applyOwner(DB::table($this->table), $employeeId, $companyId)
            ->whereDate('check_in_time', date('Y-m-d'))
            ->orderByDesc('id')
            ->first();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function checkCheckinExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function findById(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function startBreak(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function endBreak(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function endWork(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
}
