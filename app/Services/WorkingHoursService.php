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
        $this->companyId = (session('company_id') ?? 0);
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
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId): object
    {
        return $this->repository->getEmployeeWorkingHoursStatistics($this->companyId, $employeeId, $fromDate, $toDate);
    }
    //---------------
    public function getEmployeeReportEntries(string $fromDate, string $toDate, int $employeeId, int $limit = 10, string $cursorName = 'working_hours_cursor')
    {
        return $this->repository->getEmployeeWorkingHoursEntries($this->companyId, $employeeId, $fromDate, $toDate, $limit, $cursorName);
    }
    //---------------
    public function getEmployeeWorkingHours(int $employeeId, ?string $fromDate, ?string $toDate, int $limit = 10)
    {
        return $this->repository->getEmployeeWorkingHoursList($this->companyId, $employeeId, $fromDate, $toDate, $limit);
    }
    //---------------
    public function getEmployeeWorkingHoursTotal(int $employeeId, ?string $fromDate, ?string $toDate): string
    {
        $totalSeconds = $this->repository->getEmployeeWorkingHoursTotalHours($this->companyId, $employeeId, $fromDate, $toDate);
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        return $hours . ' orë ' . $minutes . ' min';
    }
}
