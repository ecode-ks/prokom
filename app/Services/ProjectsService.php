<?php

namespace App\Services;

use App\Repositories\ProjectReportsRepository;
use App\Repositories\ProjectsRepository;
use App\Repositories\TaskReportsRepository;

class ProjectsService
{
    use DeletesStoredFiles;
    protected ProjectsRepository $repository;
    protected ClientsService $clients;
    protected ProjectReportsRepository $projectReports;
    protected TaskReportsRepository $taskReports;

    public function __construct(
        ProjectsRepository $repository,
        ClientsService $clients,
        ProjectReportsRepository $projectReports,
        TaskReportsRepository $taskReports
    ) {
        $this->repository = $repository;
        $this->clients = $clients;
        $this->projectReports = $projectReports;
        $this->taskReports = $taskReports;
    }
    //---------------
    public function createProject(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getProjects(int $limit, array $filters, int $companyId, ?int $managerId = null)
    {
        $limit = $limit ?: 10;

        if ($managerId !== null) {
            $filters['project_manager'] = $managerId;
        }

        if ($this->hasActiveFilters($filters)) {
            return $this->repository->getSearchedProjects($limit, $companyId, $filters);
        }

        return $this->repository->getProjects($limit, $companyId, $managerId);
    }
    //---------------
    public function getProjectStatistics(array $filters, int $companyId, ?int $managerId = null): object
    {
        if ($managerId !== null) {
            $filters['project_manager'] = $managerId;
        }

        return $this->repository->getProjectStatistics($companyId, $filters);
    }
    //---------------
    private function hasActiveFilters(array $filters): bool
    {
        return !empty($filters['search'])
            || !empty($filters['statuses'])
            || !empty($filters['project_manager'])
            || !empty($filters['client_id'])
            || !empty($filters['from_date'])
            || !empty($filters['to_date']);
    }
    //---------------
    public function getAllManagers(int $companyId)
    {
        return $this->repository->getAllManagers($companyId);
    }
    //---------------
    public function getAllProjects(int $companyId, ?int $managerId = null)
    {
        return $this->repository->getAllProjects($companyId, $managerId);
    }
    //---------------
    public function isManagedBy(int $projectId, int $companyId, int $managerId): bool
    {
        return $this->repository->isManagedBy($projectId, $companyId, $managerId);
    }
    //---------------
    public function getAllClients(int $companyId)
    {
        return $this->clients->getAllClients($companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkProjectExist($id, $companyId);
    }
    //---------------
    public function getProjectById(int $id, int $companyId)
    {
        return $this->repository->findProject($id, $companyId);
    }
    //---------------
    public function getProjectDetails(int $id, int $companyId)
    {
        return $this->repository->getProjectDetails($id, $companyId);
    }
    //---------------
    public function updateProject(int $id, array $data, int $companyId): bool
    {
        $project = $this->repository->findProject($id, $companyId);
        if (!$project) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteProject(int $id, int $companyId): bool
    {
        $project = $this->repository->findProject($id, $companyId);
        if (!$project) {
            return false;
        }

        // Cascade DB deletes leave orphan files — clean storage first.
        $this->deleteManyStoredFiles($this->projectReports->getImagePayloadsByProject($id, $companyId));
        $this->deleteManyStoredFiles($this->taskReports->getImagePayloadsByProject($id, $companyId));
        $this->taskReports->deleteByProject($id, $companyId);

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function updateProjectStatus(int $id, string $status, int $companyId): bool
    {
        $project = $this->repository->findProject($id, $companyId);
        if (!$project) {
            return false;
        }

        $allowedStatuses = ['planning', 'in_progress', 'completed', 'on_hold'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        if ($project->status === $status) {
            return true;
        }

        return $this->repository->updateStatus($id, $status, $companyId);
    }
    //---------------
    public function countManagerActiveProjects(int $managerId, int $companyId): int
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return 0;
        }

        return $this->repository->countManagerActiveProjects($managerId, $companyId);
    }
    //---------------
    public function getManagerUpcomingProjects(int $managerId, int $companyId, int $limit = 5)
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return collect();
        }

        return $this->repository->getManagerUpcomingProjects($managerId, $companyId, $limit);
    }
    //---------------
    public function getCompanyUpcomingProjects(int $companyId, int $limit = 5)
    {
        if ($companyId <= 0) {
            return collect();
        }

        return $this->repository->getCompanyUpcomingProjects($companyId, $limit);
    }
    //---------------
    public function countCompanyProjects(int $companyId): int
    {
        if ($companyId <= 0) {
            return 0;
        }

        return $this->repository->countCompanyProjects($companyId);
    }
}
