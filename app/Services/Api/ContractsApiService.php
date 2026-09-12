<?php

namespace App\Services\Api;

use App\Repositories\EmployeesRepository;
use App\Services\ContractsService;

class ContractsApiService
{
    public function __construct(
        protected ContractsService $contracts,
        protected EmployeesRepository $employeesRepository
    ) {
    }

    public function createContract(array $data): bool
    {
        return $this->contracts->createContract($data);
    }

    public function getContracts(int $limit, string $search, string $status, int $companyId, ?int $employeeId = null)
    {
        return $this->contracts->getContracts($limit, $search, $status, $companyId, $employeeId);
    }

    public function getContractStatistics(string $search, int $companyId, ?int $employeeId = null): object
    {
        return $this->contracts->getContractStatistics($search, $companyId, $employeeId);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->employeesRepository->getAllEmployees($companyId);
    }

    public function getContractDetails(int $id, int $companyId)
    {
        return $this->contracts->getContractDetails($id, $companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->contracts->findOrFail($id, $companyId);
    }

    public function getContractById(int $id, int $companyId)
    {
        return $this->contracts->getContractById($id, $companyId);
    }

    public function updateContract(int $id, array $data, int $companyId): bool
    {
        return $this->contracts->updateContract($id, $data, $companyId);
    }

    public function deleteContract(int $id, int $companyId): bool
    {
        return $this->contracts->deleteContract($id, $companyId);
    }
}
