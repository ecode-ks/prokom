<?php

namespace App\Repositories;

use App\Models\MaterialsModel;
use Illuminate\Support\Facades\DB;

class MaterialsRepository
{
    protected string $table;
    public function __construct(MaterialsModel $model)
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
    public function getMaterials(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedMaterials(int $limit, int $companyId, string $search)
    {
        return DB::table($this->table)
            ->where('material', 'like', "%{$search}%")
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getAllMaterials(int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderBy('material')
            ->get();
    }
    //---------------
    public function findMaterial(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkMaterialExist(int $id, int $companyId): bool
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
