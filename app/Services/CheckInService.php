<?php

namespace App\Services;

use App\Repositories\CheckInRepository;
use App\Repositories\WorkingHoursRepository;
use Carbon\Carbon;

class CheckInService
{
    protected int $companyId;
    protected int $employeeId;
    protected CheckInRepository $repository;
    protected WorkingHoursRepository $workingHoursRepository;

    public function __construct(CheckInRepository $repository, WorkingHoursRepository $workingHoursRepository)
    {
        $this->repository = $repository;
        $this->workingHoursRepository = $workingHoursRepository;
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
        $checkin = $this->repository->findById($id, $this->companyId);
        if (!$checkin) {
            return false;
        }

        $ok = $this->repository->endWork($id, $data, $this->companyId);
        if (!$ok) {
            return false;
        }

        $this->syncToWorkingHours($checkin, $data['check_out_time'] ?? null);

        return true;
    }
    //---------------
    private function syncToWorkingHours(object $checkin, ?string $checkOutTime): void
    {
        if (empty($checkin->check_in_time) || empty($checkOutTime)) {
            return;
        }

        $checkInAt = Carbon::parse($checkin->check_in_time);
        $checkOutAt = Carbon::parse($checkOutTime);

        $pauseSeconds = 0;
        if (!empty($checkin->pause_start_time)) {
            $pauseStart = Carbon::parse($checkin->pause_start_time);
            $pauseEnd = !empty($checkin->pause_end_time)
                ? Carbon::parse($checkin->pause_end_time)
                : $checkOutAt;
            $pauseSeconds = max(0, $pauseStart->diffInSeconds($pauseEnd));
        }

        $workedSeconds = max(0, $checkInAt->diffInSeconds($checkOutAt) - $pauseSeconds);
        $hours = sprintf(
            '%02d:%02d',
            intdiv($workedSeconds, 3600),
            intdiv($workedSeconds % 3600, 60)
        );

        $this->workingHoursRepository->create([
            'date' => $checkInAt->toDateString(),
            'employee_id' => $checkin->employee_id ?: null,
            'check_in' => $checkin->check_in_time,
            'check_out' => $checkOutTime,
            'hours' => $hours,
            'company_id' => $this->companyId,
        ]);
    }
}
