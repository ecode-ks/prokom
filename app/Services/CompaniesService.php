<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use App\Repositories\PackagesRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\ContractsRepository;
use App\Repositories\ProjectReportsRepository;
use App\Repositories\TaskReportsRepository;
use App\Support\StoredFiles;
use Illuminate\Support\Facades\Hash;

class CompaniesService
{
    //---------------
    protected CompaniesRepository $companiesRepository;
    protected UsersRepository $usersRepository;
    protected PackagesRepository $packagesRepository;
    protected EmployeesRepository $employeesRepository;
    protected ContractsRepository $contractsRepository;
    protected ProjectReportsRepository $projectReportsRepository;
    protected TaskReportsRepository $taskReportsRepository;

    public function __construct(
        CompaniesRepository $companiesRepository,
        UsersRepository $usersRepository,
        PackagesRepository $packagesRepository,
        EmployeesRepository $employeesRepository,
        ContractsRepository $contractsRepository,
        ProjectReportsRepository $projectReportsRepository,
        TaskReportsRepository $taskReportsRepository
    ) {
        $this->companiesRepository = $companiesRepository;
        $this->usersRepository = $usersRepository;
        $this->packagesRepository = $packagesRepository;
        $this->employeesRepository = $employeesRepository;
        $this->contractsRepository = $contractsRepository;
        $this->projectReportsRepository = $projectReportsRepository;
        $this->taskReportsRepository = $taskReportsRepository;
    }
    //---------------
    public function registerCompany(array $data): bool
    {
        if($this->usersRepository->checkUserExists($data['username']) || $this->companiesRepository->checkCompanyExists($data['username']) || $this->employeesRepository->checkEmployeeUsername($data['username'])) {
            throw new \Exception(__('pages.username_taken'));
        }
        $language = $data['language'] ?? 'sq';
        $data['language'] = in_array($language, ['sq', 'en', 'de'], true) ? $language : 'sq';
        $data['password'] = Hash::make($data['password']);
        $data['payment_expire'] = now()->addDays(30);
        $data['status'] = 'Active';
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

        StoredFiles::deleteMany($this->contractsRepository->getDocPayloadsByCompany($id));
        StoredFiles::deleteMany($this->projectReportsRepository->getImagePayloadsByCompany($id));
        StoredFiles::deleteMany($this->taskReportsRepository->getImagePayloadsByCompany($id));

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
