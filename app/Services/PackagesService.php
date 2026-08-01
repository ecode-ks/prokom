<?php

namespace App\Services;

use App\Repositories\PackagesRepository;

class PackagesService
{
    protected PackagesRepository $repository;
    public function __construct(PackagesRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createPackage(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getPackages(int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedPackages($limit, $search);
        }
        return $this->repository->getPackages($limit);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkPackageExist($id);
    }
    //---------------
    public function getPackageById(int $id)
    {
        return $this->repository->findPackage($id);
    }
    //---------------
    public function updatePackage(int $id, array $data): bool
    {
        $package = $this->repository->findPackage($id);
        if (!$package) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deletePackage(int $id): bool
    {
        $package = $this->repository->findPackage($id);
        if (!$package) {
            return false;
        }
        return $this->repository->delete($id);
    }
    //---------------
    public function getPackageStatistics(): object
    {
        return $this->repository->getPackageStatistics();
    }
}
