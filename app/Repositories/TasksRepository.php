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
    public function create(array $data): int
    {
        return (int) DB::table($this->table)->insertGetId($data);
    }
    //---------------
    public function getTasks(int $limit, int $companyId, ?int $employeeId = null, ?int $managerId = null)
    {
        $query = DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId);

        if ($employeeId !== null) {
            $query->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId]);
        }

        if ($managerId !== null) {
            $query->where('p.project_manager', $managerId);
        }

        return $query
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
    public function getSearchedTasks(int $limit, int $companyId, string $search, ?int $employeeId = null, ?int $managerId = null)
    {
        $query = DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId);

        if ($employeeId !== null) {
            $query->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId]);
        }

        if ($managerId !== null) {
            $query->where('p.project_manager', $managerId);
        }

        $query->where(function ($q) use ($search) {
            $q->where('t.title', 'like', "%{$search}%")
                ->orWhere('t.task', 'like', "%{$search}%")
                ->orWhere('t.status', 'like', "%{$search}%")
                ->orWhere('t.assigned_to', 'like', "%{$search}%")
                ->orWhere('p.project_name', 'like', "%{$search}%")
                ->orWhere('ab.employee', 'like', "%{$search}%")
                ->orWhere('c.company', 'like', "%{$search}%");
        });

        return $query
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
            ->where('role', 'employee')
            ->where('status', 'Active')
            ->orderBy('employee')
            ->get(['id', 'employee']);
    }
    //---------------
    public function getMyOpenTasks(int $employeeId, int $companyId, int $limit = 5)
    {
        return DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId])
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->select(
                't.id',
                't.title',
                't.deadline',
                't.status',
                'p.project_name'
            )
            ->orderByRaw('CASE WHEN t.deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('t.deadline')
            ->orderByDesc('t.id')
            ->limit($limit)
            ->get();
    }
    //---------------
    public function getEmployeeTaskCounts(int $employeeId, int $companyId): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereRaw('FIND_IN_SET(?, assigned_to)', [$employeeId])
            ->selectRaw('COUNT(*) as my_tasks')
            ->selectRaw("SUM(CASE WHEN LOWER(status) IN ('working', 'in_progress') THEN 1 ELSE 0 END) as working_tasks")
            ->selectRaw("SUM(CASE WHEN LOWER(status) IN ('review', 'under_review', 'in_review') THEN 1 ELSE 0 END) as review_tasks")
            ->selectRaw("SUM(CASE WHEN LOWER(status) <> 'completed' THEN 1 ELSE 0 END) as open_tasks")
            ->first();
    }
    //---------------
    public function getManagerTeamTasks(int $managerId, int $companyId, int $limit = 5)
    {
        return DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->select(
                't.id',
                't.title',
                't.deadline',
                't.status',
                'p.project_name'
            )
            ->orderByRaw('CASE WHEN t.deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('t.deadline')
            ->orderByDesc('t.id')
            ->limit($limit)
            ->get();
    }
    //---------------
    public function getManagerTaskCounts(int $managerId, int $companyId): object
    {
        $today = date('Y-m-d');
        $weekEnd = date('Y-m-d', strtotime('+7 days'));

        $openTasks = (int) DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->count();

        $overdueTasks = (int) DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->whereNotNull('t.deadline')
            ->whereDate('t.deadline', '<', $today)
            ->count();

        $dueSoonTasks = (int) DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->whereNotNull('t.deadline')
            ->whereDate('t.deadline', '>=', $today)
            ->whereDate('t.deadline', '<=', $weekEnd)
            ->count();

        return (object) [
            'open_tasks' => $openTasks,
            'overdue_tasks' => $overdueTasks,
            'due_soon_tasks' => $dueSoonTasks,
        ];
    }
    //---------------
    public function getManagerTeamPresence(int $managerId, int $companyId): object
    {
        $assignedRows = DB::table($this->table . ' as t')
            ->join('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw('LOWER(t.status) <> ?', ['completed'])
            ->pluck('t.assigned_to');

        $employeeIds = [];
        foreach ($assignedRows as $assigned) {
            foreach (explode(',', (string) $assigned) as $id) {
                $id = (int) trim($id);
                if ($id > 0) {
                    $employeeIds[$id] = true;
                }
            }
        }
        $employeeIds = array_keys($employeeIds);
        $teamSize = count($employeeIds);

        if ($teamSize === 0) {
            return (object) [
                'team_size' => 0,
                'checked_in_today' => 0,
            ];
        }

        $checkedInToday = (int) DB::table('check_ins')
            ->where('company_id', $companyId)
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('check_in_time', date('Y-m-d'))
            ->distinct()
            ->count('employee_id');

        return (object) [
            'team_size' => $teamSize,
            'checked_in_today' => $checkedInToday,
        ];
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
    public function getAllProjects(int $companyId, ?int $managerId = null)
    {
        $query = DB::table('projects')
            ->where('company_id', $companyId);

        if ($managerId !== null) {
            $query->where('project_manager', $managerId);
        }

        return $query
            ->orderBy('project_name')
            ->get(['id', 'project_name']);
    }
    //---------------
    public function isProjectManagedBy(int $projectId, int $companyId, int $managerId): bool
    {
        return DB::table('projects')
            ->where('id', $projectId)
            ->where('company_id', $companyId)
            ->where('project_manager', $managerId)
            ->exists();
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
    public function getTaskStatistics(int $companyId, string $search = '', ?int $employeeId = null, ?int $managerId = null): object
    {
        $query = DB::table($this->table . ' as t')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('employees as ab', 't.assigned_by', '=', 'ab.id')
            ->leftJoin('companies as c', 't.company_id', '=', 'c.id')
            ->where('t.company_id', $companyId);

        if ($employeeId !== null) {
            $query->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId]);
        }

        if ($managerId !== null) {
            $query->where('p.project_manager', $managerId);
        }

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
            ->selectRaw("COALESCE(SUM(CASE WHEN t.status = 'new' THEN 1 ELSE 0 END), 0) as new_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN t.status = 'working' THEN 1 ELSE 0 END), 0) as working_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count")
            ->first();
    }
    //---------------
    public function getEmployeeFinishedTasksStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereRaw('FIND_IN_SET(?, assigned_to)', [$employeeId])
            ->selectRaw('COUNT(*) as finished_tasks_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count")
            ->first();
    }
    //---------------
    public function getEmployeeTaskCompletionStatistics(int $companyId, int $employeeId, string $fromDate, string $toDate): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereBetween('date', [$fromDate, $toDate])
            ->whereRaw('FIND_IN_SET(?, assigned_to)', [$employeeId])
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count")
            ->selectRaw("COALESCE(SUM(CASE WHEN status <> 'completed' THEN 1 ELSE 0 END), 0) as uncompleted_count")
            ->first();
    }
    //---------------
    public function getEmployeeTaskEntries(int $companyId, int $employeeId, string $fromDate, string $toDate, int $limit = 10, string $cursorName = 'tasks_cursor')
    {
        return DB::table($this->table . ' as t')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->where('t.company_id', $companyId)
            ->whereBetween('t.date', [$fromDate, $toDate])
            ->whereRaw('FIND_IN_SET(?, t.assigned_to)', [$employeeId])
            ->orderByDesc('t.date')
            ->orderByDesc('t.id')
            ->cursorPaginate($limit, [
                't.id',
                't.date',
                't.title',
                't.status',
                't.deadline',
                'p.project_name',
            ], $cursorName)
            ->withQueryString();
    }
}
