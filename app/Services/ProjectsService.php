<?php

namespace App\Services;

use App\Repositories\ProjectsRepository;

class ProjectsService
{
    protected ProjectsRepository $repository;
    protected ClientsService $clients;

    public function __construct(ProjectsRepository $repository, ClientsService $clients)
    {
        $this->repository = $repository;
        $this->clients = $clients;
    }
    //---------------
    public function createProject(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getProjects(int $limit, array $filters, int $companyId)
    {
        $limit = $limit ?: 10;

        if ($this->hasActiveFilters($filters)) {
            return $this->repository->getSearchedProjects($limit, $companyId, $filters);
        }

        return $this->repository->getProjects($limit, $companyId);
    }
    //---------------
    public function getProjectStatistics(array $filters, int $companyId): object
    {
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
    public function getAllProjects(int $companyId)
    {
        return $this->repository->getAllProjects($companyId);
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
}
