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
		return $this->repository->create($data);
	}
	//---------------
	public function getCompanyNews(int $limit, string $search = '')
	{
		$limit = (int) $limit ?: 10;
		if (!empty($search)) {
			return $this->repository->getSearchedCompanyNews($limit, $search);
		}
		return $this->repository->getCompanyNews($limit);
	}
	//---------------
	public function getAllProjects()
	{
		return $this->repository->getAllProjects();
	}
	//---------------
	public function findOrFail(int $id): bool
	{
		return $this->repository->checkCompanyNewsExist($id);
	}
	//---------------
	public function getCompanyNewsById(int $id)
	{
		return $this->repository->findCompanyNews($id);
	}
	//---------------
	public function updateCompanyNews(int $id, array $data): bool
	{
		$news = $this->repository->findCompanyNews($id);
		if (!$news) {
			return false;
		}
		return $this->repository->update($id, $data);
	}
	//---------------
	public function deleteCompanyNews(int $id): bool
	{
		$news = $this->repository->findCompanyNews($id);
		if (!$news) {
			return false;
		}
		return $this->repository->delete($id);
	}
}
