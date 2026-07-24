<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use App\Repositories\PackagesRepository;
use Illuminate\Support\Facades\Hash;

class CompaniesService
{
    //---------------
    protected CompaniesRepository $companiesRepository;
    protected UsersRepository $usersRepository;
    protected PackagesRepository $packagesRepository;
    public function __construct(CompaniesRepository $companiesRepository, UsersRepository $usersRepository, PackagesRepository $packagesRepository)
    {
        $this->companiesRepository = $companiesRepository;
        $this->usersRepository = $usersRepository;
        $this->packagesRepository = $packagesRepository;
    }
    //---------------
    public function registerCompany(array $data): bool
    {
        if($this->usersRepository->checkUserExists($data['username'])) {
            throw new \Exception('Ky përdorues tashmë ekziston. Ju lutemi zgjidhni një përdorues tjetër.');
        }
        if($this->companiesRepository->checkCompanyExists($data['username'])) {
            throw new \Exception('Ky përdorues tashmë ekziston. Ju lutemi zgjidhni një përdorues tjetër.');
        }
        // Per Employee
        // if($this->companiesRepository->checkCompanyExists($data['username'])) {
        //     throw new \Exception('Ky përdorues tashmë ekziston. Ju lutemi zgjidhni një përdorues tjetër.');
        // }
        $data['password'] = Hash::make($data['password']);
        $data['payment_expire'] = now()->addDays(30);
        return $this->companiesRepository->registerCompany($data);
    }
    //---------------
    public function getCompanies(int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        return $this->companiesRepository->getCompanies($limit, $search);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->companiesRepository->checkCompanyExist($id);
    }
    //---------------
    public function getCompanyById(int $id)
    {
        return $this->companiesRepository->findCompanyById($id);
    }
    //---------------
    public function updateCompanyStatus(int $id, string $status): bool
    {
        $company = $this->companiesRepository->findCompanyById($id);
        if (!$company) {
            return false;
        }

        $allowedStatuses = ['Active', 'Blocked'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        if ($company->status === $status) {
            return true;
        }

        return $this->companiesRepository->updateStatus($id, $status);
    }
    //---------------
    public function deleteCompany(int $id): bool
    {
        $company = $this->companiesRepository->findCompanyById($id);
        if (!$company) {
            return false;
        }

        return $this->companiesRepository->delete($id);
    }
}
