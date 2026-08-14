<?php

namespace App\Repositories;

use App\Models\FinancialTransactionsModel;
use Illuminate\Support\Facades\DB;

class FinancialTransactionsRepository
{
    protected string $table;
    public function __construct(FinancialTransactionsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function getTransactions(int $limit, int $companyId)
    {
        $query = DB::table($this->table);

        return $query
            ->orderByDesc('id')
            ->where('company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedTransactions(int $limit, string $search, int $companyId)
    {
        $query = DB::table($this->table);

        return $query
            ->where('comment', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->where('company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getLatestForDashboard(int $companyId, int $limit = 5)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'date', 'comment', 'type', 'amount']);
    }
    //---------------
    public function getRevenues(int $companyId)
    {
        $query = DB::table($this->table);

        return $query
            ->where('type', 'revenue')
            ->where('company_id', $companyId)
            ->sum('amount');
    }
    //---------------
    public function getExpenses(int $companyId)
    {
        $query = DB::table($this->table);

        return $query
            ->where('type', 'expense')
            ->where('company_id', $companyId)
            ->sum('amount');
    }
    //---------------
    public function getTransactionsBetweenDates(string $fromDate, string $toDate, int $limit, int $companyId)
    {
        $query = DB::table($this->table);

        return $query
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->where('company_id', $companyId)
            ->cursorPaginate($limit);
    }
    //---------------
    public function getReportSummary(string $fromDate, string $toDate, int $companyId): array
    {
        $baseQuery = DB::table($this->table)
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->where('company_id', $companyId);

        $revenues = (clone $baseQuery)
            ->where('type', 'revenue')
            ->sum('amount');

        $expenses = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $totalTransactions = (clone $baseQuery)->count();

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'balance' => $revenues - $expenses,
            'transaction_count' => $totalTransactions,
            'average_transaction' => $totalTransactions > 0 ? (clone $baseQuery)->avg('amount') : 0,
        ];
    }
    //---------------
    public function findTransaction(int $id, int $companyId)
    {
        $query = DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId);

        return $query
            ->first();
    }
    //---------------
    public function checkransactionExist(int $id, int $companyId): bool
    {
        $query = DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId);

        return $query
            ->exists();
    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        $query = DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId);

        return $query
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        $query = DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId);

        return $query
            ->delete() > 0;
    }
}
