<?php

namespace App\Services;

use App\Repositories\MaterialsRepository;

class MaterialsService
{
    protected MaterialsRepository $repository;
    public function __construct(MaterialsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createMaterial(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getMaterials(int $limit, int $companyId, string $search = '')
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedMaterials($limit, $companyId, $search);
        }
        return $this->repository->getMaterials($limit, $companyId);
    }
    //---------------
    public function getAllMaterials(int $companyId)
    {
        return $this->repository->getAllMaterials($companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkMaterialExist($id, $companyId);
    }
    //---------------
    public function getMaterialById(int $id, int $companyId)
    {
        return $this->repository->findMaterial($id, $companyId);
    }
    //---------------
    public function updateMaterial(int $id, array $data, int $companyId): bool
    {
        $material = $this->repository->findMaterial($id, $companyId);
        if (!$material) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteMaterial(int $id, int $companyId): bool
    {
        $material = $this->repository->findMaterial($id, $companyId);
        if (!$material) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function getMaterialStatistics(int $companyId): object
    {
        return $this->repository->getMaterialStatistics($companyId);
    }
}
