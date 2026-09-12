<?php

namespace App\Services\Api;

use App\Services\TasksService;

class TasksApiService
{
    public function __construct(protected TasksService $tasks)
    {
    }

    public function createTask(array $data): bool
    {
        return $this->tasks->createTask($data);
    }

    public function getTasks(int $limit, string $search, int $companyId, ?int $employeeId = null, ?int $managerId = null)
    {
        return $this->tasks->getTasks($limit, $search, $companyId, $employeeId, $managerId);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->tasks->getAllEmployees($companyId);
    }

    public function getAllProjects(int $companyId, ?int $managerId = null)
    {
        return $this->tasks->getAllProjects($companyId, $managerId);
    }

    public function isProjectManagedBy(int $projectId, int $companyId, int $managerId): bool
    {
        return $this->tasks->isProjectManagedBy($projectId, $companyId, $managerId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->tasks->findOrFail($id, $companyId);
    }

    public function getTaskById(int $id, int $companyId)
    {
        return $this->tasks->getTaskById($id, $companyId);
    }

    public function getTaskDetails(int $id, int $companyId)
    {
        return $this->tasks->getTaskDetails($id, $companyId);
    }

    public function getTaskStatuses(): array
    {
        return $this->tasks->getTaskStatuses();
    }

    public function updateTask(int $id, array $data, int $companyId): bool
    {
        return $this->tasks->updateTask($id, $data, $companyId);
    }

    public function deleteTask(int $id, int $companyId): bool
    {
        return $this->tasks->deleteTask($id, $companyId);
    }

    public function updateTaskStatus(int $id, string $status, int $companyId): bool
    {
        return $this->tasks->updateTaskStatus($id, $status, $companyId);
    }

    public function getTaskStatistics(string $search, int $companyId, ?int $employeeId = null, ?int $managerId = null): object
    {
        return $this->tasks->getTaskStatistics($search, $companyId, $employeeId, $managerId);
    }

    public function employeeIsAssigned(int $taskId, int $companyId, int $employeeId): bool
    {
        return $this->tasks->employeeIsAssigned($taskId, $companyId, $employeeId);
    }
}
