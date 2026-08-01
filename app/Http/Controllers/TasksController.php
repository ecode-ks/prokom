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

        $data = [
            'assigned_to' => implode(',', array_map('intval', $request->assigned_to)),
            'assigned_by' => session('employee_id') ? (int) session('employee_id') : null,
            'project_id' => (int) $request->project_id,
            'deadline' => $request->deadline,
            'title' => trim((string) $request->title),
            'task' => trim((string) $request->task),
            'status' => 'new',
            'company_id' => session('company_id'),
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
        $employees = $this->service->getAllEmployees($companyId);
        $projects = $this->service->getAllProjects($companyId);

        if (count($employees) === 0) {
            return redirect()->route('employees')->with('error', 'Ju lutemi regjistroni së pari punonjësit.');
        }
        if (count($projects) === 0) {
            return redirect()->route('projects')->with('error', 'Ju lutemi regjistroni së pari projektet.');
        }

        $tasks = $this->service->getTasks(10, $search, $companyId);
        $taskStats = $this->service->getTaskStatistics($search, (int) $companyId);

        return view('management.tasks')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('projects', $projects)
            ->with('tasks', $tasks)
            ->with('taskStats', $taskStats);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Tasks';
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        $employees = $this->service->getAllEmployees($companyId);
        $projects = $this->service->getAllProjects($companyId);
        $editTask = $this->service->getTaskById($id, $companyId);

        return view('management.tasks')
            ->with('page', $page)
            ->with('employees', $employees)
            ->with('projects', $projects)
            ->with('editTask', $editTask);
    }
    //---------------
    public function taskDetails(int $id)
    {
        $page = 'Tasks';
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('tasks')->with('error', 'Detyra nuk u gjet.');
        }

        $taskDetails = $this->service->getTaskDetails($id, $companyId);
        $taskStatuses = $this->service->getTaskStatuses();

        return view('management.task-details')
            ->with('page', $page)
            ->with('taskDetails', $taskDetails)
            ->with('taskStatuses', $taskStatuses);
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
        $data = [
            'assigned_to' => implode(',', array_map('intval', $request->assigned_to)),
            'project_id' => (int) $request->project_id,
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

        if (!$this->service->findOrFail($id, $companyId)) {
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

        if (!$this->service->findOrFail($id, $companyId)) {
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
