<?php

namespace App\Services;

use App\Repositories\MaterialsStockRepository;
use Exception;

class MaterialsStockService
{
    protected MaterialsStockRepository $repository;

    public function __construct(MaterialsStockRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createMaterialStock(array $data): bool
    {
        $this->assertSufficientStock(
            (int) $data['material_id'],
            (int) $data['company_id'],
            (string) $data['type'],
            (float) $data['qty']
        );

        return $this->repository->create($data);
    }
    //---------------
    public function getMaterialsStock(int $limit, int $companyId, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedMaterialsStock($limit, $search, $companyId);
        }
        return $this->repository->getMaterialsStock($limit, $companyId);
    }
    //---------------
    public function getStockReport(int $limit, int $companyId)
    {
        $limit = $limit ?: 10;

        return $this->repository->getStockReport($limit, $companyId);
    }
    //---------------
    public function getMaterialsFromStockReport(int $companyId)
    {
        return $this->repository->getAllStockReport($companyId)->map(function ($row) {
            $row->current_stock = round((float) $row->qty, 2);
            return $row;
        });
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkMaterialStockExist($id, $companyId);
    }
    //---------------
    public function getMaterialStockById(int $id, int $companyId)
    {
        return $this->repository->findMaterialStock($id, $companyId);
    }
    //---------------
    public function updateMaterialStock(int $id, array $data, int $companyId): bool
    {
        $existing = $this->repository->findMaterialStock($id, $companyId);
        if (!$existing) {
            return false;
        }

        $newMaterialId = (int) $data['material_id'];
        $newType = (string) $data['type'];
        $newQty = (float) $data['qty'];
        $oldMaterialId = (int) $existing->material_id;

        $oldMaterialStockWithout = $this->repository->getMaterialQtyFromReport($oldMaterialId, $companyId, $id);
        if ($oldMaterialStockWithout < 0) {
            throw new Exception('Ky ndryshim do të linte stok negativ për materialin e mëparshëm.');
        }

        $this->assertSufficientStock($newMaterialId, $companyId, $newType, $newQty, $id);

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteMaterialStock(int $id, int $companyId): bool
    {
        $existing = $this->repository->findMaterialStock($id, $companyId);
        if (!$existing) {
            return false;
        }

        $stockWithout = $this->repository->getMaterialQtyFromReport((int) $existing->material_id, $companyId, $id);
        if ($stockWithout < 0) {
            $formatted = $this->formatQty(abs($stockWithout));
            throw new Exception("Nuk mund ta fshini këtë hyrje. Do të mungonin {$formatted} njësi në stok.");
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    protected function assertSufficientStock(
        int $materialId,
        int $companyId,
        string $type,
        float $qty,
        ?int $excludeTransactionId = null
    ): void {
        if ($type !== 'out') {
            return;
        }

        $available = $this->repository->getMaterialQtyFromReport($materialId, $companyId, $excludeTransactionId);
        if ($qty > $available) {
            $availableLabel = $this->formatQty($available);
            $requestedLabel = $this->formatQty($qty);
            throw new Exception("Stoku i pamjaftueshëm për dalje. Stoku aktual: {$availableLabel}, kërkuar: {$requestedLabel}.");
        }
    }
    //---------------
    protected function formatQty(float $qty): string
    {
        return rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.') ?: '0';
    }
}
