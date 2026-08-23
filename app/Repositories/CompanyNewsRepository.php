<?php

namespace App\Repositories;

use App\Models\CompanyNewsModel;
use Illuminate\Support\Facades\DB;

class CompanyNewsRepository
{
	protected string $table;
	public function __construct(CompanyNewsModel $model)
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
	public function getCompanyNews(int $limit)
	{
		return DB::table($this->table . ' as cn')
			->leftJoin('projects as p', 'cn.project_id', '=', 'p.id')
			->select('cn.id', 'cn.date', 'cn.info', 'cn.project_id', 'p.project_name')
			->orderByDesc('cn.id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getSearchedCompanyNews(int $limit, string $search)
	{
		return DB::table($this->table . ' as cn')
			->leftJoin('projects as p', 'cn.project_id', '=', 'p.id')
			->select('cn.id', 'cn.date', 'cn.info', 'cn.project_id', 'p.project_name')
                ->where('cn.info', 'like', "%{$search}%")
                ->orWhere('cn.date', 'like', "%{$search}%")
                ->orWhere('p.project_name', 'like', "%{$search}%")
			->orderByDesc('cn.id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getAllProjects()
	{
		return DB::table('projects')
			->select('id', 'project_name')
			->orderBy('project_name')
			->get();
	}
	//---------------
	public function findCompanyNews(int $id)
	{
		return DB::table($this->table)
			->where('id', $id)
			->first();
	}
	//---------------
	public function checkCompanyNewsExist(int $id): bool
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
	public function getLatestForDashboard(int $companyId, int $limit = 5)
	{
		return DB::table($this->table . ' as cn')
			->leftJoin('projects as p', 'cn.project_id', '=', 'p.id')
			->select('cn.id', 'cn.date', 'cn.info', 'cn.project_id', 'p.project_name')
			->where('cn.company_id', $companyId)
			->orderByDesc('cn.date')
			->orderByDesc('cn.id')
			->limit($limit)
			->get();
	}
}
