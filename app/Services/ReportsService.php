<?php

namespace App\Services;

use App\Repositories\ReportsRepository;

class ReportsService
{
    protected int $companyId;
    protected ReportsRepository $repository;
    public function __construct(ReportsRepository $repository)
    {
        $this->repository = $repository;
        $this->companyId = session('company_id');
    }
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId)
    {

        return $this->repository->generateEmployeeReport($fromDate, $toDate, $employeeId, $this->companyId);
    }
}
