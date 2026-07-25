<?php

namespace App\Services;

use App\Repositories\FinancialTransactionsRepository;

class FinancialTransactionsService
{
    protected FinancialTransactionsRepository $repository;
    public function __construct(FinancialTransactionsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function createTransaction(array $data): bool
    {
        return $this->repository->create($data);
    }
    //---------------
    public function getTransactions(int $limit, string $search = '', int $companyId)
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedTransactions($limit, $search, $companyId);
        }
        return $this->repository->getTransactions($limit, $companyId);
    }
    //---------------
    public function getRevenues(int $companyId)
    {
        return $this->repository->getRevenues($companyId);
    }
    //---------------
    public function getExpenses(int $companyId)
    {
        return $this->repository->getExpenses($companyId);
    }
    //---------------
    public function getFinancialReport(string $fromDate, string $toDate, int $limit, int $companyId)
    {
        return [
            'transactions' => $this->repository->getTransactionsBetweenDates($fromDate, $toDate, $limit, $companyId),
            'summary' => $this->repository->getReportSummary($fromDate, $toDate, $companyId),
        ];
    }
    //---------------
    public function findOrFail(int $id, int $companyId): bool
    {
        return $this->repository->checkransactionExist($id, $companyId );
    }
    //---------------
    public function getTransactionById(int $id, int $companyId)
    {
        return $this->repository->findTransaction($id, $companyId);
    }
    //---------------
    public function updateTransaction(int $id, array $data, int $companyId): bool
    {
        $client = $this->repository->findTransaction($id, $companyId);
        if (!$client) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteTransaction(int $id, int $companyId): bool
    {
        $client = $this->repository->findTransaction($id, $companyId);
        if (!$client) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
