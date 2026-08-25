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
            return redirect()->route('tasks')->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $companyId = (int) session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $projectId = (int) $request->project_id;

        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Mund të shtoni detyra vetëm për projektet tuaja.')->withInput();
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
            return redirect()->route('tasks')->with('success', 'Detyra u krijua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.')->withInput();
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
            return redirect()->route('employees')->with('error', 'Ju lutemi regjistroni së pari punonjësit.');
        }
        if (count($projects) === 0) {
            return redirect()->route('projects')->with('error', 'Ju lutemi regjistroni së pari projektet.');
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
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        $editTask = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $editTask->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
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
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        $taskDetails = $this->service->getTaskDetails($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $taskDetails->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
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
            return back()->with('error', 'Të gjitha fusha duhet plotësuar sipas rregullave.')->withInput();
        }

        $id = (int) $request->id;
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $projectId = (int) $request->project_id;

        if ($role === 'manager' && !$this->service->isProjectManagedBy($projectId, (int) $companyId, (int) ($employeeId ?? 0))) {
            return back()->with('error', 'Mund të përditësoni detyra vetëm për projektet tuaja.')->withInput();
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
                return back()->with('error', 'Detyra nuk u gjet ose nuk u përditësua.')->withInput();
            }

            return redirect()->route('tasks')->with('success', 'Detyra u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        $task = $this->service->getTaskById($id, $companyId);
        if ($role === 'manager' && !$this->service->isProjectManagedBy((int) $task->project_id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        try {
            $this->service->deleteTask($id, $companyId);
            return redirect()->route('tasks')->with('success', 'Detyra u fshi me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('tasks')->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        if ($role === 'employee' && !$this->service->employeeIsAssigned($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
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
            return redirect()->route('task.details', $id)->with('error', 'Kërkesa për përditësim statusi nuk është e vlefshme.');
        }

        try {
            if (!$this->service->updateTaskStatus($id, $normalizedStatus, $companyId)) {
                return redirect()->route('task.details', $id)->with('error', 'Kërkesa për përditësim statusi nuk është e vlefshme.');
            }

            return redirect()->route('task.details', $id)->with('success', 'Statusi i detyrës u përditësua me sukses.');
        } catch (\Exception $e) {
            return redirect()->route('task.details', $id)->with('error', 'Ndodhi një problem, ju lutemi provoni përsëri.');
        }
    }
}
