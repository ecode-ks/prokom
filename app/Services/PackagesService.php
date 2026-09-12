<?php

namespace App\Services;

use App\Repositories\EmployeesRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\CompaniesRepository;

class PackagesService
{
    protected PackagesRepository $repository;
    protected CompaniesRepository $companiesRepository;
    protected EmployeesRepository $employeesRepository;
    public function __construct(PackagesRepository $repository, CompaniesRepository $companiesRepository, EmployeesRepository $employeesRepository)
    {
        $this->repository = $repository;
        $this->companiesRepository = $companiesRepository;
        $this->employeesRepository = $employeesRepository;
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
    public function getAllPackages(string $language)
    {
        return $this->repository->getAllPackages($language);
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
        $staff_limit = $package->staff_limit;

        $registeredEmployees = $this->employeesRepository ->countAllEmployees($company_id);
        if($registeredEmployees > $package->staff_limit){
            throw new \Exception(__('pages.staff_limit_package'));
            return false;
        }

        $changed = $this->repository->change($username, $company_id, $package->title, $paymentExpire, $staff_limit );
        if (!$changed) {
            return false;
        }

        session()->put('package', $package->title);
        session()->put('expire_date', $paymentExpire);
        session()->put('payment_expired', false);
        session()->put('staff_limit', $staff_limit);

        $this->companiesRepository->savePayment([
            'company' => $company,
            'package' => $package->title,
            'price' => $package->price,
        ]);

        return true;
    }
}
