<?php

namespace App\Services;

use App\Repositories\ProjectReportsRepository;
use Illuminate\Support\Facades\Storage;

class ProjectReportsService
{
    protected ProjectReportsRepository $repository;
    protected ProjectsService $projects;

    public function __construct(ProjectReportsRepository $repository, ProjectsService $projects)
    {
        $this->repository = $repository;
        $this->projects = $projects;
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

        return $this->repository->create($data);
    }
    //---------------
    public function getReports(int $limit, int $companyId)
    {
        $limit = $limit ?: 10;

        return $this->repository->getReports($limit, $companyId);
    }
    //---------------
    public function getAllProjects(int $companyId)
    {
        return $this->projects->getAllProjects($companyId);
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
            if (!empty($report->images)) {
                $oldFiles = json_decode($report->images, true);

                if (is_array($oldFiles)) {
                    Storage::disk('public')->delete($oldFiles);
                }
            }

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

        if (!empty($report->images)) {
            $files = json_decode($report->images, true);

            if (is_array($files)) {
                Storage::disk('public')->delete($files);
            }
        }

        return $this->repository->delete($id, $companyId);
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
    private function storeImages(array $images): string
    {
        $paths = [];

        foreach ($images as $image) {
            $fileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . time() . '_' . uniqid()
                . '.' . $image->getClientOriginalExtension();

            $paths[] = $image->storeAs('project-reports', $fileName, 'public');
        }

        return json_encode($paths);
    }
}
