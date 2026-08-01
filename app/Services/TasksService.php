<?php

namespace App\Services;

use App\Repositories\TasksRepository;
use App\Repositories\VacationsRepository;
use Carbon\Carbon;

class TasksService
{
    protected TasksRepository $repository;
    protected VacationsRepository $vacations;
    protected NotificationsService $notifications;

    public function __construct(
        TasksRepository $repository,
        VacationsRepository $vacations,
        NotificationsService $notifications
    ) {
        $this->repository = $repository;
        $this->vacations = $vacations;
        $this->notifications = $notifications;
    }
    //---------------
    public function createTask(array $data): bool
    {
        $taskId = $this->repository->create($data);
        if ($taskId <= 0) {
            return false;
        }

        $assigneeIds = array_values(array_filter(array_map('intval', explode(',', (string) ($data['assigned_to'] ?? '')))));
        $companyId = (int) ($data['company_id'] ?? 0);
        $title = (string) ($data['title'] ?? 'Detyrë e re');

        foreach ($assigneeIds as $employeeId) {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'task_assigned',
                'Detyrë e re',
                'Ju është caktuar detyra: ' . $title,
                route('task.details', $taskId)
            );
        }

        return true;
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
        $employees = $this->repository->getAllEmployees($companyId);
        $leaves = $this->vacations->getActiveApprovedLeaves($companyId);
        $today = Carbon::today()->toDateString();

        foreach ($employees as $employee) {
            $leave = $leaves[(int) $employee->id] ?? null;
            if (!$leave) {
                $employee->on_leave = false;
                $employee->leave_until = null;
                $employee->leave_from = null;
                $employee->leave_label = null;
                continue;
            }

            $from = Carbon::parse($leave->from_date)->format('d/m/Y');
            $to = Carbon::parse($leave->to_date)->format('d/m/Y');
            $employee->on_leave = true;
            $employee->leave_from = $leave->from_date;
            $employee->leave_until = $leave->to_date;
            $employee->leave_label = $leave->from_date <= $today
                ? 'Në pushim deri më ' . $to
                : 'Në pushim ' . $from . ' – ' . $to;
        }

        return $employees;
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
    //---------------
    public function getTaskStatistics(string $search, int $companyId): object
    {
        return $this->repository->getTaskStatistics($companyId, $search);
    }
}
