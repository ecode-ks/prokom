<?php

namespace App\Http\Controllers\API;

use App\Services\TaskReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class TaskReportsApiController extends Controller
{
    protected TaskReportsService $service;

    public function __construct(TaskReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
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
            return json_encode(['error' => __('pages.cannot_report_task')]);
        }

        return json_encode(['success' => __('pages.task_report_created')]);
    }
    //---------------
    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = trim((string) $request->query('search', ''));

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);

        if (count($tasks) === 0) {
            return json_encode(['error' => __('pages.register_tasks_first')]);
        }

        $reports = $this->service->getReports(10, $companyId, $role, $employeeId, $search);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId ? (int) $employeeId : null);

        return json_encode([
            'tasks' => $tasks,
            'reports' => $reports,
            'role' => $role,
            'reportStats' => $reportStats,
        ]);
    }
    //---------------
    public function details(int $id, int $companyId, int $employeeId, string $role)
    {

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $reportDetails = $this->service->getReportDetails($id, $companyId);

        return json_encode([
            'reportDetails' => $reportDetails,
            'role' => $role,
        ]);
    }
    //---------------
    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $tasks = $this->service->getAvailableTasks($companyId, $role, $employeeId);
        $editReport = $this->service->getReportById($id, $companyId);

        return json_encode([
            'tasks' => $tasks,
            'editReport' => $editReport,
            'role' => $role,
        ]);
    }
    //---------------
    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:task_reports,id',
            'task_id' => 'required|integer|min:1|exists:tasks,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
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
                return json_encode(['error' => __('pages.report_not_updated')]);
            }

            return json_encode(['success' => __('pages.task_report_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
    //---------------
    public function delete(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return json_encode(['success' => __('pages.task_report_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
    //---------------
    public function updateStatus(int $id, string $status, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        try {
            if (!$this->service->updateReportStatus($id, $status, $companyId)) {
                return json_encode(['error' => __('pages.invalid_status')]);
            }

            return json_encode(['success' => __('pages.report_status_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
