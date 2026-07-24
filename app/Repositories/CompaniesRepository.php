<?php

namespace App\Repositories;

use App\Models\CompaniesModel;
use Illuminate\Support\Facades\DB;

class CompaniesRepository
{
    protected string $table;
    public function __construct(CompaniesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllCompanies()
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->get();
    }
    //---------------
    public function getCompanies(int $limit)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedCompanies(int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function registerCompany(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }
    //---------------
    public function checkCompanyExists(string $username): bool
    {
        return DB::table($this->table)->where('username', $username)->exists();
    }
    //---------------
    public function findCompanyByUsername(string $username)
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->first();
    }
    //---------------
    public function updatePasswordByUsername(string $username, string $password): bool
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->update(['password' => $password]) > 0;
    }
    //---------------
    public function checkIfCompanyIsBlocked(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->where('status', 'Blocked')
            ->exists();
    }
    //---------------
    public function checkCompany(array $credentials)
    {
        return DB::table($this->table)
            ->where('username', $credentials['username'])
            ->first();
    }
    //---------------
    public function findCompanyById(int $id)
    {
        return DB::table($this->table)
            ->leftJoin('packages', 'companies.package', '=', 'packages.id')
            ->select('companies.*', 'packages.title')
            ->where('companies.id', $id)
            ->first();
    }
    //---------------
    public function checkCompanyExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->exists();
    }
    //---------------
    public function activateCompany(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->update(['status' => 'Active']);
    }
    //---------------
    public function blockCompany(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->update(['status' => 'Blocked']);
    }
    //---------------
    public function updateStatus(int $id, string $status): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update(['status' => $status]) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete() > 0;
    }
}
