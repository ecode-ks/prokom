<?php

namespace App\Services;

use App\Repositories\ProjectReportsRepository;
use App\Support\StoredFiles;

class ProjectReportsService
{
    protected ProjectReportsRepository $repository;
    protected ProjectsService $projects;
    protected NotificationsService $notifications;

    public function __construct(
        ProjectReportsRepository $repository,
        ProjectsService $projects,
        NotificationsService $notifications
    ) {
        $this->repository = $repository;
        $this->projects = $projects;
        $this->notifications = $notifications;
    }
    //---------------
    public function createReport(array $data): bool
    {
        if (!empty($data['images'])) {
            $data['images'] = $this->storeImages($data['images']);
        } else {
            $data['images'] = null;
        }

        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        $reportId = $this->repository->create($data);
        if ($reportId <= 0) {
            return false;
        }

        $companyId = (int) ($data['company_id'] ?? 0);
        $projectId = (int) ($data['project_id'] ?? 0);
        $project = $projectId > 0 ? $this->projects->getProjectById($projectId, $companyId) : null;
        $projectName = $project?->project_name ?? $this->notifications->line($companyId, 'pages.project_word');

        try {
            $this->notifications->notify(
                $companyId,
                'company',
                $companyId,
                'project_report',
                'pages.notif_project_report',
                'pages.notif_project_report_body',
                route('project.report.details', $reportId),
                ['name' => $projectName]
            );

            if (!empty($project?->project_manager)) {
                $this->notifications->notify(
                    $companyId,
                    'employee',
                    (int) $project->project_manager,
                    'project_report',
                    'pages.notif_project_report',
                    'pages.notif_project_report_body',
                    route('project.report.details', $reportId),
                    ['name' => $projectName]
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Project report notify failed: '.$e->getMessage());
        }

        return true;
    }
    //---------------
    public function getReports(int $limit, int $companyId, string $search = '', string $role = '', ?int $employeeId = null)
    {
        $limit = $limit ?: 10;

        if ($role === 'manager' && $employeeId) {
            return $this->repository->getReportsForManager($limit, $companyId, $employeeId, $search);
        }

        return $this->repository->getReports($limit, $companyId, $search);
    }
    //---------------
    public function getAllProjects(int $companyId, ?int $managerId = null)
    {
        return $this->projects->getAllProjects($companyId, $managerId);
    }
    //---------------
    public function managerOwnsReport(int $reportId, int $companyId, int $managerId): bool
    {
        $report = $this->repository->findReport($reportId, $companyId);
        if (!$report) {
            return false;
        }

        return $this->projects->isManagedBy((int) $report->project_id, $companyId, $managerId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkReportExist($id, $companyId);
    }
    //---------------
    public function getReportById(int $id, int $companyId)
    {
        return $this->repository->findReport($id, $companyId);
    }
    //---------------
    public function getReportDetails(int $id, int $companyId)
    {
        return $this->repository->getReportDetails($id, $companyId);
    }
    //---------------
    public function projectExists(int $projectId, int $companyId): bool
    {
        return $this->projects->findOrFail($projectId, $companyId);
    }
    //---------------
    public function updateReport(int $id, array $data, int $companyId): bool
    {
        $report = $this->repository->findReport($id, $companyId);
        if (!$report) {
            return false;
        }

        if (!empty($data['images'])) {
            StoredFiles::delete($report->images ?? null);
            $data['images'] = $this->storeImages($data['images']);
        } else {
            unset($data['images']);
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteReport(int $id, int $companyId): bool
    {
        $report = $this->repository->findReport($id, $companyId);
        if (!$report) {
            return false;
        }

        StoredFiles::delete($report->images ?? null);

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function deleteStoredFilesForCompany(int $companyId): void
    {
        StoredFiles::deleteMany($this->repository->getImagePayloadsByCompany($companyId));
    }
    //---------------
    public function deleteStoredFilesForProject(int $projectId, int $companyId): void
    {
        StoredFiles::deleteMany($this->repository->getImagePayloadsByProject($projectId, $companyId));
    }
    //---------------
    public function updateReportStatus(int $id, string $status, int $companyId): bool
    {
        if (!$this->repository->checkReportExist($id, $companyId)) {
            return false;
        }

        $allowedStatuses = ['active', 'under_review', 'resolved', 'closed'];
        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        return $this->repository->updateStatus($id, $status, $companyId);
    }
    //---------------
    public function getReportStatistics(int $companyId, string $role = '', ?int $employeeId = null): object
    {
        $managerId = ($role === 'manager' && $employeeId) ? $employeeId : null;

        return $this->repository->getReportStatistics($companyId, $managerId);
    }
    //---------------
    public function getManagerPendingReports(int $managerId, int $companyId, int $limit = 4)
    {
        if ($managerId <= 0 || $companyId <= 0) {
            return collect();
        }

        return $this->repository->getManagerPendingReports($managerId, $companyId, $limit);
    }
    //---------------
    private function storeImages(array $images): string
    {
        $paths = [];

        foreach ($images as $image) {
            if (!is_object($image) || !method_exists($image, 'storeAs')) {
                continue;
            }

            $fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . time() . '_' . uniqid()
                . '.' . $image->getClientOriginalExtension();

            $paths[] = $image->storeAs('project-reports', $fileName, 'public');
        }

        return json_encode($paths);
    }
}
