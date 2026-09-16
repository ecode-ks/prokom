<?php

namespace App\Services\Api;

use App\Services\TaskReportsService;

class TaskReportsApiService
{
    public function __construct(protected TaskReportsService $reports)
    {
    }

    public function createReport(array $data): bool
    {
        return $this->reports->createReport($data);
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

    public function managerOwnsReport(int $reportId, int $companyId, int $managerId): bool
    {
        return $this->reports->managerOwnsReport($reportId, $companyId, $managerId);
    }

    public function getReportDetails(int $id, int $companyId)
    {
        return $this->reports->getReportDetails($id, $companyId);
    }

    public function getReportById(int $id, int $companyId)
    {
        return $this->reports->getReportById($id, $companyId);
    }

    public function updateReport(int $id, array $data, int $companyId): bool
    {
        return $this->reports->updateReport($id, $data, $companyId);
    }

    public function deleteReport(int $id, int $companyId): bool
    {
        return $this->reports->deleteReport($id, $companyId);
    }

    public function updateReportStatus(int $id, string $status, int $companyId): bool
    {
        return $this->reports->updateReportStatus($id, $status, $companyId);
    }

    public function getReportStatuses(): array
    {
        return ['active', 'under_review', 'resolved', 'closed'];
    }

    public function resolveStatus(string $status): ?string
    {
        $normalized = strtolower(trim(urldecode($status)));

        return in_array($normalized, $this->getReportStatuses(), true) ? $normalized : null;
    }
}
