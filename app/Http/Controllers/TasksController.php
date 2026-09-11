<?php

namespace App\Http\Controllers;

use App\Services\TasksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    protected TasksService $service;

    public function __construct(TasksService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'required|integer|min:1|exists:employees,id',
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'deadline' => 'required|date|after_or_equal:today',
            'title' => 'required|string|min:3|max:255',
            'task' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return redirect()->route('tasks')->with('error', __('pages.fields_invalid'))->withInput();
        }

        $companyId = (int) session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $projectId = (int) $request->project_id;

        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.tasks_own_projects_only'))->withInput();
        }

        $data = [
            'assigned_to' => implode(',', array_map('intval', $request->assigned_to)),
            'assigned_by' => $employeeId,
            'project_id' => $projectId,
            'deadline' => $request->deadline,
            'title' => trim((string) $request->title),
            'task' => trim((string) $request->task),
            'status' => 'new',
            'company_id' => $companyId,
        ];

        try {
            $this->service->createTask($data);
            return redirect()->route('tasks')->with('success', __('pages.task_created'));
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', __('pages.try_again'))->withInput();
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Tasks';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $scopedEmployeeId = $role === 'employee' ? (int) ($employeeId ?? 0) : null;
        $scopedManagerId = $role === 'manager' ? (int) ($employeeId ?? 0) : null;

        if ($role === 'employee') {
            $tasks = $this->service->getTasks(10, $search, $companyId, $scopedEmployeeId);
            $taskStats = $this->service->getTaskStatistics($search, (int) $companyId, $scopedEmployeeId);

            return view('management.tasks')
                ->with('page', $page)
                ->with('employees', collect())
                ->with('projects', collect())
                ->with('tasks', $tasks)
                ->with('taskStats', $taskStats)
                ->with('role', $role);
        }

        $employees = $this->service->getAllEmployees($companyId);
        $projects = $this->service->getAllProjects($companyId, $scopedManagerId);

        if ($role !== 'manager' && count($employees) === 0) {
            return redirect()->route('employees')->with('error', __('pages.register_employees_first'));
        }
        if (count($projects) === 0) {
            return redirect()->route('projects')->with('error', __('pages.register_projects_first'));
        }

        $tasks = $this->service->getTasks(10, $search, $companyId, null, $scopedManagerId);
        $taskStats = $this->service->getTaskStatistics($search, (int) $companyId, null, $scopedManagerId);

        return view('management.tasks')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('projects', $projects)
            ->with('tasks', $tasks)
            ->with('taskStats', $taskStats)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Tasks';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $scopedManagerId = $role === 'manager' ? (int) ($employeeId ?? 0) : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        $editTask = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $editTask->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        $employees = $this->service->getAllEmployees($companyId);
        $projects = $this->service->getAllProjects($companyId, $scopedManagerId);

        return view('management.tasks')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('projects', $projects)
            ->with('editTask', $editTask)
            ->with('role', $role);
    }
    //---------------
    public function taskDetails(int $id)
    {
        $page = 'Tasks';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        $taskDetails = $this->service->getTaskDetails($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $taskDetails->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }
        $taskStatuses = $this->service->getTaskStatuses();

        return view('management.task-details')
            ->with('page', $page)
            ->with('taskDetails', $taskDetails)
            ->with('taskStatuses', $taskStatuses)
            ->with('role', $role);
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:tasks,id',
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'required|integer|min:1|exists:employees,id',
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'deadline' => 'required|date',
            'title' => 'required|string|min:3|max:255',
            'task' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        }

        $id = (int) $request->id;
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $projectId = (int) $request->project_id;

        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, (int) $companyId, (int) ($employeeId ?? 0))) {
            return back()->with('error', __('pages.tasks_update_own_projects_only'))->withInput();
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
                return back()->with('error', __('pages.task_not_updated'))->withInput();
            }

            return redirect()->route('tasks')->with('success', __('pages.task_updated'));
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', __('pages.try_again'));
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        $task = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $task->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        try {
            $this->service->deleteTask($id, $companyId);
            return redirect()->route('tasks')->with('success', __('pages.task_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', __('pages.try_again'));
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', __('pages.task_not_found'));
        }

        $allowedStatuses = ['new', 'working', 'review', 'blocked', 'completed'];
        $statusAliases = [
            'planning' => 'new',
            'in_progress' => 'working',
            'in progress' => 'working',
            'reviewing' => 'review',
            'on_hold' => 'blocked',
        ];
        $normalizedStatus = strtolower(trim($status));
        $normalizedStatus = $statusAliases[$normalizedStatus] ?? $normalizedStatus;

        if (!in_array($normalizedStatus, $allowedStatuses, true)) {
            return redirect()->route('task.details', $id)->with('error', __('pages.invalid_status'));
        }

        try {
            if (!$this->service->updateTaskStatus($id, $normalizedStatus, $companyId)) {
                return redirect()->route('task.details', $id)->with('error', __('pages.invalid_status'));
            }

            return redirect()->route('task.details', $id)->with('success', __('pages.task_status_updated'));
        } catch (\Exception $e) {
            return redirect()->route('task.details', $id)->with('error', __('pages.try_again'));
        }
    }
}
