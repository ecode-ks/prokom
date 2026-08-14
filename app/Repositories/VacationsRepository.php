<?php

namespace App\Repositories;

use App\Models\VacationsModel;
use Illuminate\Support\Facades\DB;

class VacationsRepository
{
    protected string $table;

    public function __construct(VacationsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): int
    {
        return (int) DB::table($this->table)->insertGetId($data);
    }
    //---------------
    public function getVacations(int $limit, int $companyId, ?int $employeeId = null, ?string $type = null)
    {
        $query = DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId)
            ->select('v.id', 'v.date', 'v.employee_id', 'v.from_date', 'v.to_date', 'v.comment', 'v.type', 'v.status', 'e.employee');

        if ($employeeId) {
            $query->where('v.employee_id', $employeeId);
        }

        if (!empty($type)) {
            $query->where('v.type', $type);
        }

        return $query
            ->orderByDesc('v.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedVacations(int $limit, int $companyId, string $search, array $statuses = [], ?int $employeeId = null, ?string $type = null)
    {
        $query = DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId)
            ->select('v.id', 'v.date', 'v.employee_id', 'v.from_date', 'v.to_date', 'v.comment', 'v.type', 'v.status', 'e.employee');

        if ($employeeId) {
            $query->where('v.employee_id', $employeeId);
        }

        if (!empty($type)) {
            $query->where('v.type', $type);
        }

        if (!empty($search)) {
            $query->where('e.employee', 'like', "%{$search}%");
        }

        if (!empty($statuses)) {
            $query->whereIn('v.status', $statuses);
        }

        return $query
            ->orderByDesc('v.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getPendingForDashboard(int $companyId, int $limit = 5)
    {
        return DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId)
            ->whereIn('v.status', ['Pending HR', 'Pending Manager', 'Reviewing'])
            ->select('v.id', 'v.from_date', 'v.to_date', 'v.type', 'v.status', 'e.employee')
            ->orderByDesc('v.id')
            ->limit($limit)
            ->get();
    }
    //---------------
    public function getVacationStatistics(int $companyId, string $search = '', ?int $employeeId = null): object
    {
        $query = DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId);

        if ($employeeId) {
            $query->where('v.employee_id', $employeeId);
        }

        if (!empty($search)) {
            $query->where('e.employee', 'like', "%{$search}%");
        }

        return $query
            ->selectRaw("COALESCE(SUM(CASE WHEN v.status IN ('Pending HR', 'Pending Manager', 'Reviewing') THEN 1 ELSE 0 END), 0) as pending_hr_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN v.status = 'Approved' THEN 1 ELSE 0 END), 0) as approved_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN v.status = 'Refused' THEN 1 ELSE 0 END), 0) as refused_count")
            ->first();
    }
    //---------------
    public function getEmployeeVacationStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereBetween('from_date', [$fromDate, $toDate])
            ->selectRaw("COALESCE(SUM(CASE WHEN status IN ('Pending HR', 'Pending Manager', 'Reviewing') THEN 1 ELSE 0 END), 0) as pending_hr_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END), 0) as approved_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'Refused' THEN 1 ELSE 0 END), 0) as refused_count")
            ->selectRaw('COUNT(*) as total_count')
            ->first();
    }
    //---------------
    public function getEmployeeVacationEntries(int $companyId, int $employeeId, string $fromDate, string $toDate, int $limit = 10, string $cursorName = 'vacations_cursor')
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->whereDate('from_date', '<=', $toDate)
            ->whereDate('to_date', '>=', $fromDate)
            ->orderByDesc('from_date')
            ->cursorPaginate($limit, ['id', 'date', 'from_date', 'to_date', 'type', 'status', 'comment'], $cursorName)
            ->withQueryString();
    }
    //---------------
    public function getActiveApprovedLeaves(int $companyId): array
    {
        $today = now()->toDateString();

        $rows = DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('status', 'Approved')
            ->whereDate('to_date', '>=', $today)
            ->orderBy('from_date')
            ->get(['employee_id', 'from_date', 'to_date']);

        $map = [];

        foreach ($rows as $row) {
            $employeeId = (int) $row->employee_id;
            if ($employeeId <= 0) {
                continue;
            }

            if (!isset($map[$employeeId])) {
                $map[$employeeId] = $row;
                continue;
            }

            $existing = $map[$employeeId];
            $existingActive = $existing->from_date <= $today;
            $rowActive = $row->from_date <= $today;

            if ($rowActive && !$existingActive) {
                $map[$employeeId] = $row;
            } elseif ($rowActive === $existingActive && $row->to_date > $existing->to_date) {
                $map[$employeeId] = $row;
            }
        }

        return $map;
    }
    //---------------
    public function findVacation(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkVacationExist(int $id, int $companyId): bool
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
    public function updateStatus(int $id, string $status, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['status' => $status]) > 0;
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
