<?php

namespace App\Services;

use App\Repositories\TasksRepository;

class TasksService
{
    protected TasksRepository $repository;

    public function __construct(TasksRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createTask(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getTasks(int $limit, string $search, int $companyId)
    {
        $limit = $limit ?: 10;

        $tasks = !empty($search)
            ? $this->repository->getSearchedTasks($limit, $companyId, $search)
            : $this->repository->getTasks($limit, $companyId);

        return $tasks->through(function ($task) {
            $task->assigned_to_names = $this->formatAssignedEmployees($task->assigned_to ?? '');
            return $task;
        });
    }
    //---------------
    public function getAllEmployees(int $companyId)
    {
        return $this->repository->getAllEmployees($companyId);
    }
    //---------------
    public function getAllProjects(int $companyId)
    {
        return $this->repository->getAllProjects($companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkTaskExist($id, $companyId);
    }
    //---------------
    public function getTaskById(int $id, int $companyId)
    {
        return $this->repository->findTask($id, $companyId);
    }
    //---------------
    public function getTaskDetails(int $id, int $companyId)
    {
        $task = $this->repository->getTaskDetails($id, $companyId);
        if (!$task) {
            return null;
        }

        $task->assigned_to_names = $this->formatAssignedEmployees($task->assigned_to ?? '');
        return $task;
    }
    //---------------
    public function getTaskStatuses(): array
    {
        return ['new', 'working', 'review', 'blocked', 'completed'];
    }
    //---------------
    public function updateTask(int $id, array $data, int $companyId): bool
    {
        $task = $this->repository->findTask($id, $companyId);
        if (!$task) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteTask(int $id, int $companyId): bool
    {
        $task = $this->repository->findTask($id, $companyId);
        if (!$task) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function updateTaskStatus(int $id, string $status, int $companyId): bool
    {
        $task = $this->repository->findTask($id, $companyId);
        if (!$task) {
            return false;
        }

        $allowedStatuses = $this->getTaskStatuses();
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        if ($task->status === $status) {
            return true;
        }

        return $this->repository->updateStatus($id, $status, $companyId);
    }
    //---------------
    public function formatAssignedEmployees(string $assignedTo): string
    {
        $ids = array_values(array_filter(array_map('intval', explode(',', $assignedTo))));
        if (count($ids) === 0) {
            return '-';
        }

        $employeesById = $this->repository->getEmployeeNamesByIds($ids);
        $orderedNames = [];
        foreach ($ids as $id) {
            if (isset($employeesById[$id])) {
                $orderedNames[] = $employeesById[$id];
            }
        }

        return count($orderedNames) > 0 ? implode(', ', $orderedNames) : '-';
    }
}
