<?php

namespace App\Http\Controllers\API;

use App\Services\ProjectReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class ProjectReportsApiController extends Controller
{
    protected ProjectReportsService $service;

    public function __construct(ProjectReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request, int $companyId, int $employeeId, string $role)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        if (!$this->service->projectExists((int) $request->project_id, $companyId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        if ($role === 'manager' && !$this->service->getAllProjects($companyId, $employeeId)->contains('id', (int) $request->project_id)) {
            return json_encode(['error' => __('pages.report_own_projects_only')]);
        }

        $data = [
            'project_id' => (int) $request->project_id,
            'report' => $request->report,
            'images' => $request->file('images', []),
            'reported_by' => session('user_id'),
            'company_id' => $companyId,
            'status' => 'active',
        ];

        try {
            $this->service->createReport($data);
            return json_encode(['success' => __('pages.project_report_created')]);
        } catch (\Exception $e) {
            return json_encode(['error' => __('pages.try_again')]);
        }
    }
    //---------------
    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        $search = trim((string) $request->query('search', ''));
        $scopedManagerId = $role === 'manager' ? (int) ($employeeId ?? 0) : null;
        $projects = $this->service->getAllProjects($companyId, $scopedManagerId);

        if (count($projects) === 0) {
            return json_encode(['error' => __('pages.register_projects_first')]);
        }

        $reports = $this->service->getReports(10, $companyId, $search, $role, $employeeId);
        $reportStats = $this->service->getReportStatistics((int) $companyId, $role, $employeeId);

        return json_encode([
            'projects' => $projects,
            'reports' => $reports,
            'reportStats' => $reportStats,
            'role' => $role,
        ]);
    }
    //---------------
    public function reportDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        if ($role === 'manager' && !$this->service->managerOwnsReport($id, (int) $companyId, (int) ($employeeId ?? 0))) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $reportDetails = $this->service->getReportDetails($id, $companyId);

        return json_encode([
            'reportDetails' => $reportDetails,
            'role' => $role,
        ]);
    }
    //---------------
    public function edit(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        $projects = $this->service->getAllProjects($companyId);
        if (count($projects) === 0) {
            return json_encode(['error' => __('pages.register_projects_first')]);
        }

        $editReport = $this->service->getReportById($id, $companyId);

        return json_encode([
            'projects' => $projects,
            'editReport' => $editReport,
        ]);
    }
    //---------------
    public function update(Request $request, int $companyId)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1|exists:project_reports,id',
            'project_id' => 'required|integer|min:1|exists:projects,id',
            'report' => 'required|string|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);

        if ($validator->fails()) {
            return json_encode(['error' => __('pages.fields_invalid')]);
        }

        $id = (int) $request->id;

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        if (!$this->service->projectExists((int) $request->project_id, $companyId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        $data = [
            'project_id' => (int) $request->project_id,
            'report' => $request->report,
        ];

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
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
    //---------------
    public function delete(int $id, int $companyId)
    {
        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.report_not_found')]);
        }

        try {
            $this->service->deleteReport($id, $companyId);
            return json_encode(['success' => __('pages.project_report_deleted')]);
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
