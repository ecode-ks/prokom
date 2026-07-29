<?php

namespace App\Repositories;

use App\Models\ProjectsModel;
use Illuminate\Support\Facades\DB;

class ProjectsRepository
{
    protected string $table;

    public function __construct(ProjectsModel $model)
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
    public function getProjects(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as p')
            ->join('employees as e', 'p.project_manager', '=', 'e.id')
            ->join('clients as c', 'p.client_id', '=', 'c.id')
            ->where('p.company_id', $companyId)
            ->select(
                'p.id',
                'p.project_name',
                'p.project_manager',
                'p.client_id',
                'p.location',
                'p.start_date',
                'p.expected_end_date',
                'p.budget',
                'p.description',
                'p.status',
                'p.created_at',
                'e.employee as manager_name',
                'c.client as client_name'
            )
            ->orderByDesc('p.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedProjects(int $limit, int $companyId, array $filters = [])
    {
        $query = DB::table($this->table . ' as p')
            ->join('employees as e', 'p.project_manager', '=', 'e.id')
            ->join('clients as c', 'p.client_id', '=', 'c.id')
            ->where('p.company_id', $companyId)
            ->select(
                'p.id',
                'p.project_name',
                'p.project_manager',
                'p.client_id',
                'p.location',
                'p.start_date',
                'p.expected_end_date',
                'p.budget',
                'p.description',
                'p.status',
                'p.created_at',
                'e.employee as manager_name',
                'c.client as client_name'
            );

        $this->applyProjectFilters($query, $filters);

        return $query
            ->orderByDesc('p.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getProjectStatistics(int $companyId, array $filters = []): object
    {
        $query = DB::table($this->table . ' as p')
            ->join('employees as e', 'p.project_manager', '=', 'e.id')
            ->join('clients as c', 'p.client_id', '=', 'c.id')
            ->where('p.company_id', $companyId);

        // Stats keep overview counts; apply search/manager/client/date but not status
        $statsFilters = $filters;
        unset($statsFilters['statuses'], $statsFilters['status']);
        $this->applyProjectFilters($query, $statsFilters);

        return $query
            ->selectRaw("SUM(CASE WHEN p.status IN ('planning', 'in_progress') THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->selectRaw("SUM(CASE WHEN p.status = 'on_hold' THEN 1 ELSE 0 END) as on_hold_count")
            ->first();
    }
    //---------------
    private function applyProjectFilters($query, array $filters)
    {
        $search = $filters['search'] ?? '';
        $statuses = $filters['statuses'] ?? [];
        $projectManager = $filters['project_manager'] ?? null;
        $clientId = $filters['client_id'] ?? null;
        $fromDate = $filters['from_date'] ?? '';
        $toDate = $filters['to_date'] ?? '';

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('p.project_name', 'like', "%{$search}%")
                    ->orWhere('e.employee', 'like', "%{$search}%")
                    ->orWhere('c.client', 'like', "%{$search}%")
                    ->orWhere('p.location', 'like', "%{$search}%");
            });
        }

        if (!empty($statuses)) {
            $query->whereIn('p.status', $statuses);
        }

        if (!empty($projectManager)) {
            $query->where('p.project_manager', (int) $projectManager);
        }

        if (!empty($clientId)) {
            $query->where('p.client_id', (int) $clientId);
        }

        if (!empty($fromDate)) {
            $query->whereDate('p.expected_end_date', '>=', $fromDate);
        }

        if (!empty($toDate)) {
            $query->whereDate('p.expected_end_date', '<=', $toDate);
        }

        return $query;
    }
    //---------------
    public function getAllManagers(int $companyId)
    {
        return DB::table('employees')
            ->where('company_id', $companyId)
            ->where('role', 'manager')
            ->orderBy('employee')
            ->get(['id', 'employee']);
    }
    //---------------
    public function getAllProjects(int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderBy('project_name')
            ->get(['id', 'project_name']);
    }
    //---------------
    public function getProjectDetails(int $id, int $companyId)
    {
        return DB::table($this->table . ' as p')
            ->join('employees as e', 'p.project_manager', '=', 'e.id')
            ->join('clients as c', 'p.client_id', '=', 'c.id')
            ->where('p.id', $id)
            ->where('p.company_id', $companyId)
            ->select(
                'p.id',
                'p.project_name',
                'p.project_manager',
                'p.client_id',
                'p.location',
                'p.start_date',
                'p.expected_end_date',
                'p.budget',
                'p.description',
                'p.status',
                'p.created_at',
                'e.employee as manager_name',
                'c.client as client_name'
            )
            ->first();
    }
    //---------------
    public function findProject(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkProjectExist(int $id, int $companyId): bool
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
}
