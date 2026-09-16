<?php

namespace App\Services;

use App\Repositories\ContractsRepository;

class ContractsService
{
    use DeletesStoredFiles;

    public const PAY_TYPE_MONTHLY = 'monthly';
    public const PAY_TYPE_HOURLY = 'hourly';
    public const PAY_TYPE_DAILY = 'daily';

    protected ContractsRepository $repository;

    public function __construct(ContractsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createContract(array $data): bool
    {
        if (!empty($data['docs'])) {
            $data['docs'] = $this->storeDocs($data['docs']);
        }

        return $this->repository->create($data);
    }
    //---------------
    public function getContracts(int $limit, string $search, string $status, int $companyId, ?int $employeeId = null)
    {
        $limit = (int) $limit ?: 10;
        $this->repository->updateContractsStatus($companyId);

        if (!empty($search) || !empty($status)) {
            return $this->repository->getSearchedContracts($limit, $companyId, $search, $status, $employeeId);
        }

        return $this->repository->getContracts($limit, $companyId, $employeeId);
    }
    //---------------
    public function getContractStatistics(string $search, int $companyId, ?int $employeeId = null): object
    {
        $this->repository->updateContractsStatus($companyId);

        return $this->repository->getContractStatistics($companyId, $search, $employeeId);
    }
    //---------------
    public function getExpiringForDashboard(int $companyId, int $limit = 5)
    {
        $this->repository->updateContractsStatus($companyId);

        return $this->repository->getExpiringForDashboard($companyId, $limit);
    }
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId): object
    {
        $companyId = (int) (session('company_id') ?? 0);

        return $this->repository->getEmployeeContractStatistics($companyId, $employeeId, $fromDate, $toDate);
    }
    //---------------
    public function getEmployeeReportEntries(string $fromDate, string $toDate, int $employeeId, int $limit = 10, string $cursorName = 'contracts_cursor')
    {
        $companyId = (int) (session('company_id') ?? 0);

        return $this->repository->getEmployeeContractEntries($companyId, $employeeId, $fromDate, $toDate, $limit, $cursorName);
    }
    //---------------
    public function getContractDetails(int $id, int $companyId)
    {
        return $this->repository->getContractDetails($id, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkContractExist($id, $companyId);
    }
    //---------------
    public function getContractById(int $id, int $companyId)
    {
        return $this->repository->findContract($id, $companyId);
    }
    //---------------
    public function updateContract(int $id, array $data, int $companyId): bool
    {
        $contract = $this->repository->findContract($id, $companyId);
        if (!$contract) {
            return false;
        }

        if (!empty($data['docs'])) {
            $this->deleteStoredFiles($contract->docs ?? null);
            $data['docs'] = $this->storeDocs($data['docs']);
        } else {
            unset($data['docs']);
        }

        $data['status'] = 'Active';

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteContract(int $id, int $companyId): bool
    {
        $contract = $this->repository->findContract($id, $companyId);
        if (!$contract) {
            return false;
        }

        $this->deleteStoredFiles($contract->docs ?? null);

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function deleteStoredFilesForCompany(int $companyId): void
    {
        $this->deleteManyStoredFiles($this->repository->getDocPayloadsByCompany($companyId));
    }
    //---------------
    private function storeDocs(array $docs): string
    {
        $paths = [];

        foreach ($docs as $doc) {
            $fileName = pathinfo($doc->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . time() . '_' . uniqid()
                . '.' . $doc->getClientOriginalExtension();

            $paths[] = $doc->storeAs('contracts', $fileName, 'public');
        }

        return json_encode($paths);
    }
    //---------------
    public static function payTypes(): array
    {
        return [
            self::PAY_TYPE_MONTHLY,
            self::PAY_TYPE_HOURLY,
            self::PAY_TYPE_DAILY,
        ];
    }
    //---------------
    public static function payTypeLabel(string $type): string
    {
        return match ($type) {
            self::PAY_TYPE_HOURLY => __('pages.pay_type_hourly'),
            self::PAY_TYPE_DAILY => __('pages.pay_type_daily'),
            default => __('pages.pay_type_monthly'),
        };
    }
    //---------------
    public static function payRateLabel(string $type): string
    {
        return match ($type) {
            self::PAY_TYPE_HOURLY => __('pages.pay_rate_hourly'),
            self::PAY_TYPE_DAILY => __('pages.pay_rate_daily'),
            default => __('pages.pay_rate_monthly'),
        };
    }
    //---------------
    public static function payTypeOptions(): array
    {
        return [
            self::PAY_TYPE_MONTHLY => self::payTypeLabel(self::PAY_TYPE_MONTHLY),
            self::PAY_TYPE_HOURLY => self::payTypeLabel(self::PAY_TYPE_HOURLY),
            self::PAY_TYPE_DAILY => self::payTypeLabel(self::PAY_TYPE_DAILY),
        ];
    }
}
