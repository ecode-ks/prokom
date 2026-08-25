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
    public function getContracts(int $limit, int $companyId, ?int $employeeId = null)
    {
        $query = DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId)
            ->select('c.id', 'c.employee_id', 'c.position', 'c.from_date', 'c.to_date', 'c.status', 'c.salary', 'c.extra_hours_price', 'c.weekend_hours_price', 'e.employee', 'e.phone', 'e.email');

        if ($employeeId !== null) {
            $query->where('c.employee_id', $employeeId);
        }

        return $query
            ->orderByDesc('c.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getSearchedContracts(int $limit, int $companyId, string $search = '', string $status = '', ?int $employeeId = null)
    {
        $query = DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId)
            ->select('c.id', 'c.employee_id', 'c.position', 'c.from_date', 'c.to_date', 'c.status', 'e.phone', 'e.employee');

        if ($employeeId !== null) {
            $query->where('c.employee_id', $employeeId);
        }

        if (!empty($search)) {
            if ($employeeId !== null) {
                $query->where(function ($q) use ($search) {
                    $q->where('e.employee', 'like', "%{$search}%")
                        ->orWhere('c.position', 'like', "%{$search}%");
                });
            } else {
                $query->where('e.employee', 'like', "%{$search}%");
            }
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
    public function getExpiringForDashboard(int $companyId, int $limit = 5)
    {
        return DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId)
            ->where('c.status', 'Expiring')
            ->select('c.id', 'c.position', 'c.to_date', 'c.status', 'e.employee')
            ->orderBy('c.to_date')
            ->limit($limit)
            ->get();
    }
    //---------------
    public function getContractStatistics(int $companyId, string $search = '', ?int $employeeId = null): object
    {
        $query = DB::table($this->table . ' as c')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            ->where('c.company_id', $companyId);

        if ($employeeId !== null) {
            $query->where('c.employee_id', $employeeId);
        }

        if (!empty($search)) {
            if ($employeeId !== null) {
                $query->where(function ($q) use ($search) {
                    $q->where('e.employee', 'like', "%{$search}%")
                        ->orWhere('c.position', 'like', "%{$search}%");
                });
            } else {
                $query->where('e.employee', 'like', "%{$search}%");
            }
        }

        return $query
            ->selectRaw("COALESCE(SUM(CASE WHEN c.status = 'Active' THEN 1 ELSE 0 END), 0) as active_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN c.status = 'Expiring' THEN 1 ELSE 0 END), 0) as expiring_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN c.status = 'Expired' THEN 1 ELSE 0 END), 0) as expired_count")
            ->first();
    }
    //---------------
    public function getEmployeeContractStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereDate('from_date', '<=', $toDate)
            ->whereDate('to_date', '>=', $fromDate)
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END), 0) as active_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'Expiring' THEN 1 ELSE 0 END), 0) as expiring_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'Expired' THEN 1 ELSE 0 END), 0) as expired_count")
            ->selectRaw('COUNT(*) as total_count')
            ->first();
    }
    //---------------
    public function getEmployeeContractEntries(int $companyId, int $employeeId, string $fromDate, string $toDate, int $limit = 10, string $cursorName = 'contracts_cursor')
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereDate('from_date', '<=', $toDate)
            ->whereDate('to_date', '>=', $fromDate)
            ->orderByDesc('id')
            ->cursorPaginate($limit, ['id', 'position', 'from_date', 'to_date', 'status', 'salary', 'extra_hours_price', 'weekend_hours_price'], $cursorName)
            ->withQueryString();
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
