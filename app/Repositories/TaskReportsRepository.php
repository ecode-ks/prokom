<?php

namespace App\Repositories;

use App\Models\TaskReportsModel;
use Illuminate\Support\Facades\DB;

class TaskReportsRepository
{
    protected string $table;

    public function __construct(TaskReportsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }
    //---------------
    public function getAllTasks(int $companyId)
    {
        return DB::table('tasks as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->orderByDesc('t.id')
            ->get(['t.id', 't.title', 'p.project_name']);
    }
    //---------------
    public function getEmployeeTasks(int $companyId, int $employeeId)
    {
        return DB::table('tasks as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId])
            ->orderByDesc('t.id')
            ->get(['t.id', 't.title', 'p.project_name']);
    }
    //---------------
    public function getManagerTasks(int $companyId, int $managerId)
    {
        return DB::table('tasks as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->orderByDesc('t.id')
            ->get(['t.id', 't.title', 'p.project_name']);
    }
    //---------------
    public function taskExists(int $taskId, int $companyId): bool
    {
        return DB::table('tasks')
            ->where('id', $taskId)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function isTaskAssignedToEmployee(int $taskId, int $companyId, int $employeeId): bool
    {
        return DB::table('tasks')
            ->where('id', $taskId)
            ->where('company_id', $companyId)
            ->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId])
            ->exists();
    }
    //---------------
    public function isTaskManagedBy(int $taskId, int $companyId, int $managerId): bool
    {
        return DB::table('tasks as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.id', $taskId)
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->exists();
    }
    //---------------
    public function getReports(int $limit, int $companyId)
    {
        return $this->baseQuery($companyId)
            ->orderByDesc('tr.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getReportsForEmployee(int $limit, int $companyId, int $employeeId)
    {
        return $this->baseQuery($companyId)
            ->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId])
            ->orderByDesc('tr.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getReportsForManager(int $limit, int $companyId, int $managerId)
    {
        return $this->baseQuery($companyId)
            ->where('p.project_manager', $managerId)
            ->orderByDesc('tr.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getReportDetails(int $id, int $companyId)
    {
        return $this->baseQuery($companyId)
            ->where('tr.id', $id)
            ->first();
    }
    //---------------
    public function findReport(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkReportExist(int $id, int $companyId): bool
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
            ->update($data) !== false;
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
    //---------------
    private function baseQuery(int $companyId)
    {
        return DB::table($this->table . ' as tr')
            ->join('tasks as t', 'tr.task_id', '=', 't.id')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as e', 'tr.reported_by', '=', 'e.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 'tr.company_id', '=', 'c.id')
            ->where('tr.company_id', $companyId)
            ->select(
                'tr.id',
                'tr.date',
                'tr.report',
                'tr.images',
                'tr.status',
                'tr.reported_by',
                'tr.task_id',
                'tr.company_id',
                't.title',
                't.task',
                't.deadline',
                't.assigned_to',
                't.status as task_status',
                'p.project_name',
                DB::raw('COALESCE(ab.employee, c.company) as assigned_by_name'),
                DB::raw('COALESCE(e.employee, c.company) as reporter_name')
            );
    }
}
