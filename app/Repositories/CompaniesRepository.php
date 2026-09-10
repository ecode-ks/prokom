<?php

namespace App\Repositories;

use App\Models\CompaniesModel;
use Illuminate\Support\Facades\DB;

class CompaniesRepository
{
    protected string $table;
    public function __construct(CompaniesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllCompanies()
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->get();
    }
    //---------------
    public function getCompanies(int $limit)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedCompanies(int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function registerCompany(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }
    //---------------
    public function checkCompanyExists(string $username): bool
    {
        return DB::table($this->table)->where('username', $username)->exists();
    }
    //---------------
    public function findCompanyByUsername(string $username)
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->first();
    }
    //---------------
    public function updatePasswordByUsername(string $username, string $password): bool
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->update(['password' => $password]) > 0;
    }
    //---------------
    public function updateLanguage(int $id, string $language): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update(['language' => $language]) > 0;
    }
    //---------------
    public function checkIfCompanyIsBlocked(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->where('status', 'Blocked')
            ->exists();
    }
    //---------------
    public function checkCompany(array $credentials)
    {
        return DB::table($this->table)
            ->where('username', $credentials['username'])
            ->first();
    }
    //---------------
    public function findCompanyById(int $id)
    {
        return DB::table($this->table)
            ->leftJoin('packages', 'companies.package', '=', 'packages.id')
            ->select('companies.*', 'packages.title')
            ->where('companies.id', $id)
            ->first();
    }
    //---------------
    public function checkCompanyExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->exists();
    }
    //---------------
    public function activateCompany(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->update(['status' => 'Active']);
    }
    //---------------
    public function blockCompany(int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $companyId)
            ->update(['status' => 'Blocked']);
    }
    //---------------
    public function updateStatus(int $id, string $status): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update(['status' => $status]) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete() > 0;
    }
    //---------------
    public function getCompanyStatistics(): object
    {
        return DB::table($this->table)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN status != 'Active' THEN 1 ELSE 0 END) as inactive_count")
            ->selectRaw('SUM(CASE WHEN payment_expire IS NOT NULL AND payment_expire >= CURDATE() AND payment_expire <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_soon_count')
            ->first();
    }
    //---------------
    public function getLatestForDashboard(int $limit = 5)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'company', 'email', 'package', 'payment_expire', 'status', 'created_at']);
    }
    //---------------
    public function getExpiringSoonForDashboard(int $limit = 5)
    {
        return DB::table($this->table)
            ->whereNotNull('payment_expire')
            ->whereDate('payment_expire', '>=', now()->toDateString())
            ->whereDate('payment_expire', '<=', now()->addDays(30)->toDateString())
            ->orderBy('payment_expire')
            ->limit($limit)
            ->get(['id', 'company', 'package', 'payment_expire', 'status']);
    }
    //---------------
    public function getPaymentStatistics(): object
    {
        $fromDate = now()->startOfMonth()->toDateString();
        $toDate = now()->toDateString();

        $totals = DB::table('payments')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('COALESCE(SUM(price), 0) as total_revenue')
            ->first();

        $month = DB::table('payments')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->selectRaw('COUNT(*) as month_count')
            ->selectRaw('COALESCE(SUM(price), 0) as month_revenue')
            ->first();

        return (object) [
            'total_count' => (int) ($totals->total_count ?? 0),
            'total_revenue' => (float) ($totals->total_revenue ?? 0),
            'month_count' => (int) ($month->month_count ?? 0),
            'month_revenue' => (float) ($month->month_revenue ?? 0),
        ];
    }
    //---------------
    public function getLatestPaymentsForDashboard(int $limit = 5)
    {
        return DB::table('payments')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'date', 'company', 'package', 'price']);
    }
    //---------------
    public function savePayment(array $data): bool
    {
        return DB::table('payments')
            ->insert($data);
    }
    //---------------
    public function getPayments(int $limit)
    {
        return DB::table('payments')
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedPayments(int $limit, string $search)
    {
        return DB::table('payments')
            ->where('company', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
}
