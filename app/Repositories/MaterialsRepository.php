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
    //---------------
    public function getMaterialStatistics(int $companyId): object
    {
        $stats = DB::table($this->table . ' as m')
            ->leftJoin('materials_stock as ms', function ($join) use ($companyId) {
                $join->on('ms.material_id', '=', 'm.id')
                    ->where('ms.company_id', '=', $companyId);
            })
            ->where('m.company_id', $companyId)
            ->selectRaw('COUNT(DISTINCT m.id) as total_count')
            ->selectRaw('COUNT(DISTINCT ms.material_id) as with_movement_count')
            ->first();

        $noStockCount = DB::table($this->table . ' as m')
            ->leftJoin(DB::raw("(SELECT material_id, SUM(CASE WHEN type = 'in' THEN qty WHEN type = 'out' THEN -qty ELSE 0 END) as net_qty FROM materials_stock WHERE company_id = {$companyId} GROUP BY material_id) as balances"), 'balances.material_id', '=', 'm.id')
            ->where('m.company_id', $companyId)
            ->where(function ($query) {
                $query->whereNull('balances.material_id')
                    ->orWhere('balances.net_qty', '<=', 0);
            })
            ->count();

        $stats->no_stock_count = $noStockCount;

        return $stats;
    }
}
