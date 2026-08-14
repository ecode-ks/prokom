<?php

namespace App\Services;

use App\Repositories\PackagesRepository;
use App\Repositories\CompaniesRepository;

class PackagesService
{
    protected PackagesRepository $repository;
    protected CompaniesRepository $companiesRepository;
    public function __construct(PackagesRepository $repository, CompaniesRepository $companiesRepository)
    {
        $this->repository = $repository;
        $this->companiesRepository = $companiesRepository;
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
    public function getAllPackages()
    {
        return $this->repository->getAllPackages();
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
    //---------------
    public function changePackage(int $id): bool
    {
        $package = $this->repository->findPackage($id);
        if (!$package) {
            return false;
        }
        $username = session()->get('username');
        $company = session()->get('company');
        $company_id = session()->get('company_id');
        $paymentExpire = now()->addMonths((int) $package->months)->format('Y-m-d');

        session()->put('package', $package->title);
        session()->put('expire_date', $paymentExpire);

        $changed = $this->repository->change($username, $company_id, (string) $package->title, $paymentExpire);
        if (!$changed) {
            return false;
        }

        $this->companiesRepository->savePayment([
            'company' => $company,
            'package' => (string) $package->title,
            'price' => $package->price,
        ]);

        return true;
    }
}
