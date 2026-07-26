<?php

namespace App\Repositories;

use App\Models\EmployeesModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeesRepository
{
    protected string $table;
    public function __construct(EmployeesModel $model)
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
	public function getEmployees(int $limit, int $companyId)
	{
		return DB::table($this->table)
			->where('company_id', $companyId)
			->orderByDesc('id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getSearchedEmployees(int $limit, string $search, int $companyId)
	{
		return DB::table($this->table)
			->where('employee', 'like', "%{$search}%")
			->where('company_id', $companyId)
			->orderByDesc('id')
			->cursorPaginate($limit);
	}
	//---------------
	public function countAllEmployees(int $companyId): int
	{
		return DB::table($this->table)
			->where('company_id', $companyId)
			->count();
	}
	//---------------
	public function countActiveEmployees(int $companyId): int
	{
		return DB::table($this->table)
			->where('company_id', $companyId)
            ->where('status', 'Active')
			->count();
	}
	//---------------
	public function countBlockedEmployees(int $companyId): int
	{
		return DB::table($this->table)
			->where('company_id', $companyId)
            ->where('status', 'Blocked')
			->count();
	}
    //---------------
    public function checkEmployeeUsername(string $username): bool
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->exists();
    }
    //---------------
    public function getAllEmployees(int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();
    }
    //---------------
    public function findEmployee(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkEmployeeExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function activate(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['status' => 'Active']) > 0;
    }
    //---------------
    public function block(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update(['status' => 'Blocked']) > 0;
    }
}
