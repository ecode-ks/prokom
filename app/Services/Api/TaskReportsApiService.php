<?php

namespace App\Services\Api;

use App\Services\TaskReportsService;

class TaskReportsApiService
{
    public function __construct(protected TaskReportsService $reports)
    {
    }

    public function createReport(array $data, string $role, ?int $employeeId, int $companyId): bool
    {
        return $this->reports->createReport($data, $role, $employeeId, $companyId);
    }

    public function getAvailableTasks(int $companyId, string $role, ?int $employeeId)
    {
        return $this->reports->getAvailableTasks($companyId, $role, $employeeId);
    }

    public function getReports(int $limit, int $companyId, string $role, ?int $employeeId, string $search = '')
    {
        return $this->reports->getReports($limit, $companyId, $role, $employeeId, $search);
    }

    public function getReportStatistics(int $companyId, string $role, ?int $employeeId)
    {
        return $this->reports->getReportStatistics($companyId, $role, $employeeId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->reports->findOrFail($id, $companyId);
    }

    public function getReportDetails(int $id, int $companyId)
    {
        return $this->reports->getReportDetails($id, $companyId);
    }

    public function getReportById(int $id, int $companyId)
    {
        return $this->reports->getReportById($id, $companyId);
    }

    public function updateReport(int $id, array $data, string $role, ?int $employeeId, int $companyId): bool
    {
        return $this->reports->updateReport($id, $data, $role, $employeeId, $companyId);
    }

    public function deleteReport(int $id, int $companyId): bool
    {
        return $this->reports->deleteReport($id, $companyId);
    }

    public function updateReportStatus(int $id, string $status, int $companyId): bool
    {
        return $this->reports->updateReportStatus($id, $status, $companyId);
    }
}
