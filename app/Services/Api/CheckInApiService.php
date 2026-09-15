<?php

namespace App\Services\Api;

use App\Repositories\CheckInRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\WorkingHoursRepository;
use Carbon\Carbon;

class CheckInApiService
{
    protected int $companyId = 0;
    protected int $employeeId = 0;
    protected ?string $actorRole = null;
    protected CheckInRepository $repository;
    protected WorkingHoursRepository $workingHoursRepository;
    protected EmployeesRepository $employeesRepository;

    public function __construct(
        CheckInRepository $repository,
        WorkingHoursRepository $workingHoursRepository,
        EmployeesRepository $employeesRepository
    ) {
        $this->repository = $repository;
        $this->workingHoursRepository = $workingHoursRepository;
        $this->employeesRepository = $employeesRepository;
    }

    public function bindActor(?string $username = null, ?int $employeeId = null, ?int $companyId = null, ?string $role = null): void
    {
        $employee = null;
        if (!empty($username)) {
            $employee = $this->employeesRepository->findEmployeeByUsername(trim($username));
        } elseif ($employeeId && $companyId) {
            $employee = $this->employeesRepository->findEmployee($employeeId, $companyId);
        }

        if (!$employee) {
            throw new \Exception(__('pages.checkin_no_permission'));
        }

        if (strcasecmp((string) ($employee->status ?? ''), 'Active') !== 0) {
            throw new \Exception(__('pages.employee_account_blocked'));
        }

        if (empty($employee->company_id)) {
            throw new \Exception(__('pages.employee_no_company'));
        }

        $this->employeeId = (int) $employee->id;
        $this->companyId = (int) $employee->company_id;
        $this->actorRole = $role ?: (string) ($employee->role ?? 'employee');
    }

    public function canCheckIn(): bool
    {
        return $this->employeeId > 0 && $this->companyId > 0;
    }

    public function startWork(array $data): bool
    {
        if (!$this->canCheckIn()) {
            throw new \Exception(__('pages.checkin_no_permission'));
        }

        $active = $this->repository->getActiveCheckIn($this->employeeId, $this->companyId);
        if ($active) {
            return true;
        }

        $data['employee_id'] = $this->employeeId;
        $data['status'] = 'Under Review';
        $data['company_id'] = $this->companyId;

        return $this->repository->create($data);
    }

    public function startBreak(array $data): bool
    {
        $checkin = $this->requireActiveCheckIn();
        return $this->repository->startBreak((int) $checkin->id, $data, $this->companyId);
    }

    public function endBreak(array $data): bool
    {
        $checkin = $this->requireActiveCheckIn();
        return $this->repository->endBreak((int) $checkin->id, $data, $this->companyId);
    }

    public function endWork(array $data): bool
    {
        $checkin = $this->requireActiveCheckIn();
        return $this->repository->endWork((int) $checkin->id, $data, $this->companyId);
    }

    private function requireActiveCheckIn()
    {
        $checkin = $this->repository->getActiveCheckIn($this->employeeId, $this->companyId);
        if (!$checkin) {
            throw new \Exception(__('pages.checkin_not_found'));
        }

        return $checkin;
    }

    public function getCheckIns(int $limit, string $search, int $companyId)
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedCheckIns($limit, $companyId, $search);
        }

        return $this->repository->getCheckIns($limit, $companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkCheckinExist($id, $companyId);
    }

    public function getCheckInById(int $id, int $companyId)
    {
        return $this->repository->findCheckInDetails($id, $companyId);
    }

    public function acceptCheckIn(int $id, int $companyId): bool
    {
        $checkin = $this->repository->findById($id, $companyId);
        if (!$checkin) {
            return false;
        }

        if (strtolower((string) ($checkin->status ?? '')) === 'accepted') {
            return true;
        }

        if (empty($checkin->check_out_time)) {
            throw new \Exception(__('pages.checkin_need_checkout'));
        }

        $this->syncToWorkingHours($checkin, $checkin->check_out_time, $companyId);

        return $this->repository->accept($id, $companyId);
    }

    public function deleteCheckIn(int $id, int $companyId): bool
    {
        if (!$this->repository->checkCheckinExist($id, $companyId)) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }

    public function getMapLocations(int $companyId): array
    {
        $rows = $this->repository->getActiveMapLocations($companyId);
        $locations = [];

        foreach ($rows as $row) {
            $onPause = !empty($row->pause_start_time) && empty($row->pause_end_time);
            $rawLocation = $onPause && !empty($row->pause_start_location)
                ? $row->pause_start_location
                : ($row->check_in_location ?? null);

            $coords = $this->parseLatLng($rawLocation);
            if (!$coords) {
                continue;
            }

            $hasCheckedOut = !empty($row->check_out_time);
            $isAccepted = strtolower((string) ($row->status ?? '')) === 'accepted';

            if ($onPause && !$hasCheckedOut) {
                $statusKey = 'pause';
            } elseif ($hasCheckedOut && !$isAccepted) {
                $statusKey = 'review';
            } else {
                $statusKey = 'work';
            }

            $statusLabel = $statusKey === 'pause'
                ? __('pages.on_pause')
                : ($statusKey === 'work' ? __('pages.at_work') : __('pages.checkin_under_review'));

            $locations[] = [
                'id' => (int) $row->id,
                'employee_id' => $row->employee_id ? (int) $row->employee_id : null,
                'name' => $row->employee,
                'phone' => $row->employee_phone ?: null,
                'role' => strtolower((string) ($row->employee_role ?? '')),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'status' => $statusLabel,
                'status_key' => $statusKey,
                'project' => $row->project_names ?: '-',
                'check_in_time' => $row->check_in_time
                    ? Carbon::parse($row->check_in_time)->format('d/m/Y H:i')
                    : '-',
            ];
        }

        return $locations;
    }

    private function parseLatLng(?string $value): ?array
    {
        if (empty($value) || !str_contains($value, ',')) {
            return null;
        }

        [$lat, $lng] = array_map('trim', explode(',', $value, 2));
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return null;
        }

        return [
            'lat' => (float) $lat,
            'lng' => (float) $lng,
        ];
    }

    private function syncToWorkingHours(object $checkin, ?string $checkOutTime, int $companyId): void
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
            'company_id' => $companyId,
        ]);
    }
}
