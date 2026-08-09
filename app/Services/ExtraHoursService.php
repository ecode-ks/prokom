<?php

namespace App\Services;

use App\Repositories\ExtraHoursRepository;

class ExtraHoursService
{
    protected int $companyId;
    protected ExtraHoursRepository $repository;
    public function __construct(ExtraHoursRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = (int) (session('company_id') ?? 0);
    }
    //---------------
    public function createExtraHour(array $data): bool
    {
        $data['company_id'] = $this->companyId;
        return $this->repository->create($data);
    }
    //---------------
    public function getExtraHours(int $limit, string $search = '')
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedExtraHours($limit, $this->companyId, $search);
        }
        return $this->repository->getExtraHours($limit, $this->companyId);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkExtraHourExist($id, $this->companyId);
    }
    //---------------
    public function getExtraHourById(int $id)
    {
        return $this->repository->findExtraHour($id, $this->companyId);
    }
    //---------------
    public function updateExtraHour(int $id, array $data): bool
    {
        $extraHour = $this->repository->findExtraHour($id, $this->companyId);
        if (!$extraHour) {
            return false;
        }
        return $this->repository->update($id, $data, $this->companyId);
    }
    //---------------
    public function deleteExtraHour(int $id): bool
    {
        $extraHour = $this->repository->findExtraHour($id, $this->companyId);
        if (!$extraHour) {
            return false;
        }
        return $this->repository->delete($id, $this->companyId);
    }
}
