<?php

namespace App\Services;

use App\Repositories\SalariesRepository;

class SalariesService
{
    protected int $companyId;
    protected SalariesRepository $repository;
    public function __construct(SalariesRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = (int) (session('company_id') ?? 0);
    }
    //---------------
    public function createSalary(array $data): bool
    {
        $data['company_id'] = $this->companyId;
        return $this->repository->create($data);
    }
    //---------------
    public function getSalaries(int $limit, string $search = '')
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSalaries($limit, $this->companyId, $search);
        }
        return $this->repository->getSalaries($limit, $this->companyId);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkSalaryExist($id, $this->companyId);
    }
    //---------------
    public function getSalaryById(int $id)
    {
        return $this->repository->findSalary($id, $this->companyId);
    }
    //---------------
    public function updateSalary(int $id, array $data): bool
    {
        $salary = $this->repository->findSalary($id, $this->companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->update($id, $data, $this->companyId);
    }
    //---------------
    public function deleteSalary(int $id): bool
    {
        $salary = $this->repository->findSalary($id, $this->companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->delete($id, $this->companyId);
    }
}
