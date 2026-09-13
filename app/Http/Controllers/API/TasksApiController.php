<?php

namespace App\Http\Controllers\API;

use App\Services\Api\TasksApiService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TasksApiController extends Controller
{
    protected TasksApiService $service;

    public function __construct(TasksApiService $service)
    {
        $this->service = $service;
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = $request->query('search', '');
        $scopedEmployeeId = $role === 'employee' ? (int) $employeeId : null;
        $scopedManagerId = $role === 'manager' ? (int) $employeeId : null;

        if ($role === 'employee') {
            return json_encode([
                'employees' => collect(),
                'projects' => collect(),
                'tasks' => $this->service->getTasks(10, $search, $companyId, $scopedEmployeeId),
                'taskStats' => $this->service->getTaskStatistics($search, $companyId, $scopedEmployeeId),
                'role' => $role,
            ]);
        }

        return json_encode([
            'employees' => $this->service->getAllEmployees($companyId),
            'projects' => $this->service->getAllProjects($companyId, $scopedManagerId),
            'tasks' => $this->service->getTasks(10, $search, $companyId, null, $scopedManagerId),
            'taskStats' => $this->service->getTaskStatistics($search, $companyId, null, $scopedManagerId),
            'role' => $role,
        ]);
    }

    public function taskDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $taskDetails = $this->service->getTaskDetails($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $taskDetails->project_id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        return json_encode([
            'taskDetails' => $taskDetails,
            'taskStatuses' => $this->service->getTaskStatuses(),
            'role' => $role,
        ]);
    }

    public function updateStatus(int $id, string $status, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $normalizedStatus = $this->service->resolveStatus($status);
        if (!$normalizedStatus) {
            return json_encode(['error' => __('pages.invalid_status')]);
        }

        try {
            if (!$this->service->updateTaskStatus($id, $normalizedStatus, $companyId)) {
                return json_encode(['error' => __('pages.invalid_status')]);
            }

            return json_encode(['success' => __('pages.task_status_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
