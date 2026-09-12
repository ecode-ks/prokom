<?php

namespace App\Services\Api;

use App\Repositories\EmployeesRepository;
use App\Services\VacationsService;

class VacationsApiService
{
    public const TYPES = [
        'Pushim vjetor',
        'Pushim mjekësor',
    ];

    public function __construct(
        protected VacationsService $vacations,
        protected EmployeesRepository $employeesRepository
    ) {
    }

    public function getTypes(): array
    {
        return self::TYPES;
    }

    public function resolveType(?string $type): ?string
    {
        return in_array((string) $type, self::TYPES, true) ? $type : null;
    }

    public function createVacation(array $data): bool
    {
        return $this->vacations->createVacation($data);
    }

    public function getVacations(int $limit, string $search, array $statuses, int $companyId, ?int $employeeId = null, ?string $type = null)
    {
        return $this->vacations->getVacations($limit, $search, $statuses, $companyId, $employeeId, $type);
    }

    public function getVacationStatistics(string $search, int $companyId, ?int $employeeId = null): object
    {
        return $this->vacations->getVacationStatistics($search, $companyId, $employeeId);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->employeesRepository->getAllEmployees($companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->vacations->findOrFail($id, $companyId);
    }

    public function getVacationById(int $id, int $companyId)
    {
        return $this->vacations->getVacationById($id, $companyId);
    }

    public function updateVacation(int $id, array $data, int $companyId): bool
    {
        return $this->vacations->updateVacation($id, $data, $companyId);
    }

    public function deleteVacation(int $id, int $companyId): bool
    {
        return $this->vacations->deleteVacation($id, $companyId);
    }

    public function updateVacationStatus(int $id, string $status, string $role, int $companyId): bool
    {
        return $this->vacations->updateVacationStatus($id, $status, $role, $companyId);
    }
}
