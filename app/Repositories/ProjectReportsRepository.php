<?php

namespace App\Repositories;

use App\Models\ProjectReportsModel;
use Illuminate\Support\Facades\DB;

class ProjectReportsRepository
{
    protected string $table;

    public function __construct(ProjectReportsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): int
    {
        return (int) DB::table($this->table)->insertGetId($data);
    }
    //---------------
    public function getReports(int $limit, int $companyId, string $search = '')
    {
        $query = $this->baseQuery($companyId);

        if ($search !== '') {
            $query->where('p.project_name', 'like', "%{$search}%");
        }

        return $query
            ->orderByDesc('pr.id')
            ->cursorPaginate($limit)
            ->withQueryString();
    }
    //---------------
    public function getReportDetails(int $id, int $companyId)
    {
        return $this->baseQuery($companyId)
            ->where('pr.id', $id)
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
    public function findReport(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
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
    public function getReportStatistics(int $companyId): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->selectRaw("SUM(CASE WHEN status IN ('active', 'new') THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review_count")
            ->selectRaw("SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count")
            ->selectRaw("SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_count")
            ->first();
    }
    //---------------
    private function baseQuery(int $companyId)
    {
        return DB::table($this->table . ' as pr')
            ->join('projects as p', 'pr.project_id', '=', 'p.id')
            ->leftJoin('employees as m', 'p.project_manager', '=', 'm.id')
            ->leftJoin('clients as cl', 'p.client_id', '=', 'cl.id')
            ->leftJoin('users as u', 'pr.reported_by', '=', 'u.id')
            ->leftJoin('companies as c', 'pr.company_id', '=', 'c.id')
            ->where('pr.company_id', $companyId)
            ->select(
                'pr.id',
                'pr.date',
                'pr.report',
                'pr.images',
                'pr.status',
                'pr.reported_by',
                'pr.project_id',
                'pr.company_id',
                'p.project_name',
                'p.location',
                'p.budget',
                'p.start_date',
                'p.expected_end_date',
                'p.status as project_status',
                'm.employee as manager_name',
                'cl.client as client_name',
                DB::raw('COALESCE(u.user, c.company) as reporter_name')
            );
    }
    //---------------
    public function getManagerPendingReports(int $managerId, int $companyId, int $limit = 4)
    {
        return DB::table($this->table . ' as r')
            ->join('projects as p', 'r.project_id', '=', 'p.id')
            ->leftJoin('employees as e', 'r.reported_by', '=', 'e.id')
            ->where('r.company_id', $companyId)
            ->where('p.project_manager', $managerId)
            ->whereRaw("LOWER(r.status) IN ('new', 'active', 'under_review')")
            ->select(
                'r.id',
                'r.date',
                'r.status',
                'p.project_name',
                'e.employee as reporter_name'
            )
            ->orderByDesc('r.date')
            ->orderByDesc('r.id')
            ->limit($limit)
            ->get();
    }
}
