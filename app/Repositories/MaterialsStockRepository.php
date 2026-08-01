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
        return $this->stockReportQuery($companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getAllStockReport(int $companyId)
    {
        return $this->stockReportQuery($companyId)->get();
    }
    //---------------
    public function getMaterialQtyFromReport(int $materialId, int $companyId, ?int $excludeTransactionId = null): float
    {
        $row = $this->stockReportQuery($companyId, $materialId, $excludeTransactionId)->first();

        return round((float) ($row->qty ?? 0), 2);
    }
    //---------------
    private function stockReportQuery(int $companyId, ?int $materialId = null, ?int $excludeTransactionId = null)
    {
        $query = DB::table('materials as m')
            ->leftJoin($this->table . ' as s', function ($join) use ($companyId, $excludeTransactionId) {
                $join->on('s.material_id', '=', 'm.id')
                    ->where('s.company_id', $companyId);

                if ($excludeTransactionId !== null) {
                    $join->where('s.id', '!=', $excludeTransactionId);
                }
            })
            ->selectRaw("m.id, m.material, m.unit, COALESCE(SUM(CASE WHEN s.type = 'in' THEN s.qty WHEN s.type = 'out' THEN -s.qty ELSE 0 END), 0) as qty")
            ->where('m.company_id', $companyId)
            ->groupBy('m.id', 'm.material', 'm.unit')
            ->orderBy('m.material');

        if ($materialId !== null) {
            $query->where('m.id', $materialId);
        }

        return $query;
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
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function getStockStatistics(int $companyId): object
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'in' THEN qty ELSE 0 END), 0) as in_qty")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'out' THEN qty ELSE 0 END), 0) as out_qty")
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'in' THEN qty WHEN type = 'out' THEN -qty ELSE 0 END), 0) as net_balance")
            ->first();
    }
    //---------------
    public function getStockReportSummary(int $companyId): object
    {
        $movement = $this->getStockStatistics($companyId);

        $movement->materials_count = DB::table('materials')->where('company_id', $companyId)->count();

        $movement->in_stock_count = $this->getAllStockReport($companyId)
            ->filter(fn ($row) => (float) $row->qty > 0)
            ->count();

        return $movement;
    }
}
