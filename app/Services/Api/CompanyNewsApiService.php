<?php

namespace App\Services\Api;

use App\Repositories\CompanyNewsRepository;

class CompanyNewsApiService
{
    public function __construct(protected CompanyNewsRepository $repository)
    {
    }

    public function createCompanyNews(array $data, int $companyId): bool
    {
        $data['company_id'] = $companyId;
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }
        if (empty($data['project_id'])) {
            $data['project_id'] = null;
        }

        return $this->repository->create($data);
    }

    public function getCompanyNews(int $limit, int $companyId, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedCompanyNews($limit, $search, $companyId);
        }

        return $this->repository->getCompanyNews($limit, $companyId);
    }

    public function getAllProjects(int $companyId)
    {
        return $this->repository->getAllProjects($companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkCompanyNewsExist($id, $companyId);
    }

    public function getCompanyNewsById(int $id, int $companyId)
    {
        return $this->repository->findCompanyNews($id, $companyId);
    }

    public function updateCompanyNews(int $id, int $companyId, array $data): bool
    {
        $news = $this->repository->findCompanyNews($id, $companyId);
        if (!$news) {
            return false;
        }

        if (array_key_exists('project_id', $data) && empty($data['project_id'])) {
            $data['project_id'] = null;
        }

        return $this->repository->update($id, $data);
    }

    public function deleteCompanyNews(int $id, int $companyId): bool
    {
        $news = $this->repository->findCompanyNews($id, $companyId);
        if (!$news) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
