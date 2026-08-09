<?php

namespace App\Repositories;

use App\Models\ContractsModel;
use Illuminate\Support\Facades\DB;

class ContractsRepository
{
    protected string $table;

    public function __construct(ContractsModel $model)
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
    public function getContracts(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId)
            ->select('c.id', 'c.employee_id', 'c.position', 'c.from_date', 'c.to_date', 'c.status', 'c.salary', 'c.extra_hours_price', 'c.weekend_hours_price', 'e.employee', 'e.phone', 'e.email')
            ->orderByDesc('c.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedContracts(int $limit, int $companyId, string $search = '', string $status = '')
    {
        $query = DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId)
            ->select('c.id', 'c.employee_id', 'c.position', 'c.from_date', 'c.to_date', 'c.status', 'e.employee');

        if (!empty($search)) {
            $query->where('e.employee', 'like', "%{$search}%");
        }

        if (!empty($status)) {
            $query->where('c.status', $status);
        }

        return $query
            ->orderByDesc('c.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getContractStatistics(int $companyId, string $search = ''): object
    {
        $query = DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId);

        if (!empty($search)) {
            $query->where('e.employee', 'like', "%{$search}%");
        }

        return $query
            ->selectRaw("SUM(CASE WHEN c.status = 'Active' THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN c.status = 'Expiring' THEN 1 ELSE 0 END) as expiring_count")
            ->selectRaw("SUM(CASE WHEN c.status = 'Expired' THEN 1 ELSE 0 END) as expired_count")
            ->first();
    }
    //---------------
    public function getContractDetails(int $id, int $companyId)
    {
        return DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.id', $id)
            ->where('c.company_id', $companyId)
            ->select('c.id', 'c.employee_id', 'c.position', 'c.from_date', 'c.to_date', 'c.docs', 'c.status', 'c.salary', 'c.extra_hours_price', 'c.weekend_hours_price', 'e.employee', 'e.phone', 'e.email')
            ->first();
    }
    //---------------
    public function updateContractsStatus(int $companyId): bool
    {
        $today = date('Y-m-d');
        $limitDate = date('Y-m-d', strtotime('+30 days'));

        DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('to_date', '<', $today)
            ->update(['status' => 'Expired']);

        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('to_date', '>=', $today)
            ->where('to_date', '<=', $limitDate)
            ->update(['status' => 'Expiring']) > 0;
    }
    //---------------
    public function findContract(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkContractExist(int $id, int $companyId): bool
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
