<?php

namespace App\Services;

use App\Repositories\MaterialsStockRepository;

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
        $material = $this->repository->findMaterialStock($id, $companyId);
        if (!$material) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteMaterialStock(int $id, int $companyId): bool
    {
        $material = $this->repository->findMaterialStock($id, $companyId);
        if (!$material) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
