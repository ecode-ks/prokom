<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Api\ProjectsApiService;
use Illuminate\Http\Request;

class ProjectsApiController extends Controller
{
    protected ProjectsApiService $service;

    public function __construct(ProjectsApiService $service)
    {
        $this->service = $service;
    }

    public function read(Request $request, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        $scopedManagerId = $role === 'manager' ? (int) $employeeId : null;
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

        return json_encode([
            'managers' => $this->service->getAllManagers($companyId),
            'clients' => $this->service->getAllClients($companyId),
            'projects' => $this->service->getProjects(10, $filters, $companyId, $scopedManagerId),
            'projectStats' => $this->service->getProjectStatistics($filters, $companyId, $scopedManagerId),
            'filters' => $filters,
            'statuses' => $this->service->getStatuses(),
            'role' => $role,
        ]);
    }

    public function projectDetails(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        if ($role === 'manager' && !$this->service->isManagedBy($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        $projectDetails = $this->service->getProjectDetails($id, $companyId);
        if (!$projectDetails) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        $projectDetails->budget = $projectDetails->budget !== null
            ? round((float) $projectDetails->budget, 2)
            : null;

        return json_encode([
            'projectDetails' => $projectDetails,
            'statuses' => $this->service->getStatuses(),
            'role' => $role,
        ]);
    }

    public function setProjectContext(int $id, int $companyId, int $employeeId, string $role)
    {
        if (!$this->canView($role)) {
            return json_encode(['error' => __('pages.no_access')]);
        }

        if (!$this->service->findOrFail($id, $companyId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        if ($role === 'manager' && !$this->service->isManagedBy($id, $companyId, $employeeId)) {
            return json_encode(['error' => __('pages.project_not_found')]);
        }

        return json_encode([
            'success' => true,
            'project_id' => $id,
            'role' => $role,
        ]);
    }

    private function canView(string $role): bool
    {
        return in_array($role, ['company', 'manager'], true);
    }
}
