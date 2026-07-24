<?php

namespace App\Repositories;

use App\Models\ClientsModel;
use App\Repositories\Concerns\TenantScopedRepository;
use Illuminate\Support\Facades\DB;

class ClientsRepository
{
    use TenantScopedRepository;

    protected string $table;

    public function __construct(ClientsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->insert($data)
            ->where('company_id', $companyId);
    }
    //---------------
    public function getClients(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->where('company_id', $companyId)
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
            ->orderBy('client')
            ->where('company_id', $companyId)
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
        return  DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data);
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete();
    }
}
?>