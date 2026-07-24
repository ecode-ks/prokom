<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\CompaniesRepository;
use Illuminate\Support\Facades\Hash;

class CompaniesService
{
    //---------------
    protected CompaniesRepository $companiesRepository;
    protected UsersRepository $usersRepository;
    public function __construct(CompaniesRepository $companiesRepository, UsersRepository $usersRepository)
    {
        $this->companiesRepository = $companiesRepository;
        $this->usersRepository = $usersRepository;
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
}
