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
        $this->companyId = (int) (session('company_id') ?? 0);
        $this->employeeId = (int) (session('employee_id') ?? 0);
    }
    //---------------
    public function canCheckIn(): bool
    {
        if (session('role') === 'company' && $this->companyId > 0) {
            return true;
        }

        return $this->employeeId > 0;
    }
    //---------------
    private function ownerEmployeeId(): ?int
    {
        if (session('role') === 'company') {
            return null;
        }

        return $this->employeeId > 0 ? $this->employeeId : null;
    }
    //---------------
    public function getActiveCheckIn()
    {
        if (!$this->canCheckIn()) {
            return null;
        }

        return $this->repository->getActiveCheckIn($this->ownerEmployeeId(), $this->companyId);
    }
    //---------------
    public function getTodayCheckIn()
    {
        if (!$this->canCheckIn()) {
            return null;
        }

        return $this->repository->getTodayCheckIn($this->ownerEmployeeId(), $this->companyId);
    }
    //---------------
    public function startWork(array $data): bool
    {
        if (!$this->canCheckIn()) {
            throw new \Exception('Nuk keni të drejtë për check-in.');
        }

        $data['employee_id'] = $this->ownerEmployeeId();
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
