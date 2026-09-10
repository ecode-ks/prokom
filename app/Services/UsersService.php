<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;
use Illuminate\Support\Facades\Hash;

class UsersService
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
    //---------------
    public function checkAdminExists(): bool
    {
        return $this->usersRepository->checkAdminExists();
    }
    //---------------
    public function registerAdmin(array $data): bool
    {
        if ($this->usersRepository->checkUserExists($data['username'])) {
            throw new \Exception(__('app.admin_username_taken'));
        }

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'admin';
        return $this->usersRepository->registerAdmin($data);
    }
    //---------------
    public function checkLogin(array $credentials)
    {
        $user = $this->usersRepository->checkUser($credentials);
        $company = $this->companiesRepository->checkCompany($credentials);
        $employee = $this->employeesRepository->checkEmployee($credentials);

        if ($user && Hash::check($credentials['password'], $user->password)) {
            session()->put('active', true);
            session()->put('user_id', $user->id);
            session()->put('user', $user->user);
            session()->put('username', $user->username);
            session()->put('role', $user->role);
            session()->put('language', 'sq');
            return true;
        }

        if ($company && Hash::check($credentials['password'], $company->password)) {
            if ($company->status === 'Blocked') {
                throw new \Exception(__('app.company_blocked'));
            }

            $isExpired = !empty($company->payment_expire) && $company->payment_expire < now();

            session()->put('active', true);
            session()->put('company_id', $company->id);
            session()->put('company', $company->company);
            session()->put('username', $company->username);
            session()->put('role', 'company');
            session()->put('expire_date', $company->payment_expire);
            session()->put('package', $company->package);
            session()->put('language', $this->normalizeLanguage($company->language ?? null));

            if ($isExpired) {
                session()->put('payment_expired', true);
            } else {
                session()->put('payment_expired', false);
            }
            return true;
        }

        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            if (strcasecmp((string) $employee->status, 'Active') !== 0) {
                throw new \Exception(__('app.employee_blocked'));
            }

            if (empty($employee->company_id)) {
                throw new \Exception(__('app.employee_no_company'));
            }

            if ($this->companiesRepository->checkIfCompanyIsBlocked((int) $employee->company_id)) {
                throw new \Exception(__('app.company_blocked_staff'));
            }

            $companyRow = $this->companiesRepository->findCompanyById((int) $employee->company_id);
            $isExpired = $companyRow
                && !empty($companyRow->payment_expire)
                && $companyRow->payment_expire < now();

            session()->put('active', true);
            session()->put('employee_id', $employee->id);
            session()->put('user', $employee->employee);
            session()->put('username', $employee->username);
            session()->put('role', $employee->role);
            session()->put('company_id', (int) $employee->company_id);
            session()->put('company', $companyRow->company ?? null);
            session()->put('payment_expired', (bool) $isExpired);
            session()->put('language', $this->normalizeLanguage($companyRow->language ?? null));

            return true;
        }

        throw new \Exception(__('app.invalid_credentials'));
    }
    //---------------
    public function updateCompanyLanguage(int $companyId, string $language): bool
    {
        $language = $this->normalizeLanguage($language);
        $updated = $this->companiesRepository->updateLanguage($companyId, $language);
        if ($updated) {
            session()->put('language', $language);
        }
        return $updated;
    }
    //---------------
    private function normalizeLanguage(?string $language): string
    {
        return in_array($language, ['sq', 'en', 'de'], true) ? $language : 'sq';
    }
    //---------------
    public function updateProfilePassword(string $oldPassword, string $newPassword, string $username): bool
    {
        $role = session('role');

        if ($role === 'company') {
            $company = $this->companiesRepository->findCompanyByUsername($username);
            if (!$company || !Hash::check($oldPassword, $company->password)) {
                return false;
            }
            return $this->companiesRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        }

        if (in_array($role, ['manager', 'employee', 'finance', 'hr'], true)) {
            $employee = $this->employeesRepository->findEmployeeByUsername($username);
            if (!$employee || !Hash::check($oldPassword, $employee->password)) {
                return false;
            }
            return $this->employeesRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        }

        $user = $this->usersRepository->findUserByUsername($username);
        if (!$user || !Hash::check($oldPassword, $user->password)) {
            return false;
        }

        return $this->usersRepository->updatePasswordByUsername($username, Hash::make($newPassword));
    }
}
