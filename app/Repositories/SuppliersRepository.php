<?php

namespace App\Repositories;

use App\Models\SuppliersModel;
use Illuminate\Support\Facades\DB;

class SuppliersRepository
{
    protected string $table;
    public function __construct(SuppliersModel $model)
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
    public function getSuppliers(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->where('company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedSuppliers(int $limit, string $search, int $companyId)
    {
        return DB::table($this->table)
            ->where('supplier', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->where('company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function findSupplier(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkSupplierExist(int $id, int $companyId): bool
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
}
