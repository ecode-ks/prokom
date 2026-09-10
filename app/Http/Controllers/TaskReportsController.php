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
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ?  session('employee_id') : null;

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task.reports')->with('error', __('app.fields_invalid'))->withInput();
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim( $request->report),
            'images' => $request->hasFile('images') ? $request->file('images') : [],
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        if (!$this->service->createReport($data, $role, $employeeId, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.cannot_report_task'))->withInput();
        }

        return redirect()->route('task.reports')->with('success', __('pages.task_report_created'));
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Task Reports';
        $search = trim((string) $request->query('search', ''));
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);

        if (count($tasks) === 0) {
            return redirect()->route('tasks')->with('error', __('pages.register_tasks_first'));
        }

        $reports = $this->service->getReports(10, $companyId, $role, $employeeId, $search);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId ? (int) $employeeId : null);

        return view('management.task-reports')
            ->with('page', $page)
            ->with('tasks', $tasks)
            ->with('reports', $reports)
            ->with('role', $role)
            ->with('reportStats', $reportStats);
    }
    //---------------
    public function details(int $id)
    {
        $page = 'Task Reports';
        $companyId = session('company_id');
        $role = session('role');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        $reportDetails = $this->service->getReportDetails($id, $companyId);

        return view('management.task-reports-details')
            ->with('page', $page)
            ->with('reportDetails', $reportDetails)
            ->with('role', $role);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Task Reports';
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);
        $editReport = $this->service->getReportById($id, $companyId);

        return view('management.task-reports')
            ->with('page', $page)
            ->with('tasks', $tasks)
            ->with('editReport', $editReport)
            ->with('role', $role);
    }
    //---------------
    public function update(Request $request)
    {
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? session('employee_id') : null;

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:task_reports,id',
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('app.fields_invalid'))->withInput();
        }

        $id = $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim( $request->report),
        ];

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        try {
            if (!$this->service->updateReport($id, $data, $role, $employeeId, $companyId)) {
                return back()->with('error', __('pages.report_not_updated'))->withInput();
            }

            return redirect()->route('task.reports')->with('success', __('pages.task_report_updated'));
        } catch (\Exception $e) {
            return redirect()->route('task.reports')->with('error', __('app.try_again'));
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return redirect()->route('task.reports')->with('success', __('pages.task_report_deleted'));
        } catch (\Exception $e) {
            return redirect()->route('task.reports')->with('error', __('app.try_again'));
        }
    }
    //---------------
    public function updateStatus(int $id, string $status)
    {
        $companyId = session('company_id');

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        try {
            if (!$this->service->updateReportStatus($id, $status, $companyId)) {
                return redirect()->route('task.report.details', $id)->with('error', __('app.invalid_status'));
            }

            return redirect()->route('task.report.details', $id)->with('success', __('pages.report_status_updated'));
        } catch (\Exception $e) {
            return redirect()->route('task.report.details', $id)->with('error', __('app.try_again'));
        }
    }
}
