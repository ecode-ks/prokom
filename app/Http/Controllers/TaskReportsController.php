<?php

namespace App\Http\Controllers;

use App\Services\TaskReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskReportsController extends Controller
{
    protected TaskReportsService $service;

    public function __construct(TaskReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        if (!$this->hasModuleAccess()) {
            return redirect()->route('login')->with('error', 'You must be logged in as company, manager or employee to access this page.');
        }

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:3',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp,heic,heif|max:4096',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task.reports')->with('error', $validator->errors()->first())->withInput();
        }

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $images[] = $imageFile->store('task-reports', 'public');
            }
        }

        $data = [
            'task_id' => (int) $request->task_id,
            'report' => trim((string) $request->report),
            'reported_by' => (int) session('user_id'),
            'images' => $images,
        ];
        $data['company_id'] = session('company_id');

        if (!$this->service->createReport($data))
            return redirect()->route('task.reports')->with('error', 'You can only report tasks assigned to you.')->withInput();
        return redirect()->route('task.reports')->with('success', 'Task report created successfully.');
    }
    //---------------
    public function read(Request $request)
    {
        if (!$this->hasModuleAccess()) {
            return redirect()->route('login')->with('error', 'You must be logged in as company, manager or employee to access this page.');
        }

        $page = 'Task Reports';
        $companyId = session('company_id');
        $userId = session('user_id');
        $role = session('role');
        $displayName = session('user');
        $username = session('username');

        $tasks = $this->service->getAvailableTasks($userId, $role, $displayName, $username, $companyId);
        $reports = $this->service->getReports(10, $userId, $role, $displayName, $username, $companyId);

        return view('management.task-reports')
            ->with('page', $page)
            ->with('tasks', $tasks)
            ->with('reports', $reports)
            ->with('role', $role);
    }
    //---------------
    public function details(int $id)
    {
        if (!$this->hasModuleAccess()) {
            return redirect()->route('login')->with('error', 'You must be logged in as company, manager or employee to access this page.');
        }

        $companyId = session('company_id');
        $userId = session('user_id');
        $role = session('role');
        $displayName = session('user');
        $username = session('username');

        if (!$this->service->canAccessReport($id, $userId, $role, $displayName, $username, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Task report not found.');
        }

        $page = 'Task Reports';
        $reportDetails = $this->service->getReportDetails($id, $userId, $role, $displayName, $username, $companyId);

        return view('management.task-reports-details')
            ->with('page', $page)
            ->with('reportDetails', $reportDetails)
            ->with('role', $role);
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        if (!$this->hasModuleAccess()) {
            return redirect()->route('login')->with('error', 'You must be logged in as company, manager or employee to access this page.');
        }

        $companyId = session('company_id');
        $userId = session('user_id');
        $role = session('role');
        $displayName = session('user');
        $username = session('username');

        if (!$this->service->canAccessReport($id, $userId, $role, $displayName, $username, $companyId)) {
            return redirect()->route('task.reports')->with('error', 'Task report not found.');
        }

        if (!$this->service->updateReportStatus($id, $status, $companyId)) {
            return redirect()->route('task.report.details', $id)->with('error', 'Invalid status update request.');
        }

        return redirect()->route('task.report.details', $id)->with('success', 'Task report status updated successfully.');
    }
    //---------------
    protected function hasModuleAccess(): bool
    {
        return session('active') === true && in_array((string) session('role'), ['company', 'manager', 'employee'], true);
    }
}
