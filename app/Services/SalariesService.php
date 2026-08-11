<?php

namespace App\Services;

use App\Repositories\SalariesRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalariesService
{
    protected int $companyId;
    protected SalariesRepository $repository;
    protected FinancialTransactionsService $financialTransactionsService;

    public function __construct(
        SalariesRepository $repository,
        FinancialTransactionsService $financialTransactionsService
    ) {
        $this->repository = $repository;
        $this->financialTransactionsService = $financialTransactionsService;
        $this->companyId = (int) (session('company_id') ?? 0);
    }
    //---------------
    public function createSalary(array $data): bool
    {
        $data['company_id'] = $this->companyId;
        return $this->repository->create($data);
    }
    //---------------
    public function getSalaries(int $limit, string $search = '')
    {
        $limit = $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSalaries($limit, $this->companyId, $search);
        }
        return $this->repository->getSalaries($limit, $this->companyId);
    }
    //---------------
    public function findOrFail(int $id): bool
    {
        return $this->repository->checkSalaryExist($id, $this->companyId);
    }
    //---------------
    public function getSalaryById(int $id)
    {
        return $this->repository->findSalary($id, $this->companyId);
    }
    //---------------
    public function updateSalary(int $id, array $data): bool
    {
        $salary = $this->repository->findSalary($id, $this->companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->update($id, $data, $this->companyId);
    }
    //---------------
    public function deleteSalary(int $id): bool
    {
        $salary = $this->repository->findSalary($id, $this->companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->delete($id, $this->companyId);
    }
    //---------------
    public function calculateSalary(string $fromDate, string $toDate, int $employeeId, int $limit = 10)
    {
        return $this->repository->calculate($fromDate, $toDate, $employeeId, $this->companyId, $limit);
    }
    //---------------
    public function getCalculationSummary(string $fromDate, string $toDate, int $employeeId): array
    {
        return $this->repository->calculateSummary($fromDate, $toDate, $employeeId, $this->companyId);
    }
    //---------------
    public function generateEmployeeSalaryReport(string $fromDate, string $toDate, int $employeeId): array
    {
        return $this->getCalculationSummary($fromDate, $toDate, $employeeId);
    }
    //---------------
    public function saveCalculatedSalaries(string $fromDate, string $toDate, int $employeeId): float
    {
        $calculations = $this->repository->calculateAll($fromDate, $toDate, $employeeId, $this->companyId);

        if ($calculations->isEmpty()) {
            throw new RuntimeException('Nuk u gjetën paga për ruajtje.');
        }

        $salaryDate = $toDate;
        $rows = $calculations->map(function ($row) use ($salaryDate) {
            return [
                'date' => $salaryDate,
                'employee_id' => (int) $row->employee_id,
                'salary' => round((float) $row->total_pay, 2),
                'company_id' => $this->companyId,
            ];
        })->all();

        $total = round((float) $calculations->sum('total_pay'), 2);
        $fromLabel = Carbon::parse($fromDate)->format('d/m/Y');
        $toLabel = Carbon::parse($toDate)->format('d/m/Y');

        DB::transaction(function () use ($rows, $total, $fromLabel, $toLabel, $salaryDate) {
            $this->repository->createMany($rows);

            $this->financialTransactionsService->createTransaction([
                'date' => $salaryDate,
                'type' => 'expense',
                'amount' => $total,
                'comment' => "Pagat per intervalin {$fromLabel} dhe {$toLabel}",
                'company_id' => $this->companyId,
            ]);
        });

        return $total;
    }
}
