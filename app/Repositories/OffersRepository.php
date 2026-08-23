<?php

namespace App\Repositories;

use App\Models\OffersModel;
use Illuminate\Support\Facades\DB;

class OffersRepository
{
	protected string $table;
	public function __construct(OffersModel $model)
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
	public function getOffers(int $limit, int $companyId)
	{
		return DB::table($this->table)
			->selectRaw('MAX(id) as id, offer_number, MAX(date) as date, MAX(client) as client, MAX(status) as status')
			->where('company_id', $companyId)
			->groupBy('offer_number')
			->orderByDesc('id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getSearchedOffers(int $limit, string $search, int $companyId)
	{
		return DB::table($this->table)
			->selectRaw('MAX(id) as id, offer_number, MAX(date) as date, MAX(client) as client, MAX(status) as status')
			->where('company_id', $companyId)
			->where(function ($query) use ($search) {
				$query->where('offer_number', 'like', "%{$search}%")
					->orWhere('client', 'like', "%{$search}%");
			})
			->groupBy('offer_number')
			->orderByDesc('id')
			->cursorPaginate($limit);
	}
	//---------------
	public function getOfferRowsByNumber(string $offerNumber, int $companyId)
	{
		return DB::table($this->table)
			->where('offer_number', $offerNumber)
			->where('company_id', $companyId)
			->orderBy('id')
			->get();
	}
	//---------------
	public function getOfferRowById(int $id, int $companyId)
	{
		return DB::table($this->table)
			->where('id', $id)
			->where('company_id', $companyId)
			->first();
	}
	//---------------
	public function updateOfferByNumber(string $offerNumber, array $data, int $companyId): bool
	{
		return DB::table($this->table)
			->where('offer_number', $offerNumber)
			->where('company_id', $companyId)
			->update($data) > 0;
	}
	//---------------
	public function deleteOfferRowById(int $id, int $companyId): bool
	{
		return DB::table($this->table)
			->where('id', $id)
			->where('company_id', $companyId)
			->delete() > 0;
	}
	//---------------
	public function deleteOfferByNumber(string $offerNumber, int $companyId): bool
	{
		return DB::table($this->table)
			->where('offer_number', $offerNumber)
			->where('company_id', $companyId)
			->delete() > 0;
	}
	//---------------
	public function checkOfferNumberExists(string $offerNumber, int $companyId): bool
	{
		return DB::table($this->table)
			->where('offer_number', $offerNumber)
			->where('company_id', $companyId)
			->exists();
	}
	//---------------
	public function getOfferStatistics(int $companyId): object
	{
		return DB::table($this->table)
			->where('company_id', $companyId)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('COUNT(DISTINCT offer_number) as offer_count')
            ->first();
	}
	//---------------
	public function getLatestForDashboard(int $companyId, int $limit = 5)
	{
		return DB::table($this->table)
			->selectRaw('MAX(id) as id, offer_number, MAX(date) as date, MAX(client) as client, MAX(status) as status, COALESCE(SUM(price), 0) as total_price')
			->where('company_id', $companyId)
			->groupBy('offer_number')
			->orderByDesc('id')
			->limit($limit)
			->get();
	}
}
