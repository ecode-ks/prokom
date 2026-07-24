<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    //---------------
    protected UsersRepository $usersRepository;
    protected CompaniesRepository $companiesRepository;
    public function __construct(UsersRepository $usersRepository, CompaniesRepository $companiesRepository)
    {
        $this->usersRepository = $usersRepository;
        $this->companiesRepository = $companiesRepository;
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

        if($user && Hash::check($credentials['password'], $user->password)){
            session()->put('active', true);
            session()->put('user_id', $user->id);
            session()->put('user', $user->user);
            session()->put('username', $user->username);
            session()->put('role', $user->role);
            return true;
        }

        if($company && Hash::check($credentials['password'], $company->password)){
            if($company->status === 'Blocked'){
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

        throw new \Exception('Emri i përdoruesit ose fjalëkalimi është i pasaktë.');
    }
    //---------------
    public function updateProfilePassword(string $oldPassword, string $newPassword, string $username): bool
    {
        if (session('role') === 'company') {
            $company = $this->companiesRepository->findCompanyByUsername($username);
            if (!$company || !Hash::check($oldPassword, $company->password)) {
                return false;
            }
            return $this->companiesRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        } else {
            $user = $this->usersRepository->findUserByUsername($username);
            if (!$user || !Hash::check($oldPassword, $user->password)) {
                return false;
            }

            return $this->usersRepository->updatePasswordByUsername($username, Hash::make($newPassword));
        }
    }
}
