<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\EmployeesRepository;
use Illuminate\Support\Facades\Hash;

class CompaniesService
{
    //---------------
    protected CompaniesRepository $companiesRepository;
    protected UsersRepository $usersRepository;
    protected PackagesRepository $packagesRepository;
    protected EmployeesRepository $employeesRepository;
    public function __construct(CompaniesRepository $companiesRepository, UsersRepository $usersRepository, PackagesRepository $packagesRepository, EmployeesRepository $employeesRepository)
    {
        $this->companiesRepository = $companiesRepository;
        $this->usersRepository = $usersRepository;
        $this->packagesRepository = $packagesRepository;
        $this->employeesRepository = $employeesRepository;
    }
    //---------------
    public function registerCompany(array $data): bool
    {
        if($this->usersRepository->checkUserExists($data['username']) || $this->companiesRepository->checkCompanyExists($data['username']) || $this->employeesRepository->checkEmployeeUsername($data['username'])) {
            throw new \Exception('Ky përdorues tashmë ekziston. Ju lutemi zgjidhni një përdorues tjetër.');
        }
        $data['password'] = Hash::make($data['password']);
        $data['payment_expire'] = now()->addDays(30);
        return $this->companiesRepository->registerCompany($data);
    }
    //---------------
    public function getCompanies(int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->companiesRepository->getSearchedCompanies($limit, $search);
        }
        return $this->companiesRepository->getCompanies($limit);
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
    //---------------
    public function getCompanyStatistics(): object
    {
        return $this->companiesRepository->getCompanyStatistics();
    }
    //---------------
    public function getLatestForDashboard(int $limit = 5)
    {
        return $this->companiesRepository->getLatestForDashboard($limit);
    }
    //---------------
    public function getExpiringSoonForDashboard(int $limit = 5)
    {
        return $this->companiesRepository->getExpiringSoonForDashboard($limit);
    }
    //---------------
    public function getPaymentStatistics(): object
    {
        return $this->companiesRepository->getPaymentStatistics();
    }
    //---------------
    public function getLatestPaymentsForDashboard(int $limit = 5)
    {
        return $this->companiesRepository->getLatestPaymentsForDashboard($limit);
    }
    //---------------
    public function getPayments(int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->companiesRepository->getSearchedPayments($limit, $search);
        }
        return $this->companiesRepository->getPayments($limit);
    }
}
