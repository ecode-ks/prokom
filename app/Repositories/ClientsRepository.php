<?php

namespace App\Repositories;

use App\Models\ClientsModel;
use Illuminate\Support\Facades\DB;

class ClientsRepository
{
    protected string $table;

    public function __construct(ClientsModel $model)
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
    public function getClients(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedClients(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table)
            ->where('client', 'like', "%{$search}%")
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getAllClients(int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderBy('client')
            ->get();
    }
    //---------------
    public function findClient(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkClientExist(int $id, int $companyId): bool
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
    //---------------
    public function getClientStatistics(int $companyId): object
    {
        return DB::table($this->table . ' as c')
            ->leftJoin('projects as p', function ($join) use ($companyId) {
                $join->on('p.client_id', '=', 'c.id')
                    ->where('p.company_id', '=', $companyId);
            })
            ->where('c.company_id', $companyId)
            ->selectRaw('COUNT(DISTINCT c.id) as total_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN p.id IS NOT NULL THEN c.id END) as with_projects_count')
            ->selectRaw("COUNT(DISTINCT NULLIF(TRIM(c.location), '')) as locations_count")
            ->selectRaw('COUNT(p.id) as linked_projects_count')
            ->first();
    }
}
