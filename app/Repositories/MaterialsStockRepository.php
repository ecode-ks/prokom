<?php

namespace App\Repositories;

use App\Models\MaterialsStockModel;
use Illuminate\Support\Facades\DB;

class MaterialsStockRepository
{
    protected string $table;
    public function __construct(MaterialsStockModel $model)
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
    public function getMaterialsStock(int $limit, int $companyId)
    {
        return DB::table($this->table . ' as s')
            ->join('materials as m', 's.material_id', '=', 'm.id')
            ->select('s.*', 'm.material', 'm.unit')
            ->orderByDesc('s.id')
            ->where('s.company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedMaterialsStock(int $limit, string $search, int $companyId)
    {
        return DB::table($this->table . ' as s')
            ->join('materials as m', 's.material_id', '=', 'm.id')
            ->select('s.*', 'm.material', 'm.unit')
            ->where('m.material', 'like', "%{$search}%")
            ->orderByDesc('s.id')
            ->where('s.company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getStockReport(int $limit, int $companyId)
    {
        return DB::table('materials as m')
            ->leftJoin($this->table . ' as s', function ($join) use ($companyId) {
                $join->on('s.material_id', '=', 'm.id')
                    ->where('s.company_id', $companyId);
            })
            ->selectRaw("m.id, m.material, m.unit, COALESCE(SUM(CASE WHEN s.type = 'in' THEN s.qty WHEN s.type = 'out' THEN -s.qty ELSE 0 END), 0) as qty")
            ->where('m.company_id', $companyId)
            ->groupBy('m.id', 'm.material', 'm.unit')
            ->orderBy('m.material')
            ->cursorPaginate($limit);
    }
    //---------------
    public function findMaterialStock(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkMaterialStockExist(int $id, int $companyId): bool
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
    public function delete(int $id, int $companyId ): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
