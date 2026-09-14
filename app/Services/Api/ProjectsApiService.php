<?php

namespace App\Services\Api;

use App\Services\ProjectsService;

class ProjectsApiService
{
    public const STATUSES = [
        'planning',
        'in_progress',
        'completed',
        'on_hold',
    ];

    public function __construct(protected ProjectsService $projects)
    {
    }

    public function getStatuses(): array
    {
        return self::STATUSES;
    }

    public function getProjects(int $limit, array $filters, int $companyId, ?int $managerId = null)
    {
        return $this->projects->getProjects($limit, $filters, $companyId, $managerId);
    }

    public function getProjectStatistics(array $filters, int $companyId, ?int $managerId = null): object
    {
        return $this->projects->getProjectStatistics($filters, $companyId, $managerId);
    }

    public function getAllManagers(int $companyId)
    {
        return $this->projects->getAllManagers($companyId);
    }

    public function getAllClients(int $companyId)
    {
        return $this->projects->getAllClients($companyId);
    }

    public function isManagedBy(int $projectId, int $companyId, int $managerId): bool
    {
        return $this->projects->isManagedBy($projectId, $companyId, $managerId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->projects->findOrFail($id, $companyId);
    }

    public function getProjectDetails(int $id, int $companyId)
    {
        return $this->projects->getProjectDetails($id, $companyId);
    }
}
