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
    //---------------
    public function getCheckIns(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as ci')
            ->leftJoin('employees as e', 'ci.employee_id', '=', 'e.id')
            ->leftJoin('companies as c', 'ci.company_id', '=', 'c.id')
            ->select(
                'ci.*',
                DB::raw("COALESCE(e.employee, c.company, 'Kompania') as employee")
            )
            ->where('ci.company_id', $companyId)
            ->orderByDesc('ci.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedCheckIns(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table . ' as ci')
            ->leftJoin('employees as e', 'ci.employee_id', '=', 'e.id')
            ->leftJoin('companies as c', 'ci.company_id', '=', 'c.id')
            ->select(
                'ci.*',
                DB::raw("COALESCE(e.employee, c.company, 'Kompania') as employee")
            )
            ->where('ci.company_id', $companyId)
            ->where(function ($q) use ($search) {
                $q->where('e.employee', 'like', "%{$search}%")
                    ->orWhere('c.company', 'like', "%{$search}%")
                    ->orWhere('ci.status', 'like', "%{$search}%");
            })
            ->orderByDesc('ci.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function findCheckInDetails(int $id, int $companyId)
    {
        return DB::table($this->table . ' as ci')
            ->leftJoin('employees as e', 'ci.employee_id', '=', 'e.id')
            ->leftJoin('companies as c', 'ci.company_id', '=', 'c.id')
            ->select(
                'ci.*',
                DB::raw("COALESCE(e.employee, c.company, 'Kompania') as employee"),
                'e.phone',
                'e.email'
            )
            ->where('ci.id', $id)
            ->where('ci.company_id', $companyId)
            ->first();
    }
    //---------------
    public function accept(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['status' => 'Accepted']) > 0;
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
    public function getCompanyPresenceToday(int $companyId): object
    {
        $today = date('Y-m-d');

        $checkedInToday = (int) DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereDate('check_in_time', $today)
            ->whereNotNull('employee_id')
            ->distinct()
            ->count('employee_id');

        $currentlyWorking = (int) DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereDate('check_in_time', $today)
            ->whereNull('check_out_time')
            ->whereNotNull('employee_id')
            ->distinct()
            ->count('employee_id');

        return (object) [
            'checked_in_today' => $checkedInToday,
            'currently_working' => $currentlyWorking,
        ];
    }
    //---------------
    public function getActiveMapLocations(int $companyId)
    {
        return DB::table($this->table . ' as ci')
            ->leftJoin('employees as e', 'ci.employee_id', '=', 'e.id')
            ->leftJoin('companies as c', 'ci.company_id', '=', 'c.id')
            ->select(
                'ci.id',
                'ci.employee_id',
                'ci.check_in_time',
                'ci.check_in_location',
                'ci.pause_start_time',
                'ci.pause_end_time',
                'ci.pause_start_location',
                'ci.status',
                'e.phone as employee_phone',
                'e.role as employee_role',
                DB::raw("COALESCE(e.employee, c.company, 'Kompania') as employee"),
                DB::raw("(
                    SELECT GROUP_CONCAT(DISTINCT p.project_name ORDER BY p.project_name SEPARATOR ', ')
                    FROM tasks t
                    INNER JOIN projects p ON p.id = t.project_id
                    WHERE ci.employee_id IS NOT NULL
                      AND FIND_IN_SET(ci.employee_id, t.assigned_to)
                      AND t.company_id = ci.company_id
                      AND LOWER(COALESCE(t.status, '')) <> 'completed'
                ) as project_names")
            )
            ->where('ci.company_id', $companyId)
            ->whereNull('ci.check_out_time')
            ->whereDate('ci.check_in_time', date('Y-m-d'))
            ->where(function ($q) {
                $q->whereNotNull('ci.check_in_location')
                    ->where('ci.check_in_location', '<>', '')
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('ci.pause_start_location')
                            ->where('ci.pause_start_location', '<>', '');
                    });
            })
            ->orderBy('employee')
            ->get();
    }
}
