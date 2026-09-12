<?php

namespace App\Services\Api;

use App\Repositories\EmployeesRepository;
use App\Repositories\WorkingHoursRepository;

class WorkingHoursApiService
{
    protected WorkingHoursRepository $repository;
    protected EmployeesRepository $employeesRepository;

    public function __construct(WorkingHoursRepository $repository, EmployeesRepository $employeesRepository)
    {
        $this->repository = $repository;
        $this->employeesRepository = $employeesRepository;
    }

    public function createWorkingHour(array $data, int $companyId): bool
    {
        $data['company_id'] = $companyId;
        return $this->repository->create($data);
    }

    public function getWorkingHours(int $limit, string $search, int $companyId)
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedWorkingHours($limit, $search, $companyId);
        }
        return $this->repository->getWorkingHours($limit, $companyId);
    }

    public function getEmployeeWorkingHours(int $companyId, int $employeeId, ?string $fromDate, ?string $toDate, int $limit = 10)
    {
        return $this->repository->getEmployeeWorkingHoursList($companyId, $employeeId, $fromDate, $toDate, $limit);
    }

    public function getEmployeeWorkingHoursTotal(int $companyId, int $employeeId, ?string $fromDate, ?string $toDate): string
    {
        $totalSeconds = $this->repository->getEmployeeWorkingHoursTotalHours($companyId, $employeeId, $fromDate, $toDate);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        return __('pages.hours_min_format', ['hours' => $hours, 'minutes' => $minutes]);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->employeesRepository->getAllEmployees($companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkWorkingHourExist($id, $companyId);
    }

    public function deleteWorkingHour(int $id, int $companyId): bool
    {
        $workingHour = $this->repository->findWorkingHour($id, $companyId);
        if (!$workingHour) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
