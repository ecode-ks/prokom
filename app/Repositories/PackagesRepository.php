<?php

namespace App\Repositories;

use App\Models\PackagesModel;
use Illuminate\Support\Facades\DB;

class PackagesRepository
{
    protected string $table;
    public function __construct(PackagesModel $model)
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
    public function getPackages(int $limit)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getSearchedPackages(int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('title', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getAllPackages(string $language)
    {
        return DB::table($this->table)
            ->where('language', $language)
            ->orderByDesc('id')
            ->get();
    }
    //---------------
    public function findPackage(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->first();
    }
    //---------------
    public function checkPackageExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->exists();
    }
    //---------------
    public function update(int $id, array $data): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete() > 0;
    }
    //---------------
    public function getPackageStatistics(): object
    {
        return DB::table($this->table)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('COALESCE(AVG(price), 0) as avg_price')
            ->selectRaw('COALESCE(AVG(months), 0) as avg_months')
            ->first();
    }
    //---------------
    public function change(string $username, int $company_id, string $package, string $paymentExpire, int $staff_limit): bool
    {
        return DB::table('companies')
            ->where('username', $username)
            ->where('id', $company_id)
            ->update(['package' => $package, 'payment_expire' => $paymentExpire, 'staff_limit' => $staff_limit]) > 0;
    }
}
