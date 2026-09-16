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
        $role = strtolower(trim((string) session('role')));
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        $images = $request->file('images', []);
        if (!is_array($images)) {
            $images = $images ? [$images] : [];
        }
        $images = array_values(array_filter($images, function ($file) {
            return $file instanceof \Illuminate\Http\UploadedFile && $file->isValid();
        }));
        $request->files->set('images', $images);

        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->route('task.reports')->with('error', __('pages.fields_invalid'))->withInput();
        }

        if ($role !== 'company') {
            if (!$this->service->taskExists((int) $request->task_id, $companyId)) {
                return redirect()->route('task.reports')->with('error', __('pages.cannot_report_task'))->withInput();
            }

            if (in_array($role, ['manager', 'employee'], true)
                && !$this->service->getAvailableTasks($companyId, $role, $employeeId)->contains('id', (int) $request->task_id)) {
                return redirect()->route('task.reports')->with('error', __('pages.cannot_report_task'))->withInput();
            }
        }

        $data = [
            'task_id' => (int) $request->task_id,
            'report' => $request->report,
            'images' => $images,
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        try {
            $this->service->createReport($data);
            return redirect()->route('task.reports')->with('success', __('pages.task_report_created'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Task report create failed: '.$e->getMessage());
            return redirect()->route('task.reports')->with('error', __('pages.try_again'))->withInput();
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Task Reports';
        $search = trim((string) $request->query('search', ''));
        $companyId = session('company_id');
        $role = session('role');
        $employeeId = session('employee_id') ? (int) session('employee_id') : null;

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);

        if (count($tasks) === 0) {
            return redirect()->route('tasks')->with('error', __('pages.register_tasks_first'));
        }

        $reports = $this->service->getReports(10, $companyId, $role, $employeeId, $search);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId);

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
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:task_reports,id',
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        }

        $id = (int) $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('task.reports')->with('error', __('pages.report_not_found'));
        }

        if (session('role') !== 'company' && !$this->service->taskExists((int) $request->task_id, $companyId)) {
            return back()->with('error', __('pages.cannot_report_task'))->withInput();
        }

        $data = [
            'task_id' => (int) $request->task_id,
            'report' => $request->report,
        ];

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        try {
            if (!$this->service->updateReport($id, $data, $companyId)) {
                return back()->with('error', __('pages.report_not_updated'))->withInput();
            }

            return redirect()->route('task.reports')->with('success', __('pages.task_report_updated'));
        } catch (\Exception $e) {
            return redirect()->route('task.reports')->with('error', __('pages.try_again'));
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
            return redirect()->route('task.reports')->with('error', __('pages.try_again'));
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
                return redirect()->route('task.report.details', $id)->with('error', __('pages.invalid_status'));
            }

            return redirect()->route('task.report.details', $id)->with('success', __('pages.report_status_updated'));
        } catch (\Exception $e) {
            return redirect()->route('task.report.details', $id)->with('error', __('pages.try_again'));
        }
    }
}
