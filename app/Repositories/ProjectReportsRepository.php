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
    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }
    //---------------
    public function getReports(int $limit, int $companyId)
    {
        return $this->baseQuery($companyId)
            ->orderByDesc('pr.id')
            ->cursorPaginate($limit);
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
    private function baseQuery(int $companyId)
    {
        return DB::table($this->table . ' as pr')
            ->join('projects as p', 'pr.project_id', '=', 'p.id')
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
                DB::raw('COALESCE(u.user, c.company) as reporter_name')
            );
    }
}
