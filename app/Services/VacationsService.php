<?php

namespace App\Services;

use App\Repositories\VacationsRepository;

class VacationsService
{
    protected int $companyId;
    protected VacationsRepository $repository;

    public function __construct(VacationsRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = session('company_id');
    }
    //---------------
    public function createVacation(array $data): bool
    {
        $data['company_id'] = $this->companyId;

        return $this->repository->create($data);
    }
    //---------------
    public function getVacations(int $limit, string $search = '', array $statuses = [])
    {
        $limit = $limit ?: 10;

        if (!empty($search) || !empty($statuses)) {
            return $this->repository->getSearchedVacations($limit, $this->companyId, $search, $statuses);
        }

        return $this->repository->getVacations($limit, $this->companyId);
    }
    //---------------
    public function getVacationStatistics(string $search = ''): object
    {
        return $this->repository->getVacationStatistics($this->companyId, $search);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkVacationExist($id, $this->companyId);
    }
    //---------------
    public function getVacationById(int $id)
    {
        return $this->repository->findVacation($id, $this->companyId);
    }
    //---------------
    public function updateVacation(int $id, array $data): bool
    {
        $vacation = $this->repository->findVacation($id, $this->companyId);
        if (!$vacation) {
            return false;
        }

        return $this->repository->update($id, $data, $this->companyId);
    }
    //---------------
    public function deleteVacation(int $id): bool
    {
        $vacation = $this->repository->findVacation($id, $this->companyId);
        if (!$vacation) {
            return false;
        }

        return $this->repository->delete($id, $this->companyId);
    }
    //---------------
    public function updateVacationStatus(int $id, string $status): bool
    {
        $vacation = $this->repository->findVacation($id, $this->companyId);
        if (!$vacation) {
            return false;
        }

        if ($vacation->status === $status) {
            return true;
        }

        return $this->repository->updateStatus($id, $status, $this->companyId);
    }
}
