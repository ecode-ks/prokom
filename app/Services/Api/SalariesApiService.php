<?php

namespace App\Services\Api;

use App\Repositories\EmployeesRepository;
use App\Repositories\SalariesRepository;
use App\Services\FinancialTransactionsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalariesApiService
{
    protected SalariesRepository $repository;
    protected EmployeesRepository $employeesRepository;
    protected FinancialTransactionsService $financialTransactionsService;

    public function __construct(
        SalariesRepository $repository,
        EmployeesRepository $employeesRepository,
        FinancialTransactionsService $financialTransactionsService
    ) {
        $this->repository = $repository;
        $this->employeesRepository = $employeesRepository;
        $this->financialTransactionsService = $financialTransactionsService;
    }

    public function createSalary(array $data, int $companyId): bool
    {
        $data['company_id'] = $companyId;
        return $this->repository->create($data);
    }

    public function getSalaries(int $limit, string $search, int $companyId, ?int $employeeId = null)
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSalaries($limit, $companyId, $search, $employeeId);
        }
        return $this->repository->getSalaries($limit, $companyId, $employeeId);
    }

    public function getAllEmployees(int $companyId)
    {
        return $this->employeesRepository->getAllEmployees($companyId);
    }

    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkSalaryExist($id, $companyId);
    }

    public function getSalaryById(int $id, int $companyId)
    {
        return $this->repository->findSalary($id, $companyId);
    }

    public function updateSalary(int $id, array $data, int $companyId): bool
    {
        $salary = $this->repository->findSalary($id, $companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteSalary(int $id, int $companyId): bool
    {
        $salary = $this->repository->findSalary($id, $companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }

    public function calculateSalary(string $fromDate, string $toDate, int $employeeId, int $companyId, int $limit = 10)
    {
        return $this->repository->calculate($fromDate, $toDate, $employeeId, $companyId, $limit);
    }

    public function getCalculationSummary(string $fromDate, string $toDate, int $employeeId, int $companyId): array
    {
        return $this->repository->calculateSummary($fromDate, $toDate, $employeeId, $companyId);
    }

    public function saveCalculatedSalaries(string $fromDate, string $toDate, int $employeeId, int $companyId): float
    {
        $calculations = $this->repository->calculateAll($fromDate, $toDate, $employeeId, $companyId);

        if ($calculations->isEmpty()) {
            throw new RuntimeException(__('pages.no_salaries_to_save'));
        }

        $salaryDate = $toDate;
        $rows = $calculations->map(function ($row) use ($salaryDate, $companyId) {
            return [
                'date' => $salaryDate,
                'employee_id' => (int) $row->employee_id,
                'salary' => round((float) $row->total_pay, 2),
                'company_id' => $companyId,
            ];
        })->all();

        $total = round((float) $calculations->sum('total_pay'), 2);
        $fromLabel = Carbon::parse($fromDate)->format('d/m/Y');
        $toLabel = Carbon::parse($toDate)->format('d/m/Y');

        DB::transaction(function () use ($rows, $total, $fromLabel, $toLabel, $salaryDate, $companyId) {
            $this->repository->createMany($rows);

            $this->financialTransactionsService->createTransaction([
                'date' => $salaryDate,
                'type' => 'expense',
                'amount' => $total,
                'comment' => "Pagat per intervalin {$fromLabel} dhe {$toLabel}",
                'company_id' => $companyId,
            ]);
        });

        return $total;
    }
}
