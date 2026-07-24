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
}
