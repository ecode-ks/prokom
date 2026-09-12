<?php

namespace App\Services\Api;

use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;

class UsersApiService
{
    protected UsersRepository $usersRepository;
    protected CompaniesRepository $companiesRepository;
    protected EmployeesRepository $employeesRepository;

    public function __construct(
        UsersRepository $usersRepository,
        CompaniesRepository $companiesRepository,
        EmployeesRepository $employeesRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->companiesRepository = $companiesRepository;
        $this->employeesRepository = $employeesRepository;
    }

    public function checkLogin(array $credentials): array
    {
        $user = $this->usersRepository->checkUser($credentials);
        $company = $this->companiesRepository->checkCompany($credentials);
        $employee = $this->employeesRepository->checkEmployee($credentials);

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return [
                'username' => $user->username,
                'role' => $user->role,
                'user' => $user->user,
                'company' => null,
                'company_id' => null,
                'employee_id' => null,
                'language' => 'sq',
            ];
        }

        if ($company && Hash::check($credentials['password'], $company->password)) {
            if ($company->status === 'Blocked') {
                throw new \Exception(__('pages.company_blocked'));
            }

            return [
                'username' => $company->username,
                'role' => 'company',
                'user' => $company->company,
                'company' => $company->company,
                'company_id' => (int) $company->id,
                'employee_id' => null,
                'language' => $this->normalizeLanguage($company->language ?? null),
            ];
        }

        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            if (strcasecmp((string) $employee->status, 'Active') !== 0) {
                throw new \Exception(__('pages.employee_account_blocked'));
            }

            if (empty($employee->company_id)) {
                throw new \Exception(__('pages.employee_no_company'));
            }

            if ($this->companiesRepository->checkIfCompanyIsBlocked((int) $employee->company_id)) {
                throw new \Exception(__('pages.company_blocked_staff'));
            }

            $companyRow = $this->companiesRepository->findCompanyById((int) $employee->company_id);

            return [
                'username' => $employee->username,
                'role' => $employee->role,
                'user' => $employee->employee,
                'company' => $companyRow->company ?? null,
                'company_id' => (int) $employee->company_id,
                'employee_id' => (int) $employee->id,
                'language' => $this->normalizeLanguage($companyRow->language ?? null),
            ];
        }

        throw new \Exception(__('pages.invalid_credentials'));
    }

    public function updateProfilePassword(string $oldPassword, string $newPassword, string $username): bool
    {
        $employee = $this->employeesRepository->findEmployeeByUsername($username);
        if ($employee) {
            if (!Hash::check($oldPassword, $employee->password)) {
                return false;
            }
            return $this->employeesRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        }

        $company = $this->companiesRepository->findCompanyByUsername($username);
        if ($company) {
            if (!Hash::check($oldPassword, $company->password)) {
                return false;
            }
            return $this->companiesRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        }

        $user = $this->usersRepository->findUserByUsername($username);
        if (!$user || !Hash::check($oldPassword, $user->password)) {
            return false;
        }

        return $this->usersRepository->updatePasswordByUsername($username, Hash::make($newPassword));
    }

    public function updateCompanyLanguage(int $companyId, string $language): bool
    {
        $language = $this->normalizeLanguage($language);
        return $this->companiesRepository->updateLanguage($companyId, $language);
    }

    private function normalizeLanguage(?string $language): string
    {
        return in_array($language, ['sq', 'en', 'de'], true) ? $language : 'sq';
    }
}
