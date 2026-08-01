<?php

namespace App\Repositories;

use App\Models\TasksModel;
use Illuminate\Support\Facades\DB;

class TasksRepository
{
    protected string $table;

    public function __construct(TasksModel $model)
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
    public function getTasks(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId)
            ->select(
                't.id',
                't.date',
                't.title',
                't.assigned_to',
                't.assigned_by',
                't.project_id',
                't.deadline',
                't.task',
                't.status',
                't.company_id',
                'p.project_name',
                DB::raw('COALESCE(ab.employee, c.company) as assigned_by_name')
            )
            ->orderByDesc('t.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedTasks(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId)
            ->where(function ($query) use ($search) {
                $query->where('t.title', 'like', "%{$search}%")
                    ->orWhere('t.task', 'like', "%{$search}%")
                    ->orWhere('t.status', 'like', "%{$search}%")
                    ->orWhere('t.assigned_to', 'like', "%{$search}%")
                    ->orWhere('p.project_name', 'like', "%{$search}%")
                    ->orWhere('ab.employee', 'like', "%{$search}%")
                    ->orWhere('c.company', 'like', "%{$search}%");
            })
            ->select(
                't.id',
                't.date',
                't.title',
                't.assigned_to',
                't.assigned_by',
                't.project_id',
                't.deadline',
                't.task',
                't.status',
                't.company_id',
                'p.project_name',
                DB::raw('COALESCE(ab.employee, c.company) as assigned_by_name')
            )
            ->orderByDesc('t.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getAllEmployees(int $companyId)
    {
        return DB::table('employees')
            ->where('company_id', $companyId)
            ->orderBy('employee')
            ->get(['id', 'employee']);
    }
    //---------------
    public function getEmployeeNamesByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        return DB::table('employees')
            ->whereIn('id', $ids)
            ->pluck('employee', 'id')
            ->toArray();
    }
    //---------------
    public function getAllProjects(int $companyId)
    {
        return DB::table('projects')
            ->where('company_id', $companyId)
            ->orderBy('project_name')
            ->get(['id', 'project_name']);
    }
    //---------------
    public function getTaskDetails(int $id, int $companyId)
    {
        return DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.id', $id)
            ->where('t.company_id', $companyId)
            ->select(
                't.id',
                't.date',
                't.title',
                't.assigned_to',
                't.assigned_by',
                't.project_id',
                't.deadline',
                't.task',
                't.status',
                't.company_id',
                'p.project_name',
                DB::raw('COALESCE(ab.employee, c.company) as assigned_by_name')
            )
            ->first();
    }
    //---------------
    public function findTask(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkTaskExist(int $id, int $companyId): bool
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
    public function getTaskStatistics(int $companyId, string $search = ''): object
    {
        $query = DB::table($this->table . ' as t')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('t.title', 'like', "%{$search}%")
                    ->orWhere('t.task', 'like', "%{$search}%")
                    ->orWhere('t.status', 'like', "%{$search}%")
                    ->orWhere('t.assigned_to', 'like', "%{$search}%")
                    ->orWhere('p.project_name', 'like', "%{$search}%")
                    ->orWhere('ab.employee', 'like', "%{$search}%")
                    ->orWhere('c.company', 'like', "%{$search}%");
            });
        }

        return $query
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN t.status = 'new' THEN 1 ELSE 0 END) as new_count")
            ->selectRaw("SUM(CASE WHEN t.status = 'working' THEN 1 ELSE 0 END) as working_count")
            ->selectRaw("SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->first();
    }
}
