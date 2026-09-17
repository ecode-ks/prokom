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
        $data['reported_by'] = session('user') ?? session('company') ?? null;
        return $this->repository->create($data);
    }
    //---------------
    public function getExtraHours(int $limit, string $search = '', ?int $employeeId = null)
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedExtraHours($limit, $this->companyId, $search, $employeeId);
        }
        return $this->repository->getExtraHours($limit, $this->companyId, $employeeId);
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
        $data['reported_by'] = session('user') ?? session('company') ?? null;
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
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId): object
    {
        return $this->repository->getEmployeeExtraHoursStatistics($this->companyId, $employeeId, $fromDate, $toDate, 'Orët jashtë orarit');
    }
    //---------------
    public function generateExtraWeekendHoursReport(string $fromDate, string $toDate, int $employeeId): object
    {
        return $this->repository->getEmployeeExtraHoursStatistics($this->companyId, $employeeId, $fromDate, $toDate, 'Orët në vikend');
    }
    //---------------
    public function getEmployeeReportEntries(string $fromDate, string $toDate, int $employeeId, string $type, float $pricePerHour = 0.0, int $limit = 10, string $cursorName = 'extra_hours_cursor')
    {
        return $this->repository->getEmployeeExtraHoursEntries($this->companyId, $employeeId, $fromDate, $toDate, $type, $limit, $cursorName)
            ->through(function ($row) use ($pricePerHour) {
            $hours = (float) ($row->hours_decimal ?? 0);
            $row->price_per_hour = $pricePerHour;
            $row->value_eur = round($hours * $pricePerHour, 2);

            return $row;
            });
    }
}
