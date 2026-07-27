<?php

namespace App\Services;

use App\Repositories\CheckInRepository;

class CheckInService
{
    protected int $companyId;
    protected int $employeeId;
    protected CheckInRepository $repository;
    public function __construct(CheckInRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = session('company_id');
        $this->employeeId = session('employee_id') ?? 0;
    }
    //---------------
    public function getActiveCheckIn()
    {
        $userId = session('user_id') ?? 0;
        return $this->repository->getActiveCheckIn($userId, $this->companyId);
    }
    //---------------
    public function startWork(array $data): bool
    {
        $data['employee_id'] = $this->employeeId;
        $data['status'] = 'Under Review';
        $data['company_id'] = $this->companyId;
        return $this->repository->create($data);
    }
    //---------------
    public function startBreak(int $id, array $data): bool
    {
        $checkin = $this->repository->checkCheckinExist($id, $this->companyId);
        if (!$checkin) {
            return false;
        }
        return $this->repository->startBreak($id, $data, $this->companyId);
    }
    //---------------
    public function endBreak(int $id, array $data): bool
    {
        $checkin = $this->repository->checkCheckinExist($id, $this->companyId);
        if (!$checkin) {
            return false;
        }
        return $this->repository->endBreak($id, $data, $this->companyId);
    }
    //---------------
    public function endWork(int $id, array $data): bool
    {
        $checkin = $this->repository->checkCheckinExist($id, $this->companyId);
        if (!$checkin) {
            return false;
        }
        return $this->repository->endWork($id, $data, $this->companyId);
    }
}
