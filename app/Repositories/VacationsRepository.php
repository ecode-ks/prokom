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
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function getVacations(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId)
            ->select('v.id', 'v.date', 'v.employee_id', 'v.from_date', 'v.to_date', 'v.comment', 'v.type', 'v.status', 'e.employee')
            ->orderByDesc('v.id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedVacations(int $limit, int $companyId, string $search, array $statuses = [])
    {
        $query = DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId)
            ->select('v.id', 'v.date', 'v.employee_id', 'v.from_date', 'v.to_date', 'v.comment', 'v.type', 'v.status', 'e.employee');

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
    public function getVacationStatistics(int $companyId, string $search = ''): object
    {
        $query = DB::table($this->table . ' as v')
            ->join('employees as e', 'v.employee_id', '=', 'e.id')
            ->where('v.company_id', $companyId);

        if (!empty($search)) {
            $query->where('e.employee', 'like', "%{$search}%");
        }

        return $query
            ->selectRaw("SUM(CASE WHEN v.status = 'Reviewing' THEN 1 ELSE 0 END) as reviewing_count")
            ->selectRaw("SUM(CASE WHEN v.status = 'Approved' THEN 1 ELSE 0 END) as approved_count")
            ->selectRaw("SUM(CASE WHEN v.status = 'Refused' THEN 1 ELSE 0 END) as refused_count")
            ->first();
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
