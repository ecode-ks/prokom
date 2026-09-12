<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;

class EmployeesService
{
    protected int $companyId;
    protected EmployeesRepository $repository;
    protected CompaniesRepository $companiesRepository;
    protected UsersRepository $usersRepository;
    public function __construct(EmployeesRepository $repository, CompaniesRepository $companiesRepository, UsersRepository $usersRepository)
    {
        $this->repository = $repository;
        $this->companiesRepository = $companiesRepository;
        $this->usersRepository = $usersRepository;
        $this->companyId = (session('company_id') ?? 0);
    }
    //---------------
    public function createEmployee(array $data): bool
    {
        if($this->repository->countAllEmployees($this->companyId) >= session('staff_limit')){
            throw new \Exception(__('pages.employees_errors_limit'));
            return false;
        }

        if($this->usersRepository->checkUserExists($data['username']) || $this->companiesRepository->checkCompanyExists($data['username']) || $this->repository->checkEmployeeUsername($data['username'])) {
            throw new \Exception(__('pages.username_taken'));
        }
        $data['password'] = Hash::make($data['password']);
        $data['company_id'] = $this->companyId;

        return $this->repository->create($data);
    }
	//---------------
	public function getEmployees(int $limit, string $search = '')
	{
		$limit = (int) $limit ?: 10;
		if (!empty($search)) {
			return $this->repository->getSearchedEmployees($limit, $search, $this->companyId);
		}
		return $this->repository->getEmployees($limit, $this->companyId);
	}
    //---------------
    public function getAllEmployees()
    {
        return $this->repository->getAllEmployees($this->companyId);
    }
    //---------------
    public function countAllEmployees(): int
    {
        return $this->repository->countAllEmployees($this->companyId);
    }
    //---------------
    public function countActiveEmployees(): int
    {
        return $this->repository->countActiveEmployees($this->companyId);
    }
    //---------------
    public function countBlockedEmployees(): int
    {
        return $this->repository->countBlockedEmployees($this->companyId);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkEmployeeExist($id, $this->companyId);
    }
    //---------------
    public function getEmployeeById(int $id)
    {
        return $this->repository->findEmployee($id, $this->companyId);
    }
    //---------------
    public function updateEmployee(int $id, array $data): bool
    {
        $employee = $this->repository->findEmployee($id, $this->companyId);
        if (!$employee) {
            return false;
        }
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->repository->update($id, $data, $this->companyId);
    }
    //---------------
    public function deleteEmployee(int $id): bool
    {
        $employee = $this->repository->findEmployee($id, $this->companyId);
        if (!$employee) {
            return false;
        }
        return $this->repository->delete($id, $this->companyId);
    }
    //---------------
    public function activateEmployee(int $id): bool
    {
        $employee = $this->repository->findEmployee($id, $this->companyId);
        if (!$employee) {
            return false;
        }
        return $this->repository->activate($id, $this->companyId);
    }
    //---------------
    public function blockEmployee(int $id): bool
    {
        $employee = $this->repository->findEmployee($id, $this->companyId);
        if (!$employee) {
            return false;
        }
        return $this->repository->block($id, $this->companyId);
    }
}
