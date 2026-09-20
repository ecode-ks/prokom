<?php

namespace App\Http\Controllers\API;

use App\Services\Api\TasksApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class TasksApiController extends Controller
{
    protected TasksApiService $service;

    public function __construct(TasksApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'required|integer',
            'project_id' => 'required|integer',
            'deadline' => 'required|date',
            'title' => 'required|string',
            'task' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $projectId = (int) $request->project_id;
        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.tasks_own_projects_only')]);
        }

        $data = [
            'assigned_to' => implode(',', array_map('intval', $request->assigned_to)),
            'assigned_by' => $employeeId ?: null,
            'project_id' => $projectId,
            'deadline' => $request->deadline,
            'title' => trim((string) $request->title),
            'task' => trim((string) $request->task),
            'status' => 'new',
            'company_id' => $companyId,
        ];

        try {
            $this->service->createTask($data);
            return json_encode(['success' => __('pages.task_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $search = $request->query('search', '');
        $scopedEmployeeId = $role === 'employee' ? (int) $employeeId : null;
        $scopedManagerId = $role === 'manager' ? (int) $employeeId : null;

        if ($role === 'employee') {
            return json_encode([
                'employees' => collect(),
                'projects' => collect(),
                'tasks' => $this->service->getTasks(10, $search, $companyId, $scopedEmployeeId),
                'taskStats' => $this->service->getTaskStatistics($search, $companyId, $scopedEmployeeId),
                'canManage' => false,
                'canChangeStatus' => true,
                'role' => $role,
            ]);
        }

        return json_encode([
            'employees' => $this->canManage($role) ? $this->service->getAllEmployees($companyId) : collect(),
            'projects' => $this->service->getAllProjects($companyId, $scopedManagerId),
            'tasks' => $this->service->getTasks(10, $search, $companyId, null, $scopedManagerId),
            'taskStats' => $this->service->getTaskStatistics($search, $companyId, null, $scopedManagerId),
            'canManage' => $this->canManage($role),
            'canChangeStatus' => $this->canChangeStatus($role),
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $editTask = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $editTask->project_id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $scopedManagerId = $role === 'manager' ? (int) $employeeId : null;

        return json_encode([
            'employees' => $this->service->getAllEmployees($companyId),
            'projects' => $this->service->getAllProjects($companyId, $scopedManagerId),
            'editTask' => $editTask,
            'role' => $role,
        ]);
    }

    public function taskDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

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
            'canManage' => $this->canManage($role),
            'canChangeStatus' => $this->canChangeStatus($role),
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tasks,id',
            'assigned_to' => 'required|array',
            'assigned_to.*' => 'required|integer',
            'project_id' => 'required|integer',
            'deadline' => 'required|date',
            'title' => 'required|string',
            'task' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $projectId = (int) $request->project_id;

        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.tasks_update_own_projects_only')]);
        }

        $data = [
            'assigned_to' => implode(',', array_map('intval', $request->assigned_to)),
            'project_id' => $projectId,
            'deadline' => $request->deadline,
            'title' => trim((string) $request->title),
            'task' => trim((string) $request->task),
        ];

        try {
            if (!$this->service->updateTask($id, $data, $companyId)) {
                return json_encode(['error' => __('pages.task_not_updated')]);
            }

            return json_encode(['success' => __('pages.task_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canManage($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $task = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $task->project_id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        try {
            $this->service->deleteTask($id, $companyId);
            return json_encode(['success' => __('pages.task_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function updateStatus(int $id, string $status, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canChangeStatus($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        if ($role === 'manager' && !$this->service->isProjectManagedBy(
            (int) ($this->service->getTaskById($id, $companyId)->project_id ?? 0),
            $companyId,
            $employeeId
        )) {
            return json_encode(['error' => __('pages.task_not_found')]);
        }

        $normalizedStatus = $this->service->resolveStatus($status);
        if (!$normalizedStatus) {
            return json_encode(['error' => __('pages.invalid_status')]);
        }

        $current = $this->service->resolveStatus((string) ($this->service->getTaskById($id, $companyId)->status ?? ''));
        if ($current === 'completed' && $normalizedStatus !== 'completed') {
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

    private function canManage(string $role): bool
    {
        return in_array($role, ['manager', 'company'], true);
    }

    private function canChangeStatus(string $role): bool
    {
        return in_array($role, ['manager', 'employee'], true);
    }

    private function canView(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'employee'], true);
    }
}
