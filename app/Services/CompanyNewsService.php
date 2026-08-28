<?php

namespace App\Services;

use App\Repositories\CompanyNewsRepository;

class CompanyNewsService
{
	protected CompanyNewsRepository $repository;
	public function __construct(CompanyNewsRepository $repository)
	{
		$this->repository = $repository;
	}
	//---------------
	public function createCompanyNews(array $data): bool
	{
		$data['company_id'] = (int) (session('company_id') ?? 0);
		if (empty($data['date'])) {
			$data['date'] = now()->toDateString();
		}
		return $this->repository->create($data);
	}
	//---------------
	public function getCompanyNews(int $limit, int $companyId, string $search = '')
	{
		$limit = (int) $limit ?: 10;
		if (!empty($search)) {
			return $this->repository->getSearchedCompanyNews($limit, $search, $companyId);
		}
		return $this->repository->getCompanyNews($limit, $companyId);
	}
	//---------------
	public function getAllProjects(int $companyId)
	{
		return $this->repository->getAllProjects($companyId);
	}
	//---------------
	public function findOrFail(int $id, int $companyId): bool
	{
		return $this->repository->checkCompanyNewsExist($id, $companyId);
	}
	//---------------
	public function getCompanyNewsById(int $id, int $companyId)
	{
		return $this->repository->findCompanyNews($id, $companyId);
	}
	//---------------
	public function updateCompanyNews(int $id, int $companyId, array $data): bool
	{
		$news = $this->repository->findCompanyNews($id, $companyId);
		if (!$news) {
			return false;
		}
		return $this->repository->update($id, $data);
	}
	//---------------
	public function deleteCompanyNews(int $id, int $companyId): bool
	{
		$news = $this->repository->findCompanyNews($id, $companyId);
		if (!$news) {
			return false;
		}
		return $this->repository->delete($id);
	}
	//---------------
	public function getLatestForDashboard(int $companyId, int $limit = 5)
	{
		if ($companyId <= 0) {
			return collect();
		}

		return $this->repository->getLatestForDashboard($companyId, $limit);
	}
}
