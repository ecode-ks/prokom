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
    public function getActiveCheckIn(int $userId, int $companyId)
    {
        return DB::table($this->table)
            ->where('employee_id', $userId)
            ->where('company_id', $companyId)
            ->whereNull('check_out_time')
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
