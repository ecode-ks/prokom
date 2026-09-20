<?php

namespace App\Http\Controllers\API;

use App\Services\Api\ProjectReportsApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ProjectReportsApiController extends Controller
{
    protected ProjectReportsApiService $service;

    public function __construct(ProjectReportsApiService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->isManager($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->only(['project_id', 'report']), [
            'project_id' => 'required|integer',
            'report' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $projectId = (int) $request->project_id;
        if (!$this->service->getAllProjects($companyId, $employeeId)->contains('id', $projectId)) {
            return json_encode(['error' => __('pages.report_own_projects_only')]);
        }

        $data = [
            'project_id' => $projectId,
            'report' => trim((string) $request->report),
            'images' => $this->uploadedImages($request),
            'reported_by' => $employeeId,
            'company_id' => $companyId,
            'status' => 'active',
            'date' => now(),
        ];

        try {
            $this->service->createReport($data);
            return json_encode(['success' => __('pages.project_report_created')]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Project report create failed: '.$e->getMessage(), [
                'exception' => $e,
            ]);
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $search = trim((string) $request->query('search', ''));
        $managerId = $role === 'manager' ? $employeeId : null;

        return json_encode([
            'projects' => $this->service->getAllProjects($companyId, $managerId),
            'reports' => $this->service->getReports(10, $companyId, $search, $role, $employeeId),
            'reportStats' => $this->service->getReportStatistics($companyId, $role, $employeeId),
            'role' => $role,
        ]);
    }

    public function reportDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
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
        if (!$this->isManager($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId) || !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        return json_encode([
            'projects' => $this->service->getAllProjects($companyId, $employeeId),
            'editReport' => $this->service->getReportById($id, $companyId),
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->isManager($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $validator = Validator::make($request->only(['id', 'project_id', 'report']), [
            'id' => 'required|integer|exists:project_reports,id',
            'project_id' => 'required|integer',
            'report' => 'required|string',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;
        $projectId = (int) $request->project_id;

        if (!$this->service->findOrFail($id, $companyId) || !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        if (!$this->service->getAllProjects($companyId, $employeeId)->contains('id', $projectId)) {
            return json_encode(['error' => __('pages.report_own_projects_only')]);
        }

        $data = [
            'project_id' => $projectId,
            'report' => trim((string) $request->report),
        ];
        if ($request->hasFile('images')) {
            $data['images'] = $this->uploadedImages($request);
        }

        try {
            if (!$this->service->updateReport($id, $data, $companyId)) {
                return json_encode(['error' => __('pages.report_not_updated')]);
            }

            return json_encode(['success' => __('pages.project_report_updated')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function delete(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->isManager($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId) || !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return json_encode(['success' => __('pages.project_report_deleted')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }

    public function updateStatus(int $id, string $status, int $companyId, int $employeeId, string $role)
    {
        if (!$this->isManager($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId) || !$this->service->managerOwnsReport($id, $companyId, $employeeId)) {
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

    private function isManager(string $role): bool
    {
        return $role === 'manager';
    }

    private function canView(string $role): bool
    {
        return in_array($role, ['manager', 'company'], true);
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
