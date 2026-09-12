<?php

namespace App\Services\Api;

use App\Repositories\EmployeesRepository;
use App\Repositories\ExtraHoursRepository;

class ExtraHoursApiService
{
    protected ExtraHoursRepository $repository;
    protected EmployeesRepository $employeesRepository;

    public function __construct(ExtraHoursRepository $repository, EmployeesRepository $employeesRepository)
    {
        $this->repository = $repository;
        $this->employeesRepository = $employeesRepository;
    }

    public function createExtraHour(array $data, int $companyId): bool
    {
        $data['company_id'] = $companyId;
        return $this->repository->create($data);
    }

    public function getExtraHours(int $limit, string $search, int $companyId, ?int $employeeId = null)
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedExtraHours($limit, $companyId, $search, $employeeId);
        }
        return $this->repository->getExtraHours($limit, $companyId, $employeeId);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->employeesRepository->getAllEmployees($companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkExtraHourExist($id, $companyId);
    }

    public function getExtraHourById(int $id, int $companyId)
    {
        return $this->repository->findExtraHour($id, $companyId);
    }

    public function updateExtraHour(int $id, array $data, int $companyId): bool
    {
        $extraHour = $this->repository->findExtraHour($id, $companyId);
        if (!$extraHour) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteExtraHour(int $id, int $companyId): bool
    {
        $extraHour = $this->repository->findExtraHour($id, $companyId);
        if (!$extraHour) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
