<?php

namespace App\Services;

use App\Repositories\SuppliersRepository;

class SuppliersService
{
    protected SuppliersRepository $repository;
    public function __construct(SuppliersRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createSupplier(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getSuppliers(int $limit, string $search = '', int $companyId)
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSuppliers($limit, $search, $companyId);
        }
        return $this->repository->getSuppliers($limit, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkSupplierExist($id, $companyId);
    }
    //---------------
    public function getSupplierById(int $id, int $companyId)
    {
        return $this->repository->findSupplier($id, $companyId);
    }
    //---------------
    public function updateSupplier(int $id, array $data, int $companyId): bool
    {
        $supplier = $this->repository->findSupplier($id, $companyId);
        if (!$supplier) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteSupplier(int $id, int $companyId): bool
    {
        $supplier = $this->repository->findSupplier($id, $companyId);
        if (!$supplier) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
