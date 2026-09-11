<?php

namespace App\Http\Controllers;

use App\Services\ProjectsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    protected ProjectsService $service;
    public function __construct(ProjectsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_name' => 'required|string|min:3|max:255',
            'project_manager' => 'required|integer|min:1|exists:employees,id',
            'client_id' => 'required|integer|min:1|exists:clients,id',
            'location' => 'required|string|min:2|max:255',
            'start_date' => 'required|date',
            'expected_end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|min:1',
            'status' => 'required|in:planning,in_progress,completed,on_hold'
        ]);

        if ($validator->fails()) {
            return redirect()->route('projects')->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only([
                'project_name',
                'project_manager',
                'client_id',
                'location',
                'start_date',
                'expected_end_date',
                'budget',
                'description',
                'status'
            ]);
            $data['company_id'] = session('company_id');
            $data['created_at'] = now()->toDateString();

            try {
                $this->service->createProject($data);
                return redirect()->route('projects')->with('success', __('pages.project_created'));
            } catch (\Exception $e) {
                return redirect()->route('projects')->with('error', __('pages.try_again_reason', ['message' => $e->getMessage()]))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Projects';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;
        $scopedManagerId = $role === 'manager' ? (int) ($employeeId ?? 0) : null;

        $managers = $this->service->getAllManagers($companyId);
        $clients = $this->service->getAllClients($companyId);

        if ($role !== 'manager') {
            if (count($managers) === 0) {
                return redirect()->route('employees')->with('error', __('pages.register_managers_first'));
            }
            if (count($clients) === 0) {
                return redirect()->route('clients')->with('error', __('pages.register_clients_first'));
            }
        }

        $statuses = array_values(array_filter((array) $request->query('statuses', [])));
        $status = $request->query('status', '');
        if (!empty($status) && empty($statuses)) {
            $statuses = [$status];
        }

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'statuses' => $statuses,
            'project_manager' => $request->query('project_manager', ''),
            'client_id' => $request->query('client_id', ''),
            'from_date' => $request->query('from_date', ''),
            'to_date' => $request->query('to_date', ''),
        ];

        $projects = $this->service->getProjects(10, $filters, $companyId, $scopedManagerId);
        $projectStats = $this->service->getProjectStatistics($filters, $companyId, $scopedManagerId);

        return view('management.projects')
            ->with('page', $page)
            ->with('managers', $managers)
            ->with('clients', $clients)
            ->with('projects', $projects)
            ->with('projectStats', $projectStats)
            ->with('filters', $filters)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Projects';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        } else {
            $managers = $this->service->getAllManagers($companyId);
            $clients = $this->service->getAllClients($companyId);

            if (count($managers) === 0) {
                return redirect()->route('employees')->with('error', __('pages.register_managers_first'));
            }
            if (count($clients) === 0) {
                return redirect()->route('clients')->with('error', __('pages.register_clients_first'));
            }

            $editProject = $this->service->getProjectById($id, $companyId);
            return view('management.projects')
                ->with('page', $page)
                ->with('managers', $managers)
                ->with('clients', $clients)
                ->with('editProject', $editProject);
        }
    }
    //---------------
    public function projectDetails(int $id)
    {
        $page = 'Projects';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        }

        if ($role === 'manager' && !$this->service->isManagedBy($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        }

        $projectDetails = $this->service->getProjectDetails($id, $companyId);
        return view('management.project-details')
            ->with('page', $page)
            ->with('projectDetails', $projectDetails)
            ->with('role', $role);
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:projects,id',
            'project_name' => 'required|string|min:3|max:255',
            'project_manager' => 'required|integer|min:1|exists:employees,id',
            'client_id' => 'required|integer|min:1|exists:clients,id',
            'location' => 'required|string|min:2|max:255',
            'start_date' => 'required|date',
            'expected_end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|min:1',
            'status' => 'required|in:planning,in_progress,completed,on_hold'
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $companyId = session('company_id');
            $data = $request->only([
                'project_name',
                'project_manager',
                'client_id',
                'location',
                'start_date',
                'expected_end_date',
                'budget',
                'description',
                'status'
            ]);
            try {
                if (!$this->service->updateProject($id, $data, $companyId)) {
                    return back()->with('error', __('pages.project_not_updated'))->withInput();
                }
                return redirect()->route('projects')->with('success', __('pages.project_updated'));
            } catch (\Exception $e) {
                return redirect()->route('projects')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        } else {
            try {
                $this->service->deleteProject($id, $companyId);
                return redirect()->route('projects')->with('success', __('pages.project_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('projects')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        }

        if ($role === 'manager' && !$this->service->isManagedBy($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return redirect()->route('projects')->with('error', __('pages.project_not_found'));
        }

        try {
            if (!$this->service->updateProjectStatus($id, $status, $companyId)) {
                return redirect()->route('project.details', $id)->with('error', __('pages.invalid_status'));
            }

            return redirect()->route('project.details', $id)->with('success', __('pages.project_status_updated'));
        } catch (\Exception $e) {
            return redirect()->route('project.details', $id)->with('error', __('pages.try_again'));
        }
    }

    //---------------
    public function setProjectContext(int $id)
    {
        if (session('active') !== true) {
            return redirect()->route('login')->with('error', __('pages.must_login_select_project'));
        }

        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return back()->with('error', __('pages.project_not_found'));
        }

        if ($role === 'manager' && !$this->service->isManagedBy($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return back()->with('error', __('pages.project_not_found'));
        }

        session()->put('project_id', $id);
        return back();
    }
}
