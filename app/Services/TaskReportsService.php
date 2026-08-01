<?php

namespace App\Services;

use App\Repositories\TaskReportsRepository;
use Illuminate\Support\Facades\Storage;

class TaskReportsService
{
    protected TaskReportsRepository $repository;
    protected TasksService $tasks;
    protected NotificationsService $notifications;

    public function __construct(
        TaskReportsRepository $repository,
        TasksService $tasks,
        NotificationsService $notifications
    ) {
        $this->repository = $repository;
        $this->tasks = $tasks;
        $this->notifications = $notifications;
    }
    //---------------
    public function getAvailableTasks(int $companyId, string $role, ?int $employeeId)
    {
        if ($role === 'manager' && $employeeId) {
            return $this->repository->getManagerTasks($companyId, $employeeId);
        }

        if ($role === 'employee' && $employeeId) {
            return $this->repository->getEmployeeTasks($companyId, $employeeId);
        }

        return $this->repository->getAllTasks($companyId);
    }
    //---------------
    public function createReport(array $data, string $role, ?int $employeeId, int $companyId): bool
    {
        $taskId = $data['task_id'];

        if (!$this->repository->taskExists($taskId, $companyId)) {
            return false;
        }

        if ($role === 'manager' && $employeeId) {
            if (!$this->repository->isTaskManagedBy($taskId, $companyId, $employeeId)) {
                return false;
            }
        }

        if ($role === 'employee' && $employeeId) {
            if (!$this->repository->isTaskAssignedToEmployee($taskId, $companyId, $employeeId)) {
                return false;
            }
        }

        if (!empty($data['images'])) {
            $data['images'] = $this->storeImages($data['images']);
        } else {
            $data['images'] = null;
        }

        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        if (empty($data['date'])) {
            $data['date'] = now();
        }

        $reportId = $this->repository->create($data);
        if ($reportId <= 0) {
            return false;
        }

        $task = $this->tasks->getTaskById((int) $taskId, $companyId);
        $taskTitle = $task->title ?? ('#' . $taskId);

        $this->notifications->notify(
            $companyId,
            'company',
            $companyId,
            'task_report',
            'Raport i ri detyre',
            'Është shtuar një raport për detyrën: ' . $taskTitle,
            route('task.report.details', $reportId)
        );

        return true;
    }
    //---------------
    public function getReports(int $limit, int $companyId, string $role, ?int $employeeId)
    {
        $limit = $limit ?: 10;

        if ($role === 'manager' && $employeeId) {
            $reports = $this->repository->getReportsForManager($limit, $companyId, $employeeId);
        } elseif ($role === 'employee' && $employeeId) {
            $reports = $this->repository->getReportsForEmployee($limit, $companyId, $employeeId);
        } else {
            $reports = $this->repository->getReports($limit, $companyId);
        }

        return $reports->through(function ($report) {
            $report->assigned_to_names = $this->tasks->formatAssignedEmployees($report->assigned_to ?? '');
            return $report;
        });
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkReportExist($id, $companyId);
    }
    //---------------
    public function getReportById(int $id, int $companyId)
    {
        return $this->repository->findReport($id, $companyId);
    }
    //---------------
    public function getReportDetails(int $id, int $companyId)
    {
        $report = $this->repository->getReportDetails($id, $companyId);
        if (!$report) {
            return null;
        }

        $report->assigned_to_names = $this->tasks->formatAssignedEmployees($report->assigned_to ?? '');
        return $report;
    }
    //---------------
    public function updateReport(int $id, array $data, string $role, ?int $employeeId, int $companyId): bool
    {
        $report = $this->repository->findReport($id, $companyId);
        if (!$report) {
            return false;
        }

        $taskId = (int) $data['task_id'];

        if (!$this->repository->taskExists($taskId, $companyId)) {
            return false;
        }

        if ($role === 'manager' && $employeeId) {
            if (!$this->repository->isTaskManagedBy($taskId, $companyId, $employeeId)) {
                return false;
            }
        }

        if ($role === 'employee' && $employeeId) {
            if (!$this->repository->isTaskAssignedToEmployee($taskId, $companyId, $employeeId)) {
                return false;
            }
        }

        if (!empty($data['images'])) {
            if (!empty($report->images)) {
                $oldFiles = json_decode($report->images, true);
                if (is_array($oldFiles)) {
                    Storage::disk('public')->delete($oldFiles);
                }
            }
            $data['images'] = $this->storeImages($data['images']);
        } else {
            unset($data['images']);
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteReport(int $id, int $companyId): bool
    {
        $report = $this->repository->findReport($id, $companyId);
        if (!$report) {
            return false;
        }

        if (!empty($report->images)) {
            $files = json_decode($report->images, true);
            if (is_array($files)) {
                Storage::disk('public')->delete($files);
            }
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function updateReportStatus(int $id, string $status, int $companyId): bool
    {
        if (!$this->repository->checkReportExist($id, $companyId)) {
            return false;
        }

        $allowedStatuses = ['active', 'under_review', 'resolved', 'closed'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        return $this->repository->updateStatus($id, $status, $companyId);
    }
    //---------------
    public function getReportStatistics(int $companyId, string $role, ?int $employeeId): object
    {
        if ($role === 'manager' && $employeeId) {
            return $this->repository->getReportStatistics($companyId, $employeeId, null);
        }

        if ($role === 'employee' && $employeeId) {
            return $this->repository->getReportStatistics($companyId, null, $employeeId);
        }

        return $this->repository->getReportStatistics($companyId);
    }
    //---------------
    private function storeImages(array $images): string
    {
        $paths = [];

        foreach ($images as $image) {
            $fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . time() . '_' . uniqid()
                . '.' . $image->getClientOriginalExtension();

            $paths[] = $image->storeAs('task-reports', $fileName, 'public');
        }

        return json_encode($paths);
    }
}
