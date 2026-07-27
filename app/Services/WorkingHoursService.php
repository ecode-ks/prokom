<?php

namespace App\Services;

use App\Repositories\WorkingHoursRepository;

class WorkingHoursService
{
    protected int $companyId;
    protected WorkingHoursRepository $repository;
    public function __construct(WorkingHoursRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = session('company_id');
    }
    //---------------
    public function createWorkingHour(array $data): bool
    {
        $data['company_id'] = session('company_id');
        return $this->repository->create($data);
    }
	//---------------
	public function getWorkingHours(int $limit, string $search = '')
	{
		$limit = (int) $limit ?: 10;
		if (!empty($search)) {
			return $this->repository->getSearchedWorkingHours($limit, $search, $this->companyId);
		}
		return $this->repository->getWorkingHours($limit, $this->companyId);
	}
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkWorkingHourExist($id, $this->companyId);
    }
    //---------------
    public function deleteWorkingHour(int $id): bool
    {
        $workingHour = $this->repository->findWorkingHour($id, $this->companyId);
        if (!$workingHour) {
            return false;
        }

        return $this->repository->delete($id, $this->companyId);
    }
}
