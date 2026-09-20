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
        if (!$this->canMutate($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->only(['task_id', 'report']), [
            'task_id' => 'required|integer',
            'report' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $taskId = (int) $request->task_id;
        if (!$this->service->getAvailableTasks($companyId, $role, $employeeId)->contains('id', $taskId)) {
            return json_encode(['error' => __('pages.cannot_report_task')]);
        }

        $data = [
            'task_id' => $taskId,
            'report' => trim((string) $request->report),
            'images' => $this->uploadedImages($request),
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        try {
            $this->service->createReport($data);
            return json_encode(['success' => __('pages.task_report_created')]);
        } catch (\Throwable $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canUse($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $search = trim((string) $request->query('search', ''));

        return json_encode([
            'tasks' => $this->canMutate($role) ? $this->service->getAvailableTasks($companyId, $role, $employeeId) : collect(),
            'reports' => $this->service->getReports(10, $companyId, $role, $employeeId, $search),
            'reportStats' => $this->service->getReportStatistics($companyId, $role, $employeeId ?: null),
            'canCreate' => $this->canMutate($role),
            'role' => $role,
        ]);
    }

    public function details(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canUse($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        if ($role === 'manager' && !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        return json_encode([
            'reportDetails' => $this->service->getReportDetails($id, $companyId),
            'role' => $role,
        ]);
    }

    public function edit(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canMutate($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId) || !$this->canEditReport($role, $id, $companyId, $employeeId)) {
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
        if (!$this->canMutate($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->only(['id', 'task_id', 'report']), [
            'id' => 'required|integer|exists:task_reports,id',
            'task_id' => 'required|integer',
            'report' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        if (!$this->service->findOrFail($id, $companyId) || !$this->canEditReport($role, $id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $data = [
            'task_id' => $request->task_id,
            'report' => trim((string) $request->report),
        ];
        if ($request->hasFile('images')) {
            $data['images'] = $this->uploadedImages($request);
        }

        try {
            if (!$this->service->updateReport($id, $data, $companyId)) {
                return json_encode(['error' => __('pages.report_not_updated')]);
            }

            return json_encode(['success' => __('pages.task_report_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canMutate($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId) || !$this->canEditReport($role, $id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return json_encode(['success' => __('pages.task_report_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function updateStatus(int $id, string $status, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canMutate($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        if ($role === 'manager' && !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
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

    private function canUse(string $role): bool
    {
        return in_array($role, ['company', 'manager', 'employee'], true);
    }

    private function canMutate(string $role): bool
    {
        return in_array(strtolower($role), ['manager', 'employee'], true);
    }

    private function canEditReport(string $role, int $id, int $companyId, int $employeeId): bool
    {
        $role = strtolower($role);
        $report = $this->service->getReportById($id, $companyId);
        if (!$report) {
            return false;
        }

        if ($role === 'employee') {
            return (int) ($report->reported_by ?? 0) === (int) $employeeId;
        }

        if ($role === 'manager') {
            return $this->service->managerOwnsReport($id, $companyId, $employeeId);
        }

        return false;
    }

    private function uploadedImages(Request $request): array
    {
        $images = $request->file('images', []);
        if (!is_array($images)) {
            $images = $images ? [$images] : [];
        }

        return array_values(array_filter($images, function ($file) {
            return $file && method_exists($file, 'isValid') && $file->isValid();
        }));
    }
}
