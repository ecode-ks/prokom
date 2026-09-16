<?php

namespace App\Services;

use App\Repositories\TasksRepository;
use App\Repositories\TaskReportsRepository;
use App\Repositories\VacationsRepository;
use Carbon\Carbon;

class TasksService
{
    use DeletesStoredFiles;
    protected TasksRepository $repository;
    protected VacationsRepository $vacations;
    protected NotificationsService $notifications;
    protected TaskReportsRepository $taskReports;

    public function __construct(
        TasksRepository $repository,
        VacationsRepository $vacations,
        NotificationsService $notifications,
        TaskReportsRepository $taskReports
    ) {
        $this->repository = $repository;
        $this->vacations = $vacations;
        $this->notifications = $notifications;
        $this->taskReports = $taskReports;
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
        $title = (string) ($data['title'] ?? $this->notifications->line($companyId, 'pages.new_task_default'));

        foreach ($assigneeIds as $employeeId) {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'task_assigned',
                'pages.notif_new_task',
                'pages.notif_new_task_body',
                route('task.details', $taskId),
                ['title' => $title]
            );
        }

        return true;
    }
    //---------------
    public function getTasks(int $limit, string $search, int $companyId, ?int $employeeId = null, ?int $managerId = null)
    {
        $limit = $limit ?: 10;

        $tasks = !empty($search)
            ? $this->repository->getSearchedTasks($limit, $companyId, $search, $employeeId, $managerId)
            : $this->repository->getTasks($limit, $companyId, $employeeId, $managerId);

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
                ? __('pages.on_leave_until', ['date' => $to])
                : __('pages.on_leave_range', ['from' => $from, 'to' => $to]);
        }

        return $employees;
    }
    //---------------
    public function getAllProjects(int $companyId, ?int $managerId = null)
    {
        return $this->repository->getAllProjects($companyId, $managerId);
    }
    //---------------
    public function isProjectManagedBy(int $projectId, int $companyId, int $managerId): bool
    {
        return $this->repository->isProjectManagedBy($projectId, $companyId, $managerId);
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
    public function generateEmployeeFinishedTasksReport(string $fromDate, string $toDate, int $employeeId): object
    {
        return $this->repository->getEmployeeFinishedTasksStatistics((int) (session('company_id') ?? 0), $employeeId, $fromDate, $toDate);
    }
    //---------------
    public function getEmployeeTaskCompletionReport(string $fromDate, string $toDate, int $employeeId): object
    {
        return $this->repository->getEmployeeTaskCompletionStatistics((int) (session('company_id') ?? 0), $employeeId, $fromDate, $toDate);
    }
    //---------------
    public function getEmployeeTaskEntriesReport(string $fromDate, string $toDate, int $employeeId, int $limit = 10, string $cursorName = 'tasks_cursor')
    {
        return $this->repository->getEmployeeTaskEntries((int) (session('company_id') ?? 0), $employeeId, $fromDate, $toDate, $limit, $cursorName);
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

        $this->deleteManyStoredFiles($this->taskReports->getImagePayloadsByTask($id, $companyId));
        $this->taskReports->deleteByTask($id, $companyId);

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
    public function getTaskStatistics(string $search, int $companyId, ?int $employeeId = null, ?int $managerId = null): object
    {
        return $this->repository->getTaskStatistics($companyId, $search, $employeeId, $managerId);
    }
    //---------------
    public function employeeIsAssigned(int $taskId, int $companyId, int $employeeId): bool
    {
        if ($employeeId <= 0) {
            return false;
        }

        $task = $this->repository->findTask($taskId, $companyId);
        if (!$task) {
            return false;
        }

        $assigneeIds = array_map('intval', array_filter(explode(',', (string) ($task->assigned_to ?? ''))));

        return in_array($employeeId, $assigneeIds, true);
    }
    //---------------
    public function getMyOpenTasks(int $employeeId, int $companyId, int $limit = 5)
    {
        if ($employeeId <= 0 || $companyId <= 0) {
            return collect();
        }

        return $this->repository->getMyOpenTasks($employeeId, $companyId, $limit);
    }
    //---------------
    public function getEmployeeTaskCounts(int $employeeId, int $companyId): object
    {
        if ($employeeId <= 0 || $companyId <= 0) {
            return (object) [
                'my_tasks' => 0,
                'working_tasks' => 0,
                'review_tasks' => 0,
                'open_tasks' => 0,
            ];
        }

        $row = $this->repository->getEmployeeTaskCounts($employeeId, $companyId);

        return (object) [
            'my_tasks' => (int) ($row->my_tasks ?? 0),
            'working_tasks' => (int) ($row->working_tasks ?? 0),
            'review_tasks' => (int) ($row->review_tasks ?? 0),
            'open_tasks' => (int) ($row->open_tasks ?? 0),
        ];
    }
    //---------------
    public function getManagerTeamTasks(int $managerId, int $companyId, int $limit = 5)
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return collect();
        }

        return $this->repository->getManagerTeamTasks($managerId, $companyId, $limit);
    }
    //---------------
    public function getManagerTaskCounts(int $managerId, int $companyId): object
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return (object) [
                'open_tasks' => 0,
                'overdue_tasks' => 0,
                'due_soon_tasks' => 0,
            ];
        }

        return $this->repository->getManagerTaskCounts($managerId, $companyId);
    }
    //---------------
    public function getManagerTeamPresence(int $managerId, int $companyId): object
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return (object) [
                'team_size' => 0,
                'checked_in_today' => 0,
            ];
        }

        return $this->repository->getManagerTeamPresence($managerId, $companyId);
    }
}
