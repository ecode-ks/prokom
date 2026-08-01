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
            throw new \Exception('Përdoruesi me këtë emër përdoruesi ekziston tashmë. Ju lutemi zgjidhni një emër përdoruesi tjetër.');
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
            return true;
        }

        if ($company && Hash::check($credentials['password'], $company->password)) {
            if ($company->status === 'Blocked') {
                throw new \Exception('Kompania është bllokuar. Ju lutemi kontaktoni stafin.');
            }

            $isExpired = !empty($company->payment_expire) && $company->payment_expire < now();

            session()->put('active', true);
            session()->put('company_id', $company->id);
            session()->put('company', $company->company);
            session()->put('username', $company->username);
            session()->put('role', 'company');

            if ($isExpired) {
                session()->put('payment_expired', true);
            } else {
                session()->put('payment_expired', false);
            }
            return true;
        }

        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            if (strcasecmp((string) $employee->status, 'Active') !== 0) {
                throw new \Exception('Llogaria e punonjësit është e bllokuar. Ju lutemi kontaktoni kompaninë.');
            }

            if (empty($employee->company_id)) {
                throw new \Exception('Punonjësi nuk është i lidhur me asnjë kompani.');
            }

            if ($this->companiesRepository->checkIfCompanyIsBlocked((int) $employee->company_id)) {
                throw new \Exception('Kompania është bllokuar. Ju lutemi kontaktoni stafin.');
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

            return true;
        }

        throw new \Exception('Emri i përdoruesit ose fjalëkalimi është i pasaktë.');
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
