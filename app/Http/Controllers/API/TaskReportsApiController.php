<?php

namespace App\Http\Controllers\API;

use App\Services\Api\TaskReportsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class TaskReportsApiController extends Controller
{
    protected TaskReportsApiService $service;

    public function __construct(TaskReportsApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer|exists:tasks,id',
            'report' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim((string) $request->report),
            'images' => $request->hasFile('images') ? $request->file('images') : [],
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        try {
            if (!$this->service->createReport($data, $role, $employeeId, $companyId)) {
                return json_encode(['error' => __('pages.cannot_report_task')]);
            }

            return json_encode(['success' => __('pages.task_report_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = trim((string) $request->query('search', ''));

        return json_encode([
            'tasks' => $this->service->getAvailableTasks($companyId, $role, $employeeId),
            'reports' => $this->service->getReports(10, $companyId, $role, $employeeId, $search),
            'reportStats' => $this->service->getReportStatistics($companyId, $role, $employeeId ?: null),
            'role' => $role,
        ]);
    }

    public function details(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        return json_encode([
            'reportDetails' => $this->service->getReportDetails($id, $companyId),
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        return json_encode([
            'tasks' => $this->service->getAvailableTasks($companyId, $role, $employeeId),
            'editReport' => $this->service->getReportById($id, $companyId),
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_reports,id',
            'task_id' => 'required|integer|exists:tasks,id',
            'report' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim((string) $request->report),
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

    public function updateStatus(int $id, string $status, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $normalizedStatus = $this->service->resolveStatus($status);
        if (!$normalizedStatus) {
            return json_encode(['error' => __('pages.invalid_status')]);
        }

        try {
            if (!$this->service->updateReportStatus($id, $normalizedStatus, $companyId)) {
                return json_encode(['error' => __('pages.invalid_status')]);
            }

            return json_encode(['success' => __('pages.report_status_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
}
